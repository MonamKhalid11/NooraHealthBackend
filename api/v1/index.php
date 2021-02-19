<?php

require_once '../include/db_connect.php';
require '../libs/Slim/Slim.php';
\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

class MCrypt
{
    function __construct()
    {
    }

    function encrypt($str, $key) {
				
		$block_size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128,MCRYPT_MODE_CBC);
		$pad = $block_size - (strlen($str) % $block_size);
		$str .= str_repeat(chr($pad),$pad);
		
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128,MCRYPT_MODE_CBC);
		$iv = mcrypt_create_iv($iv_size,MCRYPT_DEV_URANDOM);
				
		$td = mcrypt_module_open('rijndael-128', '', 'cbc', $iv);
		mcrypt_generic_init($td, $key, $iv);
		$encrypted = mcrypt_generic($td, $str);
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
		
		//IMPLEMENT HMAC
		$hmac = hash_hmac("sha256",$iv.$encrypted,$key,true);
		
		$toSend = $iv.$encrypted.$hmac;
		
		return base64_encode($toSend);//$encrypted);
    }

    function decrypt($code, $key) {
		$message = base64_decode($code);
				
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128,MCRYPT_MODE_CBC);
		$iv = substr($message,0,$iv_size);
		
		$hmac_size = strlen(hash_hmac("sha256","","",true));
		$hmac = substr($message,-$hmac_size);
		
		$crypt = substr($message,$iv_size,-$hmac_size);
		
		$crypt_hmac = hash_hmac("sha256",$iv.$crypt,$key,true);
		if ($hmac!=$crypt_hmac){ 
			return "HMAC ERROR\n";
		}
		
		//$iv = $this->iv;
		$td = mcrypt_module_open('rijndael-128', '', 'cbc', $iv);
		mcrypt_generic_init($td, $key, $iv);
		$decrypted = mdecrypt_generic($td, $crypt);
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
		return utf8_encode(trim($decrypted));
    }

    protected function hex2bin($hexdata) {
      $bindata = '';
      for ($i = 0; $i < strlen($hexdata); $i += 2) {
        $bindata .= chr(hexdec(substr($hexdata, $i, 2)));
      }
      return $bindata;
    }
}

function generateStrongPassword($length = 9, $add_dashes = false, $available_sets = 'luds'){
	$sets = array();
	if(strpos($available_sets, 'l') !== false)
		$sets[] = 'abcdefghjkmnpqrstuvwxyz';
	if(strpos($available_sets, 'u') !== false)
		$sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
	if(strpos($available_sets, 'd') !== false)
		$sets[] = '123456789';
	if(strpos($available_sets, 's') !== false)
		$sets[] = '!@#$%&*?';
	$all = '';
	$password = '';
	foreach($sets as $set)
	{
		$password .= $set[array_rand(str_split($set))];
		$all .= $set;
	}
	$all = str_split($all);
	for($i = 0; $i < $length - count($sets); $i++)
		$password .= $all[array_rand($all)];
	$password = str_shuffle($password);
	if(!$add_dashes)
		return $password;
	$dash_len = floor(sqrt($length));
	$dash_str = '';
	while(strlen($password) > $dash_len)
	{
		$dash_str .= substr($password, 0, $dash_len) . '-';
		$password = substr($password, $dash_len);
	}
	$dash_str .= $password;
	return $dash_str;
}

function authenticateForRole($db_con, $app, $req = true) {
    
	include("../include/constant.php");
	$mcrypt = new MCrypt();	
	$body = $app->request()->getBody();
	
	$api_type = $app->request()->headers->get($TAG_API_TYPE);	
	$device_id = $app->request()->headers->get($TAG_API_TOKEN);	
	$head = $app->request()->headers()->all();
	
	if(isset($device_id)){
	
		//1. CHECK DEVICE PRESENT OR NOT
		$tokenFound= registerDevice($db_con, $device_id, $api_type);

		if($tokenFound[$TAG_SUCCESS]){
		
			//2. CHECK ENCRYPTION TOKEN
			$encryption_key = getEncryptionKey($db_con, $device_id, $api_type);
			
			$decrypted = $mcrypt->decrypt($body, $encryption_key);
			$decrypted = trim($decrypted, "\x00..\x1F");
			$data = json_decode($decrypted, true);
			
			//$user_id 				= $data[$TAG_APPUSER_ID];
			//$request_access_token  	= $data[$TAG_ACCESS_TOKEN];
			
			if ( $data[$TAG_TOKEN] != $APP_TOKEN ) {
				$response["code"] 		= 400;
				$response["message"]	= "Bad Authentication Token";	
				echoRespnse(201, $response, $encryption_key);			
				return array($TAG_SUCCESS=>false, $TAG_DETAILS=>$data);
			}  	
			
			
			$data_new = array();
			foreach($data as $key => $value) {
				$input = $value;
				$input = htmlspecialchars($input);
				$input = urldecode($input);
				$input = stripslashes($input);
				$input = mysqli_real_escape_string($db_con,$input);
				$data_new[$key] = $input;
			}
			$data = $data_new;
			
			
			if($api_type == 0){
				$response["code"] 		= 401;
				$response["message"]	= "Encryption invalid" ;	
				$response["key"]		= $tokenFound[$TAG_DETAILS];
				echoRespnse(201, $response, $encryption_key);
			}

			$head_new = array();
			foreach($head as $key => $value) {
				$new_key = $key;
				$new_key = strtolower($new_key);
				$new_key = str_replace('-', '_', $new_key);
				$head_new[$new_key] = $value;
			}
			$head = $head_new;
			
			return array($TAG_SUCCESS=>true, $TAG_DETAILS=>$data, $TAG_HEADER=>$head, $TAG_APP_KEY=>$encryption_key);		
		}
	}
	
	$response["code"] 		= 401;
	$response["message"]	= "Encryption invalid";	
	$response["key"]		= $tokenFound[$TAG_DETAILS];	
	//$response["data"]		= $tokenFound[$TAG_DATA];	
	echoRespnse(201, $response, $device_id);	
	
	return array($TAG_SUCCESS=>false, $TAG_DETAILS=>$response);
};

function registerDevice($db_con, $device_id, $api_type) {
    
	include("../include/constant.php");
	
	$query	= "SELECT * FROM `$USER_APP_KEY_TABLE` WHERE `$USER_APP_KEY_DEVICE_ID` = '$device_id'";
	$result = mysqli_query($db_con,$query);	
		
	date_default_timezone_set('Asia/Kolkata');
	$dateNow = date("Y-m-d H:i:s");	
	$expiry = date('Y-m-d', strtotime($dateNow. ' + 1 days'));
	$enc_key = generateStrongPassword(16, false, 'lud');
	
	$user_path3 = 'testPath';
	
	if($result){
	
		if(mysqli_num_rows($result) == 0){
			
			$query	= "INSERT INTO `$USER_APP_KEY_TABLE` (`$USER_APP_KEY_DEVICE_ID`, `$USER_APP_KEY_APP_KEY`, `$USER_APP_KEY_KEY_EXPIRY`, `$USER_APP_KEY_CREATED_DATE`) VALUES ('$device_id', '$enc_key', '$expiry', '$dateNow')";
			$result = mysqli_query($db_con,$query);	
			return array($TAG_SUCCESS=>false, $TAG_DETAILS=>$enc_key);
				
		}else{
			
			$row = mysqli_fetch_array($result);
			$enc_key = $row[$USER_APP_KEY_APP_KEY];
			if($api_type == 0){				
				return array($TAG_SUCCESS=>false, $TAG_DETAILS=>$enc_key);				
			}
		}
		return array($TAG_SUCCESS=>true, $TAG_DETAILS=>$enc_key);
	}	
	
	$error = mysqli_error($db_con);//mysqli_error	
	return array($TAG_SUCCESS=>false, $TAG_DETAILS=>$enc_key, $TAG_DATA=>$error);
};

function getEncryptionKey($db_con, $device_id, $api_type) {
    
	include("../include/constant.php");
		
	$query	= "SELECT * FROM `$USER_APP_KEY_TABLE` WHERE `$USER_APP_KEY_DEVICE_ID` = '$device_id'";
	$result = mysqli_query($db_con,$query);	
	
	if(mysqli_num_rows($result) != 0){
		$row = mysqli_fetch_array($result);
		
		$encryption_key = $row[$USER_APP_KEY_APP_KEY];
		
		if($api_type == 0){
			return $device_id;
		}
		
		return $encryption_key;
	}
	
	return '';
};

function authenticate($db_con, $app, $req = true) {
	include("../include/constant.php");
	$body = $app->request()->getBody();
	$head = $app->request()->headers()->all();
	$token = $app->request()->headers->get($TAG_TOKEN);
	$encryption_key = "";
	$allPostPutVars = $app->request()->params();
	$data = $body;
	$data = $allPostPutVars;
	$success = true;
	
		
	$head_new = array();
	foreach($head as $key => $value) {
		$new_key = $key;
		$new_key = strtolower($new_key);
		$new_key = str_replace('-', '_', $new_key);
		$head_new[$new_key] = $value;
	}
	$head = $head_new;
	
	if ($token != $APP_TOKEN) {		
		$success = false;
		$response = array();
		$response["code"] 		= 400;
		$response["success"] 	= $success;
		$response["message"]	= "Bad Authentication Token ";
		$response["head"]	= $head;
		echoRespnse(201, $response);
	}
	
	return array($TAG_SUCCESS=>$success, $TAG_DETAILS=>$data, $TAG_HEADER=>$head, $TAG_APP_KEY=>$encryption_key);
};

function authenticateGet($db_con, $app, $req = true) {
	include("../include/constant.php");
	$encryption_key = "";
	$allPostPutVars = $app->request()->params();
	$data = $allPostPutVars; 
	$success = true;
	
	$head_new = array();
	$head = $head_new;
	
	return array($TAG_SUCCESS=>$success, $TAG_DETAILS=>$data, $TAG_HEADER=>$head, $TAG_APP_KEY=>$encryption_key);
};

/************************************  Common Functions  ******************************************/
	
/* getNurseRow
*/
function getNurseRow($db_con,$row) {
	
	include("../include/constant.php");		
	$detail = array();							
	$detail[$TAG_ID]				= $row[$NURSE_ID];
	$detail[$TAG_FIRST_NAME]		= $row[$NURSE_FIRST_NAME];
	$detail[$TAG_LAST_NAME]			= $row[$NURSE_LAST_NAME];
	$detail[$TAG_DOB]				= $row[$NURSE_DOB];
	$detail[$TAG_MOBILE_NUMBER]		= $row[$NURSE_MOBILE_NUMBER];
	$detail[$TAG_PROFILE_IMAGE]		= setImagePath($NURSE_PATH_TYPE, $row[$NURSE_PROFILE_IMAGE]);
	
	return $detail;
}
	
/* getNurseProfileRow
*/
function getNurseProfileRow($db_con,$row) {
	
	include("../include/constant.php");		
	$detail = array();							
	$detail[$TAG_ID]					= $row[$NURSE_ID];
	$detail[$TAG_HOSPITAL_ID]			= $row[$NURSE_HOSPITAL_ID];
	$detail[$TAG_HOSPITAL_CONDITION_ID]	= $row[$NURSE_HOSPITAL_CONDITION_ID];
	$detail[$TAG_HOSPITAL_JOINING_DATE]	= $row[$NURSE_HOSPITAL_JOINING_DATE];
	$detail[$TAG_BADGE_LEVEL]			= $row[$NURSE_BADGE_LEVEL];
	$detail[$TAG_GRADUATING_YEAR]		= $row[$NURSE_GRADUATING_YEAR];
	$detail[$TAG_TOT_DATE]				= $row[$NURSE_TOT_DATE];
	$detail[$TAG_DESIGNATION]			= $row[$NURSE_DESIGNATION];
	$detail[$TAG_STATUS]				= $row[$NURSE_STATUS];
	
	$_nurse_id				= $row[$NURSE_ID];
	$_hospital_id			= $row[$NURSE_HOSPITAL_ID];
	$_city_id				= $row[$NURSE_CITY_ID];
	$_hospital_condition_id	= $row[$NURSE_HOSPITAL_CONDITION_ID];
	
	$detail[$TAG_HOSPITAL]				= getHospital($db_con,$_hospital_id);
	$detail[$TAG_HOSPITAL_CONDITION]	= getHospitalCondition($db_con,$_hospital_condition_id);
	
	$detail[$TAG_LOCATION]				= getLocation($db_con, $_city_id);
	
	$detail[$TAG_NURSE]			= getNurse($db_con,$_nurse_id);
	
	return $detail;
}

