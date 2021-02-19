<?php
include 'functions.php';
$requestData= $_REQUEST;

$query1 = "SELECT ".$table_admin.".* FROM ".$table_admin." WHERE 1=1 AND ".$table_admin.".Status != '3' ";
$res1= mysqli_query($link,$query1);
$totalData = mysqli_num_rows($res1);

$query1 = "SELECT ".$table_admin.".* FROM ".$table_admin." WHERE 1=1 AND ".$table_admin.".Status != '3' ";
if( !empty($requestData['search']['value']) ) 
{   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$query1.=" AND ( ".$table_admin.".First_Name LIKE '%".$requestData['search']['value']."%' ";    
	$query1.=" OR ".$table_admin.".Last_Name LIKE '%".$requestData['search']['value']."%' ";
	$query1.=" OR ".$table_admin.".Mobile_Number LIKE '%".$requestData['search']['value']."%' ";
	$query1.=" OR ".$table_admin.".Last_Login LIKE '%".date('Y-m-d',strtotime($requestData['search']['value']))."%' ";
	$query1.=" OR ".$table_admin.".Email LIKE '%".$requestData['search']['value']."%' )";
}
$res1= mysqli_query($link,$query1);
$totalFiltered = mysqli_num_rows($res1); 
$query1.=" LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
$res1=mysqli_query($link,$query1);

$sr_no=0;
$Block_Status_Code = 2;
$data = array();
while($row1=mysqli_fetch_array($res1)) 
{  // preparing an array
	$nestedData=array(); 
	
	$ID=$row1['ID'];
	$First_Name=$row1['First_Name'];
	$Last_Name=$row1['Last_Name'];
	$Email=$row1['Email'];	
	$Mobile_Number=$row1['Mobile_Number'];	
	$Role_ID=$row1['Role_ID'];	
	$City_ID=$row1['City_ID'];	
	$Last_Login=$row1['Last_Login'];	
	$Status=$row1['Status'];	
	$Last_Login_Time = "";
	if(date('Y',strtotime($Last_Login))>2000)
	{
		$Last_Login_Time = date('d-m-Y',strtotime($Last_Login));
	}
	
	
	$Name = $First_Name;
	if($Last_Name!="")
	{
		$Name.= " ".$Last_Name;
	}
	$query2="SELECT name FROM ".$table_noora_city." where id=". $City_ID;
	$res2=mysqli_query($link,$query2);
	while($row1=mysqli_fetch_array($res2)) 
	{
		$City_Name=$row1['name'];
	}
	$query2="SELECT name FROM ".$table_user_role." where id=".$Role_ID;
	$res2=mysqli_query($link,$query2);
	while($row1=mysqli_fetch_array($res2)) 
	{
		$Role_Name=$row1['name'];
	}
	$nestedData[] = $Name;
	$nestedData[] = $Email;
	$nestedData[] = $Mobile_Number;
	$nestedData[] = $City_Name;
	$nestedData[] = $Role_Name;
	$nestedData[] = $Last_Login_Time;

	$data[] = $nestedData;
}

$json_data = array("draw" => intval( $requestData['draw'] ), "recordsTotal" => intval( $totalData ),  "recordsFiltered" => intval( $totalFiltered ), "aaData" => $data, "query" => $query1, "query2" => $requestData);

echo json_encode($json_data);  // send data as json format
?>
