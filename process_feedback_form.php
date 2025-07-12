<?php
session_start();
include("include/dbconnect.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 3) {
    header("Location: signin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_title = $_POST['form_title'];
    $faculty_id = (int) $_POST['faculty_id'];
    $created_by = $_SESSION['user_id'];
    $questions = $_POST['questions']; // Array of questions

    // Insert form header
    $stmt = $conn->prepare("INSERT INTO feedback_forms (title, faculty_id, created_by) VALUES (?, ?, ?)");
    $stmt->bind_param("sii", $form_title, $faculty_id, $created_by);
    
    if ($stmt->execute()) {
        $form_id = $stmt->insert_id;

        // Insert questions
        $qstmt = $conn->prepare("INSERT INTO feedback_questions (form_id, question_text, question_type) VALUES (?, ?, ?)");
        foreach ($questions as $q) {
            $qtext = $q['text'];
            $qtype = $q['type']; // 'scale' or 'text'
            $qstmt->bind_param("iss", $form_id, $qtext, $qtype);
            $qstmt->execute();
        }

        // Redirect or notify success
        header("Location: courseadmin.php?success=form_created");
        exit();
    } else {
        echo "Error inserting feedback form: " . $stmt->error;
    }
}
?>
