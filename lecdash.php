<?php
session_start();
include("include/dbconnect.php");

// Check login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) {
    header("Location: signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch lecturer info
$stmt = $conn->prepare("
    SELECT l.faculty_name, l.course_taught, l.unit_taught, l.verification_status,
           u.firstName, u.lastName, u.email
    FROM lecturers l
    JOIN users u ON l.user_id = u.user_id
    WHERE l.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
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
    <h2>Welcome, <?= htmlspecialchars($data['firstName']) ?> <?= htmlspecialchars($data['lastName']) ?></h2>
  </div>

<div class="dashboard">
  <div class="card">
    <h3>Update Profile</h3>
    <p>Update your details</p>
    <button id="updateProfileBtn">Update Profile</button>
  </div>

  <div class="card">
    <h3>Status</h3>
    <p>Your verification status is: <strong><?= htmlspecialchars($data['verification_status']) ?></strong></p>
  </div>

  <div class="card">
    <h3>View Submissions</h3>
    <p>Review your submitted work.</p>
    <button onclick="window.location.href='view_submissions.php'">View Submissions</button>
  </div>
</div>

<!-- Modal (hidden by default) -->
<div id="profileModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h3>Update Profile</h3>
    <form action="update_lec_profile.php" method="post">
      <div class="input-group">
        <label>Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($data['firstName'] . ' ' . $data['lastName']) ?>" readonly>
      </div>
      <div class="input-group">
        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($data['email']) ?>" readonly>
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
        <label>Courses Taught</label>
        <textarea name="course_taught" required><?= htmlspecialchars($data['course_taught']) ?></textarea>
      </div>
      <div class="input-group">
        <label>Units Taught</label>
        <textarea name="unit_taught" required><?= htmlspecialchars($data['unit_taught']) ?></textarea>
      </div>
      <button type="submit" class="save-btn">Save Changes</button>
    </form>
  </div>
</div>

<script>
// Elements
const updateProfileBtn = document.getElementById("updateProfileBtn");
const modal = document.getElementById("profileModal");
const closeBtn = document.querySelector(".close");

// Show modal
updateProfileBtn.onclick = () => {
  modal.style.display = "flex";
};

// Close modal when clicking the X
closeBtn.onclick = () => {
  modal.style.display = "none";
};

// Close modal when clicking outside the modal-content
window.onclick = (e) => {
  if (e.target === modal) {
    modal.style.display = "none";
  }
};
</script>
</body>
</html>
