<?php
include("include/dbconnect.php");

$course_name = $_POST['course_name'];
$faculty_id = $_POST['faculty_id'];

if (!empty($course_name) && !empty($faculty_id)) {
    $stmt = $conn->prepare("INSERT INTO course (course_name, faculty_id) VALUES (?, ?)");
    $stmt->bind_param("si", $course_name, $faculty_id);
    $stmt->execute();
}

header("Location: manage_courses.php");
exit();
?>
