<?php


//EDIT ME PLEASE
//THEN RENAME ME TO dbconnect.php
//THANKS

$servername = "123.123.123.123";
$username = "user";
$password = "password";
$dbname = "labtech";

define("DB_HOST", "123.123.123.123");
define("DB_NAME", "user");
define("DB_USER", "password");
define("DB_PASS", "labtech");

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$pdo = new PDO("mysql:host=123.123.123.123;dbname=labtech", 'user', 'password');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>
