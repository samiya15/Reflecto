<?php
session_start();
include("include/dbconnect.php");

// Make sure the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) {
    header("Location: signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get the submitted data safely
$faculty_name = trim($_POST['faculty_name']);
$course_taught = trim($_POST['course_taught']);
$unit_taught = trim($_POST['unit_taught']);

// Update the lecturer profile
$update = $conn->prepare("
    UPDATE lecturers
    SET faculty_name = ?, course_taught = ?, unit_taught = ?, profile_completed = 1
    WHERE user_id = ?
");
$update->bind_param("sssi", $faculty_name, $course_taught, $unit_taught, $user_id);

if ($update->execute()) {
    // Redirect to lecturer dashboard after successful update
    header("Location: lecdash.php");
    exit();
} else {
    echo "Error saving profile: " . $update->error;
}
?>
