<?php
include "functions.php";
if (!loggedin()) {
    header("Location: index.php");
    exit();
}
// print_r($_COOKIE['selectedItem']);

$id = $_COOKIE['login_adminid'];
$login_adminrole = $_COOKIE['login_adminrole'];
if ($_COOKIE['selectedItem'] == '') {
    $active = 1;
} else {
    $active = $_COOKIE['selectedItem'];
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <link rel="shortcut icon" href="assets/images/favicon.png">

    <title>Admin</title>

    <!--Morris Chart CSS -->
    <link rel="stylesheet" href="assets/plugins/morris/morris.css">

    <!-- App css -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/core.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/components.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/icons.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/pages.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/menu.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/custom.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
    <link href="assets/plugins/select2/dist/css/select2-bootstrap.css" rel="stylesheet" type="text/css">
    <link href="assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.css">



    <script src="assets/js/modernizr.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <style>
        #sidebar-menu>ul>li>a {
            font-family: 'Karla' !important;
            color: #3b3e47 !important;
        }

        #sidebar-menu>ul>li>a.active {
            border-left: 3px solid #5b69bc;
            color: #5b69bc !important;
        }

        #sidebar-menu>ul>li>a.hover {
            border-left: 3px solid #5b69bc;
            color: #5b69bc !important;
        }

        .font-15 {
            font-size: 15px;
        }
    </style>
</head>


<body class="fixed-left">

    <!-- Begin page -->
    <div id="wrapper">


        <div class="left side-menu">
            <div class="sidebar-inner">

                <!-- User -->
                <div class="user-box">
                    <div class="user-img">
                        <img src="assets/images/logo.png" alt="logo" title="Noora" class="img-responsive">
                    </div>
                </div>

                <div id="sidebar-menu">
                    <ul class="nav">

                        <li class="nav-item text-uppercase font-15" value="1" onclick="setCookie(this.value)">
                            <a href="profile.php?id=<?php echo $id ?>" <?php if ($active == 1) echo 'class="active"'; ?> class="waves-effect "><i class="fa fa-user"></i> <span> My Profile </span> </a>
                        </li>

                        <li class="nav-item text-uppercase font-15" value="2" onclick="setCookie(this.value)">
                            <a href="admin_manager_list.php" <?php if ($active == 2) echo 'class="active"'; ?> class="waves-effect">
                                <i class="fa fa-gear"></i>
                                <span> Admins & Managers </span> </a>
                        </li>
                        <li class="nav-item text-uppercase font-15" value="3" onclick="setCookie(this.value)">
                            <a href="nurse_list.php" <?php if ($active == 3) echo 'class="active"'; ?> class="waves-effect">
                                <i class="fa fa-user-md"></i> <span> Nurse List </span> </a>
                        </li>
                        <li class="nav-item text-uppercase font-15" value="4" onclick="setCookie(this.value)">
                            <a href="content_list.php" <?php if ($active == 4) echo 'class="active"'; ?> class="waves-effect">
                                <i class="fa fa-book"></i> <span> Contents </span> </a>
                        </li>
                        <li class="nav-item text-uppercase font-15" value="8" onclick="setCookie(this.value)">
                            <a href="schedule_content_list.php" <?php if ($active == 8) echo 'class="active"'; ?> class="waves-effect">
                                <i class="fa fa-hourglass-half"></i> <span>Schedule Contents </span> </a>
                        </li>

                        <li class="nav-item text-uppercase font-15" value="9" onclick="setCookie(this.value)">
                            <a href="hospital_list.php" <?php if ($active == 9) echo 'class="active"'; ?> class="waves-effect">
                                <i class="fa fa-hospital-o"></i><span>Hospital </span> </a>
                        </li>

                        <li class="nav-item text-uppercase font-15" value="10" onclick="setCookie(this.value)">
                            <a href="partner_list.php" <?php if ($active == 10) echo 'class="active"'; ?> class="waves-effect">
                                <i class="fa fa-briefcase"></i><span>Partner </span> </a>
                        </li>

                        <li class="nav-item text-uppercase font-15" value="5" onclick="setCookie(this.value)">
                            <a href="group_list.php" <?php if ($active == 5) echo 'class="active"'; ?> class="waves-effect">
                                <i class="fa fa-users"></i> <span> Groups </span> </a>
                        </li>
                        <li class="nav-item text-uppercase font-15" value="6" onclick="setCookie(this.value)">
                            <a href="attendence.php" <?php if ($active == 6) echo 'class="active"'; ?> class="waves-effect">
                                <i class="fa fa-hand-paper-o"></i> <span> CCP Attendance </span> </a>
                        </li>

                        <!-- new -->
                        <li class="nav-item text-uppercase font-15" value="11" onclick="setCookie(this.value)">
                            <a href="training_languages_listing.php" <?php if ($active == 11) echo 'class="active"'; ?> class="waves-effect">
                                <i class="fa fa-language"></i> <span> Training Languages </span> </a>
                        </li>
                        <li class="nav-item text-uppercase font-15" value="12" onclick="setCookie(this.value)">
                            <a href="training_courses_listing.php" <?php if ($active == 12) echo 'class="active"'; ?> class="waves-effect">
                                <i class="fa fa-list-alt"></i> <span> Training Courses </span> </a>
                        </li>

                        <!--li class="nav-item text-uppercase font-15" value="13" onclick="setCookie(this.value)">
                            <a href="tool_section_listing.php" <?php if ($active == 13) echo 'class="active"'; ?> class="waves-effect">
                                <i class="fa fa-toggle-on"></i> <span> CCP Tool </span> </a>
                        </li>
                        <li class="nav-item text-uppercase font-15" value="14" onclick="setCookie(this.value)">
                            <a href="tool_material_section_listing.php" <?php if ($active == 14) echo 'class="active"'; ?> class="waves-effect">
                                <i class="fa fa-link"></i> <span> CCP Tool Material </span> </a>
                        </li-->

                        <li class="nav-item text-uppercase font-15" value="7" onclick="setCookie(this.value)">
                            <a href="logout.php" <?php if ($active == 7) echo 'class="active"'; ?> class="waves-effect">
                                <i class="fa fa-sign-out"></i> <span> Logout </span> </a>
                        </li>

   

                    </ul>
                    <div class="clearfix"></div>
                </div>
                <!-- Sidebar -->
                <div class="clearfix"></div>

            </div>

        </div>
        <script type="text/javascript">
            function setCookie(value) {
                document.cookie = "selectedItem=" + value;
            }
        </script>