<?php
include 'functions.php';
// storing  request (ie, get/post) global array to a variable  
$requestData = $_REQUEST;

$query1 = "SELECT " . $table_noora_ccp_tool_type . ".*  FROM " . $table_noora_ccp_tool_type . " WHERE 1=1  ";
$res1 = mysqli_query($link, $query1);
$totalData = mysqli_num_rows($res1);
$query1 = "SELECT " . $table_noora_ccp_tool_type . ".*  FROM " . $table_noora_ccp_tool_type . " ";
if (!empty($requestData['search']['value'])) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $query1 .= " where ( " . $table_noora_ccp_tool_type . ".Name LIKE '%" . $requestData['search']['value'] . "%' )";
}
$res1 = mysqli_query($link, $query1);
$totalFiltered = mysqli_num_rows($res1);
$query1 .= " ORDER BY ID DESC ";
$query1 .= " LIMIT " . $requestData['start'] . " ," . $requestData['length'] . "   ";
$res1 = mysqli_query($link, $query1);
$data = array();
// var_dump(mysqli_fetch_array($res1));
while ($row1 = mysqli_fetch_array($res1)) {  // preparing an array
    $nestedData = array();
    $ID = $row1['ID'];
    $Name = $row1['Name'];
    $Type = $row1['Type'];
    $Material_URL = $row1['Material_URL'];
    $Status = $row1['Status'];


    $nestedData[] = $ID;
    $nestedData[] = $Name;
    // $nestedData[] = $Type == 0 ? "<>";
    $nestedData[] = $Type == 1 ? "<a href='" . $Material_URL . "'>$Material_URL</a>" : "---";
    $nestedData[] = $Status == 1 ? "<span class='label label-success'>Enabled</span>" : "<span class='label label-danger'>Disabled</span>";
    $nestedData[] = $Status == 1 ? "<button class='btn'  style='padding:5px;background-color: #5b69bc; border: 1px solid #5b69bc;color:#ffff'  onclick='Status_tool_type(0," . $ID . ")'>Disable</button>" : "<button class='btn'  style='background-color: #5b69bc; border: 1px solid #5b69bc;color:#ffff' onclick='Status_tool_type(1," . $ID . ")'>Enable</button>";
    // $nestedData[] = $Status == 1 ? "<a class='btn' href='training_languages_entry.php?id= " . $ID . "&status=" . $Status . "' style='background-color: #5b69bc; border: 1px solid #5b69bc;color:#ffff' >Disable</a>" : "<a class='btn' href='training_languages_entry.php?id= " . $ID . "&status=" . $Status . "' style='background-color: #5b69bc; border: 1px solid #5b69bc;color:#ffff' >Enable</a>";
    $nestedData[] = "<a href='tool_section.php?id=" .trim($ID," "). "' class='btn' title='Edit'
    style='background-color: #5b69bc; border: 1px solid #5b69bc;color:#ffff'>  <i class='fa fa-pencil  edit-clr'></i>
            <span>Edit&nbsp;&nbsp;</span>
        </a>";

    $action = "";


    $action .= '<a href="content?id=' . $ID . '" class="on-default edit-row edit" title="Edit"><i class="fa fa-pencil"></i></a>';
    $action .= '<a href="content_detail?id=' . $ID . '" class="on-default text-primary" title="View"><i class="fa fa-eye"></i></a>';
    $action .= '<a href="javascript:void(0);" class="on-default remove-rowS delete_btn" data_table_name="User" rel="' . $ID . '" data_id="' . $ID . '"  data_status="3" title="Delete"><i class="fa fa-trash-o"></i></a>';

    $data[] = $nestedData;
}


$json_data = array("draw" => intval($requestData['draw']), "recordsTotal" => intval($totalData),  "recordsFiltered" => intval($totalFiltered), "aaData" => $data, "query" => $query1, "query2" => $requestData);

echo json_encode($json_data);  // send data as json format
