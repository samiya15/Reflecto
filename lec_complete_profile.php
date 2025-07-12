<?php
session_start();
include("include/dbconnect.php");

// Ensure lecturer is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) {
    header("Location: signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch all faculties
$faculties = $conn->query("SELECT faculty_id, faculty_name FROM faculty")->fetch_all(MYSQLI_ASSOC);

// Fetch all courses
$courses = $conn->query("SELECT course_id, course_name FROM course")->fetch_all(MYSQLI_ASSOC);

// Fetch all units
$units = $conn->query("SELECT unit_id, unit_name FROM units")->fetch_all(MYSQLI_ASSOC);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $faculty_ids = $_POST['faculty_ids'] ?? [];
    $course_ids = $_POST['course_ids'] ?? [];
    $unit_ids = $_POST['unit_ids'] ?? [];

    if (empty($faculty_ids) || empty($course_ids) || empty($unit_ids)) {
        die("Please select at least one faculty, course, and unit.");
    }

    // Update lecturer's profile status
    $stmt = $conn->prepare("UPDATE lecturers SET profile_completed = 1 WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    // Get lecturer_id
    $lecturer = $conn->query("SELECT lecturer_id FROM lecturers WHERE user_id = $user_id")->fetch_assoc();
    $lecturer_id = $lecturer['lecturer_id'];

    // Clear previous links
    $conn->query("DELETE FROM lecturer_faculties WHERE lecturer_id = $lecturer_id");
    $conn->query("DELETE FROM lecturer_courses WHERE lecturer_id = $lecturer_id");
    $conn->query("DELETE FROM lecturer_units WHERE lecturer_id = $lecturer_id");

    // Insert new faculty links
    $insertFac = $conn->prepare("INSERT INTO lecturer_faculties (lecturer_id, faculty_id) VALUES (?, ?)");
    foreach ($faculty_ids as $fid) {
        $fid = intval($fid);
        $insertFac->bind_param("ii", $lecturer_id, $fid);
        $insertFac->execute();
    }

    // Insert new course links
    $insertCourse = $conn->prepare("INSERT INTO lecturer_courses (lecturer_id, course_id) VALUES (?, ?)");
    foreach ($course_ids as $cid) {
        $cid = intval($cid);
        $insertCourse->bind_param("ii", $lecturer_id, $cid);
        $insertCourse->execute();
    }

    // Insert new unit links
    $insertUnit = $conn->prepare("INSERT INTO lecturer_units (lecturer_id, unit_id) VALUES (?, ?)");
    foreach ($unit_ids as $uid) {
        $uid = intval($uid);
        $insertUnit->bind_param("ii", $lecturer_id, $uid);
        $insertUnit->execute();
    }

    header("Location: lecdash.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Complete Lecturer Profile</title>
  <link rel="stylesheet" href="lec_complete_profile.css">
</head>
<body>
  <div id="modal" class="modal" style="display:block;">
    <div class="modal-content">
      <h2>Complete Your Profile</h2>
      <form method="post">

        <!-- Faculties -->
        <div class="input-group">
          <label>Select Faculty (Ctrl/Cmd to choose multiple)</label>
          <select name="faculty_ids[]" multiple required>
            <?php foreach ($faculties as $fac): ?>
              <option value="<?= $fac['faculty_id'] ?>"><?= htmlspecialchars($fac['faculty_name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Courses -->
        <div class="input-group">
          <label>Select Course(s) Taught</label>
          <select name="course_ids[]" multiple required>
            <?php foreach ($courses as $c): ?>
              <option value="<?= $c['course_id'] ?>"><?= htmlspecialchars($c['course_name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Units -->
        <div class="input-group">
          <label>Select Unit(s) Taught</label>
          <select name="unit_ids[]" multiple required>
            <?php foreach ($units as $u): ?>
              <option value="<?= $u['unit_id'] ?>"><?= htmlspecialchars($u['unit_name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <button type="submit">Submit Profile</button>
      </form>
    </div>
  </div>
</body>
</html>
