<?php
include 'functions.php';
$requestData= $_REQUEST;

$query1 = "SELECT ".$table_admin.".* FROM ".$table_admin." WHERE 1=1 AND (Role_ID='3' OR Role_ID='2')  AND ".$table_admin.".Status != '3' ";
$res1= mysqli_query($link,$query1);
$totalData = mysqli_num_rows($res1);

$query1 = "SELECT ".$table_admin.".*,".$table_noora_district.".Name,".$table_user_role.".Name FROM ".$table_admin."
LEFT JOIN ".$table_noora_district." ON ".$table_admin.".District_ID = ".$table_noora_district.".ID 
LEFT JOIN ".$table_user_role." ON ".$table_admin.".Role_ID = ".$table_user_role.".ID
WHERE 1=1 AND (Role_ID='3' OR Role_ID='2') AND ".$table_admin.".Status != '3' ";
if( !empty($requestData['search']['value']) ) 
{   
	$query1.=" AND ( ".$table_admin.".First_Name LIKE '%".$requestData['search']['value']."%' ";    
	$query1.=" OR ".$table_admin.".Last_Name LIKE '%".$requestData['search']['value']."%' ";
	$query1.=" OR ".$table_admin.".Mobile_Number LIKE '%".$requestData['search']['value']."%' ";
	$query1.=" OR ".$table_admin.".Last_Login LIKE '%".date('Y-m-d',strtotime($requestData['search']['value']))."%' ";
	$query1.=" OR ".$table_admin.".Email LIKE '%".$requestData['search']['value']."%' ";
	$query1.=" OR ".$table_noora_district.".Name LIKE '%".$requestData['search']['value']."%' ";
	$query1.=" OR ".$table_user_role.".Name LIKE '%".$requestData['search']['value']."%' )";
}

$res1= mysqli_query($link,$query1);
$totalFiltered = mysqli_num_rows($res1); 
$query1.=" ORDER BY ID DESC ";
$query1.=" LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
$res1=mysqli_query($link,$query1);

$sr_no=0;
$Block_Status_Code = 2;
$data = array();
while($row1=mysqli_fetch_array($res1)) 
{  
	$nestedData=array(); 
	$Role="";
	$City="";
	$ID=$row1['ID'];
	$First_Name=$row1['First_Name'];
	$Last_Name=$row1['Last_Name'];
	$Email=$row1['Email'];	
	$Mobile_Number=$row1['Mobile_Number'];	
	$Role_ID=$row1['Role_ID'];
	if($Role_ID==2)
	{
		$Role="Admin";
	}	
	if($Role_ID==3)
	{
		$Role="Manager";
	}	
	$City_ID=$row1['District_ID'];	
	if($City_ID>0)
	{
		$query2="SELECT * FROM ".$table_noora_district." WHERE ID=".$City_ID;
		$res2=mysqli_query($link,$query2);
		while($row2=mysqli_fetch_array($res2)) 
		{
			$City=$row2["Name"];
		} 
		

	}
	$Last_Login=$row1['Last_Login'];	
	$Status=$row1['Status'];	
	$Last_Login_Time = "";
	if(date('Y',strtotime($Last_Login))>2000)
	{
		$Last_Login_Time = date('d M, Y',strtotime($Last_Login));
	}
	else{
		$Last_Login_Time ="--";
	}
	
	
	$Name = $First_Name;
	if($Last_Name!="")
	{
		$Name.= " ".$Last_Name;
	}
	$nestedData[] = $ID;
	$nestedData[] = $Name;
	$nestedData[] = $Email;
	$nestedData[] = $Mobile_Number;
	$nestedData[] = $City;
	$nestedData[] = $Role;
	$nestedData[] = $Last_Login_Time;
	$action="";


		$action.= '<a href="admin_manager?id='.$ID.'" class="on-default edit-row edit"><i class="fa fa-pencil"></i></a>';
		$action.= '<a href="javascript:void(0);" class="on-default remove-row delete_btn" data_table_name="User" data_id="'.$ID.'" rel="'.$ID.'"  data_status="3"><i class="fa fa-trash-o"></i></a>';
	
	$data[] = $nestedData;
}

$json_data = array("draw" => intval( $requestData['draw'] ), "recordsTotal" => intval( $totalData ),  "recordsFiltered" => intval( $totalFiltered ), "aaData" => $data, "query" => $query1, "query2" => $requestData);

echo json_encode($json_data);  // send data as json format
?>
