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
    $partner = $_POST['Partner'];
    $status = 1;

    $condition = "";
    $query = "SELECT * from " . $table_noora_hospital_partner . " where Status = 1 AND Name='$partner' LIMIT 1";
    $res = mysqli_query($link, $query);
    if (mysqli_fetch_array($res) == null) {
        $condition = "true";
    }

    if ($condition == "true") {
        if ($id != "") {
            $query1 = "UPDATE " . $table_noora_hospital_partner . " set Name='$partner',Edit_Time='$dateNow' where ID='$id' ";
            $res1 = mysqli_query($link, $query1);
            if ($res1) {
                echo json_encode("success");
            }
        } else {
            $query1 = "INSERT INTO " . $table_noora_hospital_partner . " (Name,Status,Edit_Time,Entry_Time) VALUES ('$partner','$status','$dateNow','$dateNow') ";
            // mysqli_query($link, $query1);
            $res1 = mysqli_query($link, $query1);
            if ($res1) {
                echo json_encode("success");
            }
        }
    } else {
        echo json_encode("exists");
    }



    mysqli_close($link);

else :

endif;
// header("location:partner_list.php");
