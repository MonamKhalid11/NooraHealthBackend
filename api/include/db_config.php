<?php
 
/*
 * All database connection variables
 */
include("setting.php");
 
/*
//new mysql
*/

/*
$mysql_host = "localhost";
$mysql_database = "noora";
$mysql_user = "root";
$mysql_password = "";
*/

/*
$mysql_host = "androcid-8";
$mysql_database = "noora";
$mysql_user = "root";
$mysql_password = "androcid";
*/

$mysql_host = 'localhost';
$mysql_database = 'noora';
$mysql_user = 'root';
$mysql_password = "";


$mysql_host = 'localhost';
$mysql_database = 'noora';		// live
if($THIS_SERVER_TYPE==$SERVER_TYPE_TEST) {
	$mysql_database = 'noora_demo_db';	// test
}
$mysql_user = 'root';
$mysql_password = "";


define('DB_USER', $mysql_user); // db user
define('DB_PASSWORD', $mysql_password); // db password (mention your db password here)
define('DB_DATABASE', $mysql_database); // database name
define('DB_SERVER', $mysql_host); // db server / db host

//LIVE
define('FCM_KEY', "AAAAcG4RftY:APA91bG_kDXavTdqydM65eo9IY7w7udBoGD74Z79GLZ0Ls1_8Rf4TJd6gIpJ9xRSblEhcBV7IZ3GGUdwmQ2pnNJqpY-z6RWKt8M8skxj4jmuU5u8taTkjFL-hWj6VfsaDHUST3sQt3WU"); 

//TEST
//define('FCM_KEY', "AAAAw_qyqYQ:APA91bHL6WYSPl5XEXE-0pzrfNVoQ_6DjVGr3arfbebzeR7bqBylns8WkUL4LtWiT86CxZoabJr8eXdYngt2kIqZkqUQQYL65xZDY12EzIZPMfS3XkgUp0ZQlD9iAjmYhvope-FKguHj"); 

date_default_timezone_set("Asia/Kolkata");

?>