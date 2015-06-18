<?php
require_once(dirname(__FILE__).'/Constants.php');

function connect(){
	$conn = new PDO('mysql:host='.DB_SERVER.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASS, 
		array(PDO::MYSQL_ATTR_FOUND_ROWS => true)); //http://sg3.php.net/manual/en/pdostatement.rowcount.php#104930
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $conn;
}

?>