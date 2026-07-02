<?php
// Database connection settings.
// For XAMPP (local) these defaults work out of the box.
// For a live host (e.g. InfinityFree), replace these 4 values with the
// ones shown in your hosting control panel.

// $host   = "localhost";
// $user   = "root";
// $pass   = "";
// $dbname = "todo_app";

// $conn = new mysqli($host, $user, $pass, $dbname);

// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }

// $conn->set_charset("utf8mb4");
// ?>





$host   = "sql101.infinityfree.com";
$user   = "if0_42306960";
$pass   = "2107hamimahmed";
$dbname = "if0_42306960_todo_app";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>
