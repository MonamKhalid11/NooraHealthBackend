<?php
include('functions.php');
$id=$_POST["Content_ID"];
$Attachment=$_POST["Attachment"];
$query1="UPDATE ".$table_content_attachment." SET Status='3' WHERE Content_ID=".$id." and Attachment='$Attachment'";
$res1= mysqli_query($link,$query1);
if($res1)
{
    echo 1;
}
else
{
    echo 2;
}
?>