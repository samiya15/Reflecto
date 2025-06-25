<?php
session_start();
include("include/dbconnect.php");

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit;
}
// Logged-in student ID
$user_id = $_SESSION['user_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user_id) {
    $first = $_POST['first_name'];
    $last = $_POST['last_name'];
    $email = $_POST['email'];
    $faculty = $_POST['faculty'];
    $course = $_POST['course'];
    $year = $_POST['year'];

    // Handle profile image upload
    $profilePath = '';
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === 0) {
        $targetDir = "uploads/";
        $filename = uniqid() . "_" . basename($_FILES["profile_photo"]["name"]);
        $targetFile = $targetDir . $filename;

        if (move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $targetFile)) {
            $profilePath = $targetFile;
        }
    }

    // Check if profile exists
    $check = $conn->prepare("SELECT * FROM students WHERE user_id = ?");
    $check->bind_param("i", $user_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        // Update profile
        $update = $conn->prepare("UPDATE students SET first_name=?, last_name=?, email=?, faculty=?, course=?, year=?, profile_photo=? WHERE user_id=?");
        $update->bind_param("sssssssi", $first, $last, $email, $faculty, $course, $year, $profilePath, $user_id);
        $update->execute();
    } else {
        // Create new profile
        $insert = $conn->prepare("INSERT INTO students (user_id, first_name, last_name, email, faculty, course, year, profile_photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $insert->bind_param("isssssss", $user_id, $first, $last, $email, $faculty, $course, $year, $profilePath);
        $insert->execute();
    }

    header("Location: test.php");
    exit;
}
?>

