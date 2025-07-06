<?php
session_start();
include("include/dbconnect.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";
    $feedback_id = intval($_POST["feedback_id"]);
    $response_text = trim($_POST["response_text"] ?? "");

    if (!$feedback_id) {
        die("Invalid feedback ID.");
    }

    // Get the feedback record to move or respond
    $stmt = $conn->prepare("
        SELECT *
        FROM feedback
        WHERE feedback_id = ?
    ");
    $stmt->bind_param("i", $feedback_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $feedback = $result->fetch_assoc();

    if (!$feedback) {
        die("Feedback not found.");
    }

    if ($action === "save") {
        // Insert into saved_feedback
        $insert = $conn->prepare("
          INSERT INTO feedback_archive
(
    user_id,
    lecturer_id,
    original_text,
    cleaned_text,
    sentiment,
    confidence_score,
    contains_profanity
)
VALUES (?, ?, ?, ?, ?, ?, ?)

        ");
        $insert->bind_param("iisssdi",
            $feedback["user_id"],
            $feedback["lecturer_id"],
            $feedback["original_text"],
            $feedback["cleaned_text"],
            $feedback["sentiment"],
            $feedback["confidence_score"],
            $feedback["contains_profanity"]

        );
        $insert->execute();

        // Delete from feedback
        $delete = $conn->prepare("DELETE FROM feedback WHERE feedback_id = ?");
        $delete->bind_param("i", $feedback_id);
        $delete->execute();

        header("Location: lecturer_feedback.php");
        exit();
    }

    if ($action === "respond") {
        if (empty($response_text)) {
            die("Response text cannot be empty.");
        }

        // Optionally, save response somewhere else (e.g., lecturer_responses table)
        $insertResponse = $conn->prepare("
            INSERT INTO lecturer_responses (
                feedback_id,
                response_text,
                responded_at
            ) VALUES (?, ?, NOW())
        ");
        $insertResponse->bind_param("is", $feedback_id, $response_text);
        $insertResponse->execute();

        // Insert into saved_feedback
        $insert = $conn->prepare("
            INSERT INTO feedback_archive (
                user_id,
                lecturer_id,
                original_text,
                cleaned_text,
                sentiment,
                confidence_score,
                contains_profanity,
                created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $insert->bind_param(
            "iisssdis",
            $feedback["user_id"],
            $feedback["lecturer_id"],
            $feedback["original_text"],
            $feedback["cleaned_text"],
            $feedback["sentiment"],
            $feedback["confidence_score"],
            $feedback["contains_profanity"],
            $feedback["created_at"]
        );
        $insert->execute();

        // Delete from feedback
        $delete = $conn->prepare("DELETE FROM feedback WHERE feedback_id = ?");
        $delete->bind_param("i", $feedback_id);
        $delete->execute();

        header("Location: lecturer_feedback.php");
        exit();
    }

    die("Invalid action.");
}
?>
