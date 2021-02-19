<?php
include 'functions.php';
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

$query1 = "SELECT ".$table_content.".* FROM ".$table_content." WHERE 1=1 AND ".$table_content.".Status != '3' ";
$res1= mysqli_query($link,$query1);
$totalData = mysqli_num_rows($res1);
$Content_Type=0;
$query1 = "SELECT ".$table_content.".*,".$table_admin.".First_Name,".$table_admin.".Last_Name FROM ".$table_content." LEFT JOIN ".$table_admin." ON ".$table_content.".Login_User_ID = ".$table_admin.".ID WHERE 1=1 AND ".$table_content.".Status != '3' ";
if( !empty($requestData['search']['value']) ) 
{   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$query1.=" AND ( ".$table_content.".Title LIKE '%".$requestData['search']['value']."%' )";    
	if($requestData['search']['value']=="Image")
	{
		$Content_Type=1;
	}
	else if($requestData['search']['value']=="Video")
	{
		$Content_Type=2;
	}
	else if($requestData['search']['value']=="Bulk Image")
	{
		$Content_Type=3;
	}
	else if($requestData['search']['value']=="Text")	
	{	
		$Content_Type=4;	
	}
	if($Content_Type!=0){
	$query1.=" OR ".$table_content.".Content_Type LIKE '%".$Content_Type."%'";
	}
}
$res1= mysqli_query($link,$query1);
$totalFiltered = mysqli_num_rows($res1); 
$query1.=" ORDER BY ID DESC ";
$query1.=" LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
$res1=mysqli_query($link,$query1);
$count_admin=0;
$sr_no=0;
$count_views=0;
$count_likes=0;
$count_comments=0;
$Content_Type=0;

$Block_Status_Code = 2;
$data = array();
$groups="";
$Content="";
while($row1=mysqli_fetch_array($res1)) 
{  // preparing an array
	$nestedData=array(); 
	$State="";
	$ID=$row1['ID'];
	$Posted_By=$row1['First_Name'];
	if($row1['Last_Name']!=''){
	   $Posted_By.= ' '.$row1['Last_Name'];
	}
	$Group_Members=$row1['Group_Members'];
	$Content_Type=$row1["Content_Type"];
	$Entry_Time=$row1["Entry_Time"];
	if($Content_Type==1)
	{
		$Content="Image";
	}
	else if($Content_Type==2)
	{
		$Content="Video";
	}
	else if($Content_Type==3)
	{
		$Content="Bulk Image";
	}
	else if($Content_Type==4)	
	{	
		$Content="Text";	
	}
    if($ID>0)
    {
        if($Group_Members==2)
        {
                $query="SELECT COUNT(ID) as Users from ".$table_content_group." where Status!='3' AND Content_ID=".$ID;
        }
        else
        {
            $query="SELECT COUNT(ID) as Users from ".$table_nurse." where Status!='3'";
        }
                $res= mysqli_query($link,$query);
                if(mysqli_num_rows($res)>0)
                {
                    while($row = mysqli_fetch_array($res))
                    {
                        $count_admin=$row["Users"];
                    }
				}
				$query3="SELECT COUNT(ID) as Likes from ".$table_likes." where Status!='3' AND Content_ID=".$ID;
				$res3= mysqli_query($link,$query3);
                if(mysqli_num_rows($res3)>0)
                {
                    while($row3 = mysqli_fetch_array($res3))
                    {
						$count_likes=$row3["Likes"];
					
                    }
				}
				$query4="SELECT COUNT(ID) as Views from ".$table_views." where Status!='3' AND Content_ID=".$ID;
				$res4= mysqli_query($link,$query4);
                if(mysqli_num_rows($res4)>0)
                {
                    while($row4 = mysqli_fetch_array($res4))
                    {
                        $count_views=$row4["Views"];
                    }
				}
				
				$query6="SELECT COUNT(ID) as Comments from ".$table_comments." where Status!='3' AND Content_ID=".$ID;
				$res6= mysqli_query($link,$query6);
                if(mysqli_num_rows($res6)>0)
                {
                    while($row6 = mysqli_fetch_array($res6))
                    {
                        $count_comments=$row6["Comments"];
                    }
				}
				
				$query5="SELECT DISTINCT ".$table_group.".Name FROM ".$table_content." LEFT JOIN ".$table_content_group." ON  ".$table_content.".ID=" .$table_content_group.".Content_ID 
				LEFT JOIN ".$table_group." ON ".$table_content_group.".Group_ID=".$table_group.".ID WHERE ".$table_content.".ID=".$ID."  AND ".$table_content.".Status!='3'";
				$res5= mysqli_query($link,$query5);
                if(mysqli_num_rows($res5)>0)
                {
                    while($row5 = mysqli_fetch_array($res5))
                    {
						if($row5["Name"]!=NULL){
						$groups.=$row5["Name"];
						if(count($row4>0))
						{
							$groups.=",";
						}
					}
                    }
				}


				
	}
	$ID=$row1['ID'];
	$Name=$row1['Title'];
	$Description=$row1['Description'];	
	$Last_Login=$row1['Last_Login'];	
	$Status=$row1['Status'];	
	$Last_Login_Time = "";
	if(date('Y',strtotime($Last_Login))>2000)
	{
		$Last_Login_Time = date('d-m-Y',strtotime($Last_Login));
	}
	
		
	$nestedData[] = $ID;
	$nestedData[] = $code.str_pad($ID,3, '0', STR_PAD_LEFT);;
	$nestedData[] = $Name;
    $nestedData[] = $count_views;
    $nestedData[] = $count_likes;
    $nestedData[] = $count_comments;    
    $nestedData[] = $groups;
    $nestedData[]=$Content;
	$nestedData[] = $Posted_By;
	$nestedData[] = $Entry_Time;
	$action="";
	$groups="";
	$count_likes=0;
	$count_comments=0;
	
		$action.= '<a href="content?id='.$ID.'" class="on-default edit-row edit" title="Edit"><i class="fa fa-pencil"></i></a>';
		$action.= '<a href="content_detail?id='.$ID.'" class="on-default text-primary" title="View"><i class="fa fa-eye"></i></a>';
		$action.= '<a href="javascript:void(0);" class="on-default remove-rowS delete_btn" data_table_name="User" rel="'.$ID.'" data_id="'.$ID.'"  data_status="3" title="Delete"><i class="fa fa-trash-o"></i></a>';

	$data[] = $nestedData;
}

$json_data = array("draw" => intval( $requestData['draw'] ), "recordsTotal" => intval( $totalData ),  "recordsFiltered" => intval( $totalFiltered ), "aaData" => $data, "query" => $query1, "query2" => $requestData);

echo json_encode($json_data);  // send data as json format
?>
