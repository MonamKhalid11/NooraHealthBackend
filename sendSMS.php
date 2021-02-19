<?php
include('functions.php');
date_default_timezone_set('Asia/Kolkata');
include("api/include/constant.php");
$dateNow = date("Y-m-d H:i:s");
$_mobile_number=$_POST["Contact"];
// $_status_deleted=3;
$query="SELECT * FROM `$ADMIN_USER_TABLE` where `$ADMIN_USER_MOBILE_NUMBER`='$_mobile_number' ORDER BY `$ADMIN_USER_ENTRY_TIME` DESC";
$result = mysqli_query($link,$query);
// echo $query;
if($result){
			
	if(mysqli_num_rows($result) != 0){
		
		$row = mysqli_fetch_array($result);
		
		$_user_id 					= $row[$USER_OTP_ID];
		$_status 					= $row[$USER_OTP_STATUS];	

		$_canLogin = true;
		if($_status != $STATUS_DELETED){	
			if($_canLogin){
				$otpData = sendOTPMessage($link,$_user_id);
				
				
				$success = $otpData[$TAG_SUCCESS];
				$message = $otpData[$TAG_MESSAGE];
				$response[$TAG_DETAILS] = $otpData;						 
				
			}else{
				$success = false;
				$message = "You are already logged in, on another device";	
									
			}	
		}
		else{
			// $success = false;	 				
			// if($_status == $STATUS_DISABLED){
			// 	$message = "Your account has been disabled by Admin.";
			// }					
			// if($_status == $STATUS_DELETED){
				// $addNewUser = True;
				$message = "Your account has been Deleted by Admin.";
				
			// }
		}
	}else{
		$message = "Please check the Mobile Number";
	}
}

echo $message;



function sendOTPMessage($link,$_user_id) {
		
	include("api/include/constant.php");
	include_once("api/include/sms/exotel.php");
	date_default_timezone_set('Asia/Kolkata');					
	$dateNow = date("Y-m-d H:i:s"); 
	$sms = new Exotel();	
	$response = array();
			
	$_send_sms = True;
	$_update = False;
	$_otp_id = 0;
	$_attempt = 0;
	$_otp = generateStrongPassword($OTP_LENGTH, false, 'd');
	$message = "OTP message sent to your number.";
	
	//CHECK IF ALREADY SENT OTP
	$query	= "SELECT * FROM `$USER_OTP_TABLE` WHERE `$USER_OTP_USER_ID` = '$_user_id' AND `$USER_OTP_STATUS` = '1'";
		
	$result = mysqli_query($link,$query);	
	
	if(mysqli_num_rows($result) != 0){
		$_update = true;
		
		//CHECK PREVIOUS ATTEMPT DETAILS
		$row = mysqli_fetch_array($result);
		
		$_otp_id = $row[$USER_OTP_ID];	
		$_otp = $row[$USER_OTP_OTP];	
		
		// 1. No. of ATTEMPTS
		$_attempt = $row[$USER_OTP_ATTEMPT];	
		// echo $_attempt;
		if($_attempt >= $MAX_ATTEMPT_ALLOWD){
			$_send_sms = False;
			$message = "Your attempt limit is over. Please try again after 6 Hours.";
			$reason="Error";
			// echo $message;
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
				$result = mysqli_query($link,$query);	
				$message = "OTP message sent to your number.";
			}
		}
		
	}else{
		//NO PREVIOUS ATTEMPTS		
	}
	
	if($_send_sms){
		
		$_key_expiry = date('Y-m-d H:i:s', strtotime('+5 minutes', strtotime($dateNow)));
		$_attempt = $_attempt + 1;
	
		$_otp = generateStrongPassword($OTP_LENGTH, false, 'd');
		
		if($_update){
			$query	= "UPDATE `$USER_OTP_TABLE` SET `$USER_OTP_OTP` = '$_otp',`$USER_OTP_ATTEMPT` = '$_attempt', `$USER_OTP_KEY_EXPIRY` = '$_key_expiry', `$USER_OTP_EDIT_TIME` = '$dateNow' WHERE `$USER_OTP_ID` = '$_otp_id'";
		}else{
			$query	= "INSERT INTO `$USER_OTP_TABLE` (Admin_ID, `$USER_OTP_OTP`, `$USER_OTP_ATTEMPT`, `$USER_OTP_KEY_EXPIRY`, `$USER_OTP_STATUS`, `$USER_OTP_ENTRY_TIME`, `$USER_OTP_EDIT_TIME`) VALUES ('$_user_id','$_otp','$_attempt','$_key_expiry','1','$dateNow','$dateNow')";
		}
		// echo $query;
		$result = mysqli_query($link,$query);	
		
		if($result){
			
			$user = getUser($link, $_user_id);
			
			$otpMessage = "The OTP for NooraHealth is $_otp. OTP is valid for next 5 min";
			// echo $otpMessage;
			// echo $user[$TAG_MOBILE_NUMBER];
			//$otpMessage = "This is a test message being sent using Exotel with a ($nurse[$TAG_FIRST_NAME]) and ($_otp). If this is being abused, report to 08088919888";
			
			
			$postData = array(
					// 'From' doesn't matter; For transactional, this will be replaced with your SenderId;
					// For promotional, this will be ignored by the SMS gateway
					'From'   => '09168393189',
					'To'    => $user[$TAG_MOBILE_NUMBER],
					'Body'  => $otpMessage,
				);	
			$postResult = $sms->sendOtpSms($postData);
			
			$response["user"] = $user;
			$response["sms_data"] = $postData;
			$response["sms_result"] = $postResult;
		}
		
	}
	
	$response[$TAG_SUCCESS] = $_send_sms;
	$response[$TAG_MESSAGE] = $message;
	return $response;
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




/* getUser
*/
function getUser($link,$_user_id) {
	
	include("api/include/constant.php");		
	$detail = array();							
	$query = "SELECT * FROM `$ADMIN_USER_TABLE` WHERE `$ADMIN_USER_ID` = '$_user_id'";
	$result = mysqli_query($link,$query);
	
	if($result){
		if(mysqli_num_rows($result) != 0){
			$row = mysqli_fetch_array($result);
			$detail = getUserRow($link, $row);
			return $detail;
		}
	}	
	return null;
}

function getUserRow($db_con,$row) {
	
	include("api/include/constant.php");		
	$detail = array();							
	$detail[$TAG_ID]				= $row[$NURSE_ID];
	$detail[$TAG_FIRST_NAME]		= $row[$NURSE_FIRST_NAME];
	$detail[$TAG_LAST_NAME]			= $row[$NURSE_LAST_NAME];
	// $detail[$TAG_DOB]				= $row[$NURSE_DOB];
	$detail[$TAG_MOBILE_NUMBER]		= $row[$NURSE_MOBILE_NUMBER];
	// $detail[$TAG_PROFILE_IMAGE]		= setImagePath($NURSE_PATH_TYPE, $row[$NURSE_PROFILE_IMAGE]);
	
	return $detail;
}


?>