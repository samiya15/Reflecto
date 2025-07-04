<?php
session_start();
include("include/dbconnect.php");

// Only system admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 4) {
    header("Location: signin.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "Invalid request.";
    exit();
}

$faculty_id = (int)$_GET['id'];

// Delete the faculty
$delete = $conn->prepare("DELETE FROM faculty WHERE faculty_id = ?");
$delete->bind_param("i", $faculty_id);
if ($delete->execute()) {
    header("Location: manage_faculties.php");
    exit();
} else {
    echo "Delete failed: " . $delete->error;
    exit();
}
?>
