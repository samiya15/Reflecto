<?php
session_start();
include("include/dbconnect.php");

// Check login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) {
    header("Location: signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get lecturer_id using user_id
$lecturerStmt = $conn->prepare("SELECT lecturer_id FROM lecturers WHERE user_id = ?");
$lecturerStmt->bind_param("i", $user_id);
$lecturerStmt->execute();
$lecturerResult = $lecturerStmt->get_result();
$lecturerRow = $lecturerResult->fetch_assoc();

if (!$lecturerRow) {
    echo "Lecturer profile not found.";
    exit();
}

$lecturer_id = $lecturerRow['lecturer_id'];

// Fetch basic user info
$userStmt = $conn->prepare("SELECT firstName, lastName, email FROM users WHERE user_id = ?");
$userStmt->bind_param("i", $user_id);
$userStmt->execute();
$userResult = $userStmt->get_result();
$userData = $userResult->fetch_assoc();

// Fetch verification status
$statusStmt = $conn->prepare("SELECT verification_status FROM lecturers WHERE user_id = ?");
$statusStmt->bind_param("i", $user_id);
$statusStmt->execute();
$statusResult = $statusStmt->get_result();
$profileData = $statusResult->fetch_assoc();

// Fetch faculties
$faculties = [];
$facQuery = $conn->prepare("SELECT f.faculty_name FROM lecturer_faculties lf JOIN faculty f ON lf.faculty_id = f.faculty_id WHERE lf.lecturer_id = ?");
$facQuery->bind_param("i", $lecturer_id);
$facQuery->execute();
$facResult = $facQuery->get_result();
while ($row = $facResult->fetch_assoc()) {
    $faculties[] = $row['faculty_name'];
}

// Fetch courses
$courses = [];
$courseQuery = $conn->prepare("SELECT c.course_name FROM lecturer_courses lc JOIN course c ON lc.course_id = c.course_id WHERE lc.lecturer_id = ?");
$courseQuery->bind_param("i", $lecturer_id);
$courseQuery->execute();
$courseResult = $courseQuery->get_result();
while ($row = $courseResult->fetch_assoc()) {
    $courses[] = $row['course_name'];
}

// Fetch units
$units = [];
$unitQuery = $conn->prepare("SELECT u.unit_name FROM lecturer_units lu JOIN units u ON lu.unit_id = u.unit_id WHERE lu.lecturer_id = ?");
$unitQuery->bind_param("i", $lecturer_id);
$unitQuery->execute();
$unitResult = $unitQuery->get_result();
while ($row = $unitResult->fetch_assoc()) {
    $units[] = $row['unit_name'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lecturer Dashboard</title>
  <link rel="stylesheet" href="lecdash.css">
</head>
<body>
<nav class="navbar">
  <div class="nav-left">
    <ul>
      <li><a href="lecdash.php">Dashboard</a></li>
    </ul>
  </div>
  <div class="nav-right">
    <a href="signin.php" class="logout-btn">Log Out</a>
  </div>
</nav>

<div class="banner">
  <h2>Welcome, <?= htmlspecialchars($userData['firstName']) ?> <?= htmlspecialchars($userData['lastName']) ?></h2>
</div>

<div class="dashboard">
  <div class="card">
    <h3>Update Profile</h3>
    <p>Update your details</p>
    <button id="updateProfileBtn">Update Profile</button>
  </div>
 <div class="card">
    <h3>View Filled Forms</h3>
    <p>Review submitted forms.</p>
    <button onclick="window.location.href='lecturer_view_filled_forms.php'">View Forms</button>
  </div>
>

  <div class="card">
    <h3>View Submissions</h3>
    <p>Review submitted feedback.</p>
    <button onclick="window.location.href='lecturer_feedback.php'">View Submissions</button>
  </div>

  <div class="card">
    <h3>Feedback Forms</h3>
    <p>Upload feedback forms to students.</p>
    <button onclick="window.location.href='lecturer_recieve_feedback.php'">View Forms</button>
  </div>
   <div class="card">
    <h3>Manage courses and units</h3>
    <p>Edit or Update your courses and units.</p>
    <button onclick="window.location.href='lecturer_manage_courses.php'">View Courses</button>
    <button onclick="window.location.href='lecturer_manage_units.php'">View Units</button>
  </div>

  <div class="card">
    <h3>Status</h3>
    <p>Your verification status is: <strong><?= htmlspecialchars($profileData['verification_status']) ?></strong></p>
</div>

  <div class="card">
    <h3>Your Faculties</h3>
    <ul>
      <?php foreach ($faculties as $faculty): ?>
        <li><?= htmlspecialchars($faculty) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>

  <div class="card">
    <h3>Your Courses</h3>
    <ul>
      <?php foreach ($courses as $course): ?>
        <li><?= htmlspecialchars($course) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>

  <div class="card">
    <h3>Your Units</h3>
    <ul>
      <?php foreach ($units as $unit): ?>
        <li><?= htmlspecialchars($unit) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
</div>

<!-- Modal -->
<div id="profileModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h3>Update Profile</h3>
    <form action="update_lec_profile.php" method="post">
      <div class="input-group">
        <label>Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($userData['firstName'] . ' ' . $userData['lastName']) ?>" readonly>
      </div>
      <div class="input-group">
        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($userData['email']) ?>" readonly>
      </div>
      <button type="submit" class="save-btn">Save Changes</button>
    </form>
  </div>
</div>

<script>
const updateProfileBtn = document.getElementById("updateProfileBtn");
const modal = document.getElementById("profileModal");
const closeBtn = document.querySelector(".close");

updateProfileBtn.onclick = () => {
  modal.style.display = "flex";
};

closeBtn.onclick = () => {
  modal.style.display = "none";
};

window.onclick = (e) => {
  if (e.target === modal) {
    modal.style.display = "none";
  }
};
</script>
</body>
</html>
