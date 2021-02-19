<?php
 
class Exotel {
	
    //put your code here
    // constructor
    function __construct() {         
    }
	
    /**
     * Sending Push Notification
     */
    public function sendOtpSms($message) {
		return $this->sendSMS($message);
    }
	
	
    /**
     * Sending SMS
     */
    public function sendSMS($post_data) {
		
		/*
		$post_data = array(
			// 'From' doesn't matter; For transactional, this will be replaced with your SenderId;
			// For promotional, this will be ignored by the SMS gateway
			'From'   => '9168393189',
			'To'    => '9850177452',
			'Body'  => $message,
		);
		*/
		
		/*
		//VISHAL
		$api_key = "892d153cf47b080503efea09e0d066e0e27bf89c8ef96996"; // Your `API KEY`.
		$api_token = "af89acdb8c482e5aed41efdc74b3d609a9be03f7326964b7"; // Your `API TOKEN`
		$exotel_sid = "androcid1"; // Your `Account Sid`
		*/
		
		/*
		//NOORA OLD
		$api_key = "noorahealth"; // Your `API KEY`.
		$api_token = "d5cb5aa04b0cb97f1778e6a50546716be183d84f"; // Your `API TOKEN`
		$exotel_sid = "noorahealth"; // Your `Account Sid`
		*/
				
		//NEW VERSION
		$api_key = "8197d64e2ebf2f343901074698902a3d049d78681031cb25"; // Your `API KEY`.
		$api_token = "144548dcd5fff67e9f3b3b33ef2a3404eeab453ae4c16710"; // Your `API TOKEN`
		$exotel_sid = "NooraBackend"; // Your `Account Sid`
		
		
		//NEW VERSION
		$api_key = "8197d64e2ebf2f343901074698902a3d049d78681031cb25"; // Your `API KEY`.
		$api_token = "144548dcd5fff67e9f3b3b33ef2a3404eeab453ae4c16710"; // Your `API TOKEN`
		$exotel_sid = "yosaid1"; // Your `Account Sid`
			
		/*
		//DEFAULT
		$api_key = "1de749516e38bb4e7de298beaf0f9081f17da56bf49ef547"; // Your `API KEY`.
		$api_token = "0957308a22ece950d6e91acedc06aa83f249f858296bc35c"; // Your `API TOKEN`
		$exotel_sid = "Default API key"; // Your `Account Sid`
		*/
		
		/*
		//DEFAULT
		$api_key = "1de749516e38bb4e7de298beaf0f9081f17da56bf49ef547"; // Your `API KEY`.
		$api_token = "0957308a22ece950d6e91acedc06aa83f249f858296bc35c"; // Your `API TOKEN`
		$exotel_sid = "yosaid1"; // Your `Account Sid`
		*/
		
		
		 
		//$url = "https://".$api_key.":".$api_token."@twilix.exotel.in/v1/Accounts/".$exotel_sid."/Sms/send.json";
		$url = "https://".$api_key.":".$api_token."@api.exotel.com/v1/Accounts/".$exotel_sid."/Sms/send.json";
		 
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FAILONERROR, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
		 
		$http_result = curl_exec($ch);
		$error = curl_error($ch);
		$http_code = curl_getinfo($ch ,CURLINFO_HTTP_CODE);
		 
		curl_close($ch);
		//return json_decode($http_result, true);
		$json = json_decode($http_result, true);
		$json["url"] = $url;
		return $json;
    }
}
 
?>