<?php
include('functions.php');
date_default_timezone_set("Asia/Kolkata");
$dateNow = date("Y-m-d H:i:s");
//statu change
$status_id = (int)$_POST['id'];
$status_val = $_POST['status'];

if ($status_id != "" &&  $status_val != "") {
    $query1 = "UPDATE " . $table_noora_online_training_language . " set Status='$status_val', Edit_Time='$dateNow' where ID='$status_id' ";
    $re = mysqli_query($link, $query1);

    echo $re;
}


