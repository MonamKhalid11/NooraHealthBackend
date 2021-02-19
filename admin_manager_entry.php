<?php
include('functions.php');
include 'csrf.class.php';
date_default_timezone_set("Asia/Kolkata");
$dateNow = date("Y-m-d H:i:s"); 

$admin_id =0;
$csrf = new csrf();
$token_id = $csrf->get_token_id();
$token_value = $csrf->get_token($token_id);
$file_name="";
$Login_User_ID=0;
if($csrf->check_valid('post')):
	$id=(int)$_POST['entry_id'];

	$First_Name=$_POST['First_Name'];
	$Last_Name=$_POST['Last_Name'];
    $Email=$_POST['Email'];
    $City=$_POST['city'];
	$Role_ID=$_POST['role'];
		
	$Mobile_Number=$_POST['Mobile_Number'];
	$current_time=date('Y-m-d H:i:s');
	$Password_md5=md5('admin123');
	if($id!=0)
	{
		$query="UPDATE ".$table_admin." set First_Name='$First_Name',Last_Name='$Last_Name',District_ID='$City',Role_ID='$Role_ID',Mobile_Number='$Mobile_Number',Edit_Time='$dateNow',Login_User_ID='$Login_User_ID'".$password_query." where ID='$id' ";
	}
	else
	{
		$query="INSERT INTO ".$table_admin." (First_Name,Last_Name,Email,District_ID,Role_ID,Password,Mobile_Number,Login_User_ID,Entry_Time,Edit_Time) VALUES ('$First_Name','$Last_Name','$Email','$City','$Role_ID','$Password_md5','$Mobile_Number','$Login_User_ID','$dateNow','$dateNow') ";	
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

	mysqli_close($link);
else:

endif;

header("location:admin_manager_list.php");
?>