<?php
session_start();
include("include/dbconnect.php");

// Ensure lecturer is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) {
    header("Location: signin.php");
    exit();
}

$form_id = intval($_GET['form_id'] ?? 0);
$unit_id = intval($_GET['unit_id'] ?? 0);
$student_id = intval($_GET['student_id'] ?? 0); // Used only for internal filtering, not displayed

if (!$form_id || !$unit_id || !$student_id) {
    echo "Invalid access.";
    exit();
}

// Get form and unit name
$form = $conn->query("SELECT title FROM feedback_forms WHERE form_id = $form_id")->fetch_assoc();
$title = $form['title'] ?? '';

// Get unit name
$unit = $conn->query("SELECT unit_name FROM units WHERE unit_id = $unit_id")->fetch_assoc();
$unit_name = $unit['unit_name'] ?? '';

// Get responses for this student's submission
$responses = $conn->query("
    SELECT aq.question_text, aq.question_type, fr.response_text
    FROM form_responses fr
    JOIN all_questions aq ON fr.question_id = aq.question_id
    WHERE fr.form_id = $form_id AND fr.student_id = $student_id
")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Individual Feedback</title>
  <link rel="stylesheet" href="single_feedback.css">
</head>
<body>
  <div class="container">
    <h2>Feedback: <?= htmlspecialchars($title) ?></h2>
    <h3>Unit: <?= htmlspecialchars($unit_name) ?></h3>

    <?php foreach ($responses as $r): ?>
      <div class="feedback-block">
        <p class="question"><?= htmlspecialchars($r['question_text']) ?></p>
        <p class="answer">
          <?php if ($r['question_type'] === 'scale'): ?>
            Rating: <?= htmlspecialchars($r['response_text']) ?> / 5
          <?php else: ?>
            <?= nl2br(htmlspecialchars($r['response_text'])) ?>
          <?php endif; ?>
        </p>
      </div>
    <?php endforeach; ?>
    <a href="individual_feedback.php?form_id=<?= $form_id ?>" class="back-button"> Back </a>

  </div>
  
</body>
</html>