/* getHospitalRow
*/
function getHospitalRow($db_con,$row) {
	
	include("../include/constant.php");		
	$detail = array();							
	$detail[$TAG_ID]			= $row[$HOSPITAL_ID];
	$detail[$TAG_NAME]			= $row[$HOSPITAL_NAME];
	$detail[$TAG_STATUS]		= $row[$HOSPITAL_STATUS];
	$detail[$TAG_ENTRY_TIME]	= $row[$HOSPITAL_ENTRY_TIME];
	//$detail[$TAG_EDIT_TIME]		= $row[$HOSPITAL_EDIT_TIME];
	
	return $detail;
}

/* getHospitalConditionRow
*/
function getHospitalConditionRow($db_con,$row) {
	
	include("../include/constant.php");		
	$detail = array();							
	$detail[$TAG_ID]			= $row[$HOSPITAL_MEDICAL_CONDITION_ID];
	$detail[$TAG_HOSPITAL_ID]	= $row[$HOSPITAL_MEDICAL_CONDITION_HOSPITAL_ID];
	$detail[$TAG_NAME]			= $row[$HOSPITAL_MEDICAL_CONDITION_MODICAL_CONDITION];
	$detail[$TAG_STATUS]		= $row[$HOSPITAL_MEDICAL_CONDITION_STATUS];
	$detail[$TAG_ENTRY_TIME]	= $row[$HOSPITAL_MEDICAL_CONDITION_ENTRY_TIME];
	$detail[$TAG_EDIT_TIME]		= $row[$HOSPITAL_MEDICAL_CONDITION_EDIT_TIME];
	
	return $detail;
}

/* getAdminUserRow
*/
function getAdminUserRow($db_con,$row) {
	
	include("../include/constant.php");		
	$detail = array();							
	$detail[$TAG_ID]				= $row[$ADMIN_USER_ID];
	$detail[$TAG_FIRST_NAME]		= $row[$ADMIN_USER_FIRST_NAME];
	$detail[$TAG_LAST_NAME]			= $row[$ADMIN_USER_LAST_NAME];
	$detail[$TAG_PROFILE_IMAGE]		= setImagePath($ADMIN_PATH_TYPE, $row[$ADMIN_USER_PROFILE_IMAGE]);
	
	return $detail;
}

/* getCommentRow
*/
function getCommentRow($db_con,$row) {
	
	include("../include/constant.php");		
	$detail = array();							
	$detail[$TAG_ID]				= $row[$CONTENT_COMMENT_ID];
	$detail[$TAG_CONTENT_ID]		= $row[$CONTENT_COMMENT_CONTENT_ID];
	$detail[$TAG_COMMENT]			= $row[$CONTENT_COMMENT_COMMENT];
	$detail[$TAG_NURSE_ID]			= $row[$CONTENT_COMMENT_LOGIN_USER_ID];
	$detail[$TAG_STATUS]			= $row[$CONTENT_COMMENT_STATUS];
	$detail[$TAG_ENTRY_TIME]		= $row[$CONTENT_COMMENT_ENTRY_TIME];
	
	//FETCH NURSE DETAIL
	$_nurse_id		= $row[$CONTENT_COMMENT_LOGIN_USER_ID];
	$detail[$TAG_USER] = getNurse($db_con, $_nurse_id);
	
	return $detail;
}

/* getContentRow
*/
function getContentRow($db_con,$row, $_user_id) {
	
	include("../include/constant.php");		
	$detail = array();	

    $detail[$TAG_ID] 			= $row[$CONTENT_ID];
    $detail[$TAG_TITLE] 		= $row[$CONTENT_TITLE];
    $detail[$TAG_DESCRIPTION] 	= $row[$CONTENT_DESCRIPTION];
    //$detail[$TAG_ROLE] 			= $row[$CONTENT_ROLE]; //USER ROLE
    $detail[$TAG_CONTENT_TYPE] 	= $row[$CONTENT_CONTENT_TYPE];
    $detail[$TAG_ATTACHMENT] 	= $row[$CONTENT_ATTACHMENT];	//Youtube Video URL
    //$detail[$TAG_LOGIN_USER_ID] = $row[$CONTENT_LOGIN_USER_ID];
    $detail[$TAG_ENTRY_TIME] 	= $row[$CONTENT_ENTRY_TIME];
    $detail[$TAG_EDIT_TIME] 	= $row[$CONTENT_EDIT_TIME];
	
    $_content_id 	= $row[$CONTENT_ID];
	
	//GET ATTACHMENT List	
	$detail[$TAG_ATTACHMENT_LIST] = getAttachmentList($db_con, $_content_id);
	
	//GET Group List	
	$detail[$TAG_GROUP] = getGroupList($db_con, $_content_id);
	
	//GET USER
    $_admin_id 	= $row[$CONTENT_LOGIN_USER_ID];
	$detail[$TAG_USER] = getAdminUser($db_con, $_admin_id);
	
	//GET COUTS
	$detail[$TAG_LIKE_COUNT] 	= getContentLikeCount($db_con, $_content_id);
	$detail[$TAG_COMMENT_COUNT] = getContentCommentCount($db_con, $_content_id);
	
	$detail[$TAG_LAST_COMMENT] = getContentLastComment($db_con, $_content_id);
	
	//IS LIKED
	$detail[$TAG_LIKE] = isContentLikedByNurse($db_con, $_content_id, $_user_id);
	
	//IS VIEWED
	$detail[$TAG_VIEW] = isContentViewedByNurse($db_con, $_content_id, $_user_id);
	
	$isUserContent = isUserContent($db_con, $_content_id, $_user_id);
	if($isUserContent){
		$detail[$TAG_STATUS] 		= $STATUS_ACTIVE;
	}else{
		$detail[$TAG_STATUS] 		= $STATUS_DELETED;		
	}
	
	return $detail;
}	

/* getClassRow
*/
function getClassRow($db_con,$row) {
	
	include("../include/constant.php");		
	$detail = array();	

    $detail[$TAG_ID] 			= $row[$CCP_ATTENDANCE_ID];
    $detail[$TAG_CLASS_TYPE_ID] = $row[$CCP_ATTENDANCE_CLASS_TYPE_ID];
    $detail[$TAG_IMAGE] 		= setImagePath($CLASS_PATH_TYPE, $row[$CCP_ATTENDANCE_IMAGE]);
    $detail[$TAG_CLASS_DATE] 	= $row[$CCP_ATTENDANCE_CLASS_DATE];
    $detail[$TAG_CLASS_TIME] 	= $row[$CCP_ATTENDANCE_CLASS_TIME];
    $detail[$TAG_NO_OF_PEOPLE] 	= $row[$CCP_ATTENDANCE_NO_OF_PEOPLE];
    $detail[$TAG_NO_OF_FAMILY] 	= $row[$CCP_ATTENDANCE_NO_OF_FAMILY];
    $detail[$TAG_STATUS] 		= $row[$CCP_ATTENDANCE_STATUS];
    $detail[$TAG_ENTRY_TIME] 	= $row[$CCP_ATTENDANCE_ENTRY_TIME];
    $detail[$TAG_EDIT_TIME] 	= $row[$CCP_ATTENDANCE_EDIT_TIME];
	
	//GET USER
    $_class_type_id 	= $row[$CCP_ATTENDANCE_CLASS_TYPE_ID];
	$detail[$TAG_CLASS_TYPE] = getClassType($db_con, $_class_type_id);
	
	return $detail;
}
	
/* getClassTypeRow
*/
function getClassTypeRow($db_con,$row) {
	
	include("../include/constant.php");		
	$detail = array();	

    $detail[$TAG_ID] 			= $row[$CCP_CLASS_TYPE_ID];
    $detail[$TAG_NAME] 			= $row[$CCP_CLASS_TYPE_CLASS_TYPE];
    $detail[$TAG_STATUS] 		= $row[$CCP_CLASS_TYPE_STATUS];
    $detail[$TAG_ENTRY_TIME] 	= $row[$CCP_CLASS_TYPE_ENTRY_TIME];
    $detail[$TAG_EDIT_TIME] 	= $row[$CCP_CLASS_TYPE_EDIT_TIME];
	return $detail;
}
	
/* getAttachmentRow
*/
function getAttachmentRow($db_con,$row) {
	
	include("../include/constant.php");		
	$detail = array();	

    $detail[$TAG_ID] 			= $row[$CONTENT_ATTACHMENT_ID];
    $detail[$TAG_CONTENT_ID] 	= $row[$CONTENT_ATTACHMENT_CONTENT_ID];
    $detail[$TAG_ATTACHMENT] 	= setImagePath($CONTENT_PATH_TYPE, $row[$CONTENT_ATTACHMENT_ATTACHMENT]);
    $detail[$TAG_STATUS] 		= $row[$CONTENT_ATTACHMENT_STATUS];
    $detail[$TAG_ENTRY_TIME] 	= $row[$CONTENT_ATTACHMENT_ENTRY_TIME];
    $detail[$TAG_EDIT_TIME] 	= $row[$CONTENT_ATTACHMENT_EDIT_TIME];
	return $detail;
}
	
/* getGroupRow
*/
function getGroupRow($db_con,$row) {
	
	include("../include/constant.php");		
	$detail = array();	

    $detail[$TAG_ID] 			= $row[$CONTENT_GROUP_ID];
    $detail[$TAG_CONTENT_ID] 	= $row[$CONTENT_GROUP_CONTENT_ID];
    $detail[$TAG_GROUP_ID] 		= $row[$CONTENT_GROUP_GROUP_ID];
    $detail[$TAG_STATUS] 		= $row[$CONTENT_GROUP_STATUS];
    $detail[$TAG_ENTRY_TIME] 	= $row[$CONTENT_GROUP_ENTRY_TIME];
    $detail[$TAG_EDIT_TIME] 	= $row[$CONTENT_GROUP_EDIT_TIME];
	return $detail;
}	

//FETCH DATE

/* getMasterOTP
*/
function getMasterOTP($db_con) {
	
	include("../include/constant.php");		
	$detail = array();							
	$query = "SELECT * FROM `$APP_SETTING_TABLE` WHERE `$APP_SETTING_NAME` = '$TAG_MASTER_OTP'";
	$result = mysqli_query($db_con,$query);
	
	if($result){
		if(mysqli_num_rows($result) != 0){
			$row = mysqli_fetch_array($result);
			return $row[$APP_SETTING_VALUE]."";
		}
	}	
	return "1234";
}

/* getAttachmentList
*/
function getAttachmentList($db_con,$_content_id) {
	
	include("../include/constant.php");		
	$response = array();						
	$detail = array();							
	$query = "SELECT * FROM `$CONTENT_ATTACHMENT_TABLE` WHERE `$CONTENT_ATTACHMENT_CONTENT_ID` = '$_content_id' AND `$CONTENT_ATTACHMENT_STATUS` = '$STATUS_ACTIVE'";
	$result = mysqli_query($db_con,$query);
	
	if($result){
		if(mysqli_num_rows($result) != 0){
			while ($row = mysqli_fetch_array($result)) {				
				$detail = getAttachmentRow($db_con, $row);
				array_push($response, $detail);
			}
		}
	}	
	return $response;
}

