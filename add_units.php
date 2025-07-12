<?php
include("include/dbconnect.php");

$unit_name = $_POST['unit_name'];
$course_id = $_POST['course_id'];

if (!empty($unit_name) && !empty($course_id)) {
    $stmt = $conn->prepare("INSERT INTO units (unit_name, course_id) VALUES (?, ?)");
    $stmt->bind_param("si", $unit_name, $course_id);
    $stmt->execute();
}

header("Location: manage_units.php");
exit();
?>
