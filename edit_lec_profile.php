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
$stmt = $conn->prepare("SELECT * FROM lecturers WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$lecturer = $result->fetch_assoc();

if (!$lecturer) {
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
    $course_taught = $_POST['course_taught'];
    $unit_taught = $_POST['unit_taught'];
   

    // Handle optional profile photo upload
    $profilePath = $lecturer['profile_photo'];
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
        UPDATE lecturers
        SET first_name = ?, last_name = ?, email = ?, faculty_name = ?, course_taught = ?, unit_taught = ?, profile_photo = ?
        WHERE user_id = ?
    ");
    $update->bind_param(
        "sssssssi",
        $first_name,
        $last_name,
        $email,
        $faculty_name,
        $course_taught,
        $unit_taught,
        $profilePath,
        $user_id
    );
    $update->execute();

    // Redirect back to dashboard
    header("Location: lecdash.php");
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
      <img src="<?= htmlspecialchars($lecturer['profile_photo'] ?: 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png') ?>" alt="Profile">
    </div>
    <form method="POST" enctype="multipart/form-data">
      <input type="file" name="profile_photo" id="profileUpload" hidden />
      <label for="profileUpload" class="upload-btn">Upload New Photo</label>
  </div>

  <div class="form-section">
      <div class="input-group">
        <label>First Name</label>
        <input type="text" name="first_name" value="<?= htmlspecialchars($lecturer['first_name']) ?>" required>
      </div>
      <div class="input-group">
        <label>Last Name</label>
        <input type="text" name="last_name" value="<?= htmlspecialchars($lecturer['last_name']) ?>" required>
      </div>
      <div class="input-group">
        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($lecturer['email']) ?>" required>
      </div>
      <div class="input-group">
        <label>Faculty</label>
        <input type="text" name="faculty_name" value="<?= htmlspecialchars($lecturer['faculty_name']) ?>" required>
      </div>
      <div class="input-group">
        <label>Course Taught</label>
        <input type="text" name="course_taught" value="<?= htmlspecialchars($lecturer['course_taught']) ?>" required>
      </div>
        <div class="input-group">
          <label>Unit Taught</label>
          <input type="text" name="unit_taught" value="<?= htmlspecialchars($lecturer['unit_taught']) ?>" required>
        </div>
           <button type="submit" class="submit-btn">Save Changes</button>
    </form>
  </div>
</div>

</body>
</html>