/* getGroupList
*/
function getGroupList($db_con,$_content_id) {
	
	include("../include/constant.php");		
	$response = array();						
	$detail = array();							
	$query = "SELECT * FROM `$CONTENT_GROUP_TABLE` WHERE `$CONTENT_GROUP_CONTENT_ID` = '$_content_id' AND `$CONTENT_GROUP_STATUS` = '$STATUS_ACTIVE'";
	$result = mysqli_query($db_con,$query);
	
	if($result){
		if(mysqli_num_rows($result) != 0){
			while ($row = mysqli_fetch_array($result)) {				
				$detail = getGroupRow($db_con, $row);
				array_push($response, $detail);
			}
		}
	}	
	return $response;
}

/* getAdminUser
*/
function getAdminUser($db_con,$_admin_id) {
	
	include("../include/constant.php");		
	$detail = array();							
	$query = "SELECT * FROM `$ADMIN_USER_TABLE` WHERE `$ADMIN_USER_ID` = '$_admin_id'";
	$result = mysqli_query($db_con,$query);
	
	if($result){
		if(mysqli_num_rows($result) != 0){
			$row = mysqli_fetch_array($result);
			$detail = getAdminUserRow($db_con, $row);
			return $detail;
		}
	}	
	return null;
}

/* getNurse
*/
function getNurse($db_con,$_nurse_id) {
	
	include("../include/constant.php");		
	$detail = array();							
	$query = "SELECT * FROM `$NURSE_TABLE` WHERE `$NURSE_ID` = '$_nurse_id'";
	$result = mysqli_query($db_con,$query);
	
	if($result){
		if(mysqli_num_rows($result) != 0){
			$row = mysqli_fetch_array($result);
			$detail = getNurseRow($db_con, $row);
			return $detail;
		}
	}	
	return null;
}

/* getNurseGroupDate
*/
function getNurseGroupDate($db_con,$_nurse_id) {
	
	include("../include/constant.php");		
	$detail = array();							
	$query = "SELECT * FROM `$GROUP_USER_TABLE` WHERE `$GROUP_USER_USER_ID` = '$_nurse_id' ORDER BY `$GROUP_USER_EDIT_TIME` DESC LIMIT 1";
	$result = mysqli_query($db_con,$query);
	
	if($result){
		if(mysqli_num_rows($result) != 0){
			$row = mysqli_fetch_array($result);
			$date = $row[$GROUP_USER_EDIT_TIME];
			return $date;
		}
	}	
	return "";
}

/* getLocation
*/
function getLocation($db_con,$_city_id) {
	
	include("../include/constant.php");		
	$detail = array();							
	$query = "SELECT * FROM `$CITY_TABLE` WHERE `$CITY_ID` = '$_city_id'";
	$result = mysqli_query($db_con,$query);
	
	if($result){
		if(mysqli_num_rows($result) != 0){
			$row = mysqli_fetch_array($result);
						
			$detail[$TAG_ID]			= $row[$CITY_ID];
			$detail[$TAG_CITY_NAME]		= $row[$CITY_NAME];
			$detail[$TAG_STATE_NAME]	= getStateName($db_con,$_city_id);
			$detail[$TAG_COUNTRY_NAME]	= getCountryName($db_con,$_city_id);
			
			return $detail;
		}
	}	
	return null;
}

/* getStateName
*/
function getStateName($db_con,$_city_id) {
	
	include("../include/constant.php");		
	$name = "";							
	$query = "SELECT * FROM `$STATE_TABLE` WHERE `$STATE_ID` IN (SELECT `$CITY_STATE_ID` FROM `$CITY_TABLE` WHERE `$CITY_ID` = '$_city_id')";
	$result = mysqli_query($db_con,$query);
	
	if($result){
		if(mysqli_num_rows($result) != 0){
			$row = mysqli_fetch_array($result);						
			$name		= $row[$STATE_NAME];			
			return $name;
		}
	}	
	return $name;
}

/* getCountryName
*/
function getCountryName($db_con,$_city_id) {
	
	include("../include/constant.php");		
	$name = "";							
	$query = "SELECT * FROM `$COUNTRY_TABLE` WHERE `$COUNTRY_ID` IN (SELECT `$STATE_COUNTRY_ID` FROM `$STATE_TABLE` WHERE `$STATE_ID` IN (SELECT `$CITY_STATE_ID` FROM `$CITY_TABLE` WHERE `$CITY_ID` = '$_city_id'))";
	$result = mysqli_query($db_con,$query);
	
	if($result){
		if(mysqli_num_rows($result) != 0){
			$row = mysqli_fetch_array($result);						
			$name		= $row[$COUNTRY_NAME];			
			return $name;
		}
	}	
	return $name;
}

/* getHospital
*/
function getHospital($db_con,$_hospital_id) {
	
	include("../include/constant.php");		
	$detail = array();							
	$query = "SELECT * FROM `$HOSPITAL_TABLE` WHERE `$HOSPITAL_ID` = '$_hospital_id'";
	$result = mysqli_query($db_con,$query);
	
	if($result){
		if(mysqli_num_rows($result) != 0){
			$row = mysqli_fetch_array($result);
			$detail = getHospitalRow($db_con, $row);
			return $detail;
		}
	}	
	return null;
}

/* getHospitalCondition
*/
function getHospitalCondition($db_con,$_hospital_condition_id) {
	
	include("../include/constant.php");		
	$detail = array();							
	$query = "SELECT * FROM `$HOSPITAL_MEDICAL_CONDITION_TABLE` WHERE `$HOSPITAL_MEDICAL_CONDITION_ID` = '$_hospital_condition_id'";
	$result = mysqli_query($db_con,$query);
	
	if($result){
		if(mysqli_num_rows($result) != 0){
			$row = mysqli_fetch_array($result);
			$detail = getHospitalConditionRow($db_con, $row);
			return $detail;
		}
	}	
	return null;
}

/* getClassType
*/
function getClassType($db_con,$_class_type_id) {
	
	include("../include/constant.php");		
	$detail = array();							
	$query = "SELECT * FROM `$CCP_CLASS_TYPE_TABLE` WHERE `$CCP_CLASS_TYPE_ID` = '$_class_type_id'";
	$result = mysqli_query($db_con,$query);
	
	if($result){
		if(mysqli_num_rows($result) != 0){
			$row = mysqli_fetch_array($result);
			$detail = getClassTypeRow($db_con, $row);
			return $detail;
		}
	}	
	return null;
}


/* getContentLikeCount
*/
function getContentLikeCount($db_con,$_content_id) {
	
	include("../include/constant.php");		
	$detail = array();							
	$query = "SELECT count(*) AS $TAG_LIKE_COUNT FROM `$CONTENT_LIKE_TABLE` WHERE `$CONTENT_LIKE_CONTENT_ID` = '$_content_id' AND `$CONTENT_LIKE_STATUS` = '$STATUS_ACTIVE'";
	$result = mysqli_query($db_con,$query);
	
	if($result){
		if(mysqli_num_rows($result) != 0){
			$row = mysqli_fetch_array($result);
			$detail = $row[$TAG_LIKE_COUNT];
			return $detail;
		}
	}	
	return 0;
}

/* getContentCommentCount
*/
function getContentCommentCount($db_con,$_content_id) {
	
	include("../include/constant.php");		
	$detail = array();							
	$query = "SELECT count(*) AS $TAG_LIKE_COUNT FROM `$CONTENT_COMMENT_TABLE` WHERE `$CONTENT_COMMENT_CONTENT_ID` = '$_content_id' AND `$CONTENT_COMMENT_STATUS` = '$STATUS_ACTIVE'";
	$result = mysqli_query($db_con,$query);
	
	if($result){
		if(mysqli_num_rows($result) != 0){
			$row = mysqli_fetch_array($result);
			$detail = $row[$TAG_LIKE_COUNT];
			return $detail;
		}
	}	
	return 0;
}

/* getContentLastComment
*/
function getContentLastComment($db_con,$_content_id) {
	
	include("../include/constant.php");		
	$detail = array();							
	$query = "SELECT * FROM `$CONTENT_COMMENT_TABLE` WHERE `$CONTENT_COMMENT_CONTENT_ID` = '$_content_id' AND `$CONTENT_COMMENT_STATUS` = '$STATUS_ACTIVE' ORDER BY `$CONTENT_COMMENT_ENTRY_TIME` DESC";
	$result = mysqli_query($db_con,$query);
	
	if($result){
		if(mysqli_num_rows($result) != 0){
			$row = mysqli_fetch_array($result);
			$detail = getCommentRow($db_con,$row);
			return $detail;
		}
	}	
	return null;
}

/* isContentLikedByNurse
*/
function isContentLikedByNurse($db_con,$_content_id, $_user_id) {
	
	include("../include/constant.php");		
	$detail = array();							
	$query = "SELECT * FROM `$CONTENT_LIKE_TABLE` WHERE `$CONTENT_LIKE_CONTENT_ID` = '$_content_id' AND `$CONTENT_LIKE_LOGIN_USER_ID` = '$_user_id'";
	$result = mysqli_query($db_con,$query);
	
	if($result){
		if(mysqli_num_rows($result) != 0){
			$row = mysqli_fetch_array($result);
			$likeStatus = $row[$CONTENT_LIKE_STATUS];
			return ($likeStatus == $STATUS_ACTIVE);
		}
	}	
	return False;
}

/* isContentViewedByNurse
*/
function isContentViewedByNurse($db_con,$_content_id, $_user_id) {
	
	include("../include/constant.php");		
	$detail = array();							
	$query = "SELECT * FROM `$CONTENT_VIEWS_TABLE` WHERE `$CONTENT_VIEWS_CONTENT_ID` = '$_content_id' AND `$CONTENT_VIEWS_LOGIN_USER_ID` = '$_user_id'";
	$result = mysqli_query($db_con,$query);
	
	if($result){
		if(mysqli_num_rows($result) != 0){			
			return True;
		}
	}	
	return False;
}

/* isUserContent
*/
function isUserContent($db_con,$_content_id, $_user_id) {
	
	include("../include/constant.php");		
	$detail = array();							
	$query = "SELECT * FROM `$CONTENT_TABLE` WHERE `$CONTENT_ID` IN (SELECT `$CONTENT_GROUP_CONTENT_ID` FROM `$CONTENT_GROUP_TABLE` WHERE `$CONTENT_GROUP_GROUP_ID` IN (SELECT `$GROUP_USER_GROUP_ID` FROM `$GROUP_USER_TABLE` WHERE `$GROUP_USER_USER_ID` = '$_user_id') OR `$CONTENT_GROUP_GROUP_ID` = '0') AND `$CONTENT_STATUS` = '$STATUS_ACTIVE' AND `$CONTENT_ID` = '$_content_id'";
	$result = mysqli_query($db_con,$query);
	
	if($result){
		if(mysqli_num_rows($result) != 0){			
			return True;
		}
	}	
	return False;
}

/* updateContentEdit
*/
function updateContentEdit($db_con,$_content_id, $dateNow) {
	
	include("../include/constant.php");		
	$detail = array();							
	$query = "UPDATE `$CONTENT_TABLE` SET `$CONTENT_EDIT_TIME` = '$dateNow' WHERE `$CONTENT_ID` = '$_content_id'";
	$result = mysqli_query($db_con,$query);
	return 0;
}

