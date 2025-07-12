<?php
session_start();
include("include/dbconnect.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: signin.php");
    exit();
}

$student_id = $_SESSION['user_id'];

if (!isset($_GET['form_id'])) {
    echo "Form ID not provided.";
    exit();
}

$form_id = intval($_GET['form_id']);

// Fetch form metadata
$formMeta = $conn->prepare("SELECT title FROM feedback_forms WHERE form_id = ?");
$formMeta->bind_param("i", $form_id);
$formMeta->execute();
$formResult = $formMeta->get_result();
$form = $formResult->fetch_assoc();

if (!$form) {
    echo "Form not found.";
    exit();
}

// Fetch standard questions
$stdQ = $conn->prepare("SELECT question_id, question_text, question_type FROM feedback_questions WHERE form_id = ?");
$stdQ->bind_param("i", $form_id);
$stdQ->execute();
$stdRes = $stdQ->get_result();
$standardQuestions = $stdRes->fetch_all(MYSQLI_ASSOC);

// Fetch lecturer-assigned questions
$lecQ = $conn->prepare("SELECT question_id AS custom_question_id, question_text, question_type FROM lecturer_form_questions WHERE form_id = ?");
$lecQ->bind_param("i", $form_id);
$lecQ->execute();
$lecRes = $lecQ->get_result();
$customQuestions = $lecRes->fetch_all(MYSQLI_ASSOC);

// Combine all questions
$questions = array_merge($standardQuestions, $customQuestions);

// Normalize ID field
foreach ($questions as &$q) {
    if (isset($q['question_id'])) {
        $q['id'] = $q['question_id'];
    } elseif (isset($q['custom_question_id'])) {
        $q['id'] = $q['custom_question_id'];
    }
}
unset($q);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fill Feedback Form</title>
    <link rel="stylesheet" href="fill_feedback_form.css">
</head>
<body>

<div class="container">
    <h2><?= htmlspecialchars($form['title']) ?></h2>

    <form action="submit_filled_feedback.php" method="POST">
        <input type="hidden" name="form_id" value="<?= $form_id ?>">

        <?php foreach ($questions as $index => $q): ?>
            <div class="question-block">
                <p><strong><?= ($index + 1) . '. ' . htmlspecialchars($q['question_text']) ?></strong></p>

                <?php if ($q['question_type'] === 'scale'): ?>
                    <div class="scale-options">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <label>
                                <input type="radio" name="answers[<?= $q['id'] ?>]" value="<?= $i ?>" required>
                                <span class="circle"><?= $i ?></span>
                            </label>
                        <?php endfor; ?>
                    </div>
                <?php else: ?>
                    <textarea name="answers[<?= $q['id'] ?>]" rows="4" required></textarea>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <button type="submit" class="submit-btn">Submit Feedback</button>
    </form>
</div>

</body>
</html>
