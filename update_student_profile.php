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
    $course_id = !empty($_POST['course_id']) ? intval($_POST['course_id']) : null;
    $year_of_study = !empty($_POST['year_of_study']) ? intval($_POST['year_of_study']) : null;

    // Get course name for reference
    $course_name = "";
    $courseQuery = $conn->prepare("SELECT course_name FROM course WHERE course_id = ?");
    $courseQuery->bind_param("i", $course_id);
    $courseQuery->execute();
    $courseResult = $courseQuery->get_result();
    if ($row = $courseResult->fetch_assoc()) {
        $course_name = $row['course_name'];
    }

    // Check for existing pending update
    $checkStmt = $conn->prepare("SELECT update_id FROM student_updates WHERE user_id = ?");
    $checkStmt->bind_param("i", $user_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        echo "<script>alert('You already have a pending update. Please wait for admin approval.');window.location.href='studentdash.php';</script>";
        exit();
    }

    // Insert new pending update (✅ includes year_of_study)
    $insertStmt = $conn->prepare("
        INSERT INTO student_updates (user_id, faculty_id, course_id, student_course, year_of_study)
        VALUES (?, ?, ?, ?, ?)
    ");
    $insertStmt->bind_param("iiisi", $user_id, $faculty_id, $course_id, $course_name, $year_of_study);

    if ($insertStmt->execute()) {
        echo "<script>alert('Update submitted for approval.');window.location.href='studentdash.php';</script>";
    } else {
        echo "Error: " . $insertStmt->error;
    }
}
?>