/* setImagePath - Set url prefix to images if not empty
*/
function setImagePath($imageType,$url) {
	
	include("../include/constant.php");		
	
	if($url != ''){		
		
		switch($imageType){
			case $CLASS_PATH_TYPE:
				$url = $CLASS_IMAGE_PATH.$url;
				break;
			case $CONTENT_PATH_TYPE:
				$url = $CONTENT_IMAGE_PATH.$url;
				break;
			case $NURSE_PATH_TYPE:
				$url = $NURSE_IMAGE_PATH.$url;
				break;
			case $ADMIN_PATH_TYPE:
				$url = $ADMIN_IMAGE_PATH.$url;
				break;
		}		
	}
	
	return $url;
}

//SMS
/* Send SMS of OTP
* Params:
*	db_con 	-> DB connection
*	user_id	-> Id of User who has updated status
*/
function sendOTPMessage($db_con,$_user_id) {
			
	include("../include/constant.php");
	include_once("../include/sms/exotel.php");
	$sms = new Exotel();	
	
	date_default_timezone_set('Asia/Kolkata');					
	$dateNow = date("Y-m-d H:i:s"); 
	
	$response = array();
			
	$_send_sms = True;
	$_update = False;
	$_otp_id = 0;
	$_attempt = 0;
	$_otp = generateStrongPassword($OTP_LENGTH, false, 'd');
	$message = "OTP message sent to your number.";
	
	//CHECK IF ALREADY SENT OTP
	$query	= "SELECT * FROM `$USER_OTP_TABLE` WHERE `$USER_OTP_USER_ID` = '$_user_id' AND `$USER_OTP_STATUS` = '$STATUS_ACTIVE'";
	$result = mysqli_query($db_con,$query);		
	if(mysqli_num_rows($result) != 0){
		$_update = true;
		
		//CHECK PREVIOUS ATTEMPT DETAILS
		$row = mysqli_fetch_array($result);
		
		$_otp_id = $row[$USER_OTP_ID];	
		$_otp = $row[$USER_OTP_OTP];	
		
		// 1. No. of ATTEMPTS
		$_attempt = $row[$USER_OTP_ATTEMPT];		
		if($_attempt >= $MAX_ATTEMPT_ALLOWD){
			$_send_sms = False;
			$message = "Your attempt limit is over please try again after 6 Hrs.";
		}
		
		if(!$_send_sms){
		
			// 2. Check if 6 Hrs passed
			$_last_attempt = $row[$USER_OTP_ENTRY_TIME];
			
			$nowDate	= strtotime($dateNow);
			$endDate 	= strtotime($_last_attempt);
			$hourPassed = abs($endDate - $nowDate)/3600;
			$response["hourPassed"] = $hourPassed;
			
			if($hourPassed >=6){
				$_send_sms = True;
				$_update = False;
				$_attempt = 0;
				$_otp = generateStrongPassword($OTP_LENGTH, false, 'd');
				$query	= "UPDATE `$USER_OTP_TABLE` SET `$USER_OTP_STATUS` = '$STATUS_FAILURE', `$USER_OTP_EDIT_TIME` = '$dateNow' WHERE `$USER_OTP_ID` = '$_otp_id'";
				$result = mysqli_query($db_con,$query);	
				$message = "OTP message sent to your number.";
			}
		}
		
	}else{
		//NO PREVIOUS ATTEMPTS		
	}
	
	if($_send_sms){
		
		$_key_expiry = date('Y-m-d H:i:s', strtotime('+5 minutes', strtotime($dateNow)));
		$_attempt = $_attempt + 1;
		
		if($_update){
			$query	= "UPDATE `$USER_OTP_TABLE` SET `$USER_OTP_OTP` = '$_otp',`$USER_OTP_ATTEMPT` = '$_attempt', `$USER_OTP_KEY_EXPIRY` = '$_key_expiry', `$USER_OTP_EDIT_TIME` = '$dateNow' WHERE `$USER_OTP_ID` = '$_otp_id'";
		}else{
			$query	= "INSERT INTO `$USER_OTP_TABLE` (`$USER_OTP_USER_ID`, `$USER_OTP_OTP`, `$USER_OTP_ATTEMPT`, `$USER_OTP_KEY_EXPIRY`, `$USER_OTP_STATUS`, `$USER_OTP_ENTRY_TIME`, `$USER_OTP_EDIT_TIME`) VALUES ('$_user_id','$_otp','$_attempt','$_key_expiry','$STATUS_ACTIVE','$dateNow','$dateNow')";
		}
		$result = mysqli_query($db_con,$query);	
		
		if($result){
			
			$nurse = getNurse($db_con, $_user_id);
			
			$otpMessage = "The OTP for NooraHealth is $_otp. OTP is valid for next 5 min";
			
			//$otpMessage = "This is a test message being sent using Exotel with a ($nurse[$TAG_FIRST_NAME]) and ($_otp). If this is being abused, report to 08088919888";
			
			
			$postData = array(
					// 'From' doesn't matter; For transactional, this will be replaced with your SenderId;
					// For promotional, this will be ignored by the SMS gateway
					'From'   => '09168393189',
					'To'    => $nurse[$TAG_MOBILE_NUMBER],
					'Body'  => $otpMessage,
				);	
			$postResult = $sms->sendOtpSms($postData);
			
			$response["nurse"] = $nurse;
			$response["sms_data"] = $postData;
			$response["sms_result"] = $postResult;
		}
		
	}
	
	$response[$TAG_SUCCESS] = $_send_sms;
	$response[$TAG_MESSAGE] = $message;
	return $response;
}




/* updateContentEdit
*/
function updateNurseHistory($db_con,$_user_id, $_type_id, $_content_id, $_entryTime, $dateNow) {
	
	include("../include/constant.php");			
	$query = "INSERT INTO `$NURSE_HISTORY_TABLE` (`$NURSE_HISTORY_NURSEID`, `$NURSE_HISTORY_HISTORY_TYPE_ID`, `$NURSE_HISTORY_CONTENT_ID`, `$NURSE_HISTORY_ENTRY_TIME`, `$NURSE_HISTORY_EDIT_TIME`) VALUES ('$_user_id', '$_type_id', '$_content_id', '$_entryTime', '$dateNow');";
	$result = mysqli_query($db_con,$query);		
	return 0;	
}


/************************************  Common Functions End ******************************************/	

/************************************  Api Calls  ******************************************/

/** Update Device
 * NAME
 * url - /updateDeviceToken
 * method - POST
 * params - TAG_USER_ID
 */
$app->post('/updateDeviceToken', function() use ($app) {
            // check for required params
			include("../include/constant.php");
			$db = new DB_CONNECT();
			$db_con=$db->conn;
			
			$result = authenticate($db_con,$app);
			if($result[$TAG_SUCCESS]){
				//GET KEY
				$key 	= $result[$TAG_APP_KEY];
				$data 	= $result[$TAG_DETAILS];
				$header = $result[$TAG_HEADER];
				
				$_user_id 				= getval($db_con,$data,$TAG_USER_ID);
				$_device_id 			= getval($db_con,$data,$TAG_DEVICE_ID);
				$_device_name 			= getval($db_con,$data,$TAG_DEVICE_NAME);
				$_device_os 			= getval($db_con,$data,$TAG_DEVICE_OS);
				$_device_os_version 	= getval($db_con,$data,$TAG_DEVICE_OS_VERSION);
				$_app_version 			= getval($db_con,$data,$TAG_APP_VERSION);
				$_app_version_name 		= getval($db_con,$data,$TAG_APP_VERSION_NAME);
				$_gcm_token 			= getval($db_con,$data,$TAG_GCM_TOKEN);
				
				mysqli_set_charset($db_con,'utf8');			
				date_default_timezone_set('Asia/Kolkata');					
				$dateNow = date("Y-m-d H:i:s"); 
				
				$response = array();
				$response[$TAG_DETAILS] = array();
				
				//CODE
				$query	= "SELECT * FROM `$USER_DEVICE_TABLE` WHERE `$USER_DEVICE_USER_ID` = '$_user_id' AND `$USER_DEVICE_DEVICE_ID` = '$_device_id'";
				$result = mysqli_query($db_con,$query);
				
				if(mysqli_num_rows($result) != 0){
					
					$query	= "UPDATE `$USER_DEVICE_TABLE` SET `$USER_DEVICE_APP_VERSION` = '$_app_version',`$USER_DEVICE_APP_VERSION_NAME` = '$_app_version_name', `$USER_DEVICE_GCM_TOKEN` = '$_gcm_token', `$USER_DEVICE_LAST_SEEN` = '$dateNow', `$USER_DEVICE_UPDATED_DATE` = '$dateNow' WHERE `$USER_DEVICE_USER_ID` = '$_user_id' AND `$USER_DEVICE_DEVICE_ID` = '$_device_id'";
					$result = mysqli_query($db_con,$query);
					
				}
				else
				{
					$query	= "INSERT INTO `$USER_DEVICE_TABLE` (`$USER_DEVICE_USER_ID`, `$USER_DEVICE_DEVICE_ID`, `$USER_DEVICE_DEVICE_NAME`, `$USER_DEVICE_DEVICE_OS`, `$USER_DEVICE_DEVICE_OS_VERSION`, `$USER_DEVICE_APP_VERSION`, `$USER_DEVICE_APP_VERSION_NAME`, `$USER_DEVICE_GCM_TOKEN`, `$USER_DEVICE_LAST_SEEN`, `$USER_DEVICE_CREATED_DATE`, `$USER_DEVICE_UPDATED_DATE`) VALUES ('$_user_id','$_device_id','$_device_name','$_device_os','$_device_os_version','$_app_version','$_app_version_name','$_gcm_token','$dateNow','$dateNow','$dateNow')";
					$result = mysqli_query($db_con,$query);
				}
				
				$response[$TAG_SUCCESS] = True;
				$response[$TAG_MESSAGE] = "Slots Updated";
				echoRespnse(200, $response);
			}
			
			mysqli_close($db_con);
        });	

/** Get Settings
* url - /getAppSetting
* headers - token
*/
$app->post('/getAppSetting', function() use ($app){
	// check for required params
	include("../include/constant.php");
	$db = new DB_CONNECT();
	$db_con=$db->conn;
	
	$query_status = false;
	$query_message = '';
	
	$result = authenticateForRole($db_con,$app);
	if($result[$TAG_SUCCESS]){
	
		//GET KEY
		$key = $result[$TAG_APP_KEY];
		$data = $result[$TAG_DETAILS];		
		
		$response = array();
		$response[$TAG_DETAILS] = array();
		
		$detail = array();		
		
		$version_code = 0;
		$update_type = 0;
		
		$query	= "SELECT * FROM `$APP_SETTING_TABLE` ";
		$result = mysqli_query($db_con,$query);				
		if($result)
		{
			$row_count=mysqli_num_rows($result);
			if($row_count > 0) 
			{
				while ($row = mysqli_fetch_array($result)) 
				{
					$type=$row[$APP_SETTING_NAME];	
					
					switch($type){						
						case "$TAG_VERSION_CODE":
							$version_code=$row[$APP_SETTING_VALUE];
						break;
						
						case "$TAG_UPDATE_TYPE":
							$update_type=$row[$APP_SETTING_VALUE];
						break;
						
					}
						
				}
			}
		}
		
		$detail[$TAG_VERSION_CODE] = $version_code;
		$detail[$TAG_UPDATE_TYPE] = $update_type;
		
		$response[$TAG_DETAILS]= $detail;
		
		$query_status = true;
		$query_message = "Query Success.";
		
		$response[$TAG_SUCCESS] = $query_status;
		$response[$TAG_MESSAGE] = $query_message;
		echoRespnse(200, $response, $key);	
	}
	mysqli_close($db_con);
});

