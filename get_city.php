<?php
include('functions.php');
$id=$_POST["state_id"];
$city_list="";
$city_list="<option value='0' selected>Select All</option>";
$query1= "SELECT * FROM ".$table_noora_city." WHERE State_ID=".$id;
$res1= mysqli_query($link,$query1);
if(mysqli_num_rows($res1)>0)
{
	while($row1 = mysqli_fetch_array($res1))
	{
        $id=$row1['ID'];
        $name=$row1['Name'];
		$city_list.="<option value='$id'>$name</option>";
	}
}
echo $city_list;
?>