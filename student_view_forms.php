<?php
session_start();
include("include/dbconnect.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$res = $conn->query("SELECT student_id FROM students WHERE user_id = $user_id");
$studentRow = $res->fetch_assoc();
$student_id = $studentRow['student_id'];

// Get student courses and units
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

// Show only feedback forms the student hasn't submitted (per form_id + unit_id)
$query = $conn->query("
  SELECT lff.form_id, lff.assigned_unit_id, ff.title, ff.created_at, c.course_name, u.unit_name
  FROM lecturer_feedback_forms lff
  JOIN feedback_forms ff ON lff.form_id = ff.form_id
  JOIN course c ON lff.assigned_course_id = c.course_id
  JOIN units u ON lff.assigned_unit_id = u.unit_id
  WHERE lff.assigned_course_id IN ($courseList)
    AND lff.assigned_unit_id IN ($unitList)
    AND lff.is_published = 1
    AND NOT EXISTS (
        SELECT 1 FROM submitted_feedback sf
        WHERE sf.form_id = lff.form_id
          AND sf.unit_id = lff.assigned_unit_id
          AND sf.student_id = $student_id
    )
");

$forms = $query->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Feedback Forms</title>
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
          <input type="hidden" name="unit_id" value="<?= $form['assigned_unit_id'] ?>">
          <button type="submit">Fill Feedback</button>
        </form>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</body>
</html>
