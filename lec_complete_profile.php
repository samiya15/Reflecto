<?php
session_start();
include("include/dbconnect.php");

// Redirect if not a logged-in lecturer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) {
    header("Location: signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch faculties
$faculties = $conn->query("SELECT faculty_id, faculty_name FROM faculty")->fetch_all(MYSQLI_ASSOC);

// Fetch courses
$courses = $conn->query("SELECT course_id, course_name FROM course")->fetch_all(MYSQLI_ASSOC);

// Fetch units
$units = $conn->query("SELECT unit_id, unit_name FROM units")->fetch_all(MYSQLI_ASSOC);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $faculty_ids = $_POST['faculty_ids'] ?? [];
    $course_ids = $_POST['course_ids'] ?? [];
    $unit_ids = $_POST['unit_ids'] ?? [];

    // Validate selections
    if (empty($faculty_ids) || empty($course_ids) || empty($unit_ids)) {
        echo "<script>alert('Please select at least one faculty, course, and unit.'); window.history.back();</script>";
        exit();
    }

    // Get lecturer_id
    $stmt = $conn->prepare("SELECT lecturer_id FROM lecturers WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $lecturer = $result->fetch_assoc();
    $stmt->close();

    if (!$lecturer) {
        die("Error: Lecturer not found.");
    }

    $lecturer_id = $lecturer['lecturer_id'];

    // Start transaction
    $conn->begin_transaction();

    try {
        // Mark profile as completed
        $stmt = $conn->prepare("UPDATE lecturers SET profile_completed = 1 WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        // Clear previous links
        $conn->query("DELETE FROM lecturer_faculties WHERE lecturer_id = $lecturer_id");
        $conn->query("DELETE FROM lecturer_courses WHERE lecturer_id = $lecturer_id");
        $conn->query("DELETE FROM lecturer_units WHERE lecturer_id = $lecturer_id");

        // Insert faculty links
        $stmt = $conn->prepare("INSERT INTO lecturer_faculties (lecturer_id, faculty_id) VALUES (?, ?)");
        foreach ($faculty_ids as $fid) {
            $fid = intval($fid);
            $stmt->bind_param("ii", $lecturer_id, $fid);
            $stmt->execute();
        }
        $stmt->close();

        // Insert course links
        $stmt = $conn->prepare("INSERT INTO lecturer_courses (lecturer_id, course_id) VALUES (?, ?)");
        foreach ($course_ids as $cid) {
            $cid = intval($cid);
            $stmt->bind_param("ii", $lecturer_id, $cid);
            $stmt->execute();
        }
        $stmt->close();

        // Insert unit links
        $stmt = $conn->prepare("INSERT INTO lecturer_units (lecturer_id, unit_id) VALUES (?, ?)");
        foreach ($unit_ids as $uid) {
            $uid = intval($uid);
            $stmt->bind_param("ii", $lecturer_id, $uid);
            $stmt->execute();
        }
        $stmt->close();

        // Commit transaction
        $conn->commit();
        header("Location: lecdash.php");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        die("Error saving profile: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Complete Your Profile</title>
  <link rel="stylesheet" href="lec_complete_profile.css">
</head>
<body>

<div id="modal" class="modal" style="display: flex;">
  <div class="modal-content">
    <h2>Complete Your Profile</h2>
    <form method="post">

      <!-- Faculty Selection -->
      <div class="input-group">
        <label for="faculty_ids">Select Faculty (Ctrl/Cmd to choose multiple)</label>
        <select name="faculty_ids[]" id="faculty_ids" multiple required>
          <?php foreach ($faculties as $fac): ?>
            <option value="<?= $fac['faculty_id'] ?>">
              <?= htmlspecialchars($fac['faculty_name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Course Selection -->
      <div class="input-group">
        <label for="course_ids">Select Course(s) Taught</label>
        <select name="course_ids[]" id="course_ids" multiple required>
          <?php foreach ($courses as $c): ?>
            <option value="<?= $c['course_id'] ?>">
              <?= htmlspecialchars($c['course_name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Unit Selection -->
      <div class="input-group">
        <label for="unit_ids">Select Unit(s) Taught</label>
        <select name="unit_ids[]" id="unit_ids" multiple required>
          <?php foreach ($units as $u): ?>
            <option value="<?= $u['unit_id'] ?>">
              <?= htmlspecialchars($u['unit_name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Submit -->
      <button type="submit">Submit Profile</button>
    </form>
  </div>
</div>

</body>
</html>
