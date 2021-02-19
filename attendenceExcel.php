<?php
include 'functions.php';
include_once("excel/xlsxwriter.class.php"); //excel library added

$date=date('d-m-y  H-i-s');
$filename = "ccp_attendance_Report-".$date.".xlsx";
header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate');
header('Pragma: public');


$group_id=(int)$_GET['id'];
date_default_timezone_set("Asia/Kolkata");
$dateNow = date("Y-m-d H:i:s"); 
//code required only for excel export
$summary_col_name=array();
$summary_col_detail=array();
$data = array();
$total_amount=0;
$summary_header_name=array('CCP Attendance');
$data[]=$summary_header_name;
$summary_col_name=array('Sr.No.','Nurse Name','Hospital Name','Day & Date','Time','Class Type','Number of People');
$data[]=$summary_col_name;
$requestData= $_REQUEST;

$query1="SELECT Class_Date,Class_Time,Class_Type_ID,No_of_People,".$table_nurse.".First_Name,
".$table_nurse.".Last_Name,".$table_ccp_class_type.".Class_Type,".$table_noora_hospital.".Name FROM noora_ccp_attendance
  LEFT JOIN ".$table_nurse." ON ".$table_attendance." .Login_User_ID = ".$table_nurse.".ID 
  LEFT JOIN ".$table_ccp_class_type." on ".$table_attendance.".Class_Type_ID=".$table_ccp_class_type.".ID 
  LEFT JOIN ".$table_noora_hospital." on ".$table_nurse.".Hospital_ID=".$table_noora_hospital.".ID 
  where ".$table_attendance.".status!=3 AND ".$table_nurse.".status!=3 order by ".$table_attendance.".ID Desc";

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
	$No_of_people=$row['No_of_People'];
	$First_Name=$row['First_Name'];
	$Last_Name=$row['Last_Name'];
	$Hospital_Name=$row["Name"];
	$sr_no++;	
	$Name = $First_Name;
	if($Last_Name!="")
	{
		$Name.= " ".$Last_Name;
	}
	$nestedData[] = $sr_no;
	$nestedData[] = $Name;
	$nestedData[] = $Hospital_Name;
	$nestedData[] = $Date;
	$nestedData[] = $Time;
	$nestedData[] = $Class_Type;
	$nestedData[] = $No_of_people;

	
	$data[] = $nestedData;
}
mysqli_close($link);

/*
$writer = new XLSXWriter();
$writer->writeSheet($data,'Sheet1');
$writer->writeToFile('ccp_attendence_Report.xlsx');
header('Content-Type: text');
header('Content-Disposition: attachment; filename=ccp_attendence_Report_'.$dateNow.'.xlsx');
readfile('ccp_attendence_Report.xlsx');
*/

$writer = new XLSXWriter();
//$writer->setAuthor('Some Author'); 
foreach($data as $row)
	$writer->writeSheetRow('Sheet1', $row);
$writer->writeToStdOut();


?>
