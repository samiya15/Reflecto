<?php
session_start();
include("include/dbconnect.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: signin.php");
    exit();
}

$student_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_id = intval($_POST['form_id']);
    $unit_id = intval($_POST['unit_id']);
    $answers = $_POST['answers'] ?? [];

    if (!$form_id || !$unit_id || empty($answers)) {
        echo "<script>alert('Invalid form submission.'); window.location.href='studentdash.php';</script>";
        exit();
    }

    $conn->begin_transaction();

    try {
        // Prevent duplicate submissions per form+unit
        $check = $conn->prepare("SELECT 1 FROM submitted_feedback WHERE form_id = ? AND unit_id = ? AND student_id = ?");
        $check->bind_param("iii", $form_id, $unit_id, $student_id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            echo "<script>alert('You have already submitted feedback for this unit.'); window.location.href='studentdash.php';</script>";
            exit();
        }

        // Record submission
        $stmt = $conn->prepare("INSERT INTO submitted_feedback (form_id, unit_id, student_id, submitted_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iii", $form_id, $unit_id, $student_id);
        $stmt->execute();

        // Save answers
        $insert = $conn->prepare("INSERT INTO form_responses (form_id, question_id, student_id, unit_id, response_text) VALUES (?, ?, ?, ?, ?)");

        foreach ($answers as $question_id => $response) {
            $question_id = intval($question_id);
            $response_text = trim($response);
            if (!empty($response_text)) {
                $insert->bind_param("iiiis", $form_id, $question_id, $student_id, $unit_id, $response_text);
                $insert->execute();
            }
        }

        $conn->commit();
        echo "<script>alert('Thank you! Your feedback was submitted successfully.'); window.location.href='studentdash.php';</script>";

    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>alert('An error occurred. Please try again.'); window.location.href='studentdash.php';</script>";
    }

} else {
    echo "<script>alert('Invalid request.'); window.location.href='studentdash.php';</script>";
}
?>
