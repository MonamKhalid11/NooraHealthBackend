<?php
include 'functions.php';
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
$group_id=0;
if( !empty($requestData['form']['1']['value']) ) {
    $group_id=$requestData['form']['1']['value'];
    }
 if($group_id<0)
 {   
$query1 = "SELECT ".$table_nurse.".* FROM ".$table_nurse." WHERE 1=1 AND ".$table_nurse.".Status != '3' ";
$res1= mysqli_query($link,$query1);
$totalData = mysqli_num_rows($res1);

$query1 = "SELECT ".$table_nurse.".* FROM ".$table_nurse." WHERE 1=1 AND ".$table_nurse.".Status != '3' ";
 }
 else
 {
	$query1 = "SELECT ".$table_nurse." .*, ".$table_group_user. ".User_ID FROM ".$table_nurse." LEFT JOIN ".$table_group_user." ON ( ".$table_nurse." .ID = ".$table_group_user."
	.User_ID AND ".$table_group_user." .Group_ID= ".$group_id." )  WHERE 1=1 AND ".$table_nurse.".Status != '3' ";
	$res1= mysqli_query($link,$query1);
	$totalData = mysqli_num_rows($res1);
	$query1 = "SELECT ".$table_nurse." .*, ".$table_group_user. " .User_ID FROM ".$table_nurse." LEFT JOIN ".$table_group_user." ON ( ".$table_nurse." .ID = ".$table_group_user."
	.User_ID AND ".$table_group_user." .Group_ID= ".$group_id." )  WHERE 1=1 AND ".$table_nurse.".Status != '3' ";
	
	/* $query1 = "SELECT ".$table_nurse.".* FROM ".$table_nurse." WHERE 1=1 AND ".$table_nurse.".Status != '3' ";
	SELECT * FROM `noora_nurse` LEFT JOIN noora_group_user ON (noora_nurse.ID=noora_group_user.User_ID AND 
	noora_group_user.Group_ID=35) */
 }
   
if( !empty($requestData['search']['value']) ) 
{   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$query1.=" AND ( ".$table_nurse.".First_Name LIKE '%".$requestData['search']['value']."%' ";    
	$query1.=" OR ".$table_nurse.".Last_Name LIKE '%".$requestData['search']['value']."%' ";
	$query1.=" OR ".$table_nurse.".Mobile_Number LIKE '%".$requestData['search']['value']."%' ";
	$query1.=" OR ".$table_nurse.".Last_Login LIKE '%".date('Y-m-d',strtotime($requestData['search']['value']))."%' ";
	$query1.=" OR ".$table_nurse.".Email LIKE '%".$requestData['search']['value']."%' )";
}
$res1= mysqli_query($link,$query1);
$totalFiltered = mysqli_num_rows($res1); 
$query1.=" ORDER BY ID DESC ";
$query1.=" LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
$res1=mysqli_query($link,$query1);

$sr_no=0;
$Block_Status_Code = 2;
$data = array();
$count=0;
while($row1=mysqli_fetch_array($res1)) 
{  
    $count++;
	$nestedData=array(); 
	$State="";
	$ID=$row1['ID'];
	$User_ID_Group=$row1["User_ID"];
	$First_Name=$row1['First_Name'];
	$Last_Name=$row1['Last_Name'];	
	$Mobile_Number=$row1['Mobile_Number'];	
	$Role_ID=$row1['Role_ID'];	
    $City_ID=$row1['City_ID'];	
    if($City_ID!=0)
    {
        $query="SELECT ".$table_noora_state.".Name as State_Name FROM ".$table_noora_district." LEFT JOIN  ".$table_noora_state." ON ".$table_noora_district.".State_ID=".$table_noora_state.".ID WHERE ".$table_noora_district.".ID=".$City_ID;
        $res2=mysqli_query($link,$query);
        while($row=mysqli_fetch_array($res2)) 
        { 
            $State=$row["State_Name"];
        }

    }
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
    $nestedData[] = $count;
	$nestedData[] = $Name;
    $nestedData[] = 'Hospital';
    $action="";
    $groupuser=array();
    $nurse=array();
	if($Admin_Role_ID == $Super_Admin_Role_ID)
	{
		if($User_ID_Group==NULL)
		{
				$action.=' <div class="checkbox checkbox-primary checkbox-single ">
							<input type="checkbox" id="singleCheckbox11" name="group[]" id="'.$ID.'" value="'.$ID.'">
							<label></label>
							</div>';
		}
			else
			{
			$action.='
			<div class="checkbox checkbox-primary checkbox-single ">
			<input type="checkbox" id="singleCheckbox11"name="group[]" id="'.$ID.'" value="'.$ID.'" checked>
			<label></label>
			</div>';
			}      
	}
	$nestedData[] = $action;
	$data[] = $nestedData;
}

$json_data = array("draw" => intval( $requestData['draw'] ), "recordsTotal" => intval( $totalData ),  "recordsFiltered" => intval( $totalFiltered ), "aaData" => $data, "query" => $query1, "query2" => $requestData);

echo json_encode($json_data);  // send data as json format
?>
