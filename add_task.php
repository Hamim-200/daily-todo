<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty(trim($_POST['task_name'] ?? ''))) {
    $task_name = trim($_POST['task_name']);
    $today = date('Y-m-d');

    $stmt = $conn->prepare("INSERT INTO tasks (task_name, task_date, is_fixed) VALUES (?, ?, 0)");
    $stmt->bind_param("ss", $task_name, $today);
    $stmt->execute();
}

header("Location: index.php");
exit;
?>
