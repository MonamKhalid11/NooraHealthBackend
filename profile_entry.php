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
if(isset($_FILES["profileImage"]))
{
	$target_folder=$file_upload_path."/ProfileImages/";

	if (!file_exists($target_folder)) {
    mkdir($target_folder, 0777, true);
}
	
$fileTmpPath = $_FILES['profileImage']['tmp_name'];
$fileName = $_FILES['profileImage']['name'];
$fileName=str_replace(" ","_",$fileName);
$fileSize = $_FILES['profileImage']['size'];
$fileType = $_FILES['profileImage']['type'];
$fileNameCmps = explode(".", $fileName);
$fileExtension = strtolower(end($fileNameCmps));
$dest_path = $target_folder . $fileName;
if(move_uploaded_file($fileTmpPath, $dest_path))
{
  $file_name=$fileName;
}
}
if($csrf->check_valid('post')):
	$id=(int)$_POST['entry_id'];

	$First_Name=$_POST['First_Name'];
	$Last_Name=$_POST['Last_Name'];
	$Email=$_POST['Email'];
	
	$Mobile_Number=$_POST['Mobile_Number'];
	$Password=$_POST['Password'];
	$profile_pic=$_POST['profileImage'];
	$password_query = "";
	$Password_md5 = "";
	if($Password!="")
	{
		$Password_md5 = md5($Password);
		$password_query = ",Password='$Password_md5'";
	}
		
	$current_time=date('Y-m-d H:i:s');
	if($id!=0)
	{
		if($file_name=='')
		{
			$query="UPDATE ".$table_admin." set First_Name='$First_Name',Last_Name='$Last_Name',Mobile_Number='$Mobile_Number',Edit_Time='$dateNow',Login_User_ID='$Login_User_ID'".$password_query." where ID='$id' ";
		}
		else{
			$query="UPDATE ".$table_admin." set First_Name='$First_Name',Last_Name='$Last_Name',Mobile_Number='$Mobile_Number',profile_image='$file_name',Edit_Time='$dateNow',Login_User_ID='$Login_User_ID'".$password_query." where ID='$id' ";

		}
	
	}
	else
	{
			$query="INSERT INTO ".$table_admin." (First_Name,Last_Name,Email,Role_ID,Password,Mobile_Number,Login_User_ID,profile_image,Entry_Time,Edit_Time) VALUES ('$First_Name','$Last_Name','$Email','$Role_ID','$Password_md5','$Mobile_Number','$Login_User_ID','$file_name','$dateNow','$dateNow') ";	
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
	}
	
	//UPDATE CONTENT
	$queryContent = "UPDATE ".$table_content." set Edit_Time='$dateNow' where Login_User_ID = '$User_ID' ";
	mysqli_query($link,$queryContent);
	//echo $queryContent;
	
	//UPDATE COOKIE
	if($Login_User_ID==$User_ID){			
		$Posted_By=$First_Name;
		if($Last_Name!=''){
		   $Posted_By.= ' '.$Last_Name;
		}
		setcookie("login_postedby",$Posted_By,time()+172800 ,'/');	
	}
	
	
	mysqli_close($link);
else:

endif;
if($Login_User_ID==$User_ID){	
					header("Location: profile.php?id=$admin_id");
}else{
header("location:admin_manager_list.php");
}
?>