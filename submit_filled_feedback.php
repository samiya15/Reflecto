<?php
session_start();
header("Content-Type: application/json");
include("include/dbconnect.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit();
}

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

$form_id = intval($data['form_id'] ?? 0);
$unit_id = intval($data['unit_id'] ?? 0);
$student_id = $_SESSION['user_id'];

$scale_answers = $data['scale_answers'] ?? [];
$text_answers = $data['text_answers'] ?? [];

$success = true;

// Prepare insert query
$stmt = $conn->prepare("INSERT INTO form_responses (form_id, unit_id, question_id, student_id, response_text, cleaned_text, sentiment, confidence_score, contains_profanity, submitted_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

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

echo json_encode(["success" => $success]);
