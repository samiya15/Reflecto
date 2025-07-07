<?php
session_start();
include("include/dbconnect.php");

// Make sure student is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch student details
$stmt = $conn->prepare("
    SELECT s.faculty_id, s.student_course, s.status,
           u.firstName, u.lastName, u.email
    FROM students s
    JOIN users u ON s.user_id = u.user_id
    WHERE s.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

// If no faculty yet (first login), force them to fill profile
if (empty($student['faculty_id'])) {
    header("Location: student_complete_profile.php");
    exit();
}

// Get faculty name for display
$facultyName = "Unknown";
if (!empty($student['faculty_id'])) {
    $fstmt = $conn->prepare("SELECT faculty_name FROM faculty WHERE faculty_id = ?");
    $fstmt->bind_param("i", $student['faculty_id']);
    $fstmt->execute();
    $fresult = $fstmt->get_result();
    if ($frow = $fresult->fetch_assoc()) {
        $facultyName = $frow['faculty_name'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Dashboard</title>
  <link rel="stylesheet" href="studentdash.css" />
</head>
<body>
  <nav class="navbar">
    <div class="nav-left">
      <ul>
        <li><a href="studentdash.php">Dashboard</a></li>
      </ul>
    </div>
    <div class="nav-right">
      <a href="signin.php" class="logout-btn">Log Out</a>
    </div>
  </nav>
<div class="banner">
  <h2>Welcome, <?= htmlspecialchars($student['firstName']) ?>!</h2>
  </div>
  <p>Faculty: <?= htmlspecialchars($facultyName) ?> | Course: <?= htmlspecialchars($student['student_course']) ?></p>
  <p>Status: <?= htmlspecialchars($student['status']) ?></p>

  <div class="cards-container">
    <!-- View Profile Card -->
    <div class="card">
      <h3>View and Update Profile</h3>
      <button id="openProfileBtn">View Profile</button>
    </div>
    <!-- Submit Personalized Feedback -->
    <div class="card">
      <h3>Submit Personalized Feedback</h3>
      <a href="student_feedback.php" class="card-btn">Go</a>
    </div>
    <!-- Fill Feedback Form -->
    <div class="card">
      <h3>Fill Feedback Form</h3>
      <a href="feedback_form.php" class="card-btn">Go</a>
    </div>
  </div>
   <div class="card">
      <h3>View Feedback responses</h3>
      <a href="student_view_responses.php" class="card-btn">Go</a>
    </div>
  </div>

  <!-- Modal Popup -->
  <div id="profileModal" class="modal">
    <div class="modal-content">
      <span class="closeBtn">&times;</span>
      <h3>Update Profile</h3>
      <form action="update_student_profile.php" method="post">
        <div class="input-group">
          <label>First Name</label>
          <input type="text" name="firstName" value="<?= htmlspecialchars($student['firstName']) ?>" required>
        </div>
        <div class="input-group">
          <label>Last Name</label>
          <input type="text" name="lastName" value="<?= htmlspecialchars($student['lastName']) ?>" required>
        </div>
        <div class="input-group">
          <label>Email</label>
          <input type="email" name="email" value="<?= htmlspecialchars($student['email']) ?>" readonly>
        </div>
        <div class="input-group">
  <label>Faculty</label>
  <select name="faculty_id" required>
    <option value="">Select Faculty</option>
    <?php
    // Load faculties
    $facQuery = $conn->query("SELECT faculty_id, faculty_name FROM faculty ORDER BY faculty_name");
    while ($fac = $facQuery->fetch_assoc()):
    ?>
      <option value="<?= $fac['faculty_id'] ?>"><?= htmlspecialchars($fac['faculty_name']) ?></option>
    <?php endwhile; ?>
  </select>
</div>

        <div class="input-group">
          <label>Course</label>
          <input type="text" name="course_name" value="<?= htmlspecialchars($student['student_course']) ?>" required>
        </div>
        <button type="submit">Update Profile</button>
      </form>
      <p class="note">*Updates will require system admin approval.</p>
    </div>
  </div>

  <script>
    const modal = document.getElementById("profileModal");
    const btn = document.getElementById("openProfileBtn");
    const span = document.getElementsByClassName("closeBtn")[0];
    btn.onclick = () => modal.style.display = "block";
    span.onclick = () => modal.style.display = "none";
    window.onclick = (event) => {
      if (event.target == modal) modal.style.display = "none";
    };
  </script>
</body>
</html>
