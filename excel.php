<?php
include 'functions.php';
include_once("excel/xlsxwriter.class.php"); //excel library added

$filename = "nurse_ccp_attendance_Report.xlsx";
header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate');
header('Pragma: public');

$group_id=(int)$_GET['id'];
//code required only for excel export
$summary_col_name=array();
$summary_col_detail=array();
$data = array();
$total_amount=0;
$summary_header_name=array('CCP Attendance Nurse');
$data[]=$summary_header_name;
$summary_col_name=array('Sr.No.','Day & Date','Time','Class Type');
$data[]=$summary_col_name;
$requestData= $_REQUEST;

$query1="SELECT ".$table_attendance.".*, ".$table_ccp_class_type.".ID, ".$table_ccp_class_type.".Class_Type 
FROM ".$table_attendance." LEFT JOIN ".$table_ccp_class_type." ON ".$table_attendance.".Class_Type_ID = 
".$table_ccp_class_type.".ID WHERE 1=1 AND ".$table_attendance.".Status != '3' AND 
".$table_attendance.".Login_User_ID=".$group_id; 

$res1= mysqli_query($link,$query1);
$sr_no=0;
while($row=mysqli_fetch_array($res1)) 
{  // preparing an array
	$nestedData=array(); 
	
    $Date=date('d-m-Y',strtotime($row["Class_Date"]));
    $timestamp = strtotime($Date);
    $day = date('l', $timestamp);
    $Time=date("g:i a", strtotime($row["Class_Time"]));
    $Class_Type=$row['Class_Type'];
	$sr_no++;	
	/*
	$name = $Fname;
	if($Lname!="")
	{
		$name.= " ".$Lname;
	}
	*/
	$nestedData[] = $sr_no;
	$nestedData[] = $Date;
	$nestedData[] = $Time;
	$nestedData[] = $Class_Type;

	
	$data[] = $nestedData;
}
mysqli_close($link);
//echo $query1."<br/>";
//echo json_encode($data);

/*
//code required only for excel export
$writer = new XLSXWriter();
$writer->writeSheet($data,'Sheet1');
$writer->writeToFile('nurse_ccp_attendence_Report.xlsx');
header('Content-Type: text');
header('Content-Disposition: attachment; filename=nurse_ccp_attendence_Report.xlsx');
readfile('nurse_ccp_attendence_Report.xlsx');
*/

$writer = new XLSXWriter();
//$writer->setAuthor('Some Author'); 
foreach($data as $row)
	$writer->writeSheetRow('Sheet1', $row);
$writer->writeToStdOut();


?>