/** Get Master Data
* url - /getMasterData
* headers - token
*/
$app->post('/getMasterData', function() use ($app){
	// check for required params
	include("../include/constant.php");
	$db = new DB_CONNECT();
	$db_con=$db->conn;
	
	$query_status = false;
	$query_message = '';
	
	$result = authenticateForRole($db_con,$app);
	if($result[$TAG_SUCCESS]){
	
		//GET KEY
		$key = $result[$TAG_APP_KEY];
		$data = $result[$TAG_DETAILS];		
		
		$response = array();
		$response[$TAG_DETAILS] = array();
		
		$detail = array();
		$classTypes = array();
		$hospitals = array();
		$hospitalConditions = array();
		
		
		//1. Get Class Types
		$query	= "SELECT * FROM `$CCP_CLASS_TYPE_TABLE` ";
		$result = mysqli_query($db_con,$query);				
		if($result){
			if(mysqli_num_rows($result) != 0){
				while ($row = mysqli_fetch_array($result)) {				
					$item = getClassTypeRow($db_con, $row);
					array_push($classTypes, $item);
				}	
			}
		}
		
		//2. Get Hospital
		$query	= "SELECT * FROM `$HOSPITAL_TABLE` ";
		$result = mysqli_query($db_con,$query);				
		if($result){
			if(mysqli_num_rows($result) != 0){
				while ($row = mysqli_fetch_array($result)) {
					$item = getHospitalRow($db_con, $row);
					array_push($hospitals, $item);
				}	
			}
		}
		
		//3. Get MEDICAL_CONDITION
		$query	= "SELECT * FROM `$HOSPITAL_MEDICAL_CONDITION_TABLE` ";
		$result = mysqli_query($db_con,$query);				
		if($result){
			if(mysqli_num_rows($result) != 0){
				while ($row = mysqli_fetch_array($result)) {				
					$item = getHospitalConditionRow($db_con, $row);
					array_push($hospitalConditions, $item);
				}	
			}
		}
		
		$detail[$TAG_CLASS_TYPE] = $classTypes;	
		$detail[$TAG_HOSPITAL] = $hospitals;		
		$detail[$TAG_HOSPITAL_CONDITION] = $hospitalConditions;		
		
		$response[$TAG_DETAILS]= $detail;
		
		$query_status = true;
		$query_message = "Query Success.";
		
		$response[$TAG_SUCCESS] = $query_status;
		$response[$TAG_MESSAGE] = $query_message;
		echoRespnse(200, $response, $key);	
	}
	mysqli_close($db_con);
});

/** Send OTP to Phone
* url - /sendMobileOTP
* headers - token
*/
$app->post('/sendMobileOTP', function() use ($app){
	// check for required params
	include("../include/constant.php");
	$db = new DB_CONNECT();
	$db_con=$db->conn;
	
	$result = authenticateForRole($db_con,$app);
	if($result[$TAG_SUCCESS]){
		//GET KEY
		$key 	= $result[$TAG_APP_KEY];
		$data 	= $result[$TAG_DETAILS];
		$header = $result[$TAG_HEADER];
			
		$_mobile_number 		= getval($db_con, $data,$TAG_MOBILE_NUMBER);
		$_device_id		 		= getval($db_con, $data,$TAG_DEVICE_ID);
		$_device_name	 		= getval($db_con, $data,$TAG_DEVICE_NAME);
		
		mysqli_set_charset($db_con,'utf8');
		date_default_timezone_set('Asia/Kolkata');
		$dateNow = date("Y-m-d H:i:s");
		
		// Make attempt entry to of mobile
		$query	= "INSERT INTO `$USER_MOBILE_ATTEMPT_TABLE` (`$USER_MOBILE_ATTEMPT_MOBILE`, `$USER_MOBILE_ATTEMPT_DEVICE_ID`, `$USER_MOBILE_ATTEMPT_DEVICE_NAME`, `$USER_MOBILE_ATTEMPT_STATUS`, `$USER_MOBILE_ATTEMPT_ENTRY_TIME`, `$USER_MOBILE_ATTEMPT_EDIT_TIME`) VALUES ('$_mobile_number', '$_device_id', '$_device_name', '$STATUS_ACTIVE', '$dateNow', '$dateNow')";
		$result = mysqli_query($db_con,$query);		
		
		
		$response = array();
		$success = false;
		$message = "There is some issue with server. Please try again later.";
		
		$addNewUser = false;
						
		$query = "SELECT * FROM `$NURSE_TABLE` WHERE `$NURSE_MOBILE_NUMBER` = '$_mobile_number' ORDER BY `$NURSE_ENTRY_TIME` DESC";
		$result = mysqli_query($db_con,$query);				
		if($result){
			
			if(mysqli_num_rows($result) != 0){
				
				$row = mysqli_fetch_array($result);
				
				$_nurse_id 					= $row[$NURSE_ID];
				$_status 					= $row[$NURSE_STATUS];				
				$_canLogin = true;	
				
				if($_status == $STATUS_ACTIVE){
											
					if($_canLogin){
						$otpData = sendOTPMessage($db_con,$_nurse_id);
						
						
						$success = $otpData[$TAG_SUCCESS];
						$message = $otpData[$TAG_MESSAGE];
						$response[$TAG_DETAILS] = $otpData;						 
						
					}else{
						$success = false;
						$message = "You are already logged in, on another device";							
					}						
									
				}else{
					$success = false;					
					if($_status == $STATUS_DISABLED){
						$message = "Your account has been disabled by Admin.";
					}					
					if($_status == $STATUS_DELETED){
						$addNewUser = True;
						$message = "Your account has been Deleted by Admin.";
					}
				}
			
			}
			else {
				// Lax: If mobile number is not present then add new nurse
				
				$message = "The user is not not yet registered by Admin.";
				$addNewUser = True;
			}
			
			if($addNewUser) {			
				$query	= "INSERT INTO `$NURSE_TABLE` (`$NURSE_MOBILE_NUMBER`, `$NURSE_STATUS`, `$NURSE_ENTRY_TIME`, `$NURSE_EDIT_TIME`) VALUES ('$_mobile_number', '$STATUS_ACTIVE', '$dateNow', '$dateNow')";
				$result = mysqli_query($db_con,$query);		
				
				if($result) {
					
					$query = "SELECT * FROM `$NURSE_TABLE` WHERE `$NURSE_MOBILE_NUMBER` = '$_mobile_number'";
					$result = mysqli_query($db_con,$query);	
					
					if(mysqli_num_rows($result) != 0){
					
						$row = mysqli_fetch_array($result);
						
						$_nurse_id 					= $row[$NURSE_ID];
						$_status 					= $row[$NURSE_STATUS];				
						$_canLogin = true;	
						
						if($_status == $STATUS_ACTIVE){
													
							if($_canLogin){
								$otpData = sendOTPMessage($db_con,$_nurse_id);
								
								
								$success = $otpData[$TAG_SUCCESS];
								$message = $otpData[$TAG_MESSAGE];
								$response[$TAG_DETAILS] = $otpData;						 
								
							}else{
								$success = false;
								$message = "Already logged in another device";							
							}						
											
						}else{
							$success = false;					
							if($_status == $STATUS_DISABLED){
								$message = "Your account has been disabled by Admin.";
							}
						}				
					}
					else {
						$message = "Registration has failed.";	
						
					}
				}
				else {
					$message = "Failed to register this user.";	
				}
			}
		}
		
		if($DEBUG){
			$response[$TAG_QUERY] = $query;			
		}
		$response[$TAG_SUCCESS] = $success;
		$response[$TAG_MESSAGE] = $message;
		echoRespnse(200, $response, $key);	
	}
	mysqli_close($db_con);
});

/** Verify OTP to Phone
* url - /verifyMobileOTP
* headers - token
*/
$app->post('/verifyMobileOTP', function() use ($app){
	// check for required params
	include("../include/constant.php");
	$db = new DB_CONNECT();
	$db_con=$db->conn;
	
	$result = authenticateForRole($db_con,$app);
	if($result[$TAG_SUCCESS]){
		//GET KEY
		$key 	= $result[$TAG_APP_KEY];
		$data 	= $result[$TAG_DETAILS];
		$header = $result[$TAG_HEADER];
		
		$_mobile_number 	= getval($db_con, $data,$TAG_MOBILE_NUMBER);
		$_otp 				= getval($db_con, $data,$TAG_OTP);
		
		mysqli_set_charset($db_con,'utf8');
		date_default_timezone_set('Asia/Kolkata');
		$dateNow = date("Y-m-d H:i:s");
		
		$response = array();
		$success = false;
		$message = "No User with Mobile Found";
						
		$query = "SELECT * FROM `$NURSE_TABLE` WHERE `$NURSE_MOBILE_NUMBER` = '$_mobile_number'";
		$result = mysqli_query($db_con,$query);				
		if($result){
			
			if(mysqli_num_rows($result) != 0){
				
				$row = mysqli_fetch_array($result);
				
				$_user_id 					= $row[$NURSE_ID];
				$_status 					= $row[$NURSE_STATUS];				
				$_canLogin = true;	
				
				if($_status == $STATUS_ACTIVE){
											
					if($_canLogin){
						
						//CHECK MASTER OTP
						$_masterOTP = getMasterOTP($db_con);
						
						if($_masterOTP == $_otp){
							$success = true;
							$message = "OTP verified Successfully.";
							$response[$TAG_DETAILS] = getNurse($db_con,$_user_id);	
							
							$query	= "UPDATE `$USER_OTP_TABLE` SET `$USER_OTP_STATUS` = '$STATUS_VERIFIED', `$USER_OTP_EDIT_TIME` = '$dateNow' WHERE `$USER_OTP_STATUS` = '$STATUS_ACTIVE' AND `$USER_OTP_USER_ID` = '$_user_id'";
							$result = mysqli_query($db_con,$query);	
							
						}else{						
						
							//VERIFY OTP
							$query	= "SELECT * FROM `$USER_OTP_TABLE` WHERE `$USER_OTP_USER_ID` = '$_user_id' AND `$USER_OTP_OTP` = '$_otp' AND `$USER_OTP_STATUS` = '$STATUS_ACTIVE'";
							$result = mysqli_query($db_con,$query);		
							if(mysqli_num_rows($result) != 0){
								$row = mysqli_fetch_array($result);
								
								$_otp_id = $row[$USER_OTP_ID];
								
								// 1. Check if OTP expired
								$_expire_time = $row[$USER_OTP_KEY_EXPIRY];
								
								$nowDate	= strtotime($dateNow);
								$endDate 	= strtotime($_expire_time);
								$timePassed = $endDate - $nowDate;

								if($timePassed<0){
									$success = false;
									$message = "OTP Expired. Please regenerate OTP and try again";	
																	
									$query	= "UPDATE `$USER_OTP_TABLE` SET `$USER_OTP_STATUS` = '$STATUS_FAILURE', `$USER_OTP_EDIT_TIME` = '$dateNow' WHERE `$USER_OTP_ID` = '$_otp_id'";
									$result = mysqli_query($db_con,$query);	
									
								}else{
									$success = true;
									$message = "OTP verified Successfully.";									
																	
									$query	= "UPDATE `$USER_OTP_TABLE` SET `$USER_OTP_STATUS` = '$STATUS_VERIFIED', `$USER_OTP_EDIT_TIME` = '$dateNow' WHERE `$USER_OTP_ID` = '$_otp_id'";
									$result = mysqli_query($db_con,$query);	
									
									$response[$TAG_DETAILS] = getNurse($db_con,$_user_id);								
								}
								
							}else{
								$success = false;
								$message = "Invalid OTP. Please check and try again";	
							}
						}						
						
					}else{
						$success = false;
						$message = "Already logged in another device";							
					}						
									
				}else{
					$success = false;					
					if($_status == $STATUS_DISABLED){
						$message = "Your account has been disabled by Admin.";
					}
				}
			}
		}
		
		if($DEBUG){
			$response[$TAG_QUERY] = $query;			
		}
		$response[$TAG_SUCCESS] = $success;
		$response[$TAG_MESSAGE] = $message;
		echoRespnse(200, $response, $key);	
	}
	mysqli_close($db_con);
});

