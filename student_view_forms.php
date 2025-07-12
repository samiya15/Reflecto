<?php
session_start();
include("include/dbconnect.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: signin.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$getStudent = $conn->prepare("SELECT student_id FROM students WHERE user_id = ?");
$getStudent->bind_param("i", $user_id);
$getStudent->execute();
$studentRes = $getStudent->get_result();
$studentData = $studentRes->fetch_assoc();

if (!$studentData) {
    echo "Student not found for user ID $user_id";
    exit();
}

$student_id = $studentData['student_id'];




// Get student's courses and units
$courses = $conn->query("SELECT course_id FROM student_courses WHERE student_id = $student_id")->fetch_all(MYSQLI_ASSOC);

$units = $conn->query("SELECT unit_id FROM student_units WHERE student_id = $student_id")->fetch_all(MYSQLI_ASSOC);


$courseIds = array_column($courses, 'course_id');
$unitIds = array_column($units, 'unit_id');

if (empty($courseIds) || empty($unitIds)) {
    echo "No registered courses/units found.";
    exit();
}

$courseList = implode(',', array_map('intval', $courseIds));
$unitList = implode(',', array_map('intval', $unitIds));

// Fetch available feedback forms
$query = $conn->query("  SELECT lff.form_id, ff.title, ff.created_at, c.course_name, u.unit_name
    FROM lecturer_feedback_forms lff
    JOIN feedback_forms ff ON lff.form_id = ff.form_id
    JOIN course c ON lff.assigned_course_id = c.course_id
    JOIN units u ON lff.assigned_unit_id = u.unit_id
    WHERE lff.assigned_course_id IN ($courseList)
      AND lff.assigned_unit_id IN ($unitList)
      AND lff.is_published = 1
      AND lff.form_id NOT IN (
          SELECT form_id FROM submitted_feedback WHERE student_id = $student_id
      )
");

$forms = $query->fetch_all(MYSQLI_ASSOC);
?>


<!DOCTYPE html>
<html>
<head>
  <title>Available Feedback Forms</title>
  <link rel="stylesheet" href="student_view_feedback.css">
</head>
<body>
  <h2>Feedback Forms Assigned To You</h2>

  <?php if (empty($forms)): ?>
    <p>No available feedback forms.</p>
  <?php else: ?>
    <?php foreach ($forms as $form): ?>
      <div class="form-card">
        <h3><?= htmlspecialchars($form['title']) ?></h3>
        <p><strong>Course:</strong> <?= htmlspecialchars($form['course_name']) ?></p>
        <p><strong>Unit:</strong> <?= htmlspecialchars($form['unit_name']) ?></p>
        <form action="fill_feedback_form.php" method="get">
          <input type="hidden" name="form_id" value="<?= $form['form_id'] ?>">
          <button type="submit">Fill Feedback</button>
        </form>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</body>
</html>
