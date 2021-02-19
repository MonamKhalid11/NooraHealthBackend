<?php
include 'functions.php';
// storing  request (ie, get/post) global array to a variable  
$requestData = $_REQUEST;

$query1 = "SELECT " . $table_noora_hospital_partner . ".*  FROM " . $table_noora_hospital_partner . " WHERE 1=1 AND Status = '1' ";
$res1 = mysqli_query($link, $query1);
$totalData = mysqli_num_rows($res1);
$query1 = "SELECT " . $table_noora_hospital_partner . ".*  FROM " . $table_noora_hospital_partner . "  WHERE 1=1 AND Status = '1' ";
if (!empty($requestData['search']['value'])) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $query1 .= " AND ( " . $table_noora_hospital_partner . ".Name LIKE '%" . $requestData['search']['value'] . "%' )";
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
    $Partner = $row1['Name'];
    $Status = $row1['Status'];


    $nestedData[] = $ID;
    $nestedData[] = $Partner;
    $nestedData[] = $Status;

    $action = "";


    $action .= '<a href="content?id=' . $ID . '" class="on-default edit-row edit" title="Edit"><i class="fa fa-pencil"></i></a>';
    $action .= '<a href="content_detail?id=' . $ID . '" class="on-default text-primary" title="View"><i class="fa fa-eye"></i></a>';
    $action .= '<a href="javascript:void(0);" class="on-default remove-rowS delete_btn" data_table_name="User" rel="' . $ID . '" data_id="' . $ID . '"  data_status="3" title="Delete"><i class="fa fa-trash-o"></i></a>';

    $data[] = $nestedData;
}
file_put_contents('test.txt', $partner);

$json_data = array("draw" => intval($requestData['draw']), "recordsTotal" => intval($totalData),  "recordsFiltered" => intval($totalFiltered), "aaData" => $data, "query" => $query1, "query2" => $requestData);

echo json_encode($json_data);  // send data as json format
