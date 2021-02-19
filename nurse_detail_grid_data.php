<?php
include 'functions.php';
$requestData= $_REQUEST;

$query1 = "SELECT ".$table_nurse.".* FROM ".$table_nurse." WHERE 1=1 AND ".$table_nurse.".Status != '3' ";
$res1= mysqli_query($link,$query1);
$totalData = mysqli_num_rows($res1);

$query1 = "SELECT ".$table_nurse.".*,".$table_noora_hospital.".name, ".$table_noora_district.".State_ID,".$table_noora_state.".Name as State_Name FROM ".$table_nurse." LEFT JOIN ".$table_noora_hospital." ON ".$table_nurse.".Hospital_ID = ".$table_noora_hospital.".ID LEFT JOIN ".$table_noora_district." ON ".$table_noora_district.".ID=".$table_nurse.".City_ID 
LEFT JOIN ".$table_noora_state." ON ".$table_noora_district.".State_ID=".$table_noora_state.".ID WHERE 1=1 AND ".$table_nurse.".Status != '3' ";
if( !empty($requestData['search']['value']) ) 
{   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$query1.=" AND ( ".$table_nurse.".First_Name LIKE '%".$requestData['search']['value']."%' ";    
	$query1.=" OR ".$table_nurse.".Last_Name LIKE '%".$requestData['search']['value']."%' ";
	$query1.=" OR ".$table_noora_hospital.".name LIKE '%".$requestData['search']['value']."%' ";
	$query1.=" OR ".$table_noora_state.".Name LIKE '%".$requestData['search']['value']."%' ";
	$query1.=" OR ".$table_nurse.".Mobile_Number LIKE '%".$requestData['search']['value']."%' ) ";
	$query1.=" OR ".$table_nurse.".Entry_Time LIKE '%".date('Y-m-d',strtotime($requestData['search']['value']))."%' ";


}
$query1.=" AND ".$table_nurse.".Status != '3'";
$res1= mysqli_query($link,$query1);
$totalFiltered = mysqli_num_rows($res1); 
$query1.=" order by ".$table_nurse.".ID desc LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
$res1=mysqli_query($link,$query1);

$sr_no=0;

$Block_Status_Code = 2;
$data = array();
while($row1=mysqli_fetch_array($res1)) 
{  // preparing an array
	$nestedData=array(); 
	$State="";
	$ID=$row1['ID'];
	$Hospital_Name="";
	$First_Name=$row1['First_Name'];
	$Last_Name=$row1['Last_Name'];	
	$Mobile_Number=$row1['Mobile_Number'];	
	$Hospital_ID=$row1["Hospital_ID"];
	$Role_ID=$row1['Role_ID'];	
    $City_ID=$row1['District_ID'];	
    if($City_ID!=0)
    {
        $query="SELECT ".$table_noora_state.".Name as State_Name FROM ".$table_noora_district." LEFT JOIN  ".$table_noora_state." ON ".$table_noora_district.".State_ID=".$table_noora_state.".ID WHERE ".$table_noora_district.".ID=".$City_ID;
        $res2=mysqli_query($link,$query);
        while($row=mysqli_fetch_array($res2)) 
        { 
            $State=$row["State_Name"];
        }
	}
	if($Hospital_ID!=0)
    {
		$query3="SELECT * FROM ".$table_noora_hospital." WHERE ID=".$Hospital_ID;
        $res3=mysqli_query($link,$query3);
        while($row3=mysqli_fetch_array($res3)) 
        { 
            $Hospital_Name=$row3["Name"];
        }

	}
	
	$Last_Login=$row1['Entry_Time'];	
	$Status=$row1['Status'];
	$Last_Login_Time = date('d M, Y h:i A', strtotime($Last_Login));
	
	$Name = $First_Name;
	if($Last_Name!="")
	{
		$Name.= " ".$Last_Name;
	}
	$nestedData[] = $ID;
	$nestedData[] = $Name;
    $nestedData[] = $Mobile_Number;
    $nestedData[] = $Hospital_Name;
	$nestedData[] = $State;
	$nestedData[] = $Last_Login_Time;
	$action="";

		$action.= '<a href="nurse?id='.$ID.'" class="on-default edit-row  edit" title="Edit"><i class="fa fa-pencil"></i></a>';
		$action.= '<a href="nurse_details?id='.$ID.'" class="on-default text-primary" title="View"><i class="fa fa-eye"></i></a>';
		$action.= '<a href="javascript:void(0);"  class="on-default remove-row delete_btn" data_table_name="User" data_id="'.$ID.'" rel="'.$ID.'"  data_status="3" title="Delete"><i class="fa fa-trash-o"></i></a>';

	$data[] = $nestedData;
}

$json_data = array("draw" => intval( $requestData['draw'] ), "recordsTotal" => intval( $totalData ),  "recordsFiltered" => intval( $totalFiltered ), "aaData" => $data, "query" => $query1, "query2" => $requestData);

echo json_encode($json_data);  // send data as json format
?>
