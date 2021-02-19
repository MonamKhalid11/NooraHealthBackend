<?php 
include('functions.php');
// $hospital_list="";
$query="select * from ".$table_noora_hospital." where status!=3";
$res= mysqli_query($link,$query);
if(mysqli_num_rows($res)>0)
{
	while($row = mysqli_fetch_array($res))
	{
        // $rowcount=mysqli_num_rows($res);
        // $nestedData[] = $row['Name'];
        $data[] = $row['Name'];
   
    }
    // $data[]  = $nestedData;
}
echo json_encode($data);
?>