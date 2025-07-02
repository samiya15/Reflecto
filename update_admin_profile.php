<?php
session_start();
include("include/dbconnect.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 3) {
    header("Location: signin.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $name = $_POST['name'];
    $faculty = $_POST['faculty_name'];

    $stmt = $conn->prepare("UPDATE courseadmin SET course_admin_name=?, faculty_name=? WHERE email=?");
    $stmt->bind_param("sss", $name, $faculty, $email);
    $stmt->execute();
    $stmt->close();

    header("Location: courseadmindash.php");
    exit;
}
?>
