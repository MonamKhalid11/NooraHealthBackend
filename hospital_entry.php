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
    $hospital = $_POST['Hospital'];
    $state = $_POST['State'];
    $partner = $_POST['partner'];
    $status = 1;
    $last_id = "";


    if ($id != "") {
        $query1 = "UPDATE " . $table_noora_hospital . " set Name='$hospital',State_ID='$state',Status= '$status',Edit_Time='$dateNow' where ID='$id' ";
        mysqli_query($link, $query1);
        if ($_POST['partner'] !== "") {
            $query2 = "DELETE from " . $table_noora_hospital_partner_mapping . " where Hospital_ID = '$id' ";
            mysqli_query($link, $query2);

            // foreach ($_POST['partner'] as $partner) {
            $query2 = "INSERT INTO " . $table_noora_hospital_partner_mapping . " (Hospital_ID,Partner_ID) VALUES ($id,$partner) ";
            mysqli_query($link, $query2);
            // }
        }
    } else {
        $query1 = "INSERT INTO " . $table_noora_hospital . " (Name,State_ID,Status,Edit_Time,Entry_Time) VALUES ('$hospital',$state,$status,'$dateNow','$dateNow') ";
        $res1 = mysqli_query($link, $query1);
        if ($res1 === TRUE) {
            $last_id = $link->insert_id;
        }

        if ($last_id != "") {
            // foreach ($_POST['partner'] as $partner) {
            $query2 = "INSERT INTO " . $table_noora_hospital_partner_mapping . " (Hospital_ID,Partner_ID) VALUES ($last_id,$partner) ";
            mysqli_query($link, $query2);
            // }
        }
    }

    mysqli_close($link);

else :

endif;
header("location:hospital_list.php");
