<?php
//error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_WARNING);
/**
 * A class file to connect to database
 */
class DB_CONNECT 
{
	
    // constructor
    function __construct() 
	{
        // connecting to database
        $this->conn = $this->connect();
    }
	
    // destructor
    function __destruct() {
        // closing db connection
        $this->close();
    }
	
    /**
     * Function to connect with database
     */
    function connect() 
	{
		require_once 'db_config.php';		
		// Connecting to mysql database
        $link = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD,DB_DATABASE);
		$db = mysqli_select_db($link,DB_DATABASE);
        // returing connection cursor
        return $link;
    }
	
    /**
     * Function to close db connection
     */
    function close() {
        // closing db connection
    }
} 
?>