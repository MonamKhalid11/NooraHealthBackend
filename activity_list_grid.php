<?php
include 'functions.php';
$requestData= $_REQUEST;
//echo $requestData;
$data=[];
$totalData=0;
$totalFiltered=0;
$query1="SELECT DISTINCT ".$table_nurse_history.".session_id,".$table_nurse_history.".Entry_Time from ". $table_nurse_history." where ".$table_nurse_history.".NurseID=".$requestData['id']." ORDER BY ".$table_nurse_history.".Entry_Time DESC";
$res1= mysqli_query($link,$query1);
if( mysqli_num_rows($res1)>0)
{
while($row1=mysqli_fetch_array($res1)) 
{
	$session_id=$row1['session_id'];
	if($session_id!='')
	{
	$query2 = "SELECT ".$table_nurse_history.".*,DATE_FORMAT(".$table_nurse_history.".Entry_Time,'%H:%i:%S') AS TIME,".$table_noora_history_type.".Title, ".$table_noora_history_type.".Image, 
	".$table_noora_history_type.".Description FROM `".$table_nurse_history."` LEFT JOIN  ".$table_noora_history_type." ON 
	".$table_nurse_history.".History_Type_Id =  ".$table_noora_history_type.".Id LEFT JOIN ".$table_content." ON ".$table_nurse_history.".Content_ID=".$table_content.".ID where ".$table_nurse_history.".NurseID=".$requestData['id']." AND ".
	$table_nurse_history.".session_id='$session_id'";
	
	$res2= mysqli_query($link,$query2);
	//echo "count".mysqli_num_rows($res2);
	
	if( !empty($requestData['search']['value']) ) 
	{    
		$query2.=" AND ( ".$table_noora_history_type.".Description LIKE '%".$requestData['search']['value']."%' ";
		$query2.=" OR ".$table_nurse_history.".Entry_Time LIKE '%".date('Y-m-d',strtotime($requestData['search']['value']))."%' ";
		$query2.=" OR ".$table_noora_history_type.".Title LIKE '%".$requestData['search']['value']."%' ) ";
		
	}
	$res2= mysqli_query($link,$query2);
	/* $query2.=" LIMIT ".$requestData['start']." ,".$requestData['length'];
	$res2=mysqli_query($link,$query2); */
	$sr_no=0;
	$Block_Status_Code = 2;
	$data = array();
	$count_activity=0;
	while($row2=mysqli_fetch_array($res2)) 
	{
		if($row2["History_Type_Id"]!=1 && $row2["History_Type_Id"]!=2)
		{
			$count_activity++;
		}
		$Entry_Date = date('d M, Y',strtotime($row2['Entry_Time']));
		$Entry_Time= date("g:i:s A", strtotime($row2['Entry_Time']));
		$output[$row2["session_id"]]["Count"]=$count_activity;
		if($row2['Description']=="Session Ended")
		{
			$output[$row2["session_id"]]["End"]=$row2['Description'];
			$output[$row2["session_id"]]["End_Time"]=$Entry_Time;
			$output[$row2["session_id"]]["End_Date"]=$Entry_Date;
			$output[$row2["session_id"]]["End_Title"]=$row2['Title'];
		}
		if($row2['Description']=="Session Started")
		{
			$output[$row2["session_id"]]["Start"]=$row2['Description'];
			$output[$row2["session_id"]]["Start_Time"]=$Entry_Time;
			$output[$row2["session_id"]]["Start_Date"]=$Entry_Date;
			$output[$row2["session_id"]]["Start_Title"]=$row2['Title'];
		}
	}
}
}
$totalFiltered=count($output);
$totalData=count($output);
foreach($output as $key=>$value)
{
	$session=$key;
	$nestedData=array(); 
	$start_stmt=$value["Start"];
	$end_stmt=$value["End"];
	if($value["Start_Date"]!="")
	{
		$start_stmt.= " : ".$value["Start_Date"].",";
	}
	if($value["Start_Time"]!="")
	{
		$start_stmt.= $value["Start_Time"];
	}
	if($value["End_Date"]!="")
	{
		$end_stmt.= " : ".$value["End_Date"].",";
	}
	
	if($value["End_Time"]!="")
	{
		$end_stmt.= $value["End_Time"];
	}
	if($end_stmt=="")
	{
		$end_stmt="Session not yet ended ";
	}
	$nestedData[]=$start_stmt;
	$nestedData[]=$end_stmt;
	if($value["Count"]>0)
	{
	$nestedData[]='<a href="javascript:void(0);" class="activities" data_id='.$requestData['id'].' rel="'.$key.'">'.$value["Count"].' Activities </a>';
	}
	else
	{
		$nestedData[]=$value["Count"].' Activities';
	}
	$data[] = $nestedData;
}
}
$data=array_slice($data, $requestData['start'], $requestData['length']); 
$json_data = array("draw" => intval( $requestData['draw'] ), "recordsTotal" => intval( $totalData ),  "recordsFiltered" => intval( $totalFiltered ), "aaData" => $data, "query" => $query2, "query2" => $requestData);

echo json_encode($json_data);  // send data as json format
?>
