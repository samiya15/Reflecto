<?php
session_start();
include("include/dbconnect.php");

// Ensure lecturer is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) {
    header("Location: signin.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"]) && $_POST["action"] === "save") {
    $feedback_id = $_POST["id"];

    // Step 1: Fetch original feedback
    $stmt = $conn->prepare("SELECT * FROM feedback WHERE id = ?");
    $stmt->bind_param("i", $feedback_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $feedback = $result->fetch_assoc();

    if (!$feedback) {
        die("Feedback not found.");
    }

    // Step 2: Archive it
    $insert = $conn->prepare("
        INSERT INTO feedback_archive (
            user_id,
            lecturer_id,
            original_text,
            cleaned_text,
            sentiment,
            confidence_score,
            contains_profanity,
            is_anonymous,
            reviewed_at,
            feedback_id
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)
    ");

    $insert->bind_param(
        "iisssdiii",
        $feedback['user_id'],
        $feedback['lecturer_id'],
        $feedback['original_text'],
        $feedback['cleaned_text'],
        $feedback['sentiment'],
        $feedback['confidence_score'],
        $feedback['contains_profanity'],
        $feedback['is_anonymous'],
        $feedback['id']  // reference back to original
    );

    if ($insert->execute()) {
        echo "<script>alert('Feedback archived successfully'); window.location.href='lecturer_feedback.php';</script>";
    } else {
        echo "Failed to archive: " . $conn->error;
    }
} else {
    echo "Invalid action.";
}
?>