/** Get Content List
* url - /getNurseContent
* headers - token
*/
$app->post('/getNurseContent', function() use ($app){
	// check for required params
	include("../include/constant.php");
	$db = new DB_CONNECT();
	$db_con=$db->conn;
	
	$result = authenticateForRole($db_con,$app);
	if($result[$TAG_SUCCESS]){
		//GET KEY
		$key 	= $result[$TAG_APP_KEY];
		$data 	= $result[$TAG_DETAILS];
		$header = $result[$TAG_HEADER];
		
		$_nurse_id 			= getval($db_con, $data, $TAG_NURSE_ID);
		$_content_date 		= getval($db_con, $data, $TAG_UPDATED_DATE);
		
		
		mysqli_set_charset($db_con,'utf8');
		date_default_timezone_set('Asia/Kolkata');
		$dateNow = date("Y-m-d H:i:s");
		
		$response = array();
		$response[$TAG_DETAILS] = array();
		$success = false;
		$message = "No Content found for the Nurse";
		$contentDate="";
		$_latestQuery = "";
		$_groupDate ="";
		$groupChange = false;

		if($_content_date != ''){
			
			$_groupDate = getNurseGroupDate($db_con, $_nurse_id);
			if($_groupDate!=""){
				if($_groupDate > $_content_date){
					$_latestQuery = "";
					$groupChange = True;
				}else{
					$_latestQuery = " AND `$CONTENT_EDIT_TIME` > '$_content_date'";	
				}
			}else{
				$_latestQuery = " AND `$CONTENT_EDIT_TIME` > '$_content_date'";					
			}
		}
		$response[$TAG_UPDATED_DATE] = $_content_date;

		$query = "SELECT * FROM `$CONTENT_TABLE` WHERE 1=1 $_latestQuery ORDER BY `$CONTENT_EDIT_TIME` DESC";
		$result = mysqli_query($db_con,$query);				
		if($result){
			
			if(mysqli_num_rows($result) != 0){
				
				$success = true;
				$message = "Content found Successfully.";
				
				while ($row = mysqli_fetch_array($result)) {	
					if($contentDate == ''){
						$contentDate = $row[$CONTENT_EDIT_TIME];
					}
					$detail = getContentRow($db_con, $row, $_nurse_id);
					array_push($response[$TAG_DETAILS], $detail);
				}	
				$response[$TAG_UPDATED_DATE] = $contentDate;			
			}
		}
		
		if($groupChange){
			$response[$TAG_UPDATED_DATE] = $_groupDate;			
		}
		
		if($DEBUG){
			$response[$TAG_QUERY] = $query;			
		}
		$response[$TAG_SUCCESS] = $success;
		$response[$TAG_MESSAGE] = $message;
		echoRespnse(200, $response, $key);	
	}
	mysqli_close($db_con);
});

/** Get Class List
* url - /getNurseClass
* headers - token
*/
$app->post('/getNurseClass', function() use ($app){
	// check for required params
	include("../include/constant.php");
	$db = new DB_CONNECT();
	$db_con=$db->conn;
	
	$result = authenticateForRole($db_con,$app);
	if($result[$TAG_SUCCESS]){
		//GET KEY
		$key 	= $result[$TAG_APP_KEY];
		$data 	= $result[$TAG_DETAILS];
		$header = $result[$TAG_HEADER];
		
		$_nurse_id 		= getval($db_con, $data, $TAG_NURSE_ID);
		$_content_date 	= getval($db_con, $data, $TAG_UPDATED_DATE);
		
		mysqli_set_charset($db_con,'utf8');
		date_default_timezone_set('Asia/Kolkata');
		$dateNow = date("Y-m-d H:i:s");
		
		$response = array();
		$response[$TAG_DETAILS] = array();
		$success = false;
		$message = "No Content found for the Nurse";
		
		$contentDate="";
		$_latestQuery = "";
		if($_content_date != ''){
			$_latestQuery = " AND `$CCP_ATTENDANCE_EDIT_TIME` > '$_content_date'";			
		}
		$response[$TAG_UPDATED_DATE] = $_content_date;
		
		$query = "SELECT * FROM `$CCP_ATTENDANCE_TABLE` WHERE `$CCP_ATTENDANCE_LOGIN_USER_ID` = '$_nurse_id' $_latestQuery ORDER BY `$CCP_ATTENDANCE_EDIT_TIME` DESC";
		$result = mysqli_query($db_con,$query);				
		if($result){
			
			if(mysqli_num_rows($result) != 0){
				
				$success = true;
				$message = "Content found Successfully.";
				
				while ($row = mysqli_fetch_array($result)) {	
					if($contentDate == ''){
						$contentDate = $row[$CCP_ATTENDANCE_EDIT_TIME];
					}				
					$detail = getClassRow($db_con, $row, $_nurse_id);
					array_push($response[$TAG_DETAILS], $detail);
				}
				$response[$TAG_UPDATED_DATE] = $contentDate;				
			}
		}
		
		
		if($DEBUG){
			$response[$TAG_QUERY] = $query;			
		}
		$response[$TAG_SUCCESS] = $success;
		$response[$TAG_MESSAGE] = $message;
		echoRespnse(200, $response, $key);	
	}
	mysqli_close($db_con);
});

/** Set Content Like
 * NAME
 * url - /setContentLike
 * method - POST
 * params - TAG_USER_ID
 */
$app->post('/setContentLike', function() use ($app) {
	// check for required params
	include("../include/constant.php");
	$db = new DB_CONNECT();
	$db_con=$db->conn;
	
	$result = authenticateForRole($db_con,$app);
	if($result[$TAG_SUCCESS]){
		//GET KEY
		$key 	= $result[$TAG_APP_KEY];
		$data 	= $result[$TAG_DETAILS];
		$header = $result[$TAG_HEADER];
		
		$_user_id 			= getval($db_con,$data,$TAG_USER_ID);
		$_content_id 		= getval($db_con,$data,$TAG_CONTENT_ID);
		$_status 			= getval($db_con,$data,$TAG_STATUS);
		
		mysqli_set_charset($db_con,'utf8');			
		date_default_timezone_set('Asia/Kolkata');					
		$dateNow = date("Y-m-d H:i:s"); 
		
		$response = array();
		$response[$TAG_DETAILS] = array();
		
		//CODE
		$query	= "SELECT * FROM `$CONTENT_LIKE_TABLE` WHERE `$CONTENT_LIKE_CONTENT_ID` = '$_content_id' AND `$CONTENT_LIKE_LOGIN_USER_ID` = '$_user_id'";
		$result = mysqli_query($db_con,$query);
		
		if(mysqli_num_rows($result) != 0){
			
			$query	= "UPDATE `$CONTENT_LIKE_TABLE` SET `$CONTENT_LIKE_STATUS` = '$_status',`$CONTENT_LIKE_EDIT_TIME` = '$dateNow' WHERE `$CONTENT_LIKE_CONTENT_ID` = '$_content_id' AND `$CONTENT_LIKE_LOGIN_USER_ID` = '$_user_id'";
			$result = mysqli_query($db_con,$query);					
		}
		else
		{
			$query	= "INSERT INTO `$CONTENT_LIKE_TABLE` (`$CONTENT_LIKE_CONTENT_ID`, `$CONTENT_LIKE_LOGIN_USER_ID`, `$CONTENT_LIKE_STATUS`, `$CONTENT_LIKE_ENTRY_TIME`) VALUES ('$_content_id','$_user_id','$_status','$dateNow')";
			$result = mysqli_query($db_con,$query);
		}
			
		updateContentEdit($db_con, $_content_id, $dateNow);
		
		$response[$TAG_SUCCESS] = True;
		$response[$TAG_MESSAGE] = "Slots Updated";
		echoRespnse(200, $response);
	}
	
	mysqli_close($db_con);
});	

/** Set Content Like Bulk
 * NAME
 * url - /setContentLikeBulk
 * method - POST
 * params - TAG_USER_ID
 */
$app->post('/setContentLikeBulk', function() use ($app) {
	// check for required params
	include("../include/constant.php");
	$db = new DB_CONNECT();
	$db_con=$db->conn;
	
	$result = authenticateForRole($db_con,$app);
	if($result[$TAG_SUCCESS]){
		//GET KEY
		$key 	= $result[$TAG_APP_KEY];
		$data 	= $result[$TAG_DETAILS];
		$header = $result[$TAG_HEADER];
		
		$_data 			= $data[$TAG_DATA];
		
		mysqli_set_charset($db_con,'utf8');			
		date_default_timezone_set('Asia/Kolkata');					
		$dateNow = date("Y-m-d H:i:s"); 
		
		$response = array();
		$response[$TAG_DETAILS] = array();
		
		$array = json_decode(stripcslashes($_data), true);
		
		foreach($array as $item) {
			$_user_id 		= $item[$TAG_USER_ID];
			$_content_id 	= $item[$TAG_CONTENT_ID];
			$_status 		= $item[$TAG_STATUS];
			$_entryTime		= $item[$TAG_ENTRY_TIME];
			
			
			//CODE
			$query	= "SELECT * FROM `$CONTENT_LIKE_TABLE` WHERE `$CONTENT_LIKE_CONTENT_ID` = '$_content_id' AND `$CONTENT_LIKE_LOGIN_USER_ID` = '$_user_id'";
			$result = mysqli_query($db_con,$query);
			$like_id = 0;
			if(mysqli_num_rows($result) != 0){
				
				$row = mysqli_fetch_array($result);
				$like_id = $row[$CONTENT_LIKE_ID];
				
				$query	= "UPDATE `$CONTENT_LIKE_TABLE` SET `$CONTENT_LIKE_STATUS` = '$_status',`$CONTENT_LIKE_EDIT_TIME` = '$dateNow' WHERE `$CONTENT_LIKE_CONTENT_ID` = '$_content_id' AND `$CONTENT_LIKE_LOGIN_USER_ID` = '$_user_id'";
				$result = mysqli_query($db_con,$query);					
			}
			else
			{
				$query	= "INSERT INTO `$CONTENT_LIKE_TABLE` (`$CONTENT_LIKE_CONTENT_ID`, `$CONTENT_LIKE_LOGIN_USER_ID`, `$CONTENT_LIKE_STATUS`, `$CONTENT_LIKE_ENTRY_TIME`, `$CONTENT_LIKE_EDIT_TIME`) VALUES ('$_content_id','$_user_id','$_status','$dateNow','$dateNow')";
				$result = mysqli_query($db_con,$query);
				
				$like_id = mysqli_insert_id($db_con);
				
			}	
			
			// Track Nurse Content like/unlike
			//updateNurseHistory($db_con,$_user_id, $HISTORY_TYPE_CONTENT_LIKE, $like_id, $_entryTime, $dateNow);
						
			updateContentEdit($db_con, $_content_id, $dateNow);		
		}
		
		
		
		$response[$TAG_DATA] = $array;
		$response[$TAG_SUCCESS] = True;
		$response[$TAG_MESSAGE] = "Slots Updated";
		echoRespnse(200, $response);
	}
	
	mysqli_close($db_con);
});	

/** Get Content Comment
 * NAME
 * url - /getContentComment
 * method - POST
 * params - TAG_USER_ID
 */
