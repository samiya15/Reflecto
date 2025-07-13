<?php
session_start();
include("include/dbconnect.php");

// Enable full error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Ensure student is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch faculties
$faculties = [];
$fstmt = $conn->prepare("SELECT faculty_id, faculty_name FROM faculty ORDER BY faculty_name");
$fstmt->execute();
$fresult = $fstmt->get_result();
while ($row = $fresult->fetch_assoc()) {
    $faculties[] = $row;
}

// Fetch all courses with faculty_id
$courses = [];
$cstmt = $conn->prepare("SELECT course_id, course_name, faculty_id FROM course ORDER BY course_name");
$cstmt->execute();
$cresult = $cstmt->get_result();
while ($row = $cresult->fetch_assoc()) {
    $courses[] = $row;
}

// Fetch all units with course_id
$units = [];
$ustmt = $conn->prepare("SELECT unit_id, unit_name, course_id FROM units ORDER BY unit_name");
$ustmt->execute();
$uresult = $ustmt->get_result();
while ($row = $uresult->fetch_assoc()) {
    $units[] = $row;
}

$error = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $faculty_id = (int) $_POST['faculty_id'];
    $course_id = (int) $_POST['course_id'];
    $year_of_study = (int) $_POST['year_of_study'];
    $unit_id = (int) $_POST['unit_id'];

    if (empty($faculty_id) || empty($course_id) || empty($year_of_study) || empty($unit_id)) {
        $error = "All fields are required.";
    } else {
        $check = $conn->prepare("SELECT * FROM students WHERE user_id = ?");
        $check->bind_param("i", $user_id);
        $check->execute();
        $checkResult = $check->get_result();
        if ($checkResult->num_rows === 0) {
            $error = "Student record not found.";
        } else {
            $update = $conn->prepare("UPDATE students SET faculty_id = ?, course_id = ?, year_of_study = ?, unit_id = ? WHERE user_id = ?");
            $update->bind_param("iiiii", $faculty_id, $course_id, $year_of_study, $unit_id, $user_id);
            if ($update->execute()) {
                if ($update->affected_rows > 0) {
                    header("Location: studentdash.php");
                    exit();
                } else {
                    $error = "Update executed but no changes were made.";
                }
            } else {
                $error = "Error updating profile: " . $update->error;
            }
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
      <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="post">
      <div class="input-group">
        <label for="faculty_id">Select Faculty</label>
        <select name="faculty_id" id="faculty_id" required>
          <option value="">-- Choose Faculty --</option>
          <?php foreach ($faculties as $faculty): ?>
            <option value="<?= $faculty['faculty_id'] ?>"><?= htmlspecialchars($faculty['faculty_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="input-group">
        <label for="course_id">Select Course</label>
        <select name="course_id" id="course_id" required>
          <option value="">-- Choose Course --</option>
        </select>
      </div>

      <div class="input-group">
        <label for="unit_id">Select Unit</label>
        <select name="unit_id" id="unit_id" required>
          <option value="">-- Choose Unit --</option>
        </select>
      </div>

      <div class="input-group">
        <label for="year_of_study">Year of Study</label>
        <select name="year_of_study" id="year_of_study" required>
          <option value="">-- Choose Year --</option>
          <option value="1">Year 1</option>
          <option value="2">Year 2</option>
          <option value="3">Year 3</option>
          <option value="4">Year 4</option>
        </select>
      </div>

      <button type="submit">Submit Profile</button>
    </form>
  </div>

  <script>
    const allCourses = <?= json_encode($courses) ?>;
    const allUnits = <?= json_encode($units) ?>;

    const facultySelect = document.getElementById('faculty_id');
    const courseSelect = document.getElementById('course_id');
    const unitSelect = document.getElementById('unit_id');

    facultySelect.addEventListener('change', () => {
      const facultyId = parseInt(facultySelect.value);
      courseSelect.innerHTML = '<option value="">-- Choose Course --</option>';
      unitSelect.innerHTML = '<option value="">-- Choose Unit --</option>';

      allCourses.forEach(course => {
        if (course.faculty_id === facultyId) {
          const option = document.createElement('option');
          option.value = course.course_id;
          option.textContent = course.course_name;
          courseSelect.appendChild(option);
        }
      });
    });

    courseSelect.addEventListener('change', () => {
      const courseId = parseInt(courseSelect.value);
      unitSelect.innerHTML = '<option value="">-- Choose Unit --</option>';

      allUnits.forEach(unit => {
        if (unit.course_id === courseId) {
          const option = document.createElement('option');
          option.value = unit.unit_id;
          option.textContent = unit.unit_name;
          unitSelect.appendChild(option);
        }
      });
    });
  </script>
</body>
</html>
