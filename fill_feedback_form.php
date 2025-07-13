<?php
session_start();
include("include/dbconnect.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: signin.php");
    exit();
}

$form_id = intval($_GET['form_id'] ?? 0);
$unit_id = intval($_GET['unit_id'] ?? 0);

if (!$form_id || !$unit_id) {
    echo "Invalid form access.";
    exit();
}

// Get form title
$form = $conn->query("SELECT title FROM feedback_forms WHERE form_id = $form_id")->fetch_assoc();
$title = $form['title'] ?? '';

// Get questions
$questions = $conn->query("SELECT aq.question_id, aq.question_text, aq.question_type
    FROM lecturer_form_questions fq
    JOIN all_questions aq ON fq.question_id = aq.question_id
    WHERE fq.form_id = $form_id
")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($title) ?></title>
  <link rel="stylesheet" href="fill_feedback_form.css">
</head>
<body>
  <div class="form-container">
    <h2><?= htmlspecialchars($title) ?></h2>

    <form action="submit_filled_feedback.php" method="post">
      <input type="hidden" name="form_id" value="<?= $form_id ?>">
      <input type="hidden" name="unit_id" value="<?= $unit_id ?>">

      <?php foreach ($questions as $q): ?>
        <div class="question-block">
          <label><?= htmlspecialchars($q['question_text']) ?></label>

          <?php if ($q['question_type'] === 'scale'): ?>
            <div class="scale-container">
              <?php for ($i = 1; $i <= 5; $i++): ?>
                <label class="circle-option">
                  <input type="radio" name="answers[<?= $q['question_id'] ?>]" value="<?= $i ?>" required>
                  <span><?= $i ?></span>
                </label>
              <?php endfor; ?>
            </div>
          <?php else: ?>
            <textarea name="answers[<?= $q['question_id'] ?>]" rows="3" required></textarea>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>

      <button type="submit">Submit Feedback</button>
    </form>
  </div>
</body>
</html>
