<?php
session_start();
include("include/dbconnect.php");

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    die("Not logged in.");
}

$query = $conn->prepare("SELECT * FROM students WHERE user_id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
$student = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Student Profile</title>
  <link rel="stylesheet" href="test.css" />
</head>
<body>

<div class="card">
  <div class="profile-section">
    <div class="profile-picture">
      <img src="<?= $student['profile_photo'] ?? 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png' ?>" alt="Profile" />
    </div>

    <?php if (!$student): ?>
    <form method="POST" action="submit.php" enctype="multipart/form-data">
      <input type="file" name="profile_photo" id="profileUpload" hidden />
      <label for="profileUpload" class="upload-btn">Upload Profile</label>
  </div>

  <div class="form-section">
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
        <input type="text" name="faculty" required>
      </div>
      <div class="input-group">
        <label>Course</label>
        <input type="text" name="course" required>
      </div>
      <div class="input-group">
        <label>Year</label>
        <input type="text" name="year" required>
      </div>
      <button type="submit" class="submit-btn">Save Student</button>
    </form>
    <?php else: ?>
      <!-- Display saved profile -->
      <div style="text-align:left;">
        <p><strong>Name:</strong> <?= $student['first_name'] ?> <?= $student['last_name'] ?></p>
        <p><strong>Email:</strong> <?= $student['email'] ?></p>
        <p><strong>Faculty:</strong> <?= $student['faculty'] ?></p>
        <p><strong>Course:</strong> <?= $student['course'] ?></p>
        <p><strong>Year:</strong> <?= $student['year'] ?></p>
      </div>
      <form method="GET" action="edit_profile.php">
        <button class="submit-btn" style="margin-top: 15px;">Update Information</button>
      </form>
    <?php endif; ?>
  </div>
</div>

</body>
</html>
