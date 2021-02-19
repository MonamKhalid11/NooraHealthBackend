<?php
include('functions.php');
include 'csrf.class.php';

date_default_timezone_set("Asia/Kolkata");
$dateNow = date("Y-m-d H:i:s"); 

$Login_User_ID = $_COOKIE['login_adminid'];
$csrf = new csrf();
$token_id = $csrf->get_token_id();
$token_value = $csrf->get_token($token_id);
$file_name="";

$date = new DateTime();
$result = $date->format('Ymd_His');

if($csrf->check_valid('post')):

	$id=(int)$_POST['entry_id'];
	$Content_Title=$_POST['Content_Title'];
    $Content_Description=$_POST['Content_Description'];
    $Content_Description=str_replace("'","\'",$_POST['Content_Description']);
    $Role=$_POST['Group_Members'];
  
    $Content_Type=$_POST["Content_Type_Select"];

    if($Content_Type==1){
        if(isset($_FILES["Content_Type_image"])){
			$target_folder=$file_upload_path."/Content_Attachments/";
			if (!file_exists($target_folder)) {
				mkdir($target_folder, 0777, true);
			}
	
			$fileTmpPath = $_FILES['Content_Type_image']['tmp_name'];
			$fileName = $_FILES['Content_Type_image']['name'];
			$fileName=str_replace(" ","_",$fileName);

			$fileSize = $_FILES['Content_Type_image']['size'];
			$fileType = $_FILES['Content_Type_image']['type'];
			$fileNameCmps = explode(".", $fileName);
			$fileExtension = strtolower(end($fileNameCmps));
			
			
			$fileName = 'IMG_'.$result.'.'.$fileExtension;
			$dest_path = $target_folder . $fileName;

			if(move_uploaded_file($fileTmpPath, $dest_path)){
				$query2="DELETE FROM ".$table_content_attachment." WHERE Content_ID=".$id;
				mysqli_query($link,$query2);
				$file_name=$fileName;
			}
		}
    }
	
    if($Content_Type==2){
        $file_name=$_POST["Video_URL"];
        //$file_name=str_replace('embed/','watch?v=', $file_name);  
	}

    $current_time=date('Y-m-d H:i:s');

	if($id!=0){
        if($Content_Type!=2){
			if($file_name==""){
				$query="UPDATE ".$table_content." set Title='$Content_Title',Description='$Content_Description',Role='$Role',Content_Type='$Content_Type',Edit_Time='$dateNow' where ID='$id' ";	//,Login_User_ID='$Login_User_ID'
			}
			else{
				$query2="DELETE FROM ".$table_content_attachment." WHERE Content_ID=".$id;
				mysqli_query($link,$query2);
				$query1="INSERT INTO ".$table_content_attachment." (Content_ID,Attachment,Entry_Time,Edit_Time) VALUES ($id,'$file_name','$dateNow','$dateNow') ";
				mysqli_query($link,$query1);

				$query="UPDATE ".$table_content." set Title='$Content_Title',Description='$Content_Description',Role='$Role',Content_Type='$Content_Type',Edit_Time='$dateNow' where ID='$id' ";	//,Login_User_ID='$Login_User_ID'
			}
        }
        else{
            $query="UPDATE ".$table_content." set Title='$Content_Title',Description='$Content_Description',Role='$Role',Content_Type='$Content_Type',Edit_Time='$dateNow',Attachment='$file_name' where ID='$id' ";	//,Login_User_ID='$Login_User_ID'
        }
	}
	else{
		if($Content_Type!=2){
            $query="INSERT INTO ".$table_content." (Title,Description,Role,Content_Type,Attachment,Login_User_ID,Entry_Time,Edit_Time) VALUES ('$Content_Title','$Content_Description','$Role','$Content_Type','','$Login_User_ID','$dateNow','$dateNow') ";
		}
		else{
            $query="INSERT INTO ".$table_content." (Title,Description,Role,Content_Type,Attachment,Login_User_ID,Entry_Time,Edit_Time) VALUES ('$Content_Title','$Content_Description','$Role','$Content_Type','$file_name','$Login_User_ID','$dateNow','$dateNow') ";	
		}
    }

	if(mysqli_query($link,$query))
	{
		if($id!=0)
		{
			$User_ID = $id;
		}
		else
		{			
			$User_ID = mysqli_insert_id($link);
        }
		if($Content_Type==1){
            if($id!=0)
            {
                if($file_name!='')
                {
                    $query1="UPDATE ".$table_content_attachment." set status=1,Edit_Time='$dateNow', Attachment='$file_name' where Content_ID='$User_ID'";
                }
                else{
                 
                    $query1="UPDATE ".$table_content_attachment."set status=1,Edit_Time='$dateNow'. Content_ID='$User_ID'  where Content_ID='$User_ID'";
                }

            }
            else{
                $query1="INSERT INTO ".$table_content_attachment." (Content_ID,Attachment,Entry_Time,Edit_Time) VALUES ($User_ID,'$file_name','$dateNow','$dateNow') ";	
            }
        
            mysqli_query($link,$query1)	;
        }
	
    }

    if($User_ID>0 ){
		if($Content_Type==3){
			if(isset($_FILES['Content_Type'])){
				$query2="DELETE FROM ".$table_content_attachment." WHERE Content_ID=".$id." and status=3";
				mysqli_query($link,$query2);
				$target_folder=$file_upload_path."/Content_Attachments/";

                if (!file_exists($target_folder)) {
					mkdir($target_folder, 0777, true);
				}

				$file_name="";
				$pos = 0;
				foreach($_FILES['Content_Type']['tmp_name'] as $key => $tmp_name ){
					$file_name = $key.$_FILES['Content_Type']['name'][$key];
					$file_name=str_replace(" ","_",$file_name);
					$file_size =$_FILES['Content_Type']['size'][$key];
					$file_tmp =$_FILES['Content_Type']['tmp_name'][$key];
					$file_type=$_FILES['Content_Type']['type'][$key];
					
					$fileNameCmps = explode(".", $file_name);
					$fileExtension = strtolower(end($fileNameCmps));
					
					$pos = $pos+1;
					$file_name = 'IMG_'.$pos.$result.'.'.$fileExtension;
					$dest_path = $target_folder . $file_name;

					if(move_uploaded_file($file_tmp, $dest_path)){
						$query="INSERT INTO ".$table_content_attachment." (Content_ID,Attachment,Entry_Time,Edit_Time) VALUES ($User_ID,'$file_name','$dateNow','$dateNow') ";
						mysqli_query($link,$query);
					}
				}
			}
		}

        if(isset($_POST["group"])){
			$users=$_POST["group"];
            $query2="SELECT Content_ID  FROM ".$table_content_group." WHERE Content_ID=".$User_ID;

            $res2=mysqli_query($link,$query2);
            if(mysqli_num_rows($res2)>0){
				while($row2 = mysqli_fetch_array($res2)){
					$query1="DELETE FROM ".$table_content_group." WHERE Content_ID=".$User_ID;
					mysqli_query($link,$query1)	;  
				}

				for($i=0;$i<count($users);$i++){
					$query1="INSERT INTO ".$table_content_group." (Content_ID,Group_ID,Entry_Time,Edit_Time) VALUES ('$User_ID','$users[$i]','$dateNow','$dateNow') ";
					mysqli_query($link,$query1)	;
				}
			}
			else{
				for($i=0;$i<count($users);$i++){
					$query1="INSERT INTO ".$table_content_group." (Content_ID,Group_ID,Entry_Time,Edit_Time) VALUES ('$User_ID','$users[$i]','$dateNow','$dateNow') ";
					mysqli_query($link,$query1)	;
				}
			} 
        }
		
        if($_POST['Group_Members']==1){
            $query1="SELECT * FROM ".$table_content_group." where Content_ID=".$User_ID;

            $res1=mysqli_query($link,$query1);
            if(mysqli_num_rows($res1)>0){
                while($row2 = mysqli_fetch_array($res1)){
                    $query1="DELETE FROM ".$table_content_group." WHERE Content_ID=".$User_ID;          
                    mysqli_query($link,$query1);
				}
            }
			
            $Group_ID=0;
            $query2="INSERT INTO ".$table_content_group." (Content_ID,Group_ID,Entry_Time,Edit_Time) VALUES ('$User_ID',' $Group_ID','$dateNow','$dateNow') ";
			mysqli_query($link,$query2)	;           
        }
    }
	
	//Send Notification
	echo "Send Notification";
	echo "<br/><br/>";
	$registration_ids = array();	
	$deviceExist = false;
	
	/*
	// 1. Check if Content for all Group_ID = 0
	$query1="SELECT * FROM `$table_content_group` where `Content_ID`='$User_ID' and `Group_ID` = '0'";
	$res1=mysqli_query($link,$query1);
	if(mysqli_num_rows($res1)>0){
		//Show Notification to all users
		$query2 = "SELECT * FROM `$table_noora_user_device`";		
	}else{		
		$query2 = "SELECT * FROM `$table_noora_user_device` where `user_id` IN (SELECT `User_ID` FROM `$table_group_user` WHERE `Group_ID` IN (SELECT `Group_ID` FROM `$table_content_group` where `Content_ID` = '$User_ID'))";
	}
	*/
	
	//GET CONTENT GROUP	
	$query2 = "SELECT * FROM `$table_noora_user_device` where `user_id` IN (SELECT `User_ID` FROM `$table_group_user` WHERE `Group_ID` IN (SELECT `Group_ID` FROM `$table_content_group` where `Content_ID` = '$User_ID'))";
	
	$query1="SELECT * FROM `$table_content_group` where `Content_ID`='$User_ID'";
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
	if(mysqli_num_rows($res2)>0){
		
		$deviceExist = true;
		while($row2 = mysqli_fetch_array($res2)){
			$gcm_token = $row2['gcm_token'];
			array_push($registration_ids, $gcm_token);
		}
	}
	echo "Device ".$deviceExist;
	echo "<br/><br/>";
	
	if($deviceExist)
	{
		include('GCM.php');
		$gcm = new GCM();
		
		$message = array();
		{	  	
			$data = array();
			$data['to_user_id'] = 0;
			$data['notification_type'] = $NOTIFICATION_CONTENT;
			$data['message'] = $Content_Title;	
				
			$detail = array();	
			//GET CONTENT DATA
			$query ="SELECT * FROM `$table_content` WHERE `ID`  = '$User_ID'";
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
						$queryAttachment ="SELECT * FROM `$table_content_attachment` WHERE `Content_ID`  = '$User_ID' and `Status` = '1' limit 1";
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
		$_postResult = "No Devices";
		
		$queryPost	= "INSERT INTO `noora_notification_attempt_data` (`post_client`, `post_data`, `post_response`) VALUES ('$_postClient', '$_postData', '$_postResult')";
		$resultPost = mysqli_query($link,$queryPost);
	}
	
	mysqli_close($link);
	
else:

endif;
header("location:content_list.php");
?>