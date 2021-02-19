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
    $toolSection = $_POST['toolSection'];
    $isLink = $_POST['MaterialURL'] != "" ? 1 : 0;
    $MaterialURL = $_POST['MaterialURL'];
    $status = 1;

    if ($id != "") {
        $query1 = "UPDATE " . $table_noora_ccp_tool_type . " set Name='$toolSection',Type='$isLink',Material_URL='$MaterialURL', Edit_Time='$dateNow' where ID='$id' ";
        mysqli_query($link, $query1);
    } else {
        $query1 = "INSERT INTO " . $table_noora_ccp_tool_type . " (Name,Type,Material_URL,Status,Edit_Time,Entry_Time) VALUES ('$toolSection','$isLink','$MaterialURL',$status,'$dateNow','$dateNow') ";
        $res1 = mysqli_query($link, $query1);
    }



    mysqli_close($link);

else :


endif;
header("location:tool_section_listing.php");