$app->post('/getContentComment', function() use ($app) {

	// check for required params
	include("../include/constant.php");
	$db = new DB_CONNECT();
	$db_con=$db->conn;

	$result = authenticateForRole($db_con,$app);
	if($result[$TAG_SUCCESS]){
		//GET KEY
		$key 	= $result[$TAG_APP_KEY];
		$data 	= $result[$TAG_DETAILS];
		$header = $result[$TAG_HEADER];
		
		$_user_id 			= getval($db_con,$data,$TAG_USER_ID);
		$_content_id 		= getval($db_con,$data,$TAG_CONTENT_ID);
		
		mysqli_set_charset($db_con,'utf8');			
		date_default_timezone_set('Asia/Kolkata');					
		$dateNow = date("Y-m-d H:i:s"); 
		
		$response = array();
		$response[$TAG_DETAILS] = array();
		$success = false;
		$message = "No Content found for the Nurse";
		
		//CODE
		$query	= "SELECT * FROM `$CONTENT_COMMENT_TABLE` WHERE `$CONTENT_COMMENT_CONTENT_ID` = '$_content_id' AND `$CONTENT_COMMENT_STATUS` = '$STATUS_ACTIVE' ORDER BY `$CONTENT_COMMENT_ENTRY_TIME` DESC";
		$result = mysqli_query($db_con,$query);
		
		if($result){
			
			if(mysqli_num_rows($result) != 0){
				
				$success = true;
				$message = "Content found Successfully.";
				
				while ($row = mysqli_fetch_array($result)) {				
					$detail = getCommentRow($db_con, $row);
					array_push($response[$TAG_DETAILS], $detail);
				}				
			}
		}		
		
		if($DEBUG){
			$response[$TAG_QUERY] = $query;			
		}
		$response[$TAG_SUCCESS] = $success;
		$response[$TAG_MESSAGE] = $message;
		echoRespnse(200, $response, $key);	

	}
	mysqli_close($db_con);
});	

/** Post Content Comment
 * NAME
 * url - /postContentComment
 * method - POST
 * params - TAG_USER_ID
 */
$app->post('/postContentComment', function() use ($app) {
	// check for required params
	include("../include/constant.php");
	$db = new DB_CONNECT();
	$db_con=$db->conn;
	
	$result = authenticateForRole($db_con,$app);
	if($result[$TAG_SUCCESS]){
		//GET KEY
		$key 	= $result[$TAG_APP_KEY];
		$data 	= $result[$TAG_DETAILS];
		$header = $result[$TAG_HEADER];
		
		$_user_id 			= getval($db_con,$data,$TAG_USER_ID);
		$_content_id 		= getval($db_con,$data,$TAG_CONTENT_ID);
		$_comment			= getval($db_con,$data,$TAG_COMMENT);
		
		mysqli_set_charset($db_con,'utf8');			
		date_default_timezone_set('Asia/Kolkata');					
		$dateNow = date("Y-m-d H:i:s"); 
		
		$response = array();
		$response[$TAG_DETAILS] = array();
		
		//CODE
		$query	= "INSERT INTO `$CONTENT_COMMENT_TABLE` (`$CONTENT_COMMENT_CONTENT_ID`, `$CONTENT_COMMENT_COMMENT`, `$CONTENT_COMMENT_LOGIN_USER_ID`, `$CONTENT_COMMENT_ENTRY_TIME`) VALUES ('$_content_id','$_comment','$_user_id','$dateNow')";
		$result = mysqli_query($db_con,$query);
		$comment_id = mysqli_insert_id($db_con);
		
		
		// Track Nurse Content comment
		//updateNurseHistory($db_con,$_user_id, $HISTORY_TYPE_CONTENT_COMMENT, $comment_id, $dateNow, $dateNow);
		
		updateContentEdit($db_con, $_content_id, $dateNow);
			
		$response[$TAG_SUCCESS] = True;
		$response[$TAG_MESSAGE] = "Slots Updated";
		echoRespnse(200, $response);
	}
	
	mysqli_close($db_con);
});	

/** Post Content Comment Bulk
 * NAME
 * url - /postContentCommentBulk
 * method - POST
 * params - TAG_USER_ID
 */
$app->post('/postContentCommentBulk', function() use ($app) {
	// check for required params
	include("../include/constant.php");
	$db = new DB_CONNECT();
	$db_con=$db->conn;
	
	$result = authenticateForRole($db_con,$app);
	if($result[$TAG_SUCCESS]){
		//GET KEY
		$key 	= $result[$TAG_APP_KEY];
		$data 	= $result[$TAG_DETAILS];
		$header = $result[$TAG_HEADER];
		
		$_data 			= $data[$TAG_DATA];
		
		
		mysqli_set_charset($db_con,'utf8');			
		date_default_timezone_set('Asia/Kolkata');					
		$dateNow = date("Y-m-d H:i:s"); 
		
		$response = array();
		$response[$TAG_DETAILS] = array();
		
		$array = json_decode(stripcslashes($_data), true);
		//$array = json_decode($_data, true);
		
		foreach($array as $item) { 
			$_user_id 			= getval($db_con,$item,$TAG_USER_ID);
			$_content_id 		= getval($db_con,$item,$TAG_CONTENT_ID);
			$_comment			= getval1($db_con,$item,$TAG_COMMENT);
			$_entryTime			= getval($db_con,$item,$TAG_ENTRY_TIME);
			
			//CODE
			$query	= "INSERT INTO `$CONTENT_COMMENT_TABLE` (`$CONTENT_COMMENT_CONTENT_ID`, `$CONTENT_COMMENT_COMMENT`, `$CONTENT_COMMENT_LOGIN_USER_ID`, `$CONTENT_COMMENT_ENTRY_TIME`, `$CONTENT_COMMENT_EDIT_TIME`) VALUES ('$_content_id','$_comment','$_user_id','$_entryTime','$dateNow')";
			$result = mysqli_query($db_con,$query);	
			$comment_id = mysqli_insert_id($db_con);

			// Track Nurse Content comment
			//updateNurseHistory($db_con,$_user_id, $HISTORY_TYPE_CONTENT_COMMENT, $comment_id, $_entryTime, $dateNow);
		

			updateContentEdit($db_con, $_content_id, $dateNow);			
		}
		
		$response[$TAG_SUCCESS] = True;
		$response[$TAG_MESSAGE] = "Slots Updated";
		echoRespnse(200, $response);
	}
	
	mysqli_close($db_con);
});	

/** Get Nurse Profile
* url - /getNurseProfile
* headers - token
*/
$app->post('/getNurseProfile', function() use ($app){
	// check for required params
	include("../include/constant.php");
	$db = new DB_CONNECT();
	$db_con=$db->conn;
	
	$result = authenticateForRole($db_con,$app);
	if($result[$TAG_SUCCESS]){
		//GET KEY
		$key 	= $result[$TAG_APP_KEY];
		$data 	= $result[$TAG_DETAILS];
		$header = $result[$TAG_HEADER];
		
		$_nurse_id 		= getval($db_con, $data, $TAG_NURSE_ID);
		
		mysqli_set_charset($db_con,'utf8');
		date_default_timezone_set('Asia/Kolkata');
		$dateNow = date("Y-m-d H:i:s");
		
		$response = array();
		$response[$TAG_DETAILS] = array();
		$success = false;
		$message = "No Content found for the Nurse";
		
		$query = "SELECT * FROM `$NURSE_TABLE` WHERE `$NURSE_ID` = '$_nurse_id'";
		$result = mysqli_query($db_con,$query);				
		if($result){
			
			if(mysqli_num_rows($result) != 0){
				
				$success = true;
				$message = "Content found Successfully.";
				
				while ($row = mysqli_fetch_array($result)) {				
					$detail = getNurseProfileRow($db_con, $row);
					$response[$TAG_DETAILS]= $detail;
				}				
			}
		}
		
		
		if($DEBUG){
			$response[$TAG_QUERY] = $query;			
		}
		$response[$TAG_SUCCESS] = $success;
		$response[$TAG_MESSAGE] = $message;
		echoRespnse(200, $response, $key);	
	}
	mysqli_close($db_con);
});

/** Submit Class
* url - /submitClass
* headers - token
*/
$app->post('/submitClass', function() use ($app){
	// check for required params
	include("../include/constant.php");
	$db = new DB_CONNECT();
	$db_con=$db->conn;
	
	$result = authenticateForRole($db_con,$app);
	if($result[$TAG_SUCCESS]){
		//GET KEY
		$key 	= $result[$TAG_APP_KEY];
		$data 	= $result[$TAG_DETAILS];
		$header = $result[$TAG_HEADER];
		
		$_user_id 		= getval($db_con,$data,$TAG_USER_ID);
		$_image 		= getval($db_con,$data,$TAG_IMAGE);
		$_class_date 	= getval($db_con,$data,$TAG_CLASS_DATE);
		$_class_time 	= getval($db_con,$data,$TAG_CLASS_TIME);
		$_no_of_people 	= getval($db_con,$data,$TAG_NO_OF_PEOPLE);
		$_no_of_family 	= getval($db_con,$data,$TAG_NO_OF_FAMILY);
		$_class_type 	= getval($db_con,$data,$TAG_CLASS_TYPE);
		$_entryTime 	= getval($db_con,$data,$TAG_ENTRY_TIME);
				
		mysqli_set_charset($db_con,'utf8');
		date_default_timezone_set('Asia/Kolkata');
		$dateNow = date("Y-m-d H:i:s");
		
		$response = array();
		$success = false;
		$message = "Unable to submit Ticket";
		
		//UPLOAD IMAGE
		$_image_name = "";
		if(!empty($_image)) {
			$imageUploaded = array();
			$imageUploaded = _upload_file($_image, $CLASS_FOLDER);
			if($imageUploaded[$TAG_SUCCESS]) {
				$image_upload_status = 1;
				$_image_name = $imageUploaded[$TAG_NAME];
				$image_upload_message = $imageUploaded[$TAG_MESSAGE];
			}
			else {
				$_image_name = "";
				$image_upload_status = 0;
				$image_upload_message = $imageUploaded[$TAG_MESSAGE];
			}
			$response[$TAG_DETAILS] = $imageUploaded;
		}
				
		$query = "INSERT INTO `$CCP_ATTENDANCE_TABLE` (`$CCP_ATTENDANCE_LOGIN_USER_ID`, `$CCP_ATTENDANCE_CLASS_TYPE_ID`, `$CCP_ATTENDANCE_IMAGE`, `$CCP_ATTENDANCE_CLASS_DATE`, `$CCP_ATTENDANCE_CLASS_TIME`, `$CCP_ATTENDANCE_NO_OF_PEOPLE`, `$CCP_ATTENDANCE_NO_OF_FAMILY`, `$CCP_ATTENDANCE_STATUS`, `$CCP_ATTENDANCE_ENTRY_TIME`, `$CCP_ATTENDANCE_EDIT_TIME`) VALUES ('$_user_id', '$_class_type', '$_image_name', '$_class_date', '$_class_time', '$_no_of_people', '$_no_of_family','$STATUS_ACTIVE', '$_entryTime', '$dateNow');";
		$result = mysqli_query($db_con,$query);
		if($result){
			$ccp_id = mysqli_insert_id($db_con);
			$success = true;
			$message = "Ticket Raised Successfully.";			
		}
		
		// Track Nurse Class Entry
		updateNurseHistory($db_con,$_user_id, $HISTORY_TYPE_CLASS_ADD, $ccp_id, $_entryTime, $dateNow);
		
		$response[$TAG_SUCCESS] = $success;
		$response[$TAG_MESSAGE] = $message." ".$query ;
		echoRespnse(200, $response, $key);	
	}
	mysqli_close($db_con);
});

/** Update User Session
 * NAME
 * url - /updateUserSession
 * method - POST
 * params - TAG_USER_ID
 */
