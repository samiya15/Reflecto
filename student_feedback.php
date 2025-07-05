<?php
session_start();
include("include/dbconnect.php");

// Ensure student is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch the student's course
$stmt = $conn->prepare("
    SELECT s.student_course, f.faculty_id
    FROM students s
    JOIN faculty f ON s.faculty_id = f.faculty_id
    WHERE s.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    echo "Student record not found.";
    exit();
}

$course = $student['student_course'];
$faculty_id = $student['faculty_id'];

// Fetch lecturers teaching this course
$lecStmt = $conn->prepare("
    SELECT l.lecturer_id, l.course_taught, l.unit_taught, u.firstName, u.lastName, u.email
    FROM lecturers l
    JOIN users u ON l.user_id = u.user_id
    JOIN lecturer_faculties lf ON l.lecturer_id = lf.lecturer_id
    WHERE lf.faculty_id = ?
");
$lecStmt->bind_param("i", $faculty_id);
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
        <p>Course: <?= htmlspecialchars($lec['course_taught']) ?></p>
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
        const response = await fetch("http://127.0.0.1:8000/feedback/analyze", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            message: message,
            lecturer_id: lecturerId,
            is_anonymous: isAnonymous
          })
        });

        const data = await response.json();
        document.getElementById("result").innerHTML = `
          <p style="color:green;"><strong>Thank you!</strong> Your feedback has been submitted successfully.</p>
        `;
      } catch (error) {
        console.error("Error:", error);
        document.getElementById("result").innerHTML = `<p style="color:red;">Failed to connect to backend.</p>`;
      }
    }
  </script>
</body>
</html>
