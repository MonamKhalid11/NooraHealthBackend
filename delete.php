<?php
include('functions.php');
$id = $_POST["id"];
date_default_timezone_set("Asia/Kolkata");
$dateNow = date("Y-m-d H:i:s");

$status = $_POST["status"];
switch ($status) {
	case 'Admin_Manager':
		$query1 = "UPDATE " . $table_admin . " SET Status='3',Edit_Time='$dateNow' WHERE ID=" . $id;
		break;
	case 'Nurse':
		$query1 = "UPDATE " . $table_nurse . " SET Status='3',Edit_Time='$dateNow' WHERE ID=" . $id;
		$query2 = "UPDATE " . $table_group_user . " SET Status='3',Edit_Time='$dateNow' WHERE User_ID=" . $id;
		$query3 = "UPDATE " . $table_content . " SET Edit_Time='$dateNow' WHERE `ID` IN (SELECT `Content_ID` FROM " . $table_content_group . " WHERE `Group_ID` IN (SELECT `Group_ID` FROM " . $table_group_user . " WHERE `User_ID` = '$id'))"; {
			//Send Notification
			//echo "Send Notification";
			//echo "<br/><br/>";
			$registration_ids = array();
			$deviceExist = false;

			$queryDevice = "SELECT * FROM `$table_noora_user_device` where `user_id` = '$id'";
			$resDevice = mysqli_query($link, $queryDevice);
			if (mysqli_num_rows($resDevice) > 0) {

				$deviceExist = true;
				while ($row2 = mysqli_fetch_array($resDevice)) {
					$gcm_token = $row2['gcm_token'];
					array_push($registration_ids, $gcm_token);
				}
			}
			//echo "Device ".$deviceExist;
			//echo "<br/><br/>";

			if ($deviceExist) {
				include('GCM.php');
				$gcm = new GCM();

				$message = array(); {
					$data = array();
					$data['to_user_id'] = $id;
					$data['notification_type'] = $NOTIFICATION_LOGOUT;
					$data['message'] = "Your account has been deleted bu Admin";

					$detail = array();
					//GET CONTENT DATA	
					$content = array();
					$content["id"] 			= $id;
					$content["message"] 	= "Your account has been deleted bu Admin";
					$detail["logout"] = $content;
					$data['details'] = $detail;
				}
				$message["message"] = $data;
				$result = $gcm->send_notification($registration_ids, $message);
				//entry in notification table
				/*
				echo "<br/><br/>";	
				echo $queryDevice;
				echo "<br/><br/>";	
				echo var_dump($registration_ids);
				echo "<br/><br/>";
				echo "RESULT: ".$result;
				echo "<br/><br/>";
				var_dump($message);
				*/
			}
		}

		break;
	case 'Content':
		$query1 = "UPDATE " . $table_content . " SET Status='3',Edit_Time='$dateNow' WHERE ID=" . $id;
		break;
	case 'Group':
		$query1 = "UPDATE " . $table_group . " SET Status='3',Edit_Time='$dateNow' WHERE ID=" . $id;
		break;
	case 'Comment':
		$query1 = "UPDATE " . $table_comments . " SET Status='3',Edit_Time='$dateNow' WHERE ID=" . $id;
		break;
	case 'Attendence':
		$query1 = "UPDATE " . $table_attendance . " SET Status='3',Edit_Time='$dateNow' WHERE ID=" . $id;
		break;
	case 'Schedule_Content':
		$query1 = "UPDATE " . $table_schedule_content . " SET Status='3',Edit_Time='$dateNow' WHERE ID=" . $id;
		break;
	case 'Hospital':
		$query1 = "UPDATE " . $table_noora_hospital . " SET Status='0',Edit_Time='$dateNow' WHERE ID=" . $id;
		mysqli_query($link, $query1);
		$query1 = "DELETE from " . $table_noora_hospital_partner_mapping . " where Hospital_ID = '$id' ";
		break;
	case 'training_courses':
		$query1 = "UPDATE " . $table_noora_online_training_courses . " SET Status='0',Edit_Time='$dateNow' WHERE ID=" . $id;
		break;
	case 'tool_material_section':
		$query1 = "UPDATE " . $table_noora_ccp_tool_material . " SET Status='0',Edit_Time='$dateNow' WHERE ID=" . $id;
		break;	

		
}
$res1 = mysqli_query($link, $query1);
$res2 = mysqli_query($link, $query2);
$res3 = mysqli_query($link, $query3);
if ($res1) {
	echo 1;
} else {
	echo 2;
}
