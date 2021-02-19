<?php
include "header.php";
$query1 = "SELECT COUNT(ID) as hospitals from " . $table_noora_hospital . " where Status = 1";
$count_admin = 0;
$res1 = mysqli_query($link, $query1);
if (mysqli_num_rows($res1) > 0) {
    while ($row1 = mysqli_fetch_array($res1)) {
        $count_admin = $row1["hospitals"];
    }
}

?>

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
                    <h4 class="page-title font-karla">Hospitals</h4>
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


                            <div class="col-lg-3 col-md-6 col-xs-6">
                                <div class=" widget-user">
                                    <div>
                                        <img src="assets/images/content.svg" class="img-responsive m-r-10" alt="user">
                                        <div class="wid-u-info">
                                            <h3 class="m-t-0 m-b-0 font-600 font-karla"> <?= $count_admin ?></h3>
                                            <p class="text-muted m-b-5 font-karla"><b>Hospital Added</b></p>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- end col -->
                            <a href="hospital.php" type="button" class="clr-white font-karla btn btn-purple w-md waves-effect waves-light m-b-20 pull-right">Add New Entry</a>
                        </div><!-- end col -->
                        <br>
                        <table id="datatable-ajax" data-url="hospital_detail_grid.php" class="font-karla table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%" data-search_placeholder="State Name">
                            <thead>
                                <tr>
                                    <th style="display:none">ID</th>
                                    <th>Hospital Name</th>
                                    <th>State</th>
                                    <th>Partners</th>
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
        $(document).ready(function() {
            var table = $('#datatable-ajax').DataTable();

            $('#datatable-ajax tbody').on('click', 'tr', function() {
                var data = table.row(this).data();

                window.location.href = "hospital_detail.php?id=" + data[0];
            });

        });
    </script>