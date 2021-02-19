<?php
include "header.php";
require_once("csrf.class.php");
$csrf = new csrf();
$token_id = $csrf->get_token_id();
$token_value = $csrf->get_token($token_id);

?>
<script>
    function Status_lang(status, id) {
        let check = status == 1 ? "enable" : "disable";
        bootbox.confirm({
            size: "small",
            message: "Are you sure you want to " + check + " this?",
            callback: function(result) {
                if (result == true) {
                    var formData = new FormData();
                    // formData.append('<?= $token_id; ?>', '<?= $token_value; ?>');
                    formData.append('id', id);
                    formData.append('status', status);
                    console.log(formData)
                    $.ajax({
                        method: "POST",
                        url: "training_languages_status_update.php",
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: "json",
                        success: function(response) {
                            // alert(response);
                            if (response == 1) {


                                window.location.href = "training_languages_listing.php";

                            }
                        }
                    });
                }
            }
        })
        // alert(id + "" + status);

    }
</script>

<link href="assets/plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
<link href="assets/plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="assets/plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="assets/plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="assets/plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
<style>
    tr td:first-child {
        display: none;
    }

    .font-karla {
        font-family: 'Karla' !important;
        color: #3b3e47 !important;
    }

    .clr-white {
        color: #ffffff !important;
    }

    table.dataTable tbody tr:hover {
        background-color: #c7cce8 !important;
        cursor: pointer;
    }
</style>
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
                    <h4 class="page-title font-karla">Online Training Languages</h4>
                </li>
            </ul>

        </div><!-- end container -->
    </div><!-- end navbar -->
</div>

<div class="content-page">
    <div class="content">
        <div class="container">

            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box table-responsive">
                        <div class="row">
                            <a href="training_languages.php" type="button" class="clr-white font-karla btn btn-purple w-md waves-effect waves-light m-b-20 pull-right">Add New Entry</a>
                        </div><!-- end col -->
                        <br>
                        <table id="datatable-ajax" data-url="training_languages_detail_grid.php" class="font-karla table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%" data-search_placeholder="Language Name">
                            <thead>
                                <tr>
                                    <th style="display:none">ID</th>
                                    <th>Language</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                    <th>Edit</th>

                                </tr>
                            </thead>
                        </table>
                    </div>
                </div><!-- end col -->
            </div>

        </div> <!-- container -->

    </div> <!-- content -->

    <footer class="footer">
        <?php echo date('Y'); ?> © Noora.
    </footer>

    <?php
    include "footer.php";
    ?>
    <script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="assets/plugins/datatables/dataTables.bootstrap.js"></script>

    <script src="assets/pages/datatables.ajax.js"></script>
    <script type="text/javascript">

    </script>