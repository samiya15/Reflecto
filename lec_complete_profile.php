<?php
session_start();
include("include/dbconnect.php");

// Make sure a lecturer is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) {
    header("Location: signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];


// Get the user's name and email
$stmt = $conn->prepare("SELECT firstName, lastName, email FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Complete Your Profile</title>
  <link rel="stylesheet" href="lec_profile.css" />
</head>
<body>
  <!-- Page Container -->
  <div class="container">
    <h2>Complete Your Profile</h2>
    <form action="save_lec_profile.php" method="post">
      <!-- Name and Email will be pre-filled -->
      <div class="input-group">
        <label>Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($user_data['firstName']. ' ' . $user_data['lastName'])?>" readonly>
      </div>

      <div class="input-group">
        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user_data['email'])?>" readonly>
      </div>

      <div class="input-group">
        <label>Faculty</label>
        <input type="text" name="faculty_name" required>
      </div>

      <div class="input-group">
        <label>Courses Taught (Separate by comma)</label>
        <textarea  name="course_taught" rows="2" required></textarea>
      </div>

      <div class="input-group">
        <label>Units Taught (separate by comma)</label>
        <textarea name="unit_taught" rows="3" required></textarea>
      </div>

      <button type="submit">Save Profile</button>
    </form>
  </div>
</body>
</html>
