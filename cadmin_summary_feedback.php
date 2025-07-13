<?php
session_start();
include("include/dbconnect.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 3) {
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
$title = $titleQuery->fetch_assoc()['title'] ?? 'Untitled Form';

// Fetch all questions in the form
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
    <meta charset="UTF-8">
    <title>Feedback Summary</title>
    <link rel="stylesheet" href="cadmin_summary_feedback.css">
</head>
<body>
<div class="container">
    <h2>Feedback Summary: <?= htmlspecialchars($title) ?></h2>

    <?php if (empty($questions)): ?>
        <p>No questions found in this form.</p>
    <?php else: ?>
        <?php foreach ($questions as $q): ?>
            <div class="question-block">
                <strong><?= htmlspecialchars($q['question_text']) ?></strong><br>

                <?php if ($q['question_type'] === 'scale'): ?>
                    <?php
                    $avgQ = $conn->query("
                        SELECT AVG(CAST(response_text AS DECIMAL)) AS avg_score, COUNT(*) AS total_responses
                        FROM form_responses
                        WHERE form_id = $form_id AND question_id = {$q['question_id']}
                    ")->fetch_assoc();

                    $avg = round($avgQ['avg_score'], 2);
                    $total = intval($avgQ['total_responses']);
                    ?>

                    <?php if ($total > 0): ?>
                        <div class="rating-display">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <label class="radio-circle">
                                    <input type="radio" disabled <?= ($i == round($avg)) ? 'checked' : '' ?>>
                                    <span><?= $i ?></span>
                                </label>
                            <?php endfor; ?>
                            <span class="numeric-rating">(<?= $avg ?> / 5 from <?= $total ?> responses)</span>
                        </div>
                    <?php else: ?>
                        <p>No feedback submitted yet.</p>
                    <?php endif; ?>

                <?php else: ?>
                    <?php
                  $textRes = $conn->query("
    SELECT response_text, cleaned_text
    FROM form_responses
    WHERE form_id = $form_id AND question_id = {$q['question_id']}
    LIMIT 3
")->fetch_all(MYSQLI_ASSOC);
?>

<?php if (!empty($textRes)): ?>
    <ul class="text-responses">
        <?php foreach ($textRes as $res): ?>
            <li>
                <strong>Cleaned:</strong> <?= htmlspecialchars($res['cleaned_text']) ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>No responses submitted yet.</p>
<?php endif; ?>

                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="button-group">
        <a href="courseadmin.php"><button class="back">Back</button></a>
    </div>
</div>
</body>
</html>
