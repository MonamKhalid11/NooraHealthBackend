<?php
include('functions.php');
date_default_timezone_set('Asia/Kolkata');
$dateNow = date("Y-m-d H:i:s");
$_mobile_number=$_COOKIE['userContactNo'];
$Password=$_POST['Password'];

$Password_md5 = md5($Password);
$query = "SELECT ID FROM ".$table_admin." WHERE `Mobile_Number` = ".$_mobile_number." and status !=3";
$result=mysqli_query($link,$query);
if($result){
    if(mysqli_num_rows($result) != 0){
        $row = mysqli_fetch_array($result);
        $_user_id = $row['ID'];
    }
}
$query= "UPDATE ".$table_admin." SET Password = '$Password_md5', Edit_Time='$dateNow' WHERE ID=". $_user_id;

mysqli_query($link,$query);
// unset($_COOKIE['userContactNo']);
setcookie("userContactNo", "", time() - 3600);
header("location:index.php");

?>