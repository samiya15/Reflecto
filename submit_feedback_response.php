<?php
session_start();
include("include/dbconnect.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) {
    header("Location: signin.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["feedback_id"], $_POST["response_text"])) {
    header("Location: lecturer_feedback.php");
    exit();
}

$feedback_id = intval($_POST["feedback_id"]);
$response_text = trim($_POST["response_text"]);

$stmt = $conn->prepare("SELECT lecturer_id FROM lecturers WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$lecturer = $result->fetch_assoc();

if (!$lecturer) {
    die("Lecturer not found.");
}
$lecturer_id = $lecturer["lecturer_id"];

// Save response
$ins = $conn->prepare("
    INSERT INTO feedback_responses (feedback_id, lecturer_id, response_text)
    VALUES (?, ?, ?)
");
$ins->bind_param("iis", $feedback_id, $lecturer_id, $response_text);
$ins->execute();

// Remove from feedback table
$del = $conn->prepare("DELETE FROM feedback WHERE feedback_id = ?");
$del->bind_param("i", $feedback_id);
$del->execute();

header("Location: lecturer_feedback.php");
exit();
?>
