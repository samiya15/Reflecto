<?php
session_start();
include("include/dbconnect.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) {
    header("Location: signin.php");
    exit();
}

$form_id = intval($_GET['form_id'] ?? 0);
if (!$form_id) {
    echo "Invalid Form ID.";
    exit();
}

// Fetch form title
$titleQuery = $conn->query("SELECT title FROM feedback_forms WHERE form_id = $form_id");
$title = $titleQuery->fetch_assoc()['title'] ?? '';

// Get questions
$questions = $conn->query("
    SELECT aq.question_id, aq.question_text, aq.question_type
    FROM lecturer_form_questions fq
    JOIN all_questions aq ON fq.question_id = aq.question_id
    WHERE fq.form_id = $form_id
")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Feedback Summary</title>
    <link rel="stylesheet" href="summary_feedback.css">
</head>
<body>
<div class="container">
    <h2>Feedback Summary: <?= htmlspecialchars($title) ?></h2>

    <?php foreach ($questions as $q): ?>
        <div class="question-block">
            <strong><?= htmlspecialchars($q['question_text']) ?></strong><br>

            <?php if ($q['question_type'] === 'scale'): ?>
                <?php
                $avgQ = $conn->query("
                    SELECT AVG(CAST(response_text AS DECIMAL)) AS avg_score
                    FROM form_responses
                    WHERE form_id = $form_id AND question_id = {$q['question_id']}
                ")->fetch_assoc();
                $avg = round($avgQ['avg_score'], 2);
                ?>
                <div class="rating-display">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <label class="radio-circle">
                            <input type="radio" disabled <?= ($i == round($avg)) ? 'checked' : '' ?>>
                            <span><?= $i ?></span>
                        </label>
                    <?php endfor; ?>
                    <span class="numeric-rating">(<?= $avg ?> / 5)</span>
                </div>

            <?php else: ?>
                <?php
               $textRes = $conn->query("
    SELECT cleaned_text
    FROM form_responses
    WHERE form_id = $form_id AND question_id = {$q['question_id']}
    LIMIT 3
")->fetch_all(MYSQLI_ASSOC);
?>
<ul class="text-responses">
    <?php foreach ($textRes as $res): ?>
        <li><?= htmlspecialchars($res['cleaned_text']) ?></li>
    <?php endforeach; ?>
</ul>

            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <div class="button-group">
        <a href="individual_feedback.php?form_id=<?= $form_id ?>"><button>View Individual Submissions</button></a>
        <a href="lecturer_view_filled_forms.php"><button class="back">Back</button></a>
    </div>
</div>
</body>
</html>
