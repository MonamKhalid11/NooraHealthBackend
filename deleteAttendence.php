<?php
date_default_timezone_set("Asia/Kolkata");
$dateNow = date("Y-m-d H:i:s"); 
include('functions.php');
$id=$_POST["id"];
$query1="UPDATE ".$table_attendance." SET Status='3',Edit_Time='$dateNow' WHERE ID=".$id;
// echo $query1;
// exit();
// if($status=="Admin_Manager")
// {
//     $query1="UPDATE ".$table_admin." SET Status='3' WHERE ID=".$id;
// }
// if($status=="Nurse")
// {
//     $query1="UPDATE ".$table_nurse." SET Status='3' WHERE ID=".$id;
// }
// if($status=="Content")
// {
//     $query1="UPDATE ".$table_content." SET Status='3' WHERE ID=".$id;
// }
// if($status=="Group")
// {
//     $query1="UPDATE ".$table_group." SET Status='3' WHERE ID=".$id;
// }
$res1= mysqli_query($link,$query1);
if($res1)
{
    echo 1;
}
else
{
    echo 2;
}
?>