<?php
include('functions.php');
$Email=$_POST["email"];
$Phone=$_POST["mobile"];
$Id=(int)$_POST['id'];
$table=$_POST["tableName"];
    if($table=='noora_admin_user')
    {
        if($Id>0)
        {
            if($Phone>0)
            {
                $query1="SELECT * FROM ".$table_admin." WHERE Mobile_Number='$Phone' and ID!='$Id'";
            }
            else
            {
                $query1="SELECT * FROM ".$table_admin." WHERE Email='$Email' and ID!='$Id'";
            }
           
        }
        else{
            if($Phone>0)
            {
            $query1="SELECT * FROM ".$table_admin." WHERE Mobile_Number='$Phone' AND  Status!='3'";
            }
            else
            {
                $query1="SELECT * FROM ".$table_admin." WHERE Email='$Email' AND  Status!='3'";
            }
        }
    }
    else if($table=='noora_nurse')
    {
        if($Id>0)
        {
            $query1="SELECT * FROM ".$table_nurse." WHERE Mobile_Number='$Email' and ID!='$Id'";
        }
        else{
            $query1="SELECT * FROM ".$table_nurse." WHERE Mobile_Number='$Email' AND  Status!='3'";
        }
    }
    else if($table=='noora_group')
    {
        if($Id>0)
        {
            $query1="SELECT * FROM ".$table_group." WHERE Name='$Email' and ID!='$Id'";
        }
        else{
            $query1="SELECT * FROM ".$table_group." WHERE Name='$Email' AND  Status!='3'";
        }
    }
$res1= mysqli_query($link,$query1);		
		if (mysqli_num_rows($res1)>0) 
		{
            echo 1;
        }
        else{
            echo 0;
        }

?>