<?php
include("include/dbconnect.php");

$unit_name = $_POST['unit_name'] ?? '';
$course_id = $_POST['course_id'] ?? '';
$year = $_POST['year_of_study'] ?? '';

if (!empty($unit_name) && !empty($course_id) && !empty($year)) {
    $stmt = $conn->prepare("INSERT INTO units (unit_name, course_id, year_of_study) VALUES (?, ?, ?)");
    $stmt->bind_param("sii", $unit_name, $course_id, $year);
    $stmt->execute();
}

header("Location: manage_units.php");
exit();
?>
