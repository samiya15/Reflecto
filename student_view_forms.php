<?php
session_start();
include("include/dbconnect.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get student ID
$studentQuery = $conn->prepare("SELECT student_id FROM students WHERE user_id = ?");
$studentQuery->bind_param("i", $user_id);
$studentQuery->execute();
$studentResult = $studentQuery->get_result();
$student = $studentResult->fetch_assoc();
$student_id = $student['student_id'];

// Get registered course_ids and unit_ids
$course_ids = [];
$unit_ids = [];

$courseRes = $conn->query("SELECT course_id FROM student_courses WHERE student_id = $student_id");
while ($row = $courseRes->fetch_assoc()) {
    $course_ids[] = $row['course_id'];
}

$unitRes = $conn->query("SELECT unit_id FROM student_units WHERE student_id = $student_id");
while ($row = $unitRes->fetch_assoc()) {
    $unit_ids[] = $row['unit_id'];
}

if (empty($course_ids) || empty($unit_ids)) {
    echo "No registered courses/units found.";
    exit();
}

$courseList = implode(",", array_map("intval", $course_ids));
$unitList = implode(",", array_map("intval", $unit_ids));

// Only show forms not yet submitted by this student
$query = $conn->query("
    SELECT lff.form_id, lff.assigned_unit_id, ff.title, ff.created_at, c.course_name, u.unit_name
    FROM lecturer_feedback_forms lff
    JOIN feedback_forms ff ON ff.form_id = lff.form_id
    JOIN course c ON c.course_id = lff.assigned_course_id
    JOIN units u ON u.unit_id = lff.assigned_unit_id
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
