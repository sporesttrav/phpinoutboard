<?
if (!defined('BASE_FOLDER')) { define('BASE_FOLDER', dirname(__FILE__)); }

$db = new mysqli("localhost","dbuser","dbpass","db");

if (mysqli_connect_errno())
{
	printf("Connection failed: %s\n", mysqli_connect_error());
	exit();
}

require_once(BASE_FOLDER . "/include/validate.php");

$subnet = "12700";
?>