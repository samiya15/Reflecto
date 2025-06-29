<?php
// Start session + DB connect
session_start();
include("include/dbconnect.php");

// Check if user ID is set in session
$user_id = $_SESSION['user_id'] ?? null;

// Default: no student profile
$student = null;

if ($user_id) {
    // Fetch existing profile if logged in
    $query = $conn->prepare("SELECT * FROM students WHERE user_id = ?");
    $query->bind_param("i", $user_id);
    $query->execute();
    $result = $query->get_result();
    $student = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  <link rel="stylesheet" href="dashboard.css">
</head>
<body>

<!-- Navigation -->
<nav>
  <div class="navigation">
    <div class="nav-left">
      <ul>
        <li><a href="studentdash.php">Dashboard</a></li>
        <li><a href="about.html">About</a></li>
        <li><a href="select.html">Submit</a></li>
      </ul>
    </div>
    <div class="profile" id="profileArea">
      <div class="profile-icon" id="profileIcon"></div>
      <div class="dropdown" id="dropdownMenu">
        <a href="logout.php">Log Out</a>
      </div>
    </div>
  </div>
</nav>

<!-- Dropdown toggle script -->
<script>
const profileIcon = document.getElementById("profileIcon");
const dropdownMenu = document.getElementById("dropdownMenu");
const profileArea = document.getElementById("profileArea");

profileIcon.addEventListener("click", () => {
  dropdownMenu.style.display = dropdownMenu.style.display === "block" ? "none" : "block";
});

document.addEventListener("click", function(event) {
  if (!profileArea.contains(event.target)) {
    dropdownMenu.style.display = "none";
  }
});
</script>

<!-- Banner -->
<div class="banner">
  <h1>WELCOME STUDENT</h1>
</div>

<!-- Profile Card -->
<div class="card">
  <div class="profile-section">

    <div class="profile-picture">
      <img src="<?= $student['profile_photo'] ?? 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png' ?>" alt="Profile" />
      <?php if (!$student): ?>
        <input type="file" name="profile_photo" id="profileUpload" hidden>
        <label for="profileUpload" class="upload-btn">Upload Profile</label>
      <?php endif; ?>
    </div>

    <div class="form-section">
      <?php if (!$student): ?>
        <form method="POST" action="submit.php" enctype="multipart/form-data">
          <div class="input-group">
            <label>First Name</label>
            <input type="text" name="first_name" required>
          </div>
          <div class="input-group">
            <label>Last Name</label>
            <input type="text" name="last_name" required>
          </div>
          <div class="input-group">
            <label>Email</label>
            <input type="email" name="email" required>
          </div>
          <div class="input-group">
            <label>Faculty</label>
            <input type="text" name="faculty_name" required>
          </div>
          <div class="input-group">
            <label>Course</label>
            <input type="text" name="student_course" required>
          </div>
          <div class="input-group">
            <label>Year</label>
            <input type="text" name="year_of_study" required>
          </div>
          <button type="submit" class="submit-btn">Save Student</button>
        </form>
      <?php else: ?>
        <div style="text-align:left;">
          <p><strong>Name:</strong> <?= htmlspecialchars($student['first_name']) ?> <?= htmlspecialchars($student['last_name']) ?></p>
          <p><strong>Email:</strong> <?= htmlspecialchars($student['email']) ?></p>
          <p><strong>Faculty:</strong> <?= htmlspecialchars($student['faculty_name']) ?></p>
          <p><strong>Course:</strong> <?= htmlspecialchars($student['student_course']) ?></p>
          <p><strong>Year:</strong> <?= htmlspecialchars($student['year_of_study']) ?></p>
        </div>
        <form method="GET" action="edit_profile.php">
          <button class="submit-btn" style="margin-top: 15px;">Update Information</button>
        </form>
      <?php endif; ?>
    </div>

  </div>
</div>

</body>
</html>
