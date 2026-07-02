<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // is_fixed = 0 guard prevents deleting the 6 permanent daily tasks
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ? AND is_fixed = 0");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: index.php");
exit;
?>
