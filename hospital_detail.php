<?php
include "header.php";
$entry_id = 0;
if (isset($_GET['id'])) {
    $entry_id = $_GET['id'];
}


$Hospital = "";
$State = "";
// $Partner = array();
$Partner = "";

$query = "SELECT " . $table_noora_hospital . ".*," . $table_noora_state . ".Name as State_Name FROM " . $table_noora_hospital . " INNER JOIN " . $table_noora_state . " ON " . $table_noora_hospital . ".State_ID = " . $table_noora_state . ".ID WHERE " . $table_noora_hospital . ".Status = '1' AND " . $table_noora_hospital . ".ID=" . $entry_id;
// echo $query;
$res2 = mysqli_query($link, $query);
while ($row1 = mysqli_fetch_array($res2)) {
    $Hospital = $row1['Name'];
    $State = $row1['State_Name'];
    $ID = $row1['ID'];
}

$query3 = "SELECT " . $table_noora_hospital_partner . ".Name FROM " . $table_noora_hospital_partner_mapping . " INNER JOIN " . $table_noora_hospital_partner . " ON " . $table_noora_hospital_partner_mapping . ".Partner_ID = " . $table_noora_hospital_partner . ".ID WHERE " . $table_noora_hospital_partner_mapping . ".Hospital_ID = '$ID' ";
$res3 = mysqli_query($link, $query3);

while ($row2 = mysqli_fetch_assoc($res3)) {
    // array_push($Partner, $row2['Name']);
    $Partner = $row2['Name'];
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
                    <h4 class="page-title font-karla">Hospital Detail</h4>
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
                                        <a href="hospital.php?id=<?php echo $entry_id ?>" class="btn btn-trans waves-effect waves-light
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
                                                <label for="Hospital_Name">Hospital Name*</label>
                                                <input type="text" name="Hospital_Name" parsley-trigger="change" required disabled placeholder="Hospital Name" class="form-control" id="Hospital_Name" value="<?= $Hospital; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row p-l-r-10">
                                        <div class="col-lg-12">
                                            <div class="form-group font-karla">
                                                <label for="State">State*</label>
                                                <input type="text" class="form-control" placeholder="State" name="State" parsley-trigger="change" disabled required value="<?= $State ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row p-l-r-10">
                                        <div class="col-lg-12">
                                            <div class="form-group font-karla">
                                                <label for="pass1">Partners*</label>
                                                <!-- <textarea name="partners" parsley-trigger="change" required disabled placeholder="Partners" class="form-control" id="partners"><?php  //echo implode("&#13;&#10;", $Partner); 
                                                                                                                                                                                    ?></textarea> -->
                                                <input name="partners" parsley-trigger="change" required disabled placeholder="Partners" class="form-control" id="partners" value="<?= $Partner; ?>">
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
        function changeMember(value) {
            if (value == 1) {
                document.getElementById("showTable").style.display = "none";
            } else {
                document.getElementById("showTable").style.display = "block";
            }

        }

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
                                status: "Hospital"
                            },
                            success: function(result) {
                                if (result == 1) {
                                    window.location.href = "hospital_list.php";
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