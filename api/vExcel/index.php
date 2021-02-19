<?php

require_once '../include/db_connect_second.php';
require '../libs/Slim/Slim.php';
\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();


/* getClassRow
*/
function getClassRow($db_con,$row) {
	include("../include/constant.php");		
	$detail = array();	
	$_district_id =  $row[$CCP_ATTENDANCE_DISTRICT_ID];
	$_hospital_id = $row[$CCP_ATTENDANCE_HOSPITAL_ID];
	$_user_id = $row[$CCP_ATTENDANCE_LOGIN_USER_ID];
    $_class_type_id 	= $row[$CCP_ATTENDANCE_CLASS_TYPE_ID];
	$_user = getNurse($db_con, $_user_id);
    $_condition_id 	= $_user[$TAG_CCP_CONDITION_ID];
	
	//DATA VARIABLES
	$_state = "";
	$_district  = "";
	$_hospital  = "";
	$_hospital_partner  = "";
	$_trainer_name = "";
	$_trainer_id  = "";
	$_attendance_date  = "";
	$_attendance_year  = "";
	$_attendance_time  = "";
	$_no_of_people  = "";
	$_hospital_location  = "";
	$_class_type  = "";
	$_condition  = "";
    $_attendance_image	 = "";
	$_db_id  = "";
	
	
	//STATE
	$_state = getStateName($db_con, $_district_id);
	//DISTRICT
	$_district = getDistrictName($db_con, $_district_id);
	//HOSPITAL
	$_hospital = getHospitalName($db_con, $_hospital_id);
	//PARTNER
	$_hospital_partner = getHospitalPartnerName($db_con, $_hospital_id);
	//TRAINER_NAME
	$_trainer_name = $_user["TRAINER_NAME"];
	//TRAINER_ID
	$_trainer_id = $_user["TRAINER_ID"];
	//ATTENDANCE_DATE
	$_attendance_date = $row[$CCP_ATTENDANCE_CLASS_DATE];
	//ATTENDANCE_YEAR
	$attendanceDateUnix = strtotime($_attendance_date);
	$_attendance_year = date("Y", $attendanceDateUnix);
	//ATTENDANCE_TIME
	$_attendance_time = $row[$CCP_ATTENDANCE_CLASS_TIME];
	$_attendance_datetime = "$_attendance_date $_attendance_time";
	//ATTENDANCE_PEOPLE
	$_no_of_people =  $row[$CCP_ATTENDANCE_NO_OF_PEOPLE];
	//HOSPITAL_LOCATION
	$_hospital_location = $row[$CCP_ATTENDANCE_WARD];
	//CLASS_TYPE
	$_class_type = getClassType($db_con, $_class_type_id);
	//CONDITION
	$_condition = getClassType($db_con, $_condition_id);
	//IMAGE_UPLOAD_PATH
    $_attendance_image		= setImagePath($CLASS_PATH_TYPE, $row[$CCP_ATTENDANCE_IMAGE]);
	//DB_ID
	$_db_id = $row[$CCP_ATTENDANCE_ID];
	
	
	//ADD ALL DATA
	$detail["STATE"]								= $_state;
	$detail["DISTRICT"]							= $_district;
	$detail["HOSPITAL"]							= $_hospital;
	$detail["PARTNER"]							= $_hospital_partner;
	$detail["TRAINER_NAME"]				= $_trainer_name;
	$detail["TRAINER_ID"]						= $_trainer_id;
	$detail["ATTENDANCE_DATE"]			= $_attendance_date;
	$detail["ATTENDANCE_YEAR"]			= $_attendance_year;
	$detail["ATTENDANCE_TIME"]			= $_attendance_time;
	$detail["ATTENDANCE_DATETIME"]	= $_attendance_datetime;
	$detail["ATTENDANCE_PEOPLE"]		= $_no_of_people;
	$detail["HOSPITAL_LOCATION"]		= $_hospital_location;
	$detail["CLASS_TYPE"]						= $_class_type;
	$detail["CONDITION"]						= $_condition;
	$detail["IMAGE_UPLOAD_PATH"]		= $_attendance_image;
	$detail["DB_ID"]								= $_db_id;
	
	return $detail;
}

