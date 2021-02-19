<?php
session_start();

setcookie("selectedItem", 1);
unset($_COOKIE['login_adminname']);
unset($_COOKIE['login_adminid']);
unset($_COOKIE['login_adminrole']);
{
	setcookie('login_adminname', null, -1, '/');
    setcookie('login_adminid', null, -1, '/');
    setcookie('login_adminrole', null, -1, '/');
	session_destroy();
	header("Location: index.php");
}
?>