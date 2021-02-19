<?php
include "header.php";
$entry_id = 0;
if (isset($_GET['id'])) {
    $entry_id = $_GET['id'];
}

$Schedule_Date = "";
$Content_Description = "";
$Content_Title = "";
$Content_Type = 0;
$Attachment = "";
$Group_Members = 0;
$Attachments = array();
$query = "SELECT " . $table_schedule_content . ".Schedule_Time , " . $table_schedule_content . ".Title AS Title, " . $table_schedule_content . ".Description as Description," . $table_schedule_content . ".Role, " . $table_schedule_content . ".Content_Type, " . $table_schedule_content . ".Attachment AS File," . $table_schedule_content . ".Login_User_ID AS UserId  FROM " . $table_schedule_content . " WHERE " . $table_schedule_content . ".ID=" . $entry_id;
// echo $query;
$res2 = mysqli_query($link, $query);
$Content_Title_trim = "";

while ($row1 = mysqli_fetch_array($res2)) {
    $Content_Title = $row1["Title"];
    if (strlen($Content_Title) > 25) {
        $Content_Title_trim = substr($Content_Title, 0, 25);
        $Content_Title_trim = $Content_Title_trim . "...";
    } else {
        $Content_Title_trim = $Content_Title;
    }

    $dateJoin = $row1["Schedule_Time"];
    if ($dateJoin != "") {
        // $Schedule_Date = date('d M, Y H:m', strtotime($dateJoin));
        $sDate = new DateTime($dateJoin);
        $Schedule_Date = $sDate->format('d/m/Y, h:i A');
    }

    $Content_Description = $row1["Description"];
    $Group_Members = $row1["Role"];
    $Content_Type = $row1["Content_Type"];
    $UserId = $row1["UserId"];
    if ($Content_Type == 2) {
        //Got Youtube URL/ID
        $videoUrl = $row1["File"];

        if (strpos($videoUrl, "/")) {
            //ITS A URL
            $Attachment = $videoUrl;
            //1. Contains watch
            if (strpos($videoUrl, "watch")) {
                $Attachment = str_replace('watch?v=', 'embed/', $videoUrl);
            }
            //2. youtu.be
            if (strpos($videoUrl, "youtu.be")) {
                $Attachment = str_replace('youtu.be', 'youtube.com/embed/', $videoUrl);
            }
        } else {
            //ITS ID
            $Attachment = "https://www.youtube.com/embed/" . $videoUrl;
        }
    } else if ($Content_Type == 1) {
        $query = "SELECT " . $table_schedule_content . ".Title AS Title, " . $table_schedule_content . ".Description as Description, 
            " . $table_schedule_content_attachment . ".Status, " . $table_schedule_content . ".Role, " . $table_schedule_content . ".Content_Type, " . $table_schedule_content . ".Attachment AS File,
            " . $table_schedule_content_attachment . ".Attachment AS Files FROM  " . $table_schedule_content . " LEFT JOIN  
            " . $table_schedule_content_attachment . " ON  " . $table_schedule_content . ".ID= " . $table_schedule_content_attachment . "
            .Content_ID WHERE  " . $table_schedule_content . ".ID=" . $entry_id . " and " . $table_schedule_content_attachment . ".Status!=3";
        $res2 = mysqli_query($link, $query);


        while ($row1 = mysqli_fetch_array($res2)) {
            $Attachment = $row1["Files"];
        }
    } else if ($Content_Type == 4) {
    } else {
        $query = "SELECT " . $table_schedule_content . ".Title AS Title, " . $table_schedule_content . ".Description as Description, 
            " . $table_schedule_content_attachment . ".Status, " . $table_schedule_content . ".Role, " . $table_schedule_content . ".Content_Type, " . $table_schedule_content . ".Attachment AS File,
            " . $table_schedule_content_attachment . ".Attachment AS Files FROM  " . $table_schedule_content . " LEFT JOIN  
            " . $table_schedule_content_attachment . " ON  " . $table_schedule_content . ".ID= " . $table_schedule_content_attachment . "
            .Content_ID WHERE  " . $table_schedule_content . ".ID=" . $entry_id . " and " . $table_schedule_content_attachment . ".Status!=3";
        $res2 = mysqli_query($link, $query);


        while ($row1 = mysqli_fetch_array($res2)) {
            array_push($Attachments, $row1["Files"]);
        }
    }
}



