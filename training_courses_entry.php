<?php
include('functions.php');
include 'csrf.class.php';

date_default_timezone_set("Asia/Kolkata");
$dateNow = date("Y-m-d H:i:s");

$Login_User_ID = $_COOKIE['login_adminid'];
$csrf = new csrf();
$token_id = $csrf->get_token_id();
$token_value = $csrf->get_token($token_id);

// $date = new DateTime();
// $result = $date->format('Ymd_His');
$arr = array();

if ($csrf->check_valid('post')) :

    $id = (int)$_POST['entry_id'];
    $course = $_POST['course'];
    $training_url = $_POST['training_url'];
    $language = $_POST['language'];
    $status = 1;



    if ($id != "") {
        $query1 = "UPDATE " . $table_noora_online_training_courses . " set Name='$course',Training_URL = '$training_url',Language_ID='$language',Status= '$status',Edit_Time='$dateNow' where ID='$id' ";
        mysqli_query($link, $query1);
    } else {
        $query1 = "INSERT INTO " . $table_noora_online_training_courses . " (Language_ID,Name,Training_URL,Status,Edit_Time,Entry_Time) VALUES ($language,'$course','$training_url',$status,'$dateNow','$dateNow') ";
        // var_dump($query1);
        $res1 = mysqli_query($link, $query1);
    }

    mysqli_close($link);

else :

endif;
header("location:training_courses_listing.php");
