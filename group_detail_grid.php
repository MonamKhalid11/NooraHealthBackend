<?php
include 'functions.php';
$requestData= $_REQUEST;
$query1 = "SELECT ".$table_group.".* FROM ".$table_group." WHERE 1=1 AND ".$table_group.".Status != '3' ";
$res1= mysqli_query($link,$query1);
$totalData = mysqli_num_rows($res1);

$query1 = "SELECT ".$table_group.".* FROM ".$table_group." WHERE 1=1 AND ".$table_group.".Status != '3' ";
if( !empty($requestData['search']['value']) ) 
{   
	$query1.=" AND ( ".$table_group.".Name LIKE '%".$requestData['search']['value']."%' ";    
	$query1.=" OR ".$table_group.".Description LIKE '%".$requestData['search']['value']."%' ) ";
}
$res1= mysqli_query($link,$query1);
//$totalFiltered = mysqli_num_rows($res1); 
$query1.=" ORDER BY ID DESC ";
//$query1.=" LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
$res1=mysqli_query($link,$query1);
$count_admin=0;
$sr_no=0;
$Block_Status_Code = 2;
$data = array();

while($row1=mysqli_fetch_array($res1)) 
{  // preparing an array
	$nestedData=array(); 
	$State="";
	$nurse_Names='<div class="assign-team">
				
	<div>';
	$ID=$row1['ID'];

	$Group_Members=$row1['Group_Members'];
	if($ID!='')
	{
		if($Group_Members==2)
        {
		$query2="SELECT ".$table_group_user.".User_ID,".$table_group_user.".Group_ID,".$table_group.".NAME,
		".$table_group.".Description,".$table_nurse.".First_Name,".$table_nurse.".Last_Name,".$table_nurse.".profile_image FROM ".$table_group_user."
		 LEFT JOIN ".$table_group." ON ".$table_group_user.".Group_ID = ".$table_group.".ID 
		 LEFT JOIN ".$table_nurse." ON ".$table_group_user.".User_ID = ".$table_nurse.".ID 
		 where ".$table_group.".ID=".$ID." AND ".$table_group.".Status != '3' and ".$table_nurse.".Status!=3";

		 $res2= mysqli_query($link,$query2);
		 if(mysqli_num_rows($res1)>0)
		 {
			 while($row2 = mysqli_fetch_array($res2))
			 {
				$Image=$row2['profile_image'];
				$Name=$row2['First_Name'].'_'.$row2['Last_Name'];
				if($Image!="")
				{
					$url="uploads/NurseImage/".$Image;
				}
				else{
					$url="assets/images/user.svg";
				}
				$nurse_Names.='
					 <img class="img-circle thumb-sm m-t-10" alt='.$Name.' src='.$url.' title='.$Name.'>';
		
			 }
		 }	
		}
		else
        {
			$query2="SELECT First_Name,Last_Name,profile_image FROM ".$table_nurse." where status!=3";
			 $res2= mysqli_query($link,$query2);
			 if(mysqli_num_rows($res1)>0)
			 {
				 while($row2 = mysqli_fetch_array($res2))
				 {
				$Image=$row2['profile_image'];
				$Name=$row2['First_Name'].'_'.$row2['Last_Name'];
				if($Image!="")
				{
					$url="uploads/NurseImage/".$Image;
				}
				else{
					$url="assets/images/user.svg";
				}
				$nurse_Names.='
					 <img class="img-circle thumb-sm m-t-10" alt='.$Name.' src='.$url.' title='.$Name.' >';
			
			
				 }
			 }	
		}
		
		$nurse_Names.='</div>
				</div>';
	}
    if($ID>0)
    {
        if($Group_Members==2)
        {
				$query="SELECT COUNT(ID) as Users from ".$table_group_user." where Group_ID=".$ID." AND Status!=3";
				//$query="SELECT COUNT(".$table_group_user.".ID) as Users from ". $table_group_user." LEFT JOIN ". $table_nurse." ON ".$table_nurse.".ID= ".$table_group_user.".User_ID where Group_ID=".$ID." AND ".$table_nurse.".Status!=3";
        }
        else
        {
            $query="SELECT COUNT(ID) as Users from ".$table_nurse." where status!=3";
        }
          
                $res= mysqli_query($link,$query);
                if(mysqli_num_rows($res)>0)
                {
                    while($row = mysqli_fetch_array($res))
                    {
						$count_admin=$row["Users"];
						if($count_admin<=0)
						{
							$totalData--;
						}
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
	$nestedData[] = $ID;
	$nestedData[] = $Name;
	$nestedData[] = $Description;
    $nestedData[] = $count_admin;
	$action="";

		$action.= '<a href="group?id='.$ID.'" class="on-default edit-row edit"><i class="fa fa-pencil"></i></a>';
		$action.= '<a href="javascript:void(0);" class="on-default remove-row delete_btn" data_table_name="User" data_id="'.$ID.'" rel="'.$ID.'" data_status="3"><i class="fa fa-trash-o"></i></a>';
		$data[] = $nestedData;
	

}
}
$totalData=count($data);
$totalFiltered=count($data);
$data=array_slice($data, $requestData['start'], $requestData['length']); 

$json_data = array("draw" => intval( $requestData['draw'] ), "recordsTotal" => intval( $totalData ),  "recordsFiltered" => intval( $totalFiltered ), "aaData" => $data, "query" => $query1, "query2" => $requestData);
 echo json_encode($json_data);  
//echo count($data);
?>
