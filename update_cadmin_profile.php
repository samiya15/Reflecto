<?php
session_start();
include("include/dbconnect.php");

// Make sure only course admins can update
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 3) {
    header("Location: signin.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Validate form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $faculty_name = trim($_POST['faculty_name']);
    $email = trim($_POST['email']);

    if (empty($name) || empty($email)) {
        echo "Name and Email are required.";
        exit;
    }

    // Update courseadmin table
    $stmt = $conn->prepare("
        UPDATE courseadmin 
        SET course_admin_name = ?, faculty_name = ?
        WHERE email = ?
    ");

    if (!$stmt) {
        echo "Prepare failed: " . $conn->error;
        exit;
    }

    $stmt->bind_param("sss", $name, $faculty_name, $email);

    if ($stmt->execute()) {
        // Success
        header("Location: courseadmin.php?updated=1");
        exit;
    } else {
        echo "Update failed: " . $stmt->error;
    }
}
?>
