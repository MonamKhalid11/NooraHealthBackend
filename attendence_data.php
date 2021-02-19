<?php
include 'functions.php';
$requestData = $_REQUEST;
$subquery = "";
if ($requestData['hospital'] > 0) {
	$hospitalId = $requestData['hospital'];
	$subquery = " AND " . $table_nurse . ".Hospital_ID=" . $hospitalId;
}

if ($requestData['state'] > 0) {
	$stateId = $requestData['state'];
	$subquery = " AND " . $table_attendance . ".State_ID=" . $stateId;
	// left join ".$table_noora_state." on ".$table_attendance.".State_ID=".$table_noora_state.".ID
}

if ($requestData['district'] > 0) {
	$districtId = $requestData['district'];
	$subquery = " AND " . $table_attendance . ".District_ID=" . $districtId;
	// left join ".$table_noora_state." on ".$table_attendance.".State_ID=".$table_noora_state.".ID
}


$query1 = "SELECT " . $table_attendance . ".* FROM " . $table_attendance . " WHERE 1=1 AND " . $table_attendance . ".Status != '3' ";
// $query1 = "SELECT " . $table_attendance . ".*," . $table_noora_state . ".Name as state," . $table_noora_district . ".Name as district FROM " . $table_attendance . " LEFT JOIN " . $table_noora_state . " ON " . $table_attendance . ".State_ID = " . $table_noora_state . ".ID LEFT JOIN " . $table_noora_state . " ON " . $table_attendance . ".District_ID = " . $table_noora_district . ".ID  WHERE 1=1 AND " . $table_attendance . ".Status != '3' ";


$query1 = "SELECT Image,Class_Date,Class_Time,Class_Type_ID,No_of_People,Ward,Notes,Session_Conducted," . $table_nurse . ".First_Name,
" . $table_nurse . ".Last_Name," . $table_ccp_class_type . ".Class_Type," . $table_noora_hospital . ".Name," . $table_noora_state . ".Name as state," . $table_noora_district . ".Name as district  FROM " . $table_attendance . " 
LEFT JOIN " . $table_nurse . " ON " . $table_attendance . " .Login_User_ID = " . $table_nurse . ".ID 
left join " . $table_noora_state . " on " . $table_attendance . ".State_ID=" . $table_noora_state . ".ID
left join " . $table_noora_district . " on " . $table_attendance . ".District_ID=" . $table_noora_district . ".ID
LEFT JOIN " . $table_ccp_class_type . " on " . $table_attendance . ".Class_Type_ID=" . $table_ccp_class_type . ".ID
left join " . $table_noora_hospital . " on " . $table_nurse . ".Hospital_ID=" . $table_noora_hospital . ".ID

where " . $table_attendance . ".status!=3 AND " . $table_nurse . ".status!=3  " . $subquery . " ";
$res1 = mysqli_query($link, $query1);
$totalData = mysqli_num_rows($res1);
if (!empty($requestData['search']['value'])) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$query1 .= " AND ( " . $table_nurse . ".First_Name LIKE '%" . $requestData['search']['value'] . "%' ";
	$query1 .= " OR " . $table_nurse . ".Last_Name LIKE '%" . $requestData['search']['value'] . "%' ";
	$query1 .= " OR " . $table_ccp_class_type . ".Class_Type LIKE '%" . $requestData['search']['value'] . "%' ";
	$query1 .= " OR " . $table_noora_hospital . ".Name LIKE '%" . $requestData['search']['value'] . "%' ) ";
}
$res1 = mysqli_query($link, $query1);
$totalFiltered = mysqli_num_rows($res1);
$query1 .= "order by " . $table_attendance . ".ID Desc LIMIT " . $requestData['start'] . " ," . $requestData['length'] . "   ";
$res1 = mysqli_query($link, $query1);

$sr_no = 0;

$Block_Status_Code = 2;
$data = array();
while ($row1 = mysqli_fetch_array($res1)) {  // preparing an array

	$nestedData = array();
	$First_Name = $row1['First_Name'];
	$Last_Name = $row1['Last_Name'];
	$Class_Type = $row1['Class_Type'];
	$no_of_people = $row1['No_of_People'];
	$Date = date('d M, Y', strtotime($row1["Class_Date"]));
	$Time = date("g:i a", strtotime($row1["Class_Time"]));
	$Status = $row1['Status'];
	$Last_Login_Time = "";
	$Class_Image = $row1['Image'];
	$Hospital_Name = $row1["Name"];
	$Ward = $row1['Ward'];
	$Notes = $row1['Notes'];
	$Notes = str_replace("\\n", "\n", $Notes);
	$Session_Conducted = $row1['Session_Conducted'];

	$state = $row1['state'];
	$district = $row1['district'];

	if ($Class_Image != '') {
		$url = "uploads/ClassImages/" . $Class_Image;
		$Image = '<a href=' . $url . ' target="_blank" style="display:flex;"><img src=' . $url . ' style="height:50px;width:50px" class="img-responsive"></a>';
	} else {
		$Image = 'No Class Image';
	}
	$Name = $First_Name;
	if ($Last_Name != "") {
		$Name .= " " . $Last_Name;
	}
	$timestamp = strtotime($Date);
	$day = date('l', $timestamp);
	$nestedData[] = $Name;
	$nestedData[] = $Hospital_Name;
	$nestedData[] = $state;
	$nestedData[] = $district;
	$nestedData[] = $day . " " . $Date;
	$nestedData[] = $Time;
	$nestedData[] = $Class_Type;
	$nestedData[] = $no_of_people;
	$nestedData[] = $Ward;
	$nestedData[] = $Notes;
	$nestedData[] = $Session_Conducted;

	$nestedData[] = $Image;
	$data[] = $nestedData;
}

$json_data = array("draw" => intval($requestData['draw']), "recordsTotal" => intval($totalData),  "recordsFiltered" => intval($totalFiltered), "aaData" => $data, "query" => $query1, "query2" => $requestData);

echo json_encode($json_data);  // send data as json format
