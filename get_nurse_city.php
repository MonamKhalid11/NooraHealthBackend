<?php
include('functions.php');
$id=$_POST["country_id"];
$entry_id=$_POST["entry_id"];

$query1= "select ".$table_nurse.".ID,".$table_nurse.".First_Name,".$table_nurse.".Last_Name,".$table_noora_country.".Name as CountryName,
".$table_noora_state.".Name as StateName,".$table_noora_hospital.".name as HospitalName, ".$table_group_user.".User_ID FROM ".$table_nurse." 
LEFT JOIN ".$table_noora_city." ON ".$table_nurse.".City_ID = ".$table_noora_city.".ID 
LEFT JOIN ".$table_noora_hospital." ON ".$table_nurse.".Hospital_ID = ".$table_noora_hospital.".ID 
LEFT JOIN ".$table_noora_state." ON ".$table_noora_city.".State_ID = ".$table_noora_state.".ID 
LEFT JOIN ".$table_noora_country." ON ".$table_noora_state.".Country_ID = ".$table_noora_country.".ID
LEFT JOIN ".$table_group_user." ON (".$table_nurse.".ID = ".$table_group_user.".User_ID AND ".$table_group_user.".Group_ID= ".$entry_id." )
where 1=1 and ".$table_noora_state.".ID=".$id." and ".$table_nurse.".Status!=3";
// echo $query1;
$res1= mysqli_query($link,$query1);

if(mysqli_num_rows($res1)>0)
{
    $data = array();
	while($row1 = mysqli_fetch_array($res1))
	{
		$nestedData=array();
		$First_Name=$row1['First_Name'];
		$Name = $First_Name;
		$User_ID_Group=$row1["User_ID"];
		$Last_Name=$row1['Last_Name'];	
		$ID=$row1['ID'];
        if($Last_Name!="")
        {
            $Name.= " ".$Last_Name;
		}
		$Hospital_Name=$row1["HospitalName"];
		$nestedData[] = $Name;
		$nestedData[] = $Hospital_Name;
        $action=$User_ID_Group;
        // if($User_ID_Group==NULL)
		// 	{
        //         $action=$ID;	
        //     }
		// 	else
		// 	{
		// 		$action=$User_ID_Group;
		// 	}
        $nestedData[] = $action;
		$nestedData[] = $ID;

        $data[] = $nestedData;
	}
}
echo json_encode($data);
?>