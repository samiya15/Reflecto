<?php
session_start();
include("include/dbconnect.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) {
    header("Location: signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $faculty = $_POST['faculty_name'];
    $courses = $_POST['course_taught'];
    $units = $_POST['unit_taught'];

    $update = $conn->prepare("
        UPDATE lecturers
        SET faculty_name = ?, course_taught = ?, unit_taught = ?, profile_completed = 1
        WHERE user_id = ?
    ");
    $update->bind_param("sssi", $faculty, $courses, $units, $user_id);
    if ($update->execute()) {
        header("Location: lecdash.php");
        exit();
    } else {
        echo "Update failed: " . $conn->error;
    }
}
?>
