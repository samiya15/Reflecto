<?php
session_start();
include("include/dbconnect.php");

// Ensure lecturer is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) {
    header("Location: signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get lecturer_id
$stmt = $conn->prepare("SELECT lecturer_id FROM lecturers WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$lecturer = $result->fetch_assoc();
if (!$lecturer) {
    echo "Lecturer record not found.";
    exit();
}
$lecturer_id = $lecturer['lecturer_id'];

// Fetch saved feedback
$feedbackStmt = $conn->prepare("
    SELECT 
        f.cleaned_text,
        f.created_at,
        f.is_anonymous,
        f.sentiment,
        f.confidence_score,
        u.firstName,
        u.lastName,
        s.student_course,
        s.year_of_study,
        fa.faculty_name
    FROM feedback_archive fa
    JOIN feedback f ON fa.feedback_id = f.feedback_id
    LEFT JOIN users u ON f.user_id = u.user_id
    LEFT JOIN students s ON f.user_id = s.user_id
    LEFT JOIN faculty fa ON s.faculty_id = fa.faculty_id
    WHERE fa.lecturer_id = ?
    ORDER BY fa.saved_at DESC
");
$feedbackStmt->bind_param("i", $lecturer_id);
$feedbackStmt->execute();
$feedbackResult = $feedbackStmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Saved Feedback</title>
  <link rel="stylesheet" href="lecturer_feedback.css" />
</head>
<body>
  <nav class="navbar">
    <div class="nav-left">
      <a href="lecdash.php">Dashboard</a>
      <a href="lecturer_feedback.php">Recent Feedback</a>
    </div>
    <div class="nav-right">
      <a href="signin.php" class="logout-btn">Log Out</a>
    </div>
  </nav>

  <h2>Saved Feedback Archive</h2>

  <div class="feedback-container">
    <?php if ($feedbackResult->num_rows === 0): ?>
      <p>No saved feedback.</p>
    <?php else: ?>
      <?php while ($fb = $feedbackResult->fetch_assoc()): ?>
        <div class="feedback-card">
          <p><strong>Submitted on:</strong> <?= htmlspecialchars(date("F j, Y, g:i a", strtotime($fb['created_at']))) ?></p>
          <p><strong>Feedback:</strong> <?= nl2br(htmlspecialchars($fb['cleaned_text'])) ?></p>
          <p><strong>Sentiment:</strong> <?= htmlspecialchars($fb['sentiment']) ?> (Score: <?= htmlspecialchars($fb['confidence_score']) ?>)</p>
          <?php if ($fb['is_anonymous']): ?>
            <p><em>This feedback was submitted anonymously.</em></p>
          <?php else: ?>
            <p><strong>Student:</strong> <?= htmlspecialchars($fb['firstName'].' '.$fb['lastName']) ?></p>
            <p><strong>Course:</strong> <?= htmlspecialchars($fb['student_course']) ?></p>
            <p><strong>Year:</strong> <?= htmlspecialchars($fb['year_of_study']) ?></p>
            <p><strong>Faculty:</strong> <?= htmlspecialchars($fb['faculty_name']) ?></p>
          <?php endif; ?>
        </div>
      <?php endwhile; ?>
    <?php endif; ?>
  </div>
</body>
</html>
