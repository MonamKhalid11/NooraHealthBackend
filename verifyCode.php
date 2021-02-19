<?php include('functions.php');
date_default_timezone_set('Asia/Kolkata');
include("api/include/constant.php");
$dateNow = date("Y-m-d H:i:s");
$_mobile_number=$_POST["Contact"];
$_otp=$_POST["Code"];
$query = "SELECT * FROM `$ADMIN_USER_TABLE` WHERE `$ADMIN_USER_MOBILE_NUMBER` = '$_mobile_number'";
$result = mysqli_query($link,$query);	
if($result){
			
    if(mysqli_num_rows($result) != 0){
        
        $row = mysqli_fetch_array($result);
        
        $_user_id 					= $row[$USER_OTP_ID];
        $_status 					= $row[$USER_OTP_STATUS];				
        $_canLogin = true;	
        
        if($_status == $STATUS_ACTIVE){
                                    
            if($_canLogin){
                
                //CHECK MASTER OTP
                $_masterOTP = getMasterOTP($link,$_user_id);
                if($_masterOTP == $_otp){
					
                
                        //VERIFY OTP
                        $query	= "SELECT * FROM `$USER_OTP_TABLE` WHERE `$USER_OTP_ADMIN_ID` = '$_user_id' AND `$USER_OTP_OTP` = '$_otp' AND `$USER_OTP_STATUS` = '$STATUS_ACTIVE'";
                        $result = mysqli_query($link,$query);		
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
                                $result = mysqli_query($link,$query);	
                                
                            }else{
                                $success = true;
                                $message = "OTP verified Successfully.";									
                                                                
                                $query	= "UPDATE `$USER_OTP_TABLE` SET `$USER_OTP_STATUS` = '$STATUS_VERIFIED', `$USER_OTP_EDIT_TIME` = '$dateNow' WHERE `$USER_OTP_ID` = '$_otp_id'";
                                $result = mysqli_query($link,$query);	
                                
                                $response[$TAG_DETAILS] = getUser($link,$_user_id);								
                            }
                            
                        }else{
                            $success = false;
                            $message = "Invalid OTP. Please check and try again";	
                        }
                    







                    // $success = true;
                    // $message = "OTP verified Successfully.";
                    // $response[$TAG_DETAILS] = getUser($link,$_user_id);	
                    // $query	= "UPDATE `$USER_OTP_TABLE` SET `$USER_OTP_STATUS` = '$STATUS_VERIFIED', `$USER_OTP_EDIT_TIME` = '$dateNow' WHERE `$USER_OTP_STATUS` = '$STATUS_ACTIVE' AND `$USER_OTP_USER_ID` = '$_user_id'";
                    // $result = mysqli_query($link,$query);	
                    
                }
                // else{						
                
                //     //VERIFY OTP
                //     $query	= "SELECT * FROM `$USER_OTP_TABLE` WHERE `$USER_OTP_USER_ID` = '$_user_id' AND `$USER_OTP_OTP` = '$_otp' AND `$USER_OTP_STATUS` = '$STATUS_ACTIVE'";
                //     $result = mysqli_query($link,$query);		
                //     if(mysqli_num_rows($result) != 0){
                //         $row = mysqli_fetch_array($result);
                        
                //         $_otp_id = $row[$USER_OTP_ID];
                        
                //         // 1. Check if OTP expired
                //         $_expire_time = $row[$USER_OTP_KEY_EXPIRY];
                //         echo $_expire_time;
                //         exit();
                //         $nowDate	= strtotime($dateNow);
                //         $endDate 	= strtotime($_expire_time);
                //         $timePassed = $endDate - $nowDate;

                //         if($timePassed<0){
                //             $success = false;
                //             $message = "OTP Expired. Please regenerate OTP and try again";	
                                                            
                //             $query	= "UPDATE `$USER_OTP_TABLE` SET `$USER_OTP_STATUS` = '$STATUS_FAILURE', `$USER_OTP_EDIT_TIME` = '$dateNow' WHERE `$USER_OTP_ID` = '$_otp_id'";
                //             $result = mysqli_query($link,$query);	
                            
                //         }else{
                //             $success = true;
                //             $message = "OTP verified Successfully.";									
                                                            
                //             $query	= "UPDATE `$USER_OTP_TABLE` SET `$USER_OTP_STATUS` = '$STATUS_VERIFIED', `$USER_OTP_EDIT_TIME` = '$dateNow' WHERE `$USER_OTP_ID` = '$_otp_id'";
                //             $result = mysqli_query($link,$query);	
                            
                //             $response[$TAG_DETAILS] = getUser($link,$_user_id);								
                //         }
                        
                //     }else{
                //         $success = false;
                //         $message = "Invalid OTP. Please check and try again";	
                //     }
                // }						
                
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

echo $message;



function getMasterOTP($db_con,$_user_id) {
	
    include("api/include/constant.php");	
    $detail = array();	
    // $query = "SELECT * FROM `$APP_SETTING_TABLE` WHERE `$APP_SETTING_NAME` = '$TAG_MASTER_OTP'";
	$query = "SELECT * FROM `$USER_OTP_TABLE` where `$USER_OTP_ADMIN_ID`='$_user_id' order by $USER_OTP_ENTRY_TIME desc limit 1";
	$result = mysqli_query($db_con,$query);
    
	if($result){
		if(mysqli_num_rows($result) != 0){
            $row = mysqli_fetch_array($result);
            return $row[$USER_OTP_OTP]."";
			// return $row[$APP_SETTING_VALUE]."";
		}
	}	
	return "1234";
}

function getUser($db_con,$_user_id) {
	
	include("api/include/constant.php");		
	$detail = array();							
    $query = "SELECT * FROM `$ADMIN_USER_TABLE` WHERE `$ADMIN_USER_ID` = '$_user_id'";
	$result = mysqli_query($db_con,$query);
	
	if($result){
		if(mysqli_num_rows($result) != 0){
			$row = mysqli_fetch_array($result);
			$detail = getUserRow($db_con, $row);
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
	$detail[$TAG_MOBILE_NUMBER]		= $row[$NURSE_MOBILE_NUMBER];
	
	return $detail;
}
?>