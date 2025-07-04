<?php
session_start();
include("include/dbconnect.php");

// Make sure student is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch faculties to populate dropdown
$faculties = [];
$fstmt = $conn->prepare("SELECT faculty_id, faculty_name FROM faculty");
$fstmt->execute();
$fresult = $fstmt->get_result();
while ($row = $fresult->fetch_assoc()) {
    $faculties[] = $row;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $faculty_id = $_POST['faculty_id'];
    $course_name = trim($_POST['course_name']);

    if (empty($faculty_id) || empty($course_name)) {
        $error = "All fields are required.";
    } else {
        $update = $conn->prepare("
            UPDATE students
            SET faculty_id = ?, student_course = ?, status = 'pending'
            WHERE user_id = ?
        ");
        $update->bind_param("isi", $faculty_id, $course_name, $user_id);
        if ($update->execute()) {
            header("Location: studentdash.php");
            exit();
        } else {
            $error = "Error updating profile: " . $update->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Complete Profile</title>
  <link rel="stylesheet" href="student_complete_profile.css"/>
</head>
<body>
  <div class="container">
    <h2>Complete Your Profile</h2>
    <?php if (!empty($error)): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="post">
      <div class="input-group">
        <label for="faculty_id">Select Faculty</label>
        <select name="faculty_id" id="faculty_id" required>
          <option value="">-- Choose Faculty --</option>
          <?php foreach ($faculties as $faculty): ?>
            <option value="<?= $faculty['faculty_id'] ?>">
              <?= htmlspecialchars($faculty['faculty_name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="input-group">
        <label for="course_name">Course Name</label>
        <input type="text" name="course_name" id="course_name" required>
      </div>
      <button type="submit">Submit Profile</button>
    </form>
  </div>
</body>
</html>