if ($entry_id < 0) {
    $query1 = "SELECT " . $table_group . ".* FROM " . $table_group . " WHERE 1=1 AND " . $table_group . ".Status != '3' ";
    $res1 = mysqli_query($link, $query1);
    $totalData = mysqli_num_rows($res1);
    $query1 = "SELECT " . $table_group . ".* FROM " . $table_group . " WHERE 1=1 AND " . $table_group . ".Status != '3' ";
} else {
    $query1 = "SELECT DISTINCT " . $table_group . " .*, " . $table_schedule_content_group . ".Group_ID FROM " . $table_group . " LEFT JOIN " . $table_schedule_content_group . " ON ( " . $table_group . " .ID = " . $table_schedule_content_group . "
	.Group_ID AND " . $table_schedule_content_group . " .Content_ID= " . $entry_id . " )  WHERE 1=1 AND " . $table_group . ".Status != '3' ";
    $res1 = mysqli_query($link, $query1);
    $totalData = mysqli_num_rows($res1);
    $query1 = "SELECT DISTINCT " . $table_group . " .*, " . $table_schedule_content_group . ".Group_ID FROM " . $table_group . " LEFT JOIN " . $table_schedule_content_group . " ON ( " . $table_group . " .ID = " . $table_schedule_content_group . "
	.Group_ID AND " . $table_schedule_content_group . " .Content_ID= " . $entry_id . " )  WHERE 1=1 AND " . $table_group . ".Status != '3' ";
}


$res1 = mysqli_query($link, $query1);
$totalFiltered = mysqli_num_rows($res1);
$res1 = mysqli_query($link, $query1);
$count_admin = 0;
$sr_no = 0;
$Block_Status_Code = 2;
$data = array();
$count = 0;

