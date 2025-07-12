<?php
session_start();
include("include/dbconnect.php");

// Ensure student is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get the student's course ID and year of study
$studentStmt = $conn->prepare("SELECT course_id, year_of_study FROM students WHERE user_id = ?");
$studentStmt->bind_param("i", $user_id);
$studentStmt->execute();
$studentResult = $studentStmt->get_result();
$student = $studentResult->fetch_assoc();

$course_id = $student['course_id'];
$year_of_study = $student['year_of_study'];

$lecStmt = $conn->prepare(" SELECT DISTINCT l.lecturer_id, u.firstName, u.lastName, u.email, 
           un.unit_name AS unit_taught, c.course_name
    FROM lecturers l
    JOIN users u ON l.user_id = u.user_id
    JOIN lecturer_units lu ON l.lecturer_id = lu.lecturer_id
    JOIN units un ON lu.unit_id = un.unit_id
    JOIN lecturer_courses lc ON l.lecturer_id = lc.lecturer_id
    JOIN course c ON lc.course_id = c.course_id
    WHERE lc.course_id = ? AND un.year_of_study = ?
");

$lecStmt->bind_param("ii", $course_id, $year_of_study);
$lecStmt->execute();
$lecResult = $lecStmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Submit Personalized Feedback</title>
  <link rel="stylesheet" href="student_feedback.css" />
</head>
<body>
  <nav class="navbar">
    <div class="nav-left">
      <a href="studentdash.php">Dashboard</a>
    </div>
    <div class="nav-right">
      <a href="signin.php" class="logout-btn">Log Out</a>
    </div>
  </nav>

  <h2>Select Lecturer to Submit Feedback</h2>

  <div class="lecturer-cards">
    <?php while ($lec = $lecResult->fetch_assoc()): ?>
      <div class="card" onclick="openFeedbackForm(<?= htmlspecialchars($lec['lecturer_id']) ?>)">
        <h3><?= htmlspecialchars($lec['firstName'] . ' ' . $lec['lastName']) ?></h3>
        <p>Email: <?= htmlspecialchars($lec['email']) ?></p>
        <p>Course: <?= htmlspecialchars($lec['course_name']) ?></p>
        <p>Unit: <?= htmlspecialchars($lec['unit_taught']) ?></p>
      </div>
    <?php endwhile; ?>
  </div>

  <!-- Hidden Feedback Form Modal -->
  <div id="feedbackModal" class="modal">
    <div class="modal-content">
      <span class="closeBtn">&times;</span>
      <h3>Submit Feedback</h3>
      <form id="feedback-form">
        <input type="hidden" id="lecturer_id" name="lecturer_id">
        <div class="input-group">
          <label for="feedback">Your Feedback</label>
          <textarea id="feedback" name="feedback_text" rows="6" required></textarea>
        </div>
        <div class="button-group">
          <button type="submit" id="anonymous-btn">Submit Anonymously</button>
          <button type="submit" id="detailed-btn">Submit with My Details</button>
        </div>
      </form>
      <div id="result" style="margin-top: 20px;"></div>
    </div>
  </div>
  <script>
let isAnonymous = false;

function openFeedbackForm(lecturerId) {
  document.getElementById("lecturer_id").value = lecturerId;
  document.getElementById("feedbackModal").style.display = "block";
}

document.getElementsByClassName("closeBtn")[0].onclick = function() {
  document.getElementById("feedbackModal").style.display = "none";
};

window.onclick = function(event) {
  if (event.target == document.getElementById("feedbackModal")) {
    document.getElementById("feedbackModal").style.display = "none";
  }
};

document.getElementById("anonymous-btn").addEventListener("click", function(e) {
  e.preventDefault();
  isAnonymous = true;
  submitFeedback();
});

document.getElementById("detailed-btn").addEventListener("click", function(e) {
  e.preventDefault();
  isAnonymous = false;
  submitFeedback();
});

async function submitFeedback() {
  const message = document.getElementById("feedback").value;
  const lecturerId = document.getElementById("lecturer_id").value;

  try {
    const apiResponse = await fetch("http://127.0.0.1:8000/feedback/analyze", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        message: message,
        is_anonymous: isAnonymous
      })
    });

    const apiData = await apiResponse.json();

    const saveResponse = await fetch("save_feedback.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        lecturer_id: lecturerId,
        original_text: message,
        cleaned_text: apiData.cleaned_text,
        sentiment: apiData.sentiment,
        confidence_score: apiData.confidence_score,
        contains_profanity: apiData.contains_profanity ? 1 : 0,
        is_anonymous: isAnonymous
      })
    });

    const saveData = await saveResponse.json();

    if (saveData.success) {
      document.getElementById("result").innerHTML = `<p style="color:green;"><strong>Thank you!</strong> Your feedback has been submitted successfully.</p>`;
    } else {
      document.getElementById("result").innerHTML = `<p style="color:red;">Failed to save feedback.</p>`;
    }
  } catch (error) {
    console.error("Error:", error);
    document.getElementById("result").innerHTML = `<p style="color:red;">Error occurred.</p>`;
  }
}
</script>
</body>
</html>
