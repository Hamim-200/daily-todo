<?php
// Railway automatically provides these environment variables
// from the linked MySQL service. No hardcoding needed.
$host   = getenv('MYSQLHOST');
$user   = getenv('MYSQLUSER');
$pass   = getenv('MYSQLPASSWORD');
$dbname = getenv('MYSQLDATABASE');
$port   = getenv('MYSQLPORT') ?: 3306;
 
$conn = new mysqli($host, $user, $pass, $dbname, $port);
 
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
 
$conn->set_charset("utf8mb4");
?>