<?php
session_start();
include("include/dbconnect.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) {
    header("Location: signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get lecturer_id
$lecStmt = $conn->prepare("SELECT lecturer_id FROM lecturers WHERE user_id = ?");
$lecStmt->bind_param("i", $user_id);
$lecStmt->execute();
$lecResult = $lecStmt->get_result();
$lecRow = $lecResult->fetch_assoc();
$lecturer_id = $lecRow['lecturer_id'] ?? null;

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
    $type = $q['type'] === 'scale' ? 'scale' : 'text';
    if (!empty($text)) {
        $insertQ->bind_param("iiss", $form_id, $lecturer_id, $text, $type);
        $insertQ->execute();
    }
}

// Save the form assignment and PUBLISH it (set is_published = 1)
$insertForm = $conn->prepare("INSERT INTO lecturer_feedback_forms (form_id, lecturer_id, assigned_course_id, assigned_unit_id, is_published)
    VALUES (?, ?, ?, ?, 1)
");
$insertForm->bind_param("iiii", $form_id, $lecturer_id, $course_id, $unit_id);
$insertForm->execute();

header("Location: lecdash.php?msg=Form customized and published successfully");
exit();
?>
