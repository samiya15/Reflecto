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

// Fetch feedback
$feedbackStmt = $conn->prepare("SELECT 
        f.id, f.cleaned_text, f.created_at, f.is_anonymous,
        f.sentiment, f.confidence_score,
        CASE WHEN f.is_anonymous = 0 THEN u.firstName ELSE NULL END AS firstName,
        CASE WHEN f.is_anonymous = 0 THEN u.lastName ELSE NULL END AS lastName,
        s.student_course,
        s.year_of_study, fa.faculty_name
    FROM feedback f
    LEFT JOIN users u ON f.user_id = u.user_id
    LEFT JOIN students s ON f.user_id = s.user_id
    LEFT JOIN faculty fa ON s.faculty_id = fa.faculty_id
    LEFT JOIN feedback_archive fa2 ON fa2.feedback_id = f.id
    WHERE f.lecturer_id = ? AND fa2.feedback_id IS NULL
    ORDER BY f.created_at DESC
");

$feedbackStmt->bind_param("i", $lecturer_id);
$feedbackStmt->execute();
$feedbackResult = $feedbackStmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8" />
  <title>Lecturer Feedback</title>
  <link rel="stylesheet" href="lecturer_feedback.css" />
</head>
<body>
<nav class="navbar">
  <div class="nav-left">
    <a href="lecdash.php">Dashboard</a>
    <a href="lecturer_feedback.php">Recent Feedback</a>
    <a href="saved_feedback.php">Saved Feedback</a>
  </div>
  <div class="nav-right">
    <a href="signin.php" class="logout-btn">Log Out</a>
  </div>
</nav>

<h2>Feedback Received</h2>
<div class="feedback-container">
<?php if ($feedbackResult->num_rows === 0): ?>
  <p>No feedback submitted yet.</p>
<?php else: ?>
  <?php while ($fb = $feedbackResult->fetch_assoc()): ?>
    <div class="feedback-card">
      <p><strong>Submitted on:</strong> <?= htmlspecialchars(date("F j, Y, g:i a", strtotime($fb['created_at']))) ?></p>
      <p><strong>Feedback:</strong> <?= nl2br(htmlspecialchars($fb['cleaned_text'])) ?></p>
      <p><strong>Sentiment:</strong> <?= htmlspecialchars($fb['sentiment']) ?></p>
     <?php if ($fb['is_anonymous']): ?>
    <p><em>This feedback was submitted anonymously.</em></p>
<?php else: ?>
    <p><strong>Student:</strong> <?= htmlspecialchars($fb['firstName'].' '.$fb['lastName']) ?></p>


        <p><strong>Course:</strong> <?= htmlspecialchars($fb['student_course']) ?></p>
        <p><strong>Year:</strong> <?= htmlspecialchars($fb['year_of_study']) ?></p>
        <p><strong>Faculty:</strong> <?= htmlspecialchars($fb['faculty_name']) ?></p>
      <?php endif; ?>
 <div class="button-group">
  <form method="post" action="process_feedback_action.php" style="display:inline;">
    <input type="hidden" name="id" value="<?= $fb['id'] ?>">
    <button type="submit" name="action" value="save">Save Feedback</button>
  </form>
<?php if (!$fb['is_anonymous']): ?>
  <button type="button" class="respond-btn" data-feedback-id="<?= $fb['id'] ?>">Respond</button>
<?php else: ?>
  <button type="button" disabled title="Cannot respond to anonymous feedback" style="opacity: 0.5; cursor: not-allowed;">Respond</button>
<?php endif; ?>

</div>


    </div>
  <?php endwhile; ?>
<?php endif; ?>
</div>

<!-- Modal -->
<div id="responseModal" class="modal">
  <div class="modal-content">
    <span class="closeBtn">&times;</span>
    <h3>Send Response</h3>
    <form method="post" action="submit_feedback_response.php">
      <input type="hidden" id="response_feedback_id" name="feedback_id">
      <textarea name="response_text" required placeholder="Type your response here..."></textarea>
      <button type="submit">Send Response</button>
    </form>
  </div>
</div>

<script>
 document.querySelectorAll(".respond-btn").forEach(btn => {
  btn.addEventListener("click", () => {
    if (btn.hasAttribute("disabled")) return; // Do nothing if button is disabled

    const feedbackId = btn.getAttribute("data-feedback-id");
    document.getElementById("response_feedback_id").value = feedbackId;
    document.getElementById("responseModal").style.display = "block";
  });
});


function openResponseModal(feedbackId) {
  document.getElementById("response_feedback_id").value = feedbackId;
  document.getElementById("responseModal").style.display = "block";
}
document.querySelector(".closeBtn").onclick = function() {
  document.getElementById("responseModal").style.display = "none";
};
window.onclick = function(event) {
  if (event.target === document.getElementById("responseModal")) {
    document.getElementById("responseModal").style.display = "none";
  }
};
</script>
</body>
</html>
