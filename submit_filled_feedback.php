<?php
session_start();
header("Content-Type: application/json");
include("include/dbconnect.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit();
}

// Decode JSON input
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

$form_id = intval($data['form_id'] ?? 0);
$unit_id = intval($data['unit_id'] ?? 0);
$user_id = $_SESSION['user_id'];

// 🔍 Step 1: Get the actual student_id from user_id
$studentQ = $conn->prepare("SELECT student_id FROM students WHERE user_id = ?");
$studentQ->bind_param("i", $user_id);
$studentQ->execute();
$studentRes = $studentQ->get_result()->fetch_assoc();
$student_id = $studentRes['student_id'] ?? 0;

if (!$student_id) {
    echo json_encode(["success" => false, "message" => "Student record not found"]);
    exit();
}

// Extract responses
$scale_answers = $data['scale_answers'] ?? [];
$text_answers = $data['text_answers'] ?? [];

$success = true;

// Step 2: Insert responses
$stmt = $conn->prepare("INSERT INTO form_responses 
(form_id, unit_id, question_id, student_id, response_text, cleaned_text, sentiment, confidence_score, contains_profanity, submitted_at)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

foreach ($scale_answers as $question_id => $value) {
    $stmt->bind_param("iiissssdi", $form_id, $unit_id, $question_id, $student_id, $value, $null = null, $null = null, $null = null, $zero = 0);
    if (!$stmt->execute()) {
        $success = false;
    }
}

foreach ($text_answers as $question_id => $entry) {
    $stmt->bind_param(
        "iiissssdi",
        $form_id,
        $unit_id,
        $question_id,
        $student_id,
        $entry['original_text'],
        $entry['cleaned_text'],
        $entry['sentiment'],
        $entry['confidence_score'],
        $entry['contains_profanity']
    );
    if (!$stmt->execute()) {
        $success = false;
    }
}

// Step 3: Insert into submitted_feedback to prevent re-show
$submitLog = $conn->prepare("INSERT INTO submitted_feedback (form_id, unit_id, student_id, submitted_at) VALUES (?, ?, ?, NOW())");
$submitLog->bind_param("iii", $form_id, $unit_id, $student_id);
if (!$submitLog->execute()) {
    $success = false;
}

echo json_encode(["success" => $success]);
?>