while ($row1 = mysqli_fetch_array($res1)) {
    $nestedData = array();
    $State = "";
    $User_ID_Group = $row1["Group_ID"];
    $ID = $row1['ID'];
    $Group_Members = $row1['Group_Members'];
    if ($ID > 0) {
        if ($Group_Members == 2) {
            $query = "SELECT COUNT(ID) as Users from " . $table_group_user . " where Group_ID=" . $ID . " AND Status!='3'";
        } else {
            $query = "SELECT COUNT(ID) as Users from " . $table_nurse . "  where Status!='3' ";
        }
        $res = mysqli_query($link, $query);
        if (mysqli_num_rows($res) > 0) {
            while ($row = mysqli_fetch_array($res)) {
                $count_admin = $row["Users"];
            }
        }
    }
    $Name = $row1['Name'];
    $Description = $row1['Description'];
    $Last_Login = $row1['Last_Login'];
    $Status = $row1['Status'];
    $Last_Login_Time = "";
    if (date('Y', strtotime($Last_Login)) > 2000) {
        $Last_Login_Time = date('d-m-Y', strtotime($Last_Login));
    }
    if ($count_admin > 0) {
        $count++;
        $nestedData[] = $count;
        $nestedData[] = $Name;
        $nestedData[] = $count_admin;
        $action = "";
        //if($Admin_Role_ID == $Super_Admin_Role_ID)
        //{
        if ($User_ID_Group == NULL) {
            $action .= ' <div class="checkbox checkbox-purple checkbox-single">
								<input type="checkbox" id="singleCheckbox2" disabled name="group[]" id="' . $ID . '" value="' . $ID . '">
									<label></label>
								</div>';
        } else if ($User_ID_Group == $ID) {
            $action .= '<div class="checkbox checkbox-purple checkbox-single ">
					<input type="checkbox" id="singleCheckbox2" name="group[]" disabled id="' . $ID . '" value="' . $ID . '" checked>
						<label></label>
					</div>';
        } else {
            $action .= '<div class="checkbox checkbox-purple checkbox-single">
					<input type="checkbox" id="singleCheckbox2" name="group[]" disabled id="' . $ID . '" value="' . $ID . '">
						<label></label>
					</div>';
        }
        //}
        $nestedData[] = $action;
        $data[] = $nestedData;
    }
}
$query4 = "SELECT First_Name, Last_Name FROM " . $table_admin . " where ID=" . $UserId;
$res = mysqli_query($link, $query4);
if (mysqli_num_rows($res) > 0) {
    while ($row = mysqli_fetch_array($res)) {
        $UserName = $row["First_Name"] . " " . $row["Last_Name"];
    }
}
$query1 = "SELECT * FROM " . $table_schedule_content . " where ID='$entry_id' limit 1";
$res1 = mysqli_query($link, $query1);
if (mysqli_num_rows($res1) > 0) {
    while ($row1 = mysqli_fetch_array($res1)) {
        $Group_Members = $row1['Role'];
    }
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
                    <h4 class="page-title font-karla"><?= $Content_Title_trim; ?></h4>
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
                                        <p class="m-l-10"><b><?php echo "Content Added By : " . $UserName ?></b></p>
                                        <p class="m-l-10"><b><?php echo "Content Scheduled On : " . $Schedule_Date ?></b></p>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-xs-3">
                                        <button class="btn btn-trans waves-effect waves-light
                                    m-b-5 font-karla btn-styles" title="Delete" style="background:#ffffff" onclick="DeleteContent('<?= $entry_id; ?>')">
                                            <i class="fa fa-trash text-danger"></i>
                                            <span>Delete </span>
                                        </button>
                                        <a href="schedule_content.php?id=<?php echo $entry_id ?>" class="btn btn-trans waves-effect waves-light
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
                                                <label for="userName">Content Title*</label>
                                                <input type="text" name="Content_Title" parsley-trigger="change" required disabled placeholder="Content Title" class="form-control" id="Group_Name" value="<?= $Content_Title; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row p-l-r-10">
                                        <div class="col-lg-12">
                                            <div class="form-group font-karla">
                                                <label for="emailAddress">Content Description*</label>
                                                <textarea class="form-control" placeholder="Description" name="Content_Description" parsley-trigger="change" disabled required rows="5"><?= $Content_Description ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row p-l-r-10">
                                        <div class="col-lg-12">
                                            <div class="form-group font-karla">
                                                <label for="pass1">Content Type*</label>

                                                <select class="form-control select2" disabled name="Content_Type_Select" id="Content_Type_Select" required="required">
                                                    <option selected disabled>Select</option>
                                                    <option value="1" <?php if ($Content_Type == 1) echo 'selected="selected"' ?>>Image</option>
                                                    <option value="2" <?php if ($Content_Type == 2) echo 'selected="selected"' ?>>Video</option>
                                                    <option value="3" <?php if ($Content_Type == 3) echo 'selected="selected"' ?>>Bulk Images</option>
                                                    <option value="4" <?php if ($Content_Type == 4) echo 'selected="selected"' ?>>Text</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row p-l-r-10" <?php if ($Content_Type == 1 && $Attachment != '') echo 'style="display:block;"';
                                                                else  echo 'style="display:none;"'; ?>>
                                        <div class="col-lg-3">
                                            <div class="form-group font-karla">
                                                <img src="uploads/Content_Attachments/<?php echo $Attachment; ?>" class="img-responsive uploaded-images" alt="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row p-l-r-10" <?php if ($Content_Type == 2 && $Attachment != '') echo 'style="display:block;"';
                                                                else  echo 'style="display:none;"'; ?>>
                                        <div class="col-lg-12">
                                            <div class="form-group font-karla">
                                                <?php
                                                $Attachment = str_replace("watch?v=", "embed/", $Attachment); ?>
                                                <iframe src="<?php echo $Attachment ?>">
                                                </iframe>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row p-l-r-10 m-b-30" <?php if ($Content_Type == 3) echo 'style="display:block;"';
                                                                        else  echo 'style="display:none;"'; ?>>
                                        <?php
                                        if (!empty($Attachments)) {
                                            foreach ($Attachments as $key => $value) { ?>
                                                <div class="col-sm-3 m-b-10">
                                                    <img src="uploads/Content_Attachments/<?php echo $value; ?>" class="img-responsive uploaded-images" alt="">
                                                </div>
                                        <?php }
                                        } ?>
                                    </div>

                                    <div class="font-karla p-l-r-10">
                                        <label for="pass1">Role*</label>
                                    </div>
                                    <div class="row p-l-r-10">
                                        <div class="col-lg-4 font-karla">
                                            <div class="radio radio-purple">
                                                <input type="radio" disabled name="Group_Members" value="1" id="all" onchange="changeMember(this.value)" <?php if ($Group_Members == "1") echo 'checked="checked"'; ?> required>
                                                <label for="admin">
                                                    All Users
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 font-karla">
                                            <div class="radio radio-purple">
                                                <input type="radio" disabled name="Group_Members" value="2" id="specific" onchange="changeMember(this.value)" <?php if ($Group_Members == "2") echo 'checked="checked"'; ?>>
                                                <label for="admin">
                                                    Specific Groups
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-box table-responsive" id="showTable" <?php if ($Group_Members == 2) echo  'style="display:block;"';
                                                                                            else echo  'style="display:none;"'; ?>>
                                        <table class="font-karla table table-striped table-bordered dt-responsive nowrap">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Group Name</th>
                                                    <th>Number of Users</th>
                                                    <th>CheckBox</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if (!empty($data)) {
                                                    foreach ($data as $key => $value) {
                                                ?>
                                                        <tr>
                                                            <th><?= $value["0"] ?></th>
                                                            <td><?= $value["1"] ?></td>
                                                            <td><?= $value["2"] ?> </td>
                                                            <td>
                                                                <?= $value["3"] ?>
                                                            </td>
                                                        </tr>
                                                <?php }
                                                } ?>
                                            </tbody>
                                        </table>
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
                                status: "Schedule_Content"
                            },
                            success: function(result) {
                                if (result == 1) {
                                    window.location.href = "schedule_content_list.php";
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