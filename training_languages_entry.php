<?php
include('functions.php');
include 'csrf.class.php';

date_default_timezone_set("Asia/Kolkata");
$dateNow = date("Y-m-d H:i:s");

$Login_User_ID = $_COOKIE['login_adminid'];
$csrf = new csrf();
$token_id = $csrf->get_token_id();
$token_value = $csrf->get_token($token_id);


if ($csrf->check_valid('post')) :

    $id = (int)$_POST['entry_id'];
    $Language = $_POST['Language'];
    $status = 1;

    if ($id != "") {
        $query1 = "UPDATE " . $table_noora_online_training_language . " set Name='$Language', Edit_Time='$dateNow' where ID='$id' ";
        mysqli_query($link, $query1);
    } else {
        $query1 = "INSERT INTO " . $table_noora_online_training_language . " (Name,Status,Edit_Time,Entry_Time) VALUES ('$Language',$status,'$dateNow','$dateNow') ";
        $res1 = mysqli_query($link, $query1);
    }



    mysqli_close($link);

else :


endif;
header("location:training_languages_listing.php");
