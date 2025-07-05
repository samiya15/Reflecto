<?php
session_start();
include("include/dbconnect.php");

// Ensure lecturer is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) {
    header("Location: signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $faculty_ids = $_POST['faculty_ids'];
    $course_taught = $_POST['course_taught'];
    $unit_taught = $_POST['unit_taught'];

    if (empty($faculty_ids) || !is_array($faculty_ids)) {
        die("You must select at least one faculty.");
    }

    // Update lecturers table
    $stmt = $conn->prepare("
        UPDATE lecturers
        SET course_taught = ?, unit_taught = ?, profile_completed = 1
        WHERE user_id = ?
    ");
    $stmt->bind_param("ssi", $course_taught, $unit_taught, $user_id);
    $stmt->execute();

    // Get lecturer_id
    $getLecturerId = $conn->prepare("SELECT lecturer_id FROM lecturers WHERE user_id = ?");
    $getLecturerId->bind_param("i", $user_id);
    $getLecturerId->execute();
    $lecturerResult = $getLecturerId->get_result();
    $lecturerData = $lecturerResult->fetch_assoc();
    $lecturer_id = $lecturerData['lecturer_id'];

    // Remove previous faculties
    $conn->query("DELETE FROM lecturer_faculties WHERE lecturer_id = " . intval($lecturer_id));

    // Insert new faculties
    $insertLink = $conn->prepare("INSERT INTO lecturer_faculties (lecturer_id, faculty_id) VALUES (?, ?)");
    foreach ($faculty_ids as $fid) {
        $insertLink->bind_param("ii", $lecturer_id, $fid);
        $insertLink->execute();
    }

    header("Location: lecdash.php");
    exit();
}

// Fetch faculties
$facultiesResult = $conn->query("SELECT faculty_id, faculty_name FROM faculty");
$faculties = $facultiesResult->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Complete Lecturer Profile</title>
  <link rel="stylesheet" href="lec_complete_profile.css" />
</head>
<body>
<div id="modal" class="modal" style="display: block;">
  <div class="modal-content">
    <h2>Complete Your Profile</h2>
    <form method="post">
      <div class="input-group">
        <label>Faculties (hold Ctrl/Cmd to select multiple)</label>
        <select name="faculty_ids[]" multiple required>
          <?php foreach ($faculties as $f): ?>
            <option value="<?= htmlspecialchars($f['faculty_id']) ?>">
              <?= htmlspecialchars($f['faculty_name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="input-group">
        <label>Course Taught</label>
        <input type="text" name="course_taught" required>
      </div>
      <div class="input-group">
        <label>Unit Taught</label>
        <input type="text" name="unit_taught" required>
      </div>
      <button type="submit">Submit Profile</button>
    </form>
  </div>
</div>
</body>
</html>
