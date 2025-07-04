<?php
session_start();
include("include/dbconnect.php");

// Ensure user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: signin.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $faculty_id = !empty($_POST['faculty_id']) ? intval($_POST['faculty_id']) : null;
    $course_name = trim($_POST['course_name'] ?? '');

    // Optional: Validate course_name length
    if (strlen($course_name) < 2) {
        echo "<script>alert('Course name must be at least 2 characters.');window.history.back();</script>";
        exit();
    }

    // Check if there is already a pending update for this student
    $checkStmt = $conn->prepare("SELECT update_id FROM student_updates WHERE user_id = ?");
    $checkStmt->bind_param("i", $user_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        // Already pending update - prevent duplicates
        echo "<script>alert('You already have a pending update. Please wait for admin approval.');window.location.href='studentdash.php';</script>";
        exit();
    }

    // Insert the new pending update
    $insertStmt = $conn->prepare("
        INSERT INTO student_updates (user_id, faculty_id, student_course)
        VALUES (?, ?, ?)
    ");
    if (!$insertStmt) {
        echo "Prepare failed: " . $conn->error;
        exit();
    }

    $insertStmt->bind_param("iis", $user_id, $faculty_id, $course_name);

    if ($insertStmt->execute()) {
        echo "<script>alert('Update submitted for approval.');window.location.href='studentdash.php';</script>";
        exit();
    } else {
        echo "Error submitting update: " . $insertStmt->error;
    }
}
?>
