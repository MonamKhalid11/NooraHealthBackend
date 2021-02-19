<?php

	include('functions.php');
	include('GCM.php');
	date_default_timezone_set("Asia/Kolkata");
	$dateNow = date("Y-m-d H:i:s");
	$gcm = new GCM();
	
	$_shouldInsert = true;

	//GET SCHEDULE COUNT
	$queryCheck = "SELECT * FROM " . $table_schedule_content . " WHERE `Schedule_Time` <= '$dateNow' AND `Status` = '1'";
    $resCheck = mysqli_query($link, $queryCheck);
	$totalCheck = mysqli_num_rows($resCheck); 
	echo $queryCheck."<br/>";

	while($rowContent=mysqli_fetch_array($resCheck)) {
		//MOVE CONTENT
		$_ContentID = $rowContent["ID"];
		echo $_ContentID;
		
		//INSERT INTO MAIN TABLE
		$Content_Title = $rowContent["Title"];
		$Content_Description = $rowContent["Description"];
		$Role = $rowContent["Role"];
		$Content_Type = $rowContent["Content_Type"];
		$Content_file = $rowContent["Attachment"];
		$Login_User_ID = $rowContent["Login_User_ID"];
		$scheduleTime = $rowContent["Schedule_Time"];
		$queryContent="INSERT INTO ".$table_content." (Title, Description, Role, Content_Type, Attachment, Login_User_ID, Entry_Time, Edit_Time) VALUES ('$Content_Title', '$Content_Description', '$Role', '$Content_Type', '$Content_file', '$Login_User_ID', '$scheduleTime', '$dateNow') ";	
		if($_shouldInsert){
			mysqli_query($link, $queryContent);
			$contentID = mysqli_insert_id($link);	//GOT NEW CONTENT ID
		}else{
			$contentID = "0";	//TEST
		}
		echo $queryContent."<br/>";
		
		//FOR ATTACHMENT
		$queryAttachment = "SELECT * FROM " . $table_schedule_content_attachment . " WHERE `Content_ID` = '$_ContentID'";
		$resAttachment = mysqli_query($link, $queryAttachment);
		echo $queryAttachment."<br/>";
		while($rowAttachment=mysqli_fetch_array($resAttachment)) {
			$contentAttachment = $rowAttachment["Attachment"];
			$query1="INSERT INTO ".$table_content_attachment." (Content_ID, Attachment, Entry_Time, Edit_Time) VALUES ($contentID,'$contentAttachment','$dateNow','$dateNow') ";	
			if($_shouldInsert){
				mysqli_query($link, $query1);
			}
			echo $query1."<br/>";
		}
		
		//FOR GROUPS
		$queryGroup = "SELECT * FROM " . $table_schedule_content_group . " WHERE `Content_ID` = '$_ContentID'";
		$resGroup = mysqli_query($link, $queryGroup);
		echo $queryGroup."<br/>";
		while($rowGroup=mysqli_fetch_array($resGroup)) {
			$contentGroup = $rowGroup["Group_ID"];
			$query1="INSERT INTO ".$table_content_group." (Content_ID, Group_ID, Entry_Time, Edit_Time) VALUES ('$contentID', '$contentGroup', '$dateNow', '$dateNow') ";	
			if($_shouldInsert){
				mysqli_query($link, $query1);
			}
			echo $query1."<br/>";
		}
		
		//DELETE SCHEDULE RECORD
		$query1="UPDATE ".$table_schedule_content." SET Status='3',Edit_Time='$dateNow' WHERE ID=".$_ContentID;
		if($_shouldInsert){
			mysqli_query($link, $query1);
		}
		echo $query1."<br/>";

		//SEND NOTIFICATION
		echo "Send Notification";
		echo "<br/><br/>";
		$registration_ids = array();	
		$deviceExist = false;
		
		//GET CONTENT GROUP	
		$query2 = "SELECT * FROM `$table_noora_user_device` where `user_id` IN (SELECT `User_ID` FROM `$table_group_user` WHERE `Group_ID` IN (SELECT `Group_ID` FROM `$table_content_group` where `Content_ID` = '$contentID'))";
		
		$query1="SELECT * FROM `$table_content_group` where `Content_ID`='$contentID'";
		$res1=mysqli_query($link,$query1);
		if(mysqli_num_rows($res1)>0){
			while($row2 = mysqli_fetch_array($res1)){	
				$_groupId = $row2["Group_ID"];
				if($_groupId == 0){
					$query2 = "SELECT * FROM `$table_noora_user_device`";
					break;
				}else{
					$query3="SELECT * FROM `$table_group` where `ID`='$_groupId' and `Group_Members` = '1'";
					$res3=mysqli_query($link,$query3);
					if(mysqli_num_rows($res3)>0){
						$query2 = "SELECT * FROM `$table_noora_user_device`";
						break;					
					}
				}			
			}
		}
		
		$res2 = mysqli_query($link, $query2);
		$_deviceCount = mysqli_num_rows($res2);
		if(mysqli_num_rows($res2)>0){
			
			$deviceExist = true;
			while($row2 = mysqli_fetch_array($res2)){
				$gcm_token = $row2['gcm_token'];
				array_push($registration_ids, $gcm_token);
			}
		}
		echo "Device: ".$deviceExist;
		echo "<br/><br/>";
		
		if(!$_shouldInsert){
			$deviceExist = false;
		}
		
		if($deviceExist)
		{
			
			$message = array();
			{	  	
				$data = array();
				$data['to_user_id'] = 0;
				$data['notification_type'] = $NOTIFICATION_CONTENT;
				$data['message'] = $Content_Title;	
					
				$detail = array();	
				//GET CONTENT DATA
				$query ="SELECT * FROM `$table_content` WHERE `ID`  = '$contentID'";
				$result = mysqli_query($link,$query);
				if($result){
					if(mysqli_num_rows($result)>0){
						$row = mysqli_fetch_array($result);
						
						$content = array();					
						$content["id"] 			= $row["ID"];
						$content["title"] 		= $row["Title"];
						$content["description"] 	= $row["Description"];
						$content["content_type"] = $row["Content_Type"];
						$content["attachment"]  = "";
						$_contentType = $row["Content_Type"];
						if($_contentType == 2){
							//Youtube URL
							$content["attachment"] 	= $row["Attachment"];	
						}else{
							$queryAttachment ="SELECT * FROM `$table_content_attachment` WHERE `Content_ID`  = '$contentID' and `Status` = '1' limit 1";
							$resultAttachment = mysqli_query($link,$queryAttachment);
							if($resultAttachment){
								if(mysqli_num_rows($resultAttachment)>0){
									$rowAttachment = mysqli_fetch_array($resultAttachment);
									$content["attachment"] 	= $CONTENT_IMAGE_PATH.$rowAttachment["Attachment"];	
								}
							}
						}
						$detail["content"] = $content;
					}
				}
				
				$data['details'] = $detail;
			}
			$message["message"] = $data;
			$result = $gcm->send_notification($registration_ids, $message);	
			//entry in notification table
			echo "<br/><br/>";	
			echo $query2;
			echo "<br/><br/>";
			
			$_postClient = mysqli_real_escape_string($link, $query2);
			$_postData = json_encode($message);
			$_postResult = json_encode($result);
			
			$queryPost	= "INSERT INTO `noora_notification_attempt_data` (`post_client`, `post_data`, `post_response`) VALUES ('$_postClient', '$_postData', '$_postResult')";
			$resultPost = mysqli_query($link,$queryPost);
				
			echo $queryPost;
			echo "<br/><br/>";
			#var_dump($registration_ids);	
			echo $result;
			echo "<br/><br/>";
			var_dump($message);
		}else{
			$_postClient = mysqli_real_escape_string($link, $query2);
			$_postData = "No Devices";
			$_postResult = "No Devices Schedule ".$_deviceCount;
			if($_shouldInsert){
				$_postResult = "No Devices Schedule true ".$_deviceCount;
			}
			
			$queryPost	= "INSERT INTO `noora_notification_attempt_data` (`post_client`, `post_data`, `post_response`) VALUES ('$_postClient', '$_postData', '$_postResult')";
			$resultPost = mysqli_query($link,$queryPost);
		}

	}
	
	//UPDATE LOG
	$queryLog = "INSERT INTO " . $table_schedule_content_log . " (Update_Count,Entry_Time) VALUES ('$totalCheck','$dateNow') ";
    mysqli_query($link, $queryLog);
	echo $queryLog."<br/>";
	
    mysqli_close($link);
	
	echo "Content Updated: $totalCheck";
?>
