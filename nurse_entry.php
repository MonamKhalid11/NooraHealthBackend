<?php
include 'functions.php';
include 'csrf.class.php';
date_default_timezone_set("Asia/Kolkata");
$dateNow = date("Y-m-d H:i:s");
$condition_values = implode(',', $_POST['Hospital_Condition']);
$admin_id = 0;
$Login_User_ID = $_COOKIE['login_adminid'];
$csrf = new csrf();
$token_id = $csrf->get_token_id();
$token_value = $csrf->get_token($token_id);
$file_name = "";

if ($csrf->check_valid('post')):
    if (isset($_FILES["Profile"])) {
        $target_folder = $file_upload_path . "/NurseImage/";

        if (!file_exists($target_folder)) {
            mkdir($target_folder, 0777, true);
        }

        $fileTmpPath = $_FILES['Profile']['tmp_name'];
        $fileName = $_FILES['Profile']['name'];
        $fileName = str_replace(" ", "_", $fileName);
        $fileSize = $_FILES['Profile']['size'];
        $fileType = $_FILES['Profile']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        $dest_path = $target_folder . $fileName;

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $file_name = $fileName;
        }
    }

    $id = (int) $_POST['entry_id'];
    $User_ID = (int) $_POST['entry_id'];
    $First_Name = $_POST['First_Name'];
    $Last_Name = $_POST['Last_Name'];
    $City = $_POST['city'];
    $Mobile_Number = $_POST['Mobile_Number'];
    $Noora_Badge = $_POST["Noora_Badge"];
    $Graduating_Year = $_POST["Graduating_Year"];

    $Date_Join = "";
    $join_date = $_POST["Date_Join"];
    if ($join_date != "") {
        $join_date = str_replace('/', '-', $join_date);
        $Date_Join = date("Y-m-d", strtotime($join_date));
    }

    $birth_Date = str_replace('/', '-', $_POST["DOB"]);
    $DOB = date("Y-m-d", strtotime($birth_Date));

    $Hospital_ID = 0;
    $Hospital_Name = mysqli_real_escape_string($link, $_POST["Hospital_Name"]);
    if ($Hospital_Name != "") {
        $query = "SELECT ID FROM " . $table_noora_hospital . " where Name='" . $Hospital_Name . "' limit 1";
        $res = mysqli_query($link, $query);
        //if($res)
        //{
        if (mysqli_num_rows($res) > 0) {
            while ($row = mysqli_fetch_array($res)) {
                $Hospital_ID = $row['ID'];
            }
        } else {
            $query = "INSERT INTO " . $table_noora_hospital . " ( `Name`) VALUES ('$Hospital_Name')";
            mysqli_query($link, $query);
            $query1 = "SELECT ID FROM " . $table_noora_hospital . " where Name='$Hospital_Name' ORDER BY ID DESC";
            //echo $query1; exit();
            $res = mysqli_query($link, $query1);
            if (mysqli_num_rows($res) > 0) {
                while ($row = mysqli_fetch_array($res)) {
                    $Hospital_ID = $row['ID'];
                }
            }
        }
        //}
    }
    $Hospital_Condition = $condition_values;
    $Designation = $_POST["Designation"];

    //$date = str_replace('/', '-', $_POST["CCP_Date"]);
    //$CCP_Date = date("Y-m-d", strtotime($date));

    $CCP_Date = "";
    $ccpDate = $_POST["CCP_Date"];
    if ($ccpDate != "") {
        $ccpDate = str_replace('/', '-', $ccpDate);
        $CCP_Date = date("Y-m-d", strtotime($ccpDate));
    }

    $Mentor_Name = $_POST["Mentor_Name"];
    $Trainer_Name = $_POST["Trainer_Name"];
    //print("hello");
    //print($_POST["Trainer_Name"]);
    //echo $Trainer_Name;exit();
    $Booster_Training = $_POST["Booster_Training"];
    $CCP_Condition_ID = $_POST["CCP_Condition_Type"];
    $current_time = date('Y-m-d H:i:s');
    $Country_code = $_POST["country"];
    if ($id != 0) {

        if ($file_name != "") {
            $query = "UPDATE " . $table_nurse . " set First_Name='$First_Name',Last_Name='$Last_Name',District_ID='$City',Country_Code='$Country_code',
							Badge_Level='$Noora_Badge',Graduating_Year='$Graduating_Year',Hospital_Joining_Date='$Date_Join',
							Hospital_ID='$Hospital_ID',Hospital_Condition_ID='$Hospital_Condition',Designation='$Designation',
							TOT_Date='$CCP_Date',CCP_Mentor='$Mentor_Name',CCP_Condition_ID ='$CCP_Condition_ID',Booster_Training='$Booster_Training',Login_User_ID='$Login_User_ID',
							profile_image='$file_name',Edit_Time='$dateNow',DOB='$DOB',Trainer = '$Trainer_Name' where ID='$id' ";
        } else {
            $query = "UPDATE " . $table_nurse . " set First_Name='$First_Name',Last_Name='$Last_Name',District_ID='$City',Country_Code='$Country_code',
							Badge_Level='$Noora_Badge',Graduating_Year='$Graduating_Year',Hospital_Joining_Date='$Date_Join',
							Hospital_ID='$Hospital_ID',Hospital_Condition_ID='$Hospital_Condition',Designation='$Designation',
							TOT_Date='$CCP_Date',CCP_Mentor='$Mentor_Name',CCP_Condition_ID ='$CCP_Condition_ID',Booster_Training='$Booster_Training',Login_User_ID='$Login_User_ID',DOB='$DOB',
							Edit_Time='$dateNow',Trainer = '$Trainer_Name' where ID='$id' ";
        }

        echo $query;

    } else {
        if ($file_name != "") {
            $query = "INSERT INTO " . $table_nurse . " (First_Name,Last_Name,District_ID,Country_Code,Mobile_Number,Badge_Level,Graduating_Year,Hospital_Joining_Date,Hospital_ID,
					Hospital_Condition_ID,Designation,TOT_Date,CCP_Mentor,CCP_Condition_ID,Booster_Training,Login_User_ID,profile_image,Entry_Time,Edit_Time,DOB,Trainer)
					VALUES ('$First_Name','$Last_Name','$City','$Country_code','$Mobile_Number','$Noora_Badge',
					'$Graduating_Year','$Date_Join','$Hospital_ID','$Hospital_Condition','$Designation',
					'$CCP_Date','$Mentor_Name','$CCP_Condition_ID','$Booster_Training','$Login_User_ID','$file_name','$dateNow','$dateNow','$DOB','$Trainer_Name') ";
        } else {
            $query = "INSERT INTO " . $table_nurse . " (First_Name,Last_Name,District_ID,Country_Code,Mobile_Number,Badge_Level,Graduating_Year,Hospital_Joining_Date,Hospital_ID,
					Hospital_Condition_ID,Designation,TOT_Date,CCP_Mentor,CCP_Condition_ID,Booster_Training,Login_User_ID,Entry_Time,Edit_Time,DOB,Trainer)
					VALUES ('$First_Name','$Last_Name','$City','$Country_code','$Mobile_Number','$Noora_Badge',
					'$Graduating_Year','$Date_Join','$Hospital_ID','$Hospital_Condition','$Designation',
					'$CCP_Date','$Mentor_Name','$CCP_Condition_ID','$Booster_Training','$Login_User_ID','$dateNow','$dateNow','$DOB','$Trainer_Name') ";
        }

    }
    if (mysqli_query($link, $query)) {
        if ($id != 0) {
            $User_ID = $id;
        } else {
            $User_ID = mysqli_insert_id($link);
        }

    }

    mysqli_close($link);
else:

endif;

header("location:nurse_details.php?id=" . $User_ID);
