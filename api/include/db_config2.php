<?php
 
/*
 * All database connection variables
 */
include("setting.php");

$mysql_host = 'localhost';
$mysql_database = 'noora_db';		// live
if($THIS_SERVER_TYPE==$SERVER_TYPE_TEST) {
	$mysql_database = 'noora_demo_db';	// test
}
$mysql_user = 'root';
$mysql_password = "AvB6VMjhq2n1Li";


$mysql_host_second = '35.202.212.14';
$mysql_database_second = 'ccp_details';
$mysql_user_second = 'ccp_champion';
$mysql_password_second = "NooraHealth@123!";


define('DB_USER', $mysql_user); // db user
define('DB_PASSWORD', $mysql_password); // db password (mention your db password here)
define('DB_DATABASE', $mysql_database); // database name
define('DB_SERVER', $mysql_host); // db server / db host

define('DB_USER_SECOND', $mysql_user_second); // db user
define('DB_PASSWORD_SECOND', $mysql_password_second); // db password (mention your db password here)
define('DB_DATABASE_SECOND', $mysql_database_second); // database name
define('DB_SERVER_SECOND', $mysql_host_second); // db server / db host

date_default_timezone_set("Asia/Kolkata");

?>