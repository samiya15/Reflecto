<?php
session_start();
include("include/dbconnect.php");

// Get user ID from session
$user_id = $_SESSION['user_id'] ?? null;

// Redirect to login if no session
if (!$user_id) {
    header("Location: signin.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize form inputs
    $first_name = htmlspecialchars($_POST['first_name']);
    $last_name = htmlspecialchars($_POST['last_name']);
    $email = htmlspecialchars($_POST['email']);
    $faculty_name = htmlspecialchars($_POST['faculty_name']);
    $course_taught = htmlspecialchars($_POST['course_taught']);
   $unit_taught = htmlspecialchars($_POST['unit_taught']);

    // Prepare the profile photo
    $profilePath = '';

    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === 0) {
        // Ensure upload folder exists
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        // Generate unique filename
        $filename = uniqid() . "_" . basename($_FILES['profile_photo']['name']);
        $targetFile = $targetDir . $filename;

        if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $targetFile)) {
            $profilePath = $targetFile;
        }
    }

    // If no file was uploaded, use default icon
    if (empty($profilePath)) {
        $profilePath = 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png';
    }

    // Check if profile already exists
    $check = $conn->prepare("SELECT * FROM lecturers WHERE user_id = ?");
    $check->bind_param("i", $user_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        // Update existing record
        $update = $conn->prepare("UPDATE lecturers SET 
            first_name = ?, 
            last_name = ?, 
            email = ?, 
            faculty_name = ?, 
            course_taught = ?, 
            profile_photo = ?,
            unit_taught = ?
            WHERE user_id = ?");
        $update->bind_param(
            "sssssssi",
            $first_name,
            $last_name,
            $email,
            $faculty_name,
            $course_taught,
            $profilePath,
            $unit_taught,
            $user_id
        );
        $update->execute();
    } else {
        // Insert new record
        $insert = $conn->prepare("INSERT INTO lecturers 
            (user_id, first_name, last_name, email, faculty_name, course_taught, profile_photo, unit_taught)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $insert->bind_param(
            "isssssss",
            $user_id,
            $first_name,
            $last_name,
            $email,
            $faculty_name,
            $student_course,
            $unit_taught,
            $profilePath
        );
        $insert->execute();
    }

    // Redirect back to dashboard
    header("Location: lecdash.php");
    exit;
}
?>
