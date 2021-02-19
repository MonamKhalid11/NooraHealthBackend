<?php
include "header.php";
$entry_id = 0;
if (isset($_GET['id'])) {
    $entry_id = $_GET['id'];
}


$Course = '';
$Training_URL = '';
$Language;
$query1 = "SELECT " . $table_noora_online_training_courses . ".*," . $table_noora_online_training_language . ".Name as Language FROM " . $table_noora_online_training_courses . " LEFT JOIN " . $table_noora_online_training_language . " ON "
    . $table_noora_online_training_language . ".ID=" . $table_noora_online_training_courses . ".Language_ID where " . $table_noora_online_training_courses . ".ID='$entry_id' limit 1";
$res1 = mysqli_query($link, $query1);
// if (mysqli_num_rows($res1) > 0) {
while ($row1 = mysqli_fetch_array($res1)) {
    $Language = $row1['Language'];
    $Course = $row1['Name'];
    $Training_URL = $row1['Training_URL'];
}


?>
<style>
    .font-karla {
        font-family: 'Karla' !important;
        color: #3b3e47 !important;
    }

    .clr-white {
        color: #ffffff !important;
    }

    .edit-clr {
        color: #4BB75E;
    }

    .btn-styles {
        border-radius: 4px;
        border: 1px solid #CAD1D6;
        color: #858C91;
        padding: 8px;
    }

    .modal-open .modal {
        display: flex !important;
        align-items: center;
        justify-content: center;
    }

    .img-max-height {
        max-height: 40px;
    }

    textarea {
        resize: none;
    }
</style>
<link href="assets/plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
<link href="assets/plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="assets/plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="assets/plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="assets/plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />

<div class="topbar">
    <div class="topbar-left">
    </div>
    <div class="navbar navbar-default" role="navigation">
        <div class="container">

            <ul class="nav navbar-nav navbar-left">
                <li>
                    <button class="button-menu-mobile open-left">
                        <i class="zmdi zmdi-menu"></i>
                    </button>
                </li>
                <li>
                    <h4 class="page-title font-karla">Online Training Course</h4>
                </li>

            </ul>
        </div>
    </div>
</div>

<div class="content-page">
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="">
                        <div class="row">
                            <div class="col-lg-8 card-box">
                                <div class="row">
                                    <div class="col-lg-9 col-md-9 col-xs-9 font-karla">

                                    </div>
                                    <div class="col-lg-3 col-md-3 col-xs-3">
                                        <button class="btn btn-trans waves-effect waves-light
                                    m-b-5 font-karla btn-styles" title="Delete" style="background:#ffffff" onclick="DeleteContent('<?= $entry_id; ?>')">
                                            <i class="fa fa-trash text-danger"></i>
                                            <span>Delete </span>
                                        </button>
                                        <a href="training_courses.php?id=<?php echo $entry_id ?>" class="btn btn-trans waves-effect waves-light
                                    m-b-5 font-karla btn-styles m-l-5" title="Edit">
                                            <i class="fa fa-pencil m-r-5 edit-clr"></i>
                                            <span>Edit&nbsp;&nbsp;</span>
                                        </a>
                                    </div>
                                </div>
                                <br>
                                <div class="">
                                    <input type="hidden" name="<?= $token_id; ?>" value="<?= $token_value; ?>" />
                                    <input type="hidden" id="entry_id" name="entry_id" value="<?= $entry_id; ?>">
                                    <div class="row p-l-r-10">
                                        <div class="col-lg-12">
                                            <div class="form-group font-karla">
                                                <label for="Course_Name">Course Name*</label>
                                                <input type="text" name="Course_Name" parsley-trigger="change" required disabled placeholder="Course Name" class="form-control" id="Course_Name" value="<?= $Course; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row p-l-r-10">
                                        <div class="col-lg-12">
                                            <div class="form-group font-karla">
                                                <label for="Training_URL">Training URL*</label>
                                                <input type="text" class="form-control" placeholder="Training URL" name="Training_URL" parsley-trigger="change" disabled required value="<?= $Training_URL ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row p-l-r-10">
                                        <div class="col-lg-12">
                                            <div class="form-group font-karla">
                                                <label for="language">Language*</label>
                                                <input name="language" parsley-trigger="change" required disabled placeholder="Language" class="form-control" id="language" value="<?= $Language; ?>">
                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <?php echo date('Y'); ?> © Noora.
    </footer>

    <?php
    include "footer.php";
    ?>
    <!-- Datatables-->
    <script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="assets/plugins/datatables/dataTables.bootstrap.js"></script>

    <script src="assets/pages/datatables.ajax.js"></script>
    <script type="text/javascript">
        function DeleteContent(contentId) {
            let id = contentId;
            bootbox.confirm({
                size: "small",
                message: "Are you sure you want to delete this?",
                callback: function(result) {
                    if (result == true) {
                        $.ajax({
                            type: "POST",
                            url: "./delete.php",
                            data: {
                                id: id,
                                status: "training_courses"
                            },
                            success: function(result) {
                                if (result == 1) {
                                    window.location.href = "training_courses_listing.php";
                                } else {
                                    bootbox.alert({
                                        size: "small",
                                        message: "Error"
                                    })
                                }
                            }
                        });
                    }
                }
            })
        }
    </script>