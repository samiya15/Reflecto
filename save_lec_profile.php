<?php
session_start();
include("include/dbconnect.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) {
    header("Location: signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Validate input
if (
    !isset($_POST['faculty_ids']) ||
    empty($_POST['course_taught']) ||
    empty($_POST['unit_taught'])
) {
    echo "All fields are required.";
    exit();
}

$faculty_ids = $_POST['faculty_ids'];
if (!is_array($faculty_ids)) {
    echo "Invalid faculty selection.";
    exit();
}

// Sanitize IDs
$faculty_ids = array_map('intval', $faculty_ids);

$course_taught = trim($_POST['course_taught']);
$unit_taught = trim($_POST['unit_taught']);

// Update lecturer record
$update = $conn->prepare("
    UPDATE lecturers
    SET course_taught = ?, unit_taught = ?, profile_completed = 1
    WHERE user_id = ?
");
$update->bind_param("ssi", $course_taught, $unit_taught, $user_id);
$update->execute();

// Get lecturer_id
$getLecturerId = $conn->prepare("SELECT lecturer_id FROM lecturers WHERE user_id = ?");
$getLecturerId->bind_param("i", $user_id);
$getLecturerId->execute();
$result = $getLecturerId->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    echo "Lecturer not found.";
    exit();
}

$lecturer_id = $row['lecturer_id'];

// Remove existing faculty associations
$deleteFac = $conn->prepare("DELETE FROM lecturer_faculties WHERE lecturer_id = ?");
$deleteFac->bind_param("i", $lecturer_id);
$deleteFac->execute();

// Insert selected faculties
$insertFac = $conn->prepare("INSERT INTO lecturer_faculties (lecturer_id, faculty_id) VALUES (?, ?)");
foreach ($faculty_ids as $faculty_id) {
    $insertFac->bind_param("ii", $lecturer_id, $faculty_id);
    $insertFac->execute();
}

header("Location: lecdash.php");
exit();
?>
