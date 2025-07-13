<?php
session_start();
include("include/dbconnect.php");

// Ensure only lecturers can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) {
    header("Location: signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get lecturer_id
$lecStmt = $conn->prepare("SELECT lecturer_id FROM lecturers WHERE user_id = ?");
$lecStmt->bind_param("i", $user_id);
$lecStmt->execute();
$lecResult = $lecStmt->get_result();
$lecData = $lecResult->fetch_assoc();
$lecturer_id = $lecData['lecturer_id'] ?? null;

// Get lecturer's faculties
$facultyQuery = $conn->prepare("
    SELECT faculty_id FROM lecturer_faculties WHERE lecturer_id = ?
");
$facultyQuery->bind_param("i", $lecturer_id);
$facultyQuery->execute();
$facultyResult = $facultyQuery->get_result();

$facultyIds = [];
while ($row = $facultyResult->fetch_assoc()) {
    $facultyIds[] = $row['faculty_id'];
}

// Fetch feedback forms created by course admins that match lecturer's faculties
$facultyIdsStr = implode(',', array_map('intval', $facultyIds));
$forms = [];

if (!empty($facultyIdsStr)) {
    $formQuery = $conn->query("  SELECT f.form_id, f.title, f.created_at, f.faculty_id, fa.faculty_name
        FROM feedback_forms f
        JOIN faculty fa ON f.faculty_id = fa.faculty_id
        WHERE f.faculty_id IN ($facultyIdsStr)
    ");

    while ($form = $formQuery->fetch_assoc()) {
        // Fetch standard questions
        $qResult = $conn->query("SELECT * FROM feedback_questions WHERE form_id = {$form['form_id']}");
        $form['questions'] = $qResult->fetch_all(MYSQLI_ASSOC);
        $forms[] = $form;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Receive Feedback Forms</title>
   <link rel="stylesheet" href="lec_recieve_feedback.css">
</head>
<body>
    <h2>Feedback Forms From Course Admins</h2>

    <?php if (empty($forms)): ?>
        <p>No feedback forms available for your faculties.</p>
    <?php else: ?>
        <?php foreach ($forms as $form): ?>
            <div class="form-block">
                <h3><?= htmlspecialchars($form['title']) ?> (<?= htmlspecialchars($form['faculty_name']) ?>)</h3>
                <p><strong>Created:</strong> <?= htmlspecialchars($form['created_at']) ?></p>
                <h4>Standard Questions</h4>
                <ul>
                    <?php foreach ($form['questions'] as $q): ?>
                        <li><?= htmlspecialchars($q['question_type']) ?> - <?= htmlspecialchars($q['question_text']) ?></li>
                    <?php endforeach; ?>
                </ul>
                <form action="lecturer_customize_form.php" method="post">
                    <input type="hidden" name="form_id" value="<?= $form['form_id'] ?>">
                    <button type="submit">Add Custom Questions & Publish</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