/* getNurseRow
*/
function getNurseRow($db_con,$row) {
	
	include("../include/constant.php");		
	$detail = array();							
	$detail[$TAG_ID]								= $row[$NURSE_ID];
	$detail[$TAG_FIRST_NAME]				= $row[$NURSE_FIRST_NAME];
	$detail[$TAG_PROFILE_IMAGE]		= setImagePath($NURSE_PATH_TYPE, $row[$NURSE_PROFILE_IMAGE]);
	$detail["TRAINER_NAME"]				= $row[$NURSE_FIRST_NAME].' '.$row[$NURSE_LAST_NAME];//$row[$NURSE_CCP_MENTOR];
	$detail["TRAINER_ID"]						= $row[$NURSE_TRAINER];
	$detail[$TAG_CCP_CONDITION_ID]	= $row[$NURSE_CCP_CONDITION_ID];
	
	return $detail;
}

/* getStateName
*/
function getStateName($db_con,$_district_id) {
	
	include("../include/constant.php");		
	$name = "";
	$query = "SELECT * FROM `$STATE_TABLE` WHERE `$STATE_ID` IN (SELECT `$DISTRICT_STATE_ID` FROM `$DISTRICT_TABLE` WHERE `$DISTRICT_ID` = '$_district_id')";
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

/* getDistrictName
*/
function getDistrictName($db_con,$_district_id) {
	
	include("../include/constant.php");		
	$name = "";
	$query = "SELECT * FROM `$DISTRICT_TABLE` WHERE `$DISTRICT_ID`  = '$_district_id'";
	$result = mysqli_query($db_con,$query);
	
	if($result){
		if(mysqli_num_rows($result) != 0){
			$row = mysqli_fetch_array($result);						
			$name		= $row[$DISTRICT_NAME];			
			return $name;
		}
	}	
	return $name;
}

/* getHospitalName
*/
function getHospitalName($db_con,$_hospital_id) {
	
	include("../include/constant.php");		
	$name = "";
	$query = "SELECT * FROM `$HOSPITAL_TABLE` WHERE `$HOSPITAL_ID`  = '$_hospital_id'";
	$result = mysqli_query($db_con,$query);
	
	if($result){
		if(mysqli_num_rows($result) != 0){
			$row = mysqli_fetch_array($result);						
			$name		= $row[$HOSPITAL_NAME];			
			return $name;
		}
	}	
	return $name;
}

/* getHospitalPartnerName
*/
function getHospitalPartnerName($db_con,$_hospital_id) {
	
	include("../include/constant.php");	
	$name = "";
	$query = "SELECT * FROM `$HOSPITAL_PARTNER_TABLE` WHERE `$HOSPITAL_PARTNER_ID` IN (SELECT `$HOSPITAL_PARTNER_MAPPING_PARTNER_ID` FROM `$HOSPITAL_PARTNER_MAPPING_TABLE` WHERE `$HOSPITAL_PARTNER_MAPPING_HOSPITAL_ID` = '$_hospital_id')";
	$result = mysqli_query($db_con,$query);
	
	if($result){
		if(mysqli_num_rows($result) != 0){
			$row = mysqli_fetch_array($result);						
			$name		= $row[$HOSPITAL_PARTNER_NAME];	
		}
	}	
	return $name;
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

/* getClassType
*/
function getClassType($db_con,$_class_type_id) {
	
	include("../include/constant.php");		
	$name = "";					
	$query = "SELECT * FROM `$CCP_CLASS_TYPE_TABLE` WHERE `$CCP_CLASS_TYPE_ID` = '$_class_type_id'";
	$result = mysqli_query($db_con,$query);
	
	if($result){
		if(mysqli_num_rows($result) != 0){
			$row = mysqli_fetch_array($result);
			$name = $row[$CCP_CLASS_TYPE_CLASS_TYPE];
			return $name;
		}
	}	
	return $name;
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

/************************************  Common Functions End ******************************************/	

/************************************  Api Calls  ******************************************/


/** Get Class List
* url - /getNurseContent
* headers - token
*/
$app->get('/getCCPAttendanceOld', function() use ($app){
	// check for required params
	include("../include/constant.php");
	$db = new DB_CONNECT();
	$db_con=$db->conn;
	
	{
		$key = "";
		mysqli_set_charset($db_con,'utf8');
		date_default_timezone_set('Asia/Kolkata');
		$dateNow = date("Y-m-d H:i:s");
		
		$response = array();

		$query = "SELECT * FROM `$CCP_ATTENDANCE_TABLE`";
		$result = mysqli_query($db_con,$query);				
		if($result){
			
			if(mysqli_num_rows($result) != 0){
				
				$success = true;
				$message = "Content found Successfully.";
				
				while ($row = mysqli_fetch_array($result)) {	
					$detail = getClassRow($db_con, $row);
					array_push($response, $detail);
				}			
			}
		}
		echoRespnse(200, $response, $key);	
		//echo $response;
	}
	mysqli_close($db_con);
});



/** Get Class List
* url - /getNurseContent
* headers - token
*/
$app->get('/getCCPAttendanceNR', function() use ($app){
	// check for required params
	include("../include/constant.php");
	$db = new DB_CONNECT();
	$db_con=$db->conn;
	
	{
		$key = "";
		mysqli_set_charset($db_con,'utf8');
		date_default_timezone_set('Asia/Kolkata');
		$dateNow = date("Y-m-d H:i:s");
		
		$response = array();
		
		$extraQuery = "";
		$DB_SYNC_TYPE = 2;
		
		//GET LAST ENTRY TIME
		$query = "SELECT * FROM `noora_ccp_attendance_sync`  where type = '$DB_SYNC_TYPE' ";
		$result = mysqli_query($db_con,$query);				
		if($result){
			if(mysqli_num_rows($result) != 0){
				$row = mysqli_fetch_array($result);
				$lastTime = $row["Entry_Time"];
				$extraQuery = "WHERE `Edit_Time` >= '$lastTime' ";
			}
			
			//Update Sync TIME
			$querySync	= "INSERT INTO `noora_ccp_attendance_sync` (`type`, `Entry_Time`) VALUES ('$DB_SYNC_TYPE', '$dateNow' ) ON DUPLICATE KEY UPDATE  `Entry_Time` = '$dateNow' ";
			$result = mysqli_query($db_con, $querySync);	
		}
		
		
		//echo $query."\n\r";
		//echo $querySync."\n\r";

		$query = "SELECT * FROM `$CCP_ATTENDANCE_TABLE` $extraQuery";
		$result = mysqli_query($db_con,$query);				
		if($result){
			
			if(mysqli_num_rows($result) != 0){
				
				$success = true;
				$message = "Content found Successfully.";
				
				while ($row = mysqli_fetch_array($result)) {	
					$detail = getClassRow($db_con, $row);
					array_push($response, $detail);
				}			
			}
		}
		echoRespnse(200, $response, $key);
	}
	mysqli_close($db_con);
});




/** Get Class List
* url - /getNurseContent
* headers - token
*/
$app->get('/syncCCPAttendance', function() use ($app){
	// check for required params
	include("../include/constant.php");
	$db = new DB_CONNECT();
	$db_con=$db->conn;
	$db_con2=$db->conn2;
	
	{
		$key = "";
		mysqli_set_charset($db_con,'utf8');
		date_default_timezone_set('Asia/Kolkata');
		$dateNow = date("Y-m-d H:i:s");
		
		$response = array();
		
		$extraQuery = "";
		$DB_SYNC_TYPE = 1;
		
		//GET LAST ENTRY TIME
		$query = "SELECT * FROM `noora_ccp_attendance_sync`  where type = '$DB_SYNC_TYPE' ";
		$result = mysqli_query($db_con,$query);				
		if($result){
			if(mysqli_num_rows($result) != 0){
				$row = mysqli_fetch_array($result);
				$lastTime = $row["Entry_Time"];
				$extraQuery = "WHERE `Edit_Time` >= '$lastTime' ";
			}
			
			//Update Sync TIME
			$querySync	= "INSERT INTO `noora_ccp_attendance_sync` (`type`, `Entry_Time`) VALUES ('$DB_SYNC_TYPE', '$dateNow' ) ON DUPLICATE KEY UPDATE  `Entry_Time` = '$dateNow' ";
			$result = mysqli_query($db_con, $querySync);	
		}
		
		//echo $query."\n\r";
		//echo $querySync."\n\r";
		

		$query = "SELECT * FROM `$CCP_ATTENDANCE_TABLE` $extraQuery";
		$result = mysqli_query($db_con,$query);				
		if($result){
			
			if(mysqli_num_rows($result) != 0){
				
				$success = true;
				$message = "Content found Successfully.";
				
				while ($row = mysqli_fetch_array($result)) {	
					$detail = getClassRow($db_con, $row);
					array_push($response, $detail);
				}			
			}
		}
		
		//echo $query."\n\r";
		
		
		foreach ($response as $ccpItem) {
			//var_dump($ccpItem);
			
			$_state = $ccpItem["STATE"];
			$_district = $ccpItem["DISTRICT"];
			$_hospital = $ccpItem["HOSPITAL"];
			$_hospital_partner =$ccpItem["PARTNER"];
			$_trainer_name = $ccpItem["TRAINER_NAME"];
			$_trainer_id = $ccpItem["TRAINER_ID"];
			$_attendance_date =$ccpItem["ATTENDANCE_DATE"];
			$_attendance_year = $ccpItem["ATTENDANCE_YEAR"];
			$_attendance_time =$ccpItem["ATTENDANCE_DATETIME"];	//Requires datetime
			$_no_of_people = $ccpItem["ATTENDANCE_PEOPLE"];
			$_hospital_location = $ccpItem["HOSPITAL_LOCATION"];
			$_class_type =$ccpItem["CLASS_TYPE"];
			$_condition =$ccpItem["CONDITION"];
			$_attendance_image = $ccpItem["IMAGE_UPLOAD_PATH"];
			$_db_id = $ccpItem["DB_ID"];
			
			
			$queryInsert	= "INSERT INTO `ccp_attendance` (`id`, `state_name`, `district_name`, `facility_name`, `partner_name`, `trainer_name`, `trainer_id`, `class_date`, `year`, `start_time`, `number_of_people`, `in_hospital_location`, `class_type`, `condition`, `image_url`) VALUES ('$_db_id', '$_state', '$_district', '$_hospital',  '$_hospital_partner' ,  '$_trainer_name' ,  '$_trainer_id' ,  '$_attendance_date' ,  '$_attendance_year' ,  '$_attendance_time' ,  '$_no_of_people' ,  '$_hospital_location' ,  '$_class_type' ,  '$_condition' ,  '$_attendance_image' ) ON DUPLICATE KEY UPDATE  `state_name` = '$_state' , `district_name` = '$_district', `facility_name` = '$_hospital', `partner_name` = '$_hospital_partner', `trainer_name` = '$_trainer_name', `trainer_id` = '$_trainer_id', `class_date` = '$_attendance_date', `year` = '$_attendance_year', `start_time` = '$_attendance_time', `number_of_people` = '$_no_of_people', `in_hospital_location` = '$_hospital_location', `class_type` = '$_class_type', `condition` = '$_condition', `image_url` = '$_attendance_image'  ";
			$result = mysqli_query($db_con2,$queryInsert);	
			
			$error = mysqli_error($db_con2);
			//echo "Mysql Insert $queryInsert  Result $result  Error: $error";
		}
		
		
		
		echoRespnse(200, $response, $key);	
	}
	mysqli_close($db_con);
});


/************************************  Api Calls End  ******************************************/


/************************************  Required Functions  ******************************************/	
/**
 * Echoing json response to client
 * @param String $status_code Http response code
 * @param Int $response Json response
 */
function echoRespnse($status_code, $response, $key = '', $recall = false) {
	
	
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);

    // setting response content type to json
    $app->contentType('application/json');

	/*
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
	}*/
	
    echo json_encode($response);	
	
}

/************************************  Required Functions END ******************************************/	

$app->run();
?>
