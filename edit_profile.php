<?php
session_start();
include("include/dbconnect.php");

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch the current profile data
$stmt = $conn->prepare("SELECT * FROM students WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    echo "Profile not found.";
    exit;
}

// If form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form inputs
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $faculty_name = $_POST['faculty_name'];
    $student_course = $_POST['student_course'];
    $year_of_study = $_POST['year_of_study'];

    // Handle optional profile photo upload
    $profilePath = $student['profile_photo'];
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === 0) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $filename = uniqid() . "_" . basename($_FILES["profile_photo"]["name"]);
        $targetFile = $targetDir . $filename;

        if (move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $targetFile)) {
            $profilePath = $targetFile;
        }
    }

    // Update the profile in the database
    $update = $conn->prepare("
        UPDATE students
        SET first_name = ?, last_name = ?, email = ?, faculty_name = ?, student_course = ?, year_of_study = ?, profile_photo = ?
        WHERE user_id = ?
    ");
    $update->bind_param(
        "sssssssi",
        $first_name,
        $last_name,
        $email,
        $faculty_name,
        $student_course,
        $year_of_study,
        $profilePath,
        $user_id
    );
    $update->execute();

    // Redirect back to dashboard
    header("Location: studentdash.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Edit Profile</title>
  <link rel="stylesheet" href="dashboard.css">
</head>
<body>

<div class="card">
  <h2>Edit Your Profile</h2>
  <div class="profile-section">
    <div class="profile-picture">
      <img src="<?= htmlspecialchars($student['profile_photo'] ?: 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png') ?>" alt="Profile">
    </div>
    <form method="POST" enctype="multipart/form-data">
      <input type="file" name="profile_photo" id="profileUpload" hidden />
      <label for="profileUpload" class="upload-btn">Upload New Photo</label>
  </div>

  <div class="form-section">
      <div class="input-group">
        <label>First Name</label>
        <input type="text" name="first_name" value="<?= htmlspecialchars($student['first_name']) ?>" required>
      </div>
      <div class="input-group">
        <label>Last Name</label>
        <input type="text" name="last_name" value="<?= htmlspecialchars($student['last_name']) ?>" required>
      </div>
      <div class="input-group">
        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($student['email']) ?>" required>
      </div>
      <div class="input-group">
        <label>Faculty</label>
        <input type="text" name="faculty_name" value="<?= htmlspecialchars($student['faculty_name']) ?>" required>
      </div>
      <div class="input-group">
        <label>Course</label>
        <input type="text" name="student_course" value="<?= htmlspecialchars($student['student_course']) ?>" required>
      </div>
      <div class="input-group">
        <label>Year</label>
        <input type="text" name="year_of_study" value="<?= htmlspecialchars($student['year_of_study']) ?>" required>
      </div>
      <button type="submit" class="submit-btn">Save Changes</button>
    </form>
  </div>
</div>

</body>
</html>
