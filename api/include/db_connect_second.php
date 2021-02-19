<?php
/**
 * A class file to connect to database
 */
class DB_CONNECT 
{
	
    // constructor
    function __construct() 
	{
		require_once 'db_config2.php';	
        // connecting to database
        $this->conn = $this->connect();
        $this->conn2 = $this->connectSecond();
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
		// Connecting to mysql database
        $link = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD,DB_DATABASE);
		$db = mysqli_select_db($link,DB_DATABASE);
        // returing connection cursor
        return $link;
    }
	
    /**
     * Function to connect with database
     */
    function connectSecond() 
	{	
		// Connecting to mysql database
        $link = mysqli_connect(DB_SERVER_SECOND, DB_USER_SECOND, DB_PASSWORD_SECOND,DB_DATABASE_SECOND);
		$db = mysqli_select_db($link,DB_DATABASE_SECOND);
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