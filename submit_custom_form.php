<?php
session_start();
include("include/dbconnect.php");

// Ensure lecturer is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) {
    header("Location: signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch lecturer ID
$stmt = $conn->prepare("SELECT lecturer_id FROM lecturers WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$lecturer_id = $row['lecturer_id'];

// Validate and fetch POST data
$form_id = intval($_POST['form_id']);
$course_id = intval($_POST['assigned_course_id']);
$unit_id = intval($_POST['assigned_unit_id']);
$questions = $_POST['questions'] ?? [];

if (!$form_id || !$course_id || !$unit_id) {
    die("All fields are required.");
}

// Insert each custom question
$insertQ = $conn->prepare("INSERT INTO lecturer_form_questions (form_id, lecturer_id, question_text, question_type)
    VALUES (?, ?, ?, ?)
");

foreach ($questions as $q) {
    $text = trim($q['text']);
    $type = $q['type'] === 'scale' ? 'scale' : 'text'; // enforce enum values

    if (!empty($text)) {
        $insertQ->bind_param("iiss", $form_id, $lecturer_id, $text, $type);
        $insertQ->execute();
    }
}

// Insert form assignment to course/unit (unpublished by default)
$insertForm = $conn->prepare(" INSERT INTO lecturer_feedback_forms (form_id, lecturer_id, assigned_course_id, assigned_unit_id, is_published)
    VALUES (?, ?, ?, ?, 0)
");
$insertForm->bind_param("iiii", $form_id, $lecturer_id, $course_id, $unit_id);
$insertForm->execute();

header("Location: lecdash.php?msg=Form customized successfully");
exit();
?>
