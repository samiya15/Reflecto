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

    <form id="feedbackForm">
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

    <div id="result" style="margin-top: 20px;"></div>
  </div>

  <script>
document.getElementById("feedbackForm").addEventListener("submit", async function (e) {
  e.preventDefault();

  const form = e.target;
  const formData = new FormData(form);
  const form_id = formData.get("form_id");
  const unit_id = formData.get("unit_id");

  const rawAnswers = {};
  const scaleAnswers = {};

  for (let [key, value] of formData.entries()) {
    if (key.startsWith("answers[")) {
      const questionId = key.match(/answers\[(\d+)\]/)[1];
      if (isNaN(value)) {
        rawAnswers[questionId] = value;
      } else {
        scaleAnswers[questionId] = parseInt(value);
      }
    }
  }

  try {
    const cleanedAnswers = {};

    for (const [questionId, text] of Object.entries(rawAnswers)) {
      const apiRes = await fetch("http://127.0.0.1:8000/feedback/analyze", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ message: text, is_anonymous: false })
      });

      const data = await apiRes.json();
      cleanedAnswers[questionId] = {
        original_text: text,
        cleaned_text: data.cleaned_text,
        sentiment: data.sentiment,
        confidence_score: data.confidence_score,
        contains_profanity: data.contains_profanity ? 1 : 0
      };
    }

    const fullPayload = {
      form_id: form_id,
      unit_id: unit_id,
      scale_answers: scaleAnswers,
      text_answers: cleanedAnswers
    };

    const saveRes = await fetch("submit_filled_feedback.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(fullPayload)
    });

    const result = await saveRes.json();

    if (result.success) {
      document.getElementById("result").innerHTML = `<p style="color:green;"><strong>Thank you!</strong> Feedback submitted successfully.</p>`;
      form.reset();
    } else {
      document.getElementById("result").innerHTML = `<p style="color:red;">Failed to submit feedback.</p>`;
    }
  } catch (err) {
    console.error(err);
    document.getElementById("result").innerHTML = `<p style="color:red;">An error occurred while submitting feedback.</p>`;
  }
});
</script>
</body>
</html>
