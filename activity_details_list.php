<?php
include 'functions.php';
$requestData= $_REQUEST;
$totalData=0;
$totalFiltered=0;
 $session_id=$requestData['session'];
 $subquery="";
 if($requestData['type']>0)
 {
 $typeId=$requestData['type'];
 $subquery=" AND ".$table_nurse_history.".History_Type_Id=".$typeId;
 }
$query1 = "SELECT ".$table_nurse_history.".*,".$table_content.".Title,".$table_noora_history_type.".Title AS Type FROM ".$table_nurse_history ." LEFT JOIN ". $table_noora_history_type." on ".$table_nurse_history.".History_Type_Id="
.$table_noora_history_type.".ID left join ".$table_content ." on ".$table_nurse_history.".Content_ID=".$table_content.".ID where 
session_id='$session_id' AND ".$table_nurse_history.".NurseID=".$requestData['id'].$subquery;
$res1= mysqli_query($link,$query1);
 if( !empty($requestData['search']['value']) ) 
{   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$query1.=" AND ( ".$table_nurse_history.".Entry_Time LIKE '%".$requestData['search']['value']."%' ";    
	$query1.=" OR ".$table_noora_history_type.".Title LIKE '%".$requestData['search']['value']."%' ";
	$query1.=" OR ".$table_content.".Title LIKE '%".$requestData['search']['value']."%' )";
} 
$res1= mysqli_query($link,$query1);
 
$totalFiltered=mysqli_num_rows($res1);

$sr_no=0;
$Block_Status_Code = 2;
$data = array();
while($row1=mysqli_fetch_array($res1)) 
{
   // print_r($row1);
    $nestedData=array();
    $Entry_Date = date('d M, Y',strtotime($row1['Entry_Time']));
	$Entry_Time= date("g:i:s A", strtotime($row1['Entry_Time']));
    if($row1["History_Type_Id"]==4)
    {	
        $nestedData[] = "Liked ";
        $nestedData[] = $Entry_Date.",".$Entry_Time;
      
    }
    if($row1["History_Type_Id"]==3)
    {
        $type="";
        $sql="SELECT ".$table_ccp_class_type.".Class_Type AS Type from ".$table_ccp_class_type." LEFT JOIN ".$table_attendance." ON "
        .$table_attendance.".Class_Type_ID=".$table_ccp_class_type.".ID WHERE ".$table_attendance.".ID=".$row1["Content_ID"];
        $res3= mysqli_query($link,$sql);
		if(mysqli_num_rows($res3)>0)
		{
			$row3=mysqli_fetch_array($res3);
			$type=$row3["Type"];
		}
        $nestedData[] = "CCP Attendance, Type : ". $type;
        $nestedData[] = $Entry_Date.",".$Entry_Time;
        
    }
    if($row1["History_Type_Id"]==5)
    {
    $title="";
		$sql="SELECT ".$table_content.".Description AS Content_Title from ".$table_content." LEFT JOIN ".$table_comments." ON "
		.$table_comments.".Content_ID=".$table_content.".ID WHERE ".$table_comments.".ID=".$row1["Content_ID"];
		$res3= mysqli_query($link,$sql);
		if(mysqli_num_rows($res3)>0)
		{
			$row3=mysqli_fetch_array($res3);
			$title=$row3["Content_Title"];
		}
	
        $nestedData[] = "Commented on Content : ".$title;
        $nestedData[] = $Entry_Date.",".$Entry_Time;
       
    } 
    if(count($nestedData)>0)
    {
        $data[]=$nestedData;
    }
//print_r($data);
	//
}
$totalData = count($data); 
$totalFiltered=count($data);
$data=array_slice($data, $requestData['start'], $requestData['length']); 

$json_data = array("draw" => intval( $requestData['draw'] ), "recordsTotal" => intval( $totalData ),  "recordsFiltered" => intval( $totalFiltered ), "aaData" => $data, "query" => $query1, "query2" => $requestData);

echo json_encode($json_data);  // send data as json format
?>
 