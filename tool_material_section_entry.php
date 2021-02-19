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
    $toolMaterialSection = $_POST['toolMaterialSection'];
    $Tool_Type = $_POST['Tool_Type'];
    $isActive = $_POST['isActive'] != NULL && $_POST['isActive'] == "on" ? 1 : 0;
    $MaterialURL = $_POST['MaterialURL'];
    $status = 1;

    if ($id != "") {
        $query1 = "UPDATE " . $table_noora_ccp_tool_material . " set Name='$toolMaterialSection',Type_ID = '$Tool_Type',Type='$isActive',Material_URL='$MaterialURL', Edit_Time='$dateNow' where ID='$id' ";
        mysqli_query($link, $query1);
    } else {
        $query1 = "INSERT INTO " . $table_noora_ccp_tool_material . " (Name,Type_ID,Material_URL,Type,Status,Edit_Time,Entry_Time) VALUES ('$toolMaterialSection','$Tool_Type','$MaterialURL','$isActive',$status,'$dateNow','$dateNow') ";
        $res1 = mysqli_query($link, $query1);
        
    }



    mysqli_close($link);

else :


endif;
header("location:tool_material_section_listing.php");
