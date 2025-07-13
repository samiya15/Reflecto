<?php
session_start();
include("include/dbconnect.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) {
    header("Location: signin.php");
    exit();
}

$form_id = intval($_GET['form_id'] ?? 0);
if (!$form_id) {
    echo "Invalid form ID.";
    exit();
}

$title = $conn->query("SELECT title FROM feedback_forms WHERE form_id = $form_id")->fetch_assoc()['title'] ?? '';

$submissions = $conn->query("
    SELECT sf.student_id, sf.unit_id, sf.submitted_at, u.unit_name
    FROM submitted_feedback sf
    JOIN units u ON sf.unit_id = u.unit_id
    WHERE sf.form_id = $form_id
    GROUP BY sf.student_id, sf.unit_id
")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Individual Feedback - <?= htmlspecialchars($title) ?></title>
  <link rel="stylesheet" href="individual_feedback.css">
</head>
<body>

<h2>Feedback Submissions: <?= htmlspecialchars($title) ?></h2>

<?php if (empty($submissions)): ?>
    <p>No submissions yet.</p>
<?php else: ?>
    <?php foreach ($submissions as $entry): ?>
        <div class="feedback-entry">
            <strong>Unit:</strong> <?= htmlspecialchars($entry['unit_name']) ?><br>
            <strong>Submitted At:</strong> <?= htmlspecialchars($entry['submitted_at']) ?><br><br>
            <a href="view_single_feedback.php?form_id=<?= $form_id ?>&student_id=<?= $entry['student_id'] ?>&unit_id=<?= $entry['unit_id'] ?>">View Full Feedback</a>
        </div>
    <?php endforeach; ?>
     <a href="summary_feedback.php?form_id=<?= $form_id ?>" class="back-button"> Back </a>
<?php endif; ?>

</body>
</html>
