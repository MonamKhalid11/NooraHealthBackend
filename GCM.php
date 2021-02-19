<?php

class GCM {
	
    //put your code here
    // constructor
    function __construct() {
         
    }


    public function send_mentioned_notification($registrationIds,$msg){
    	

    	$fields = array(
							'registration_ids' 	=> $registrationIds,
							'notification'			=> $msg
						);
    	$headers = array(
							'Authorization: key=' . FCM_KEY,
							'Content-Type: application/json'
						);

    	$ch = curl_init();
		curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
		curl_setopt( $ch,CURLOPT_POST, true );
		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
		$result = curl_exec($ch );
		curl_close( $ch );
		//echo $result;
    }
	
    /**
     * Sending Push Notification
     */
    public function send_notification($registration_ids, $message) {
		
		// Send notifications
		//DEVIDE ARRAY INTO SUB ARRAY OF 500
		$arrays = array_chunk($registration_ids, 500);		
		//SEND NOTIFICATION TO EACH SUB ARRAY SET
		foreach ($arrays as $array_num => $array) {
			$result = $this->pushNotifications($array, $message);
		}
		return $result;
    }
	
	
    /**
     * Sending Push Notification
     */
    public function pushNotifications($registration_ids, $message) {
				
		require_once 'config.php';
		
        // Set POST variables
        $url = 'https://fcm.googleapis.com/fcm/send'; // for fcm
		
		$fields = array();
		
		if(count($registration_ids) == 1){
			$fields = array(
				'to' => $registration_ids[0],
				'data' => $message,
			);
		}else{		
			$fields = array(
				'registration_ids' => $registration_ids,
				'data' => $message,
			);
		}
		
        $headers = array(
            'Authorization: key=' . FCM_KEY,
            'Content-Type: application/json'
        );
		
        // Open connection
        $ch = curl_init();
		
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
		
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
		
        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));        
		}
		
        // Close connection		
        curl_close($ch);
		
        //echo 'registration_ids: '.$registration_ids;
		return $result;
    }
}
 
?>