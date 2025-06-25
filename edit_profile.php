<?php
session_start();
include("include/dbconnect.php");

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: signin.php"); // Or display an error
    exit;
}

$query = $conn->prepare("SELECT * FROM students WHERE user_id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    header("Location: profile.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Edit Profile</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>

<div class="card">
  <div class="profile-section">
    <div class="profile-picture">
      <img src="<?= $student['profile_photo'] ?? 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png' ?>" alt="Profile" />
    </div>
    <form method="POST" action="submit.php" enctype="multipart/form-data">
      <input type="file" name="profile_photo" id="profileUpload" hidden />
      <label for="profileUpload" class="upload-btn">Upload New Photo</label>
  </div>

  <div class="form-section">
      <input type="hidden" name="update" value="true" />
      <div class="input-group">
        <label>First Name</label>
        <input type="text" name="first_name" value="<?= $student['first_name'] ?>" required>
      </div>
      <div class="input-group">
        <label>Last Name</label>
        <input type="text" name="last_name" value="<?= $student['last_name'] ?>" required>
      </div>
      <div class="input-group">
        <label>Email</label>
        <input type="email" name="email" value="<?= $student['email'] ?>" required>
      </div>
      <div class="input-group">
        <label>Faculty</label>
        <input type="text" name="faculty" value="<?= $student['faculty'] ?>" required>
      </div>
      <div class="input-group">
        <label>Course</label>
        <input type="text" name="course" value="<?= $student['course'] ?>" required>
      </div>
      <div class="input-group">
        <label>Year</label>
        <input type="text" name="year" value="<?= $student['year'] ?>" required>
      </div>
      <button type="submit" class="submit-btn">Save Changes</button>
    </form>
  </div>
</div>

</body>
</html>
