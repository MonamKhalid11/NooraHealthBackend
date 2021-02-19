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
$District_ID="";
$stateID="";
$countryID="";
$District_ID=$_POST['city'];
$stateID=$_POST['state'];
$countryID=$_POST['country'];

if($countryID>0)
{
	$country=$countryID;
}
else{
	$country=0;
}
if($stateID>0)
{
	$state=$stateID;
}
else{
	$state=0;
}
if($District_ID>0)
{
	$city=$District_ID;
}
else{
	$city=0;
}

if($csrf->check_valid('post')):
	$id=(int)$_POST['entry_id'];
	$Name=$_POST['Group_Name'];
    $Description=$_POST['Group_Description'];
    $Group_Members=$_POST['Group_Members'];
	$current_time=date('Y-m-d H:i:s');
	if($id!=0)
	{

			$query="UPDATE ".$table_group." set Name='$Name',Description='$Description',Login_User_ID='$Login_User_ID',Group_Members='$Group_Members',
			CountryID='$country',StateID='$state',District_ID='$city',Edit_Time='$dateNow' where ID='$id' ";

	}
	else
	{
					$query="INSERT INTO ".$table_group." (Name,Description,Group_Members,Login_User_ID,StateID,CountryID,District_ID,Entry_Time,Edit_Time) VALUES ('$Name','$Description','$Group_Members','$Login_User_ID','$state','$country','$city','$dateNow','$dateNow') ";	
	
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
    if($User_ID>0 && $Group_Members==2)
    {
        if(isset($_POST["group"]))
        {
          $users=$_POST["group"];
          $sql="Delete from ".$table_group_user." Where ".$table_group_user.".Group_ID=".$User_ID;
                              mysqli_query($link,$sql)	;
        
                        for($i=0;$i<count($users);$i++)
                        {
                              
                            $query1="INSERT INTO ".$table_group_user." (Group_ID,User_ID,Entry_Time,Edit_Time) VALUES ('$User_ID','$users[$i]','$dateNow','$dateNow') ";
                            mysqli_query($link,$query1)	;
                            }

        }
       
    }

	mysqli_close($link);
else:

endif;

header("location:group_list.php");
?>