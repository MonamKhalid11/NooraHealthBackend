<?php
include 'functions.php';  //include the functions.php
	$username = $_POST['login_username'];
	$password = $_POST['login_password'];

	if ($username!="" && $password!="") 
	{
		$query1="SELECT * FROM ".$table_admin." WHERE Email='$username' and Status !=3";
		// echo $query1;die();
		$res1= mysqli_query($link,$query1);		
		if (mysqli_num_rows($res1)) 
		{
			while ($row1 = mysqli_fetch_assoc($res1)) 
			{
				$admin_id = $row1['ID']; 
				$db_password = $row1['Password']; 
				$admin_role = $row1['Role_ID']; 
				$status = $row1['Status'];				
				$Posted_By=$row1['First_Name'];
				if($row1['Last_Name']!=''){
				   $Posted_By.= ' '.$row1['Last_Name'];
				}
				
				if (md5($password)==$db_password) 
				{  
					$loginok = TRUE;
					
					if($status == 1)
					{
						$loginok = TRUE;
					} 
					else 
					{
						header("Location:index.php?failed=2");
						exit();
					}					
				} 
				else 
				{
					header("Location:index.php?failed=1");
					exit();
				}			

				if ($loginok==TRUE) //if it is the same password, script will continue.
				{
			
					setcookie("login_adminname",$username,time()+172800 ,'/');	
					setcookie("login_postedby",$Posted_By,time()+172800 ,'/');	
					setcookie("login_adminid",$admin_id,time()+172800 ,'/');	
					setcookie("login_adminrole",$admin_role,time()+172800 ,'/');
					
					//update in last login and login log entry
					$current_time=date('Y-m-d H:i:s');
					$query2="update ".$table_admin." set Last_Login='$current_time' where ID='$admin_id' ";
					mysqli_query($link,$query2);
					
					//insert in log entry
					$query3="INSERT INTO ".$table_admin_login_log." (User_ID,Login_Time) VALUES ('$admin_id','$current_time') ";
					mysqli_query($link,$query3);					
					
					header("Location: profile.php?id=$admin_id");//user-loggedin
					exit();
				}
			}
		} 
		else 
		{
			header("Location:index.php?failed=3");
		}
	}
	else
	{
		header("Location:index.php?failed=4");
	}

mysqli_close($link);
?>