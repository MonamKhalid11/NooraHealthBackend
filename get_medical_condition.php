<?php
include('functions.php');
$id=$_POST["hospital"];
$state_list="<option value='0' selected disabled>Select</option>";
// $query1= "SELECT * FROM ".$table_noora_hospital_medical_condition." WHERE Hospital_ID=".$id;
$query1= "SELECT * FROM ".$table_noora_hospital_medical_condition;

$res1= mysqli_query($link,$query1);
if(mysqli_num_rows($res1)>0)
{
	while($row1 = mysqli_fetch_array($res1))
	{
        $id=$row1['ID'];
        $name=$row1['Medical_Condition'];
		$state_list.="<option value='$id'>$name</option>";
	}
}
echo $state_list;
?>