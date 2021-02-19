<?php
include 'functions.php';
// storing  request (ie, get/post) global array to a variable  
$requestData = $_REQUEST;

$query1 = "SELECT " . $table_noora_hospital . ".*  FROM " . $table_noora_hospital . " WHERE 1=1 AND " . $table_content . ".Status = '1' ";
$res1 = mysqli_query($link, $query1);
$totalData = mysqli_num_rows($res1);
$query1 = "SELECT " . $table_noora_hospital . ".*," . $table_noora_state . ".Name as State_Name FROM " . $table_noora_hospital . " INNER JOIN " . $table_noora_state . " ON " . $table_noora_hospital . ".State_ID = " . $table_noora_state . ".ID WHERE 1=1 AND " . $table_noora_hospital . ".Status = '1' ";
if (!empty($requestData['search']['value'])) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $query1 .= " AND ( " . $table_noora_state . ".Name LIKE '%" . $requestData['search']['value'] . "%' )";
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
    $State = $row1['State_Name'];
    $Hospital = $row1['Name'];
    $Status = $row1['Status'];

    $partner = array();

    // SELECT MemberId, MemberName, GROUP_CONCAT(FruitName) FROM a LEFT JOIN b ON a.MemberName = b.MemberName GROUP BY a.MemberName;

    $nestedData[] = $ID;
    $nestedData[] = $Hospital;
    $nestedData[] = $State;
    $query2 = "SELECT " . $table_noora_hospital_partner . ".Name FROM " . $table_noora_hospital_partner_mapping . " INNER JOIN " . $table_noora_hospital_partner . " ON " . $table_noora_hospital_partner_mapping . ".Partner_ID = " . $table_noora_hospital_partner . ".ID WHERE " . $table_noora_hospital_partner_mapping . ".Hospital_ID = '$ID' ";
    $res2 = mysqli_query($link, $query2);

    while ($row2 = mysqli_fetch_assoc($res2)) {
        array_push($partner, $row2['Name']);
        // $partner = $row2['Name'];
    }
    // var_dump($partner);
    $nestedData[] =  implode("<br>", $partner);

    // $nestedData[] = $partner;
    $nestedData[] = $Status;

    $action = "";


    $action .= '<a href="content?id=' . $ID . '" class="on-default edit-row edit" title="Edit"><i class="fa fa-pencil"></i></a>';
    $action .= '<a href="content_detail?id=' . $ID . '" class="on-default text-primary" title="View"><i class="fa fa-eye"></i></a>';
    $action .= '<a href="javascript:void(0);" class="on-default remove-rowS delete_btn" data_table_name="User" rel="' . $ID . '" data_id="' . $ID . '"  data_status="3" title="Delete"><i class="fa fa-trash-o"></i></a>';

    $data[] = $nestedData;
}


$json_data = array("draw" => intval($requestData['draw']), "recordsTotal" => intval($totalData),  "recordsFiltered" => intval($totalFiltered), "aaData" => $data, "query" => $query1, "query2" => $requestData);

echo json_encode($json_data);  // send data as json format
