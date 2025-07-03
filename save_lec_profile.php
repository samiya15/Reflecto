<?php
session_start();
include("include/dbconnect.php");

// Make sure a lecturer is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) {
    header("Location: signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Retrieve form data safely
$faculty_name = trim($_POST['faculty_name']);
$course_taught = trim($_POST['course_taught']);
$unit_taught = trim($_POST['unit_taught']);

// Simple validation
if (empty($faculty_name) || empty($course_taught) || empty($unit_taught)) {
    echo "All fields are required.";
    exit();
}

// Check if the lecturer record already exists
$check_stmt = $conn->prepare("SELECT lecturer_id FROM lecturers WHERE user_id = ?");
$check_stmt->bind_param("i", $user_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    // Update existing record
    $update_stmt = $conn->prepare("
        UPDATE lecturers 
        SET faculty_name = ?, course_taught = ?, unit_taught = ?, verification_status = ?
        WHERE user_id = ?
    ");
    $status = "Pending";
    $update_stmt->bind_param("ssssi", $faculty_name, $course_taught, $unit_taught, $status, $user_id);
    if ($update_stmt->execute()) {
        header("Location: lecdash.php?success=updated");
        exit();
    } else {
        echo "Error updating profile: " . $update_stmt->error;
        exit();
    }
} else {
    // Insert new record
    $insert_stmt = $conn->prepare("
        INSERT INTO lecturers 
        (user_id, faculty_name, course_taught, unit_taught, verification_status)
        VALUES (?, ?, ?, ?, ?)
    ");
    $status = "Pending";
    $insert_stmt->bind_param("issss", $user_id, $faculty_name, $course_taught, $unit_taught, $status);
    if ($insert_stmt->execute()) {
        header("Location: lecdash.php?success=created");
        exit();
    } else {
        echo "Error saving profile: " . $insert_stmt->error;
        exit();
    }
}
?>
