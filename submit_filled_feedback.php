<?php
session_start();
include("include/dbconnect.php");

// Only logged-in students can submit
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: signin.php");
    exit();
}

$student_id = $_SESSION['user_id'];

// Only handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_id = intval($_POST['form_id']);
    $answers = $_POST['answers'] ?? [];

    if (!$form_id || empty($answers)) {
        echo "<p style='color: red;'>Invalid form submission.</p>";
        exit();
    }

    $conn->begin_transaction();

    try {
        // Check for duplicate submission
        $check = $conn->prepare("SELECT 1 FROM submitted_feedback WHERE form_id = ? AND student_id = ?");
        $check->bind_param("ii", $form_id, $student_id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            echo "<p style='color: red;'>You have already submitted this feedback.</p>";
            echo "<a href='studentdash.php'>Return to Dashboard</a>";
            exit();
        }

        // Record submission
        $stmt = $conn->prepare("INSERT INTO submitted_feedback (form_id, student_id, submitted_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("ii", $form_id, $student_id);
        $stmt->execute();

        // Save answers
        $insert = $conn->prepare("INSERT INTO form_responses (form_id, question_id, student_id, response_text)
                                  VALUES (?, ?, ?, ?)");

        foreach ($answers as $question_id => $response) {
            $question_id = intval($question_id);
            $response_text = trim($response);

            if (!empty($response_text)) {
                $insert->bind_param("iiis", $form_id, $question_id, $student_id, $response_text);
                $insert->execute();
            }
        }

        $conn->commit();

        echo "<p style='color: green; font-weight: bold;'>Thank you! Your feedback was submitted successfully.</p>";
        echo "<a href='studentdash.php'>Return to Dashboard</a>";

    } catch (Exception $e) {
        $conn->rollback();
        echo "<p style='color: red;'>An error occurred while submitting your feedback.</p>";
        echo "<pre>Error: " . htmlspecialchars($e->getMessage()) . "</pre>";
    }

} else {
    echo "<p style='color: red;'>Invalid request method.</p>";
}
?>
