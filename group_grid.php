<?php
include 'functions.php';
$requestData= $_REQUEST;
$group_id=$_GET['id'];


 if($group_id<0)
 {   
	$query1 = "SELECT ".$table_group.".* FROM ".$table_group." WHERE 1=1 AND ".$table_group.".Status != '3' ";
	$res1= mysqli_query($link,$query1);
	$totalData = mysqli_num_rows($res1);
	$query1 = "SELECT ".$table_group.".* FROM ".$table_group." WHERE 1=1 AND ".$table_group.".Status != '3' ";
 }
 else
 {
	$query1 = "SELECT DISTINCT ".$table_group." .*, ".$table_content_group. ".Group_ID FROM ".$table_group." LEFT JOIN ".$table_content_group." ON ( ".$table_group." .ID = ".$table_content_group."
	.Group_ID AND ".$table_content_group." .Content_ID= ".$group_id." )  WHERE 1=1 AND ".$table_group.".Status != '3' ";
	$res1= mysqli_query($link,$query1);
	$totalData = mysqli_num_rows($res1);
	$query1 = "SELECT DISTINCT ".$table_group." .*, ".$table_content_group. ".Group_ID FROM ".$table_group." LEFT JOIN ".$table_content_group." ON ( ".$table_group." .ID = ".$table_content_group."
	.Group_ID AND ".$table_content_group." .Content_ID= ".$group_id." )  WHERE 1=1 AND ".$table_group.".Status != '3' ";

 }
if( !empty($requestData['search']['value']) ) 
{   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$query1.=" AND ( ".$table_group.".Name LIKE '%".$requestData['search']['value']."%' ";    
	$query1.=" OR ".$table_group.".Description LIKE '%".$requestData['search']['value']."%' ";
	$query1.=" OR ".$table_group.".Last_Login LIKE '%".date('Y-m-d',strtotime($requestData['search']['value']))."%' ";
}
$res1= mysqli_query($link,$query1);
$totalFiltered = mysqli_num_rows($res1); 
$query1.=" LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
$res1=mysqli_query($link,$query1);
$count_admin=0;
$sr_no=0;
$Block_Status_Code = 2;
$data = array();
$count=0;

while($row1=mysqli_fetch_array($res1)) 
{  // preparing an array
	
	$nestedData=array(); 
	$State="";
	$User_ID_Group=$row1["Group_ID"];
    $ID=$row1['ID'];
	$Group_Members=$row1['Group_Members'];

    if($ID>0)
    {
        if($Group_Members==2)
        {

            $query="SELECT COUNT(ID) as Users from ".$table_group_user." where Group_ID=".$ID;
        }
        else
        {
            $query="SELECT COUNT(ID) as Users from ".$table_group;
        }
  
                $res= mysqli_query($link,$query);
                if(mysqli_num_rows($res)>0)
                {
                    while($row = mysqli_fetch_array($res))
                    {
						$count_admin=$row["Users"];
					
                    }
                }
    }
	$Name=$row1['Name'];
	$Description=$row1['Description'];	
	
	$Last_Login=$row1['Last_Login'];	
	$Status=$row1['Status'];	
	$Last_Login_Time = "";
	if(date('Y',strtotime($Last_Login))>2000)
	{
		$Last_Login_Time = date('d-m-Y',strtotime($Last_Login));
	}
	
	
		if($count_admin>0){
		$count++;
		$nestedData[] = $count;
		$nestedData[] = $Name;
		$nestedData[] = $count_admin;
		

		$action="";
			if($Admin_Role_ID == $Super_Admin_Role_ID)
			{
				if($User_ID_Group==NULL)
				{
					$action.= ' <div class="checkbox checkbox-primary checkbox-single">
								<input type="checkbox" id="singleCheckbox2" name="group[]" id="'.$ID.'" value="'.$ID.'">
									<label></label>
								</div>';

				}
				else if($User_ID_Group==$ID)
				{
					$action.= '<div class="checkbox checkbox-primary checkbox-single">
					<input type="checkbox" id="singleCheckbox2" name="group[]" id="'.$ID.'" value="'.$ID.'" checked>
						<label></label>
					</div>';
				}
				else 
				{
					$action.= '<div class="checkbox checkbox-primary checkbox-single">
					<input type="checkbox" id="singleCheckbox2" name="group[]" id="'.$ID.'" value="'.$ID.'">
						<label></label>
					</div>';
				}	
			}
	
		$nestedData[] = $action;		
		$data[] = $nestedData;
		}
}

$json_data = array("draw" => intval( $requestData['draw'] ), "recordsTotal" => intval( $totalData ),  "recordsFiltered" => intval( $totalFiltered ), "aaData" => $data, "query" => $query1, "query2" => $requestData);

echo json_encode($json_data);  // send data as json format
?>