$app->post('/updateUserSession', function() use ($app) {
	// check for required params
	include("../include/constant.php");
	$db = new DB_CONNECT();
	$db_con=$db->conn;
	
	$result = authenticateForRole($db_con,$app);
	if($result[$TAG_SUCCESS]){
		//GET KEY
		$key 	= $result[$TAG_APP_KEY];
		$data 	= $result[$TAG_DETAILS];
		$header = $result[$TAG_HEADER];
		
		$_data 			= $data[$TAG_DATA];
		
		mysqli_set_charset($db_con,'utf8');			
		date_default_timezone_set('Asia/Kolkata');					
		$dateNow = date("Y-m-d H:i:s"); 
		
		$response = array();
		$response[$TAG_DETAILS] = array();
		$query = "";
		$array = json_decode(stripcslashes($_data), true);
		
		foreach($array as $item) { 
			$_user_id 		= $item[$TAG_USER_ID];
			$_status 		= $item[$TAG_STATUS];
			$_entryTime 	= $item[$TAG_ENTRY_TIME];
			
			$_historyType 		= $HISTORY_TYPE_LOGOUT;
			
			if($_status == $STATUS_ACTIVE){
				$_historyType 		= $HISTORY_TYPE_LOGIN;			
			}
						
			// Track Session login and logout
			updateNurseHistory($db_con,$_user_id, $_historyType, $_user_id, $_entryTime, $dateNow);
			
		}
		
		
		
		$response['query'] = $query;
		$response[$TAG_DATA] = $array;
		$response[$TAG_SUCCESS] = True;
		$response[$TAG_MESSAGE] = "Slots Updated";
		echoRespnse(200, $response);
	}
	
	mysqli_close($db_con);
});	

/** Update User Content View
 * NAME
 * url - /updateUserContentView
 * method - POST
 * params - TAG_USER_ID
 */
$app->post('/updateUserContentView', function() use ($app) {
	// check for required params
	include("../include/constant.php");
	$db = new DB_CONNECT();
	$db_con=$db->conn;
	
	$result = authenticateForRole($db_con,$app);
	if($result[$TAG_SUCCESS]){
		//GET KEY
		$key 	= $result[$TAG_APP_KEY];
		$data 	= $result[$TAG_DETAILS];
		$header = $result[$TAG_HEADER];
		
		$_data 			= $data[$TAG_DATA];
		
		mysqli_set_charset($db_con,'utf8');			
		date_default_timezone_set('Asia/Kolkata');					
		$dateNow = date("Y-m-d H:i:s"); 
		
		$response = array();
		$response[$TAG_DETAILS] = array();
		$query = "";
		$array = json_decode(stripcslashes($_data), true);
		
		foreach($array as $item) { 
			$_user_id 		= $item[$TAG_USER_ID];
			$_content_id 	= $item[$TAG_CONTENT_ID];
			$_entryTime 	= $item[$TAG_ENTRY_TIME];
			
			$query = "INSERT INTO `$CONTENT_VIEWS_TABLE` (`$CONTENT_VIEWS_CONTENT_ID`, `$CONTENT_VIEWS_LOGIN_USER_ID`, `$CONTENT_VIEWS_STATUS`, `$CONTENT_VIEWS_ENTRY_TIME`, `$CONTENT_VIEWS_EDIT_TIME`) VALUES ('$_content_id', '$_user_id', '$STATUS_ACTIVE', '$_entryTime', '$dateNow');";
			$result = mysqli_query($db_con,$query);
			
			updateContentEdit($db_con, $_content_id, $dateNow);
			
		}
		
		$response[$TAG_DATA] = $array;
		$response[$TAG_SUCCESS] = True;
		$response[$TAG_MESSAGE] = "Slots Updated";
		echoRespnse(200, $response);
	}
	
	mysqli_close($db_con);
});	


/** Update User Profile
* url - /updateUserProfile
* headers - token
*/
$app->post('/updateUserProfile', function() use ($app){
	// check for required params
	include("../include/constant.php");
	$db = new DB_CONNECT();
	$db_con=$db->conn;
	
	$result = authenticateForRole($db_con,$app);
	if($result[$TAG_SUCCESS]){
		//GET KEY
		$key 	= $result[$TAG_APP_KEY];
		$data 	= $result[$TAG_DETAILS];
		$header = $result[$TAG_HEADER];
		
		$_user_id 		= getval($db_con,$data,$TAG_USER_ID);
		
		$_image 				= getval($db_con,$data,$TAG_IMAGE);
		$_hospital_id 			= getval($db_con,$data,$TAG_HOSPITAL_ID);
		$_hospital_condition_id = getval($db_con,$data,$TAG_HOSPITAL_CONDITION_ID);
		
		$_fname 				= getval($db_con,$data,$TAG_FIRST_NAME);
		$_lname 				= getval($db_con,$data,$TAG_LAST_NAME);
		$_dob 					= getval($db_con,$data,$TAG_DOB);
		$_graduation 			= getval($db_con,$data,$TAG_GRADUATING_YEAR);
		$_joining_date 			= getval($db_con,$data,$TAG_HOSPITAL_JOINING_DATE);
		$_designation 			= getval($db_con,$data,$TAG_DESIGNATION);
		$_tot_date 				= getval($db_con,$data,$TAG_TOT_DATE);
				
		mysqli_set_charset($db_con,'utf8');
		date_default_timezone_set('Asia/Kolkata');
		$dateNow = date("Y-m-d H:i:s");
		
		$response = array();
		$success = True;
		$message = "Profile Updated";
		
		//UPLOAD IMAGE
		$_image_name = "";
		if(!empty($_image)) {
			$imageUploaded = array();
			$imageUploaded = _upload_file($_image, $NURSE_FOLDER);
			if($imageUploaded[$TAG_SUCCESS]) {
				$image_upload_status = 1;
				$_image_name = $imageUploaded[$TAG_NAME];
				$image_upload_message = $imageUploaded[$TAG_MESSAGE];
			}
			else {
				$_image_name = "";
				$image_upload_status = 0;
				$image_upload_message = $imageUploaded[$TAG_MESSAGE];
			}
			$response[$TAG_DETAILS] = $imageUploaded;
		}
		
		if($_image_name!= ""){				
			$query = "UPDATE `$NURSE_TABLE` SET `$NURSE_PROFILE_IMAGE` = '$_image_name', `$NURSE_EDIT_TIME` = '$dateNow' WHERE `$NURSE_ID` = '$_user_id'";
			$result = mysqli_query($db_con,$query);
		}
		
		if($_hospital_id != 0){
			$query = "UPDATE `$NURSE_TABLE` SET `$NURSE_HOSPITAL_ID` = '$_hospital_id', `$NURSE_EDIT_TIME` = '$dateNow' WHERE `$NURSE_ID` = '$_user_id'";
			$result = mysqli_query($db_con,$query);			
		}
		
		if($_hospital_condition_id != 0){
			$query = "UPDATE `$NURSE_TABLE` SET `$NURSE_HOSPITAL_CONDITION_ID` = '$_hospital_condition_id', `$NURSE_EDIT_TIME` = '$dateNow' WHERE `$NURSE_ID` = '$_user_id'";
			$result = mysqli_query($db_con,$query);			
		}
		
		$query = "UPDATE `$NURSE_TABLE` SET `$NURSE_FIRST_NAME` = '$_fname', `$NURSE_LAST_NAME` = '$_lname', `$NURSE_DOB` = '$_dob', `$NURSE_GRADUATING_YEAR` = '$_graduation', `$NURSE_HOSPITAL_JOINING_DATE` = '$_joining_date', `$NURSE_DESIGNATION` = '$_designation', `$NURSE_TOT_DATE` = '$_tot_date', `$NURSE_EDIT_TIME` = '$dateNow' WHERE `$NURSE_ID` = '$_user_id'";
		$result = mysqli_query($db_con,$query);	
		
		
		$response[$TAG_SUCCESS] = $success;
		$response[$TAG_MESSAGE] = $message." ".$query ;
		echoRespnse(200, $response, $key);	
	}
	mysqli_close($db_con);
});



/************************************  Api Calls End  ******************************************/


/************************************  Required Functions  ******************************************/	

function getval($db_con, $array, $tag) {
	return isset($array[$tag]) ? mysqli_real_escape_string($db_con, $array[$tag]) : '';
}
	
	
function getval1($db_con, $array, $tag) {
	return isset($array[$tag]) ? $array[$tag] : '';
}
	
/**
* Uploading File
*/
function _upload_file($file_data, $upload_path) {
	include("../include/constant.php");
	
	$message = "Image uploaded";
	$file_name = "";
	$file_size = 0;
	$success 	= false;
	
	try {
		$file_data 	= str_replace('data:image/jpeg;base64,', '', $file_data);
		$file_data 	= str_replace(' ', '+', $file_data);
		$file_data 	= base64_decode($file_data);
		$file_name 	= uniqid();
		
		$target_dir = $_SERVER['DOCUMENT_ROOT'].$IMAGE_UPLOAD_PATH . $upload_path;
		
		$save_file_name	= $file_name.'.jpg';
		$file 			= $target_dir . $save_file_name ;
		$file_size 	= file_put_contents($file, $file_data);		
		$success = true;
		
	} catch(Exception $e) {
		$message = ''.$e->getMessage();
	}
		
	$imageUploaded = array();
	$imageUploaded[$TAG_NAME] 	= $save_file_name;
	$imageUploaded[$TAG_SUCCESS] = $success;
	$imageUploaded[$TAG_MESSAGE] = $message;
	return $imageUploaded;
}			
		
/*
* Calculate Distance using Latitude and Longitude
* Unit : Kilometers
*/
function distance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo) {
	$earthRadius = 6371000;
	$latFrom = deg2rad($latitudeFrom);
	$lonFrom = deg2rad($longitudeFrom);
	$latTo = deg2rad($latitudeTo);
	$lonTo = deg2rad($longitudeTo);

	$latDelta = $latTo - $latFrom;
	$lonDelta = $lonTo - $lonFrom;

	$angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
	$total = $angle * $earthRadius;
	return round($total/1000, 3);
}
		
/**
 * Validating email address
 */
function getRandomString($length) {

	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
}
		
/**
 * Verifying required params posted or not
 */
function verifyRequiredParams($required_fields) {
    $error = false;
    $error_fields = "";
    $request_params = array();
    $request_params = $_REQUEST;
    // Handling PUT request params
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        $app = \Slim\Slim::getInstance();
        parse_str($app->request()->getBody(), $request_params);
    }
    foreach ($required_fields as $field) {
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }

    if ($error) {
        // Required field(s) are missing or empty
        // echo error json and stop the app
        $response = array();
        $app = \Slim\Slim::getInstance();
        $response["error"] = true;
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        echoRespnse(400, $response);
        $app->stop();
    }
}

/**
 * Validating email address
 */
function validateEmail($email) {
    $app = \Slim\Slim::getInstance();
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response["error"] = true;
        $response["message"] = 'Email address is not valid';
        echoRespnse(400, $response);
        $app->stop();
    }
}

/**
 * Echoing json response to client
 * @param String $status_code Http response code
 * @param Int $response Json response
 */
function echoRespnse($status_code, $response, $key = '', $recall = false) {
		
    $mcrypt = new MCrypt();
	
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);

    // setting response content type to json
    $app->contentType('application/json');

	if($key == ''){ //Normal Response
		echo $json = json_encode($response);
		return;
	}else{	
		$data = array();	
		if($recall){
			$data["data"] = $response;
			$data["recall"] = true;
			
		}else{
			$json = json_encode($response);
			$encrypted = $mcrypt->encrypt($json, $key);
			$data["data"] = $encrypted;
		}	
	}
	
    echo json_encode($data);	
}

/************************************  Required Functions END ******************************************/	

$app->run();
?>