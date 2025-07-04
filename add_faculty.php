<?php
session_start();
include("include/dbconnect.php");

// Make sure a system admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 4) {
    header("Location: signin.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $facultyName = trim($_POST['faculty_name']);

    if (empty($facultyName)) {
        echo "Faculty name cannot be empty.";
        exit();
    }

    // Insert into faculty table
    $stmt = $conn->prepare("INSERT INTO faculty (faculty_name) VALUES (?)");
    if (!$stmt) {
        echo "Prepare failed: " . $conn->error;
        exit();
    }
    $stmt->bind_param("s", $facultyName);

    if ($stmt->execute()) {
        header("Location: manage_faculties.php");
        exit();
    } else {
        echo "Insert failed: " . $stmt->error;
        exit();
    }
}
?>
