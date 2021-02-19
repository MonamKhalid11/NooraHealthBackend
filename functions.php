<?php
//ob_start();
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_WARNING);
session_start(); //start session

include 'config.php'; //include the config.php file

// connect to data base
$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
if (!$link) {

    die('Failed to connect to the Server: ' . mysqli_error());
}

//Select database
$db = mysqli_select_db($link, DB_DATABASE);
if (!$db) {
    die("Unable to choose the Database");
}

$Admin_User_ID = $_COOKIE['login_adminid'];
$Admin_Role_ID = $_COOKIE['login_adminrole'];
//login chech function
function loggedin()
{
    if (isset($_SESSION['login_adminname']) || isset($_COOKIE['login_adminname'])) {
        $loggedin = TRUE;
        return $loggedin;
    }
}

function escape_input_str($inp)
{
    if (!empty($inp) && is_string($inp)) {
        return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\0', '\n', '\r', "\'", '\"', '\Z'), $inp);
    }
    return $inp;
}

function generateRandomString($length)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

date_default_timezone_set('Asia/Kolkata');
$current_time = date('Y-m-d H:i:s');
$admin_email = '';
$server_url = "http://noora.com/";
//$server_url="http://192.168.0.11/noora/";
$base_url = "Admin/";
// $image_path='App/images/';
// $ticket_image_path=$image_path.'ticket/';
$default_image = 'default.png';
$Super_Admin_Role_ID = '1';
// $Closed_Status = 1; // ticket close status value
// $Warranty_Status = 2; // ticket Warranty status value

$default_upload_image_path = '../../images/default.png';
$file_upload_path = './uploads/';

// $user_role_array=array('Admin','Scheduler','Reviewer');
// $user_status_array=array('Not Registered','Registered','Block');

mysqli_set_charset($link, "utf8");

$table_role = "noora_user_role";
$table_admin = "noora_admin_user";
$table_admin_login_log = "noora_admin_user_login_log";
$table_nurse = "noora_nurse";
$table_group = "noora_group";
$table_group_user = "noora_group_user";
$table_content = "noora_content";
$table_content_group = "noora_content_group";
$table_content_attachment = "noora_content_attachment";
$table_likes = "noora_content_like";
$table_views = "noora_content_views";
$table_comments = "noora_content_comment";
$table_attendance = "noora_ccp_attendance";
$table_ccp_class_type = "noora_ccp_class_type";
$table_user_role = "noora_admin_user_role";
$table_noora_city = "noora_city";
$table_noora_district = "noora_district";
$table_noora_state = "noora_state";
$table_noora_hospital = "noora_hospital";
$table_nurse_history = "noora_nurse_history";
$table_noora_country = "noora_country";
$table_noora_hospital_medical_condition = "noora_hospital_medical_condition";
$table_noora_booster_training = "noora_booster_training";
$table_noora_history_type = "noora_history_type";
$table_user_otp = "noora_user_otp";
$table_noora_user_device = "noora_user_device";

$table_schedule_content = "noora_schedule_content";
$table_schedule_content_attachment = "noora_schedule_content_attachment";
$table_schedule_content_group = "noora_schedule_content_group";

$table_noora_hospital_partner = "noora_hospital_partner";
$table_noora_hospital_partner_mapping = "noora_hospital_partner_mapping";

$table_noora_online_training_language  = "noora_online_training_language";
$table_noora_online_training_courses  = "noora_online_training_courses";

$table_noora_ccp_tool_type  = "noora_ccp_tool_type";
$table_noora_ccp_tool_material  = "noora_ccp_tool_material";

$code = "NH-";

//-------------------------------------ADDED------------------------------------------
$SERVER_TYPE_LIVE         = "live";
$SERVER_TYPE_TEST         = "test";
$THIS_SERVER_TYPE         = $SERVER_TYPE_TEST;

$PATH_SERVER_URL = "http://34.94.112.24";
$IMAGE_FOLDER         = "/noora/uploads"; // live	
if ($THIS_SERVER_TYPE == $SERVER_TYPE_TEST) {
    $IMAGE_FOLDER         = "/demo_noora/uploads";     //test
}
$PATH_IMAGE_FOLDER = $PATH_SERVER_URL . $IMAGE_FOLDER;

$CLASS_FOLDER         = "/ClassImages/";
$CONTENT_FOLDER     = "/Content_Attachments/";
$NURSE_FOLDER         = "/NurseImage/";
$ADMIN_FOLDER         = "/ProfileImages/";

$CLASS_IMAGE_PATH         = $PATH_IMAGE_FOLDER . $CLASS_FOLDER;
$CONTENT_IMAGE_PATH     = $PATH_IMAGE_FOLDER . $CONTENT_FOLDER;
$NURSE_IMAGE_PATH         = $PATH_IMAGE_FOLDER . $NURSE_FOLDER;
$ADMIN_IMAGE_PATH         = $PATH_IMAGE_FOLDER . $ADMIN_FOLDER;

$NOTIFICATION_NONE = 0;
$NOTIFICATION_UPDATE = 1;
$NOTIFICATION_ADHOC = 2;
$NOTIFICATION_CONTENT = 3;
$NOTIFICATION_LOGOUT = 4;
//-------------------------------------ADDED DONE------------------------------------------

$badge = array(
    "0" => "Level 0",
    "1" => "Level 1",
    "2" => "Level 2",
    "3" => "Level 3",
    "4" => "Level 4",
    "5" => "Level 5"
);
