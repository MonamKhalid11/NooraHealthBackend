<?php
include "header.php";
$Nurse_Hospital = array();
$query = "SELECT ID,Name FROM " . $table_noora_hospital . " where status!=3";
$res = mysqli_query($link, $query);
if (mysqli_num_rows($res) > 0) {
    while ($row = mysqli_fetch_array($res)) {
        $Nurse_Hospital[] = $row;
    }
}
$hospitalId = $_COOKIE['hospital'];


$query1 = "SELECT ID,Name FROM " . $table_noora_state . " where Status = 1";
$res1 = mysqli_query($link, $query1);
if (mysqli_num_rows($res1) > 0) {
    while ($row1 = mysqli_fetch_array($res1)) {
        $State[] = $row1;
    }
}
$stateId = $_COOKIE['state'];



if ($stateId == 0) {
    $query2 = "SELECT ID,Name FROM " . $table_noora_district . " where Status = 1";
} else {
    $query2 = "SELECT ID,Name FROM " . $table_noora_district . " where Status = 1 and State_ID = " . $stateId . "";
}
$res2 = mysqli_query($link, $query2);
if (mysqli_num_rows($res2) > 0) {
    while ($row2 = mysqli_fetch_array($res2)) {
        $District[] = $row2;
    }
}
$districtId = $_COOKIE['district'];

var_dump($stateId);

?>
<style>
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

    .pb-20 {
        padding-bottom: 20px;
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
                    <h4 class="page-title font-karla">CCP Attendance List</h4>
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
                    <div class="card-box table-responsive">

                        <div class="row pb-20">
                            <div class="col-md-3">
                                <select class="form-control select2 " name="type" id="type">
                                    <option value="0" selected>Select Hospital</option>
                                    <?php if (is_array($Nurse_Hospital) && !empty($Nurse_Hospital)) {

                                        foreach ($Nurse_Hospital as $key => $value) {
                                    ?>
                                            <option <?php if ($hospitalId == $value["ID"]) echo "selected='selected'"; ?> value="<?= $value["ID"] ?>"><?= $value["Name"] ?></option>
                                    <?php
                                        }
                                    } ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-control select2" name="state" id="state">
                                    <option value="0" selected>Select State</option>
                                    <?php if (is_array($State) && !empty($State)) {
                                        foreach ($State as $key => $value) {
                                    ?>
                                            <option <?php
                                                    if ($stateId == $value["ID"]) echo "selected='selected'"; ?> value="<?= $value["ID"] ?>"><?= $value["Name"] ?></option>
                                    <?php
                                        }
                                    } ?>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <select class="form-control select2" name="district" id="district">
                                    <option value="0" selected>Select District</option>
                                    <?php if (is_array($District) && !empty($District)) {
                                        foreach ($District as $key => $value) {
                                    ?>
                                            <option <?php if ($districtId == $value["ID"]) echo "selected='selected'"; ?> value="<?= $value["ID"] ?>"><?= $value["Name"] ?></option>
                                    <?php
                                        }
                                    } ?>
                                </select>
                            </div>
                            <div class="pull-right m-r-15 m-b-10">
                                <a href="attendenceExcel.php">
                                    <img src="assets/images/download.svg" title="Export Data" class="img-responsive " alt="download">
                                </a>
                            </div>
                        </div>

                        <table id="datatable-ajax" data-url="attendence_data.php?hospital=<?php echo $hospitalId ?>&state=<?php echo $stateId ?>&district=<?php echo $districtId ?>" class="font-karla table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%" data-search_placeholder="Name/Class Type/Hospital Name">
                            <thead>
                                <tr>
                                    <th>Nurse Name</th>
                                    <th>Hospital Name</th>
                                    <th>State</th>
                                    <th>District</th>
                                    <th>Day & Date</th>
                                    <th>Time</th>
                                    <th>Class Type</th>
                                    <th>Number Of People</th>
                                    <th>Ward</th>
                                    <th>Notes</th>
                                    <th>Sesssion Conducted</th>
                                    <th>Class image</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer class="footer">
        <?php echo date('Y') ?> © Noora.
    </footer>

    <?php
    include "footer.php";
    ?>
    <script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="assets/plugins/datatables/dataTables.bootstrap.js"></script>

    <script src="assets/pages/datatables.ajax.js"></script>
    <script>
        $('#type').change(function() {
            var id = $(this).val()
            // alert(id);
            document.cookie = "hospital = " + id
            location.reload()
        })

        $('#state').change(function() {
            var id = $(this).val()
            // alert(id);
            document.cookie = "state = " + id
            location.reload()
        })

        $('#district').change(function() {
            var id = $(this).val()
            // alert(id);
            document.cookie = "district = " + id
            location.reload()
        })


        // $(document).ready(function() {
        //     // alert("hi");
        //     document.cookie = "hospital =0 "
        //     document.cookie = "state =0 "
        //     document.cookie = "district =0 "
        // })

        $(".select2").select2();
    </script>