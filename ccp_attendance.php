<?php
include 'functions.php';
$requestData= $_REQUEST;
$group_id=$_GET['id'];

 if($group_id>0)
 {  
	 $query1="SELECT ".$table_attendance.".*, ".$table_ccp_class_type.".Class_Type 
	 FROM ".$table_attendance." LEFT JOIN ".$table_ccp_class_type." ON ".$table_attendance.".Class_Type_ID = 
	 ".$table_ccp_class_type.".ID WHERE 1=1 AND ".$table_attendance.".Status != '3' AND 
	 ".$table_attendance.".Login_User_ID=".$group_id ; 
	$res1= mysqli_query($link,$query1);
	$totalData = mysqli_num_rows($res1);
 }


if( !empty($requestData['search']['value']) ) 
{   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$query1.=" AND ( ".$table_attendance.".Name LIKE '%".$requestData['search']['value']."%' ";    
	$query1.=" OR ".$table_attendance.".Description LIKE '%".$requestData['search']['value']."%' ";
	$query1.=" OR ".$table_attendance.".Last_Login LIKE '%".date('Y-m-d',strtotime($requestData['search']['value']))."%' ";
}
$res1= mysqli_query($link,$query1);
$totalFiltered = mysqli_num_rows($res1); 
$query1.=" ORDER BY ".$table_attendance.".Class_Date DESC,".$table_attendance.".Class_Time DESC";
$query1.=" LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
$res1=mysqli_query($link,$query1);
$count_admin=0;
$sr_no=0;
$Block_Status_Code = 2;
$data = array();
$count=0;
$Class_Image='';

while($row1=mysqli_fetch_array($res1)) 
{  // preparing an array
	$count++;
	$nestedData=array(); 
	$State="";

	$ID=$row1['ID'];

	$Class_Type=$row1['Class_Type'];
    $Date=date('d M, Y',strtotime($row1["Class_Date"]));
    $Time=date("g:i a", strtotime($row1["Class_Time"]));
	$Last_Login=$row1['Last_Login'];	
	$Status=$row1['Status'];	
	$Last_Login_Time = "";
	$Class_Image=$row1['Image'];

	if($Class_Image!='')
	{
		$url="uploads/ClassImages/".$Class_Image;
		$Image='<a href='.$url.' target="_blank" style="display:flex;"><img src='.$url.' style="height:50px;width:50px" class="img-responsive"></a>';
	}
	else{
		$Image='No Class Image';
	}
	if(date('Y',strtotime($Last_Login))>2000)
	{
		$Last_Login_Time = date('d-m-Y',strtotime($Last_Login));
	}
	$timestamp = strtotime($Date);
	$day = date('l', $timestamp);

	$nestedData[] = $day." ".$Date;
    $nestedData[] = $Time;
	$nestedData[] = $Class_Type;
	$nestedData[] = $Image;
	
    
	$action="";

		$action.= '<a href="javascript:void(0);" class="btn delete_btn" data_table_name="User" data_id="'.$ID.'" rel="'.$ID.'"  data_status="3"><i class="fa fa-trash-o"></i></a>';
	$nestedData[] = $action;
	$data[] = $nestedData;
}

$json_data = array("draw" => intval( $requestData['draw'] ), "recordsTotal" => intval( $totalData ),  "recordsFiltered" => intval( $totalFiltered ), "aaData" => $data, "query" => $query1, "query2" => $requestData);

echo json_encode($json_data);  // send data as json format
?>
