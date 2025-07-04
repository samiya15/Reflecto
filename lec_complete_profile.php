<?php
session_start();
include("include/dbconnect.php");

// Make sure lecturer is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) {
    header("Location: signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get name/email from users table
$stmt = $conn->prepare("SELECT firstName, lastName, email FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_data = $stmt->get_result()->fetch_assoc();

// Get faculties list for dropdown
$faculties_result = $conn->query("SELECT faculty_id, faculty_name FROM faculty");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Complete Profile</title>
  <link rel="stylesheet" href="lec_profile.css">
</head>
<body>
<div class="container">
  <h2>Complete Your Profile</h2>
  <form action="save_lec_profile.php" method="post">
    <div class="input-group">
      <label>Name</label>
      <input type="text" readonly value="<?= htmlspecialchars($user_data['firstName'].' '.$user_data['lastName']) ?>">
    </div>
    <div class="input-group">
      <label>Email</label>
      <input type="email" readonly value="<?= htmlspecialchars($user_data['email']) ?>">
    </div>
    <div class="input-group">
      <label>Select Faculties (hold Ctrl to select multiple)</label>
      <select name="faculty_ids[]" multiple required>
        <?php while($faculty = $faculties_result->fetch_assoc()): ?>
          <option value="<?= $faculty['faculty_id'] ?>"><?= htmlspecialchars($faculty['faculty_name']) ?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="input-group">
      <label>Courses Taught (comma separated)</label>
      <textarea name="course_taught" required></textarea>
    </div>
    <div class="input-group">
      <label>Units Taught (comma separated)</label>
      <textarea name="unit_taught" required></textarea>
    </div>
    <button type="submit">Save Profile</button>
  </form>
</div>
</body>
</html>
