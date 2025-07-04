<?php
session_start();
include("include/dbconnect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_id'])) {
    $update_id = intval($_POST['update_id']);

    $stmt = $conn->prepare("DELETE FROM student_updates WHERE update_id = ?");
    $stmt->bind_param("i", $update_id);
    $stmt->execute();

    header("Location: manage_student_updates.php");
    exit();
}
?>
