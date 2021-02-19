<?php
include "header.php";
$entry_id = 0;
if (isset($_GET['id'])) {
    $entry_id = $_GET['id'];
}
$Course = '';
$Training_URL = '';
$Language;
$query1 = "SELECT " . $table_noora_online_training_courses . ".*," . $table_noora_online_training_language . ".ID as Language FROM " . $table_noora_online_training_courses . " LEFT JOIN " . $table_noora_online_training_language . " ON "
    . $table_noora_online_training_language . ".ID=" . $table_noora_online_training_courses . ".Language_ID where " . $table_noora_online_training_courses . ".ID='$entry_id' limit 1";
$res1 = mysqli_query($link, $query1);
// if (mysqli_num_rows($res1) > 0) {
while ($row1 = mysqli_fetch_array($res1)) {
    $Language = $row1['Language'];
    $Course = $row1['Name'];
    $Training_URL = $row1['Training_URL'];
}

// }

require_once("csrf.class.php");
$csrf = new csrf();
$token_id = $csrf->get_token_id();
$token_value = $csrf->get_token($token_id);

$admin_id = $_COOKIE['login_adminid'];
$Profile_img = '';
$query1 = "SELECT * FROM " . $table_admin . " where ID='$admin_id' limit 1";
// echo $query1;exit();
$res1 = mysqli_query($link, $query1);
// if (mysqli_num_rows($res1) > 0) {
while ($row1 = mysqli_fetch_array($res1)) {
    $Profile_img = $row1['profile_image'];
}
// }

$query2 = "SELECT * FROM " . $table_noora_online_training_language . " where Status='1' ";
$res2 = mysqli_query($link, $query2);


?>
<style>
    .font-karla {
        font-family: 'Karla' !important;
        color: #3b3e47 !important;
    }

    .clr-white {
        color: #ffffff !important;
    }

    .figcaption-display {
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
    }

    .modal-open .modal {
        display: flex !important;
        align-items: center;
        justify-content: center;
    }

    .video-container {
        height: 30vh;
        overflow: hidden;
        position: relative;
    }


    .video-container iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

    .video-container iframe {
        pointer-events: none;
    }

    .video-container iframe {
        position: absolute;
        top: -60px;
        left: 0;
        width: 100%;
        height: calc(100% + 120px);
    }

    .video-foreground {
        pointer-events: none;
    }

    #show-video {
        display: none;
    }

    #show-image {
        display: none;
    }

    #img-uploaded {
        height: 25vh;
        max-width: 25vw;
    }

    .font-assistant-bold {
        font-family: Assistant;
        font-weight: 700;
        color: #181b1e;
    }

    .font-assistant-content-regular {
        font-family: Assistant;
        font-weight: 400;
        color: #525752;
        word-break: break-all;
    }

    .font-assistant-id-color {
        color: #525752;
        font-family: Assistant;
    }

    .content-id {
        background: #E7EFE7;
        padding: 5px 12px;
        color: #10c469;
        border-radius: 3px;
        font-family: Assistant;
    }
</style>
<link href="assets/plugins/select2/dist/css/select2.min.css" rel="stylesheet" type="text/css" />
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
                    <?php if ($entry_id == 0) { ?>
                        <h4 class="page-title font-karla">Add New Course </h4>
                    <?php } else { ?>
                        <h4 class="page-title font-karla">Edit Course </h4>
                    <?php } ?>
                </li>
            </ul>

        </div>
    </div>
</div>
<div class="content-page">
    <div class="content">

        <div class="container">


            <div class="row">
                <div class="col-lg-2 col-xs-0"></div>
                <div class="col-lg-8 screen">
                    <div class="card-box">


                        <form id="db_entry_form" action="training_courses_entry.php" enctype="multipart/form-data" class="form-horizontal form-align" method="post" data-parsley-validate novalidate>
                            <input type="hidden" name="<?= $token_id; ?>" value="<?= $token_value; ?>" />
                            <input type="hidden" id="entry_id" name="entry_id" value="<?= $entry_id; ?>">

                            <div class="row">

                                <div class="col-lg-11">
                                    <div class="form-group font-karla">
                                        <label for="course">Course Name<span class="text-red">*</span></label>
                                        <input type="text" name="course" parsley-trigger="change" required placeholder="Course Name" class="form-control" id="course" value="<?= $Course; ?>">
                                        <span id="errHospitalName"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="row">

                                <div class="col-lg-11">
                                    <div class="form-group font-karla">
                                        <label for="training_url">Training URL<span class="text-red">*</span></label>
                                        <input type="text" name="training_url" parsley-trigger="change" required placeholder="Training URL" class="form-control" id="training_url" value="<?= $Training_URL; ?>">
                                        <span id="errHospitalName"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-11">
                                    <div class="form-group font-karla">
                                        <label for="language">Language<span class="text-red">*</span></label>
                                        <select class="form-control select2-language" name="language" id="language" required="required">
                                            <option disabled>Select Language</option>
                                            <?php
                                            while ($row2 = mysqli_fetch_array($res2)) { ?>

                                                <option value="<?php echo $row2['ID']; ?>"><?php echo $row2['Name'];  ?></option>
                                            <?php }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>



                            <div class="form-group text-center font-karla m-b-0 p-t-10 m-t-15">

                                <?php if ($entry_id == 0) { ?> <button class=" btn btn-purple w-md waves-effect waves-light" style="padding: 6px 20px;" data-target="#myModal"> Save
                                    </button><?php } else { ?><button class="btn btn-purple w-md waves-effect waves-light" data-target="#myModal"> Update
                                    </button><?php } ?>
                                <a href="training_courses_listing.php" class="btn btn-default waves-effect waves-light m-l-15" style="padding:6px 32px">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <footer class="footer text-right">
        <?php echo date('Y'); ?> © Noora.
    </footer>
</div>

<?php
include "footer.php";
?>

<script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="assets/plugins/datatables/dataTables.bootstrap.js"></script>
<script src="assets/pages/datatables.ajax.js"></script>
<script src="assets/plugins/select2/dist/js/select2.min.js"></script>
<script type="text/javascript">
    let popupshow = false;
    $('#myModal').on('hide.bs.modal', function(e) {
        popupshow = false;
    });
    // $(document).ready(function() {
    //     $("#text_content").prop("required", true);
    // })

    $("#db_entry_form").submit(function(event) {
        console.log("update");
        var db_entry_form = $("#db_entry_form");
        console.log("parsley", db_entry_form.parsley().isValid())
        if (db_entry_form.parsley().isValid() == true) {
            // $("#myModal").modal("show");
            // if (popupshow == false) {
            //     event.preventDefault();
            // }
            // popupshow = true;
            // $('#db_entry_form').submit();

        } else {
            // $("#myModal").modal("hide");
            bootbox.alert("Please fill all the details correctly");
            event.preventDefault();
            return false;
        }

        // document.getElementById("hospital-text").innerHTML = document.getElementById("Hospital").value;
        //    var errtitle = document.getElementById("State"); 

    });

    function discardData() {
        popupshow = false;
    }
    var Language = "<?php echo $Language; ?>";


    $(".select2-language").select2().select2('val', Language);
</script>
<script type="text/javascript" src="assets/plugins/parsleyjs/dist/parsley.min.js"></script>