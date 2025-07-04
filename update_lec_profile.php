<?php
session_start();
include("include/dbconnect.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) {
    header("Location: signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (
    empty($_POST['name']) ||
    empty($_POST['course_taught']) ||
    empty($_POST['unit_taught'])
) {
    echo "All fields are required.";
    exit();
}

// Split name
$name_parts = explode(' ', trim($_POST['name']));
$firstName = $name_parts[0];
$lastName = isset($name_parts[1]) ? $name_parts[1] : '';

$course_taught = trim($_POST['course_taught']);
$unit_taught = trim($_POST['unit_taught']);

// Update users table
$updateUser = $conn->prepare("UPDATE users SET firstName = ?, lastName = ? WHERE user_id = ?");
$updateUser->bind_param("ssi", $firstName, $lastName, $user_id);
$updateUser->execute();

// Update lecturers table
$updateLecturer = $conn->prepare("UPDATE lecturers SET course_taught = ?, unit_taught = ? WHERE user_id = ?");
$updateLecturer->bind_param("ssi", $course_taught, $unit_taught, $user_id);
$updateLecturer->execute();

header("Location: lecdash.php");
exit();
?>
