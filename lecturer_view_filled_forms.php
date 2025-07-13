<?php
session_start();
include("include/dbconnect.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) {
    header("Location: signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get lecturer_id
$lec = $conn->query("SELECT lecturer_id FROM lecturers WHERE user_id = $user_id")->fetch_assoc();
$lecturer_id = $lec['lecturer_id'];

// Get filled feedback forms assigned to this lecturer
$query = $conn->query("
    SELECT DISTINCT ff.form_id, ff.title, ff.created_at
    FROM lecturer_feedback_forms lff
    JOIN feedback_forms ff ON lff.form_id = ff.form_id
    JOIN submitted_feedback sf ON sf.form_id = lff.form_id AND sf.unit_id = lff.assigned_unit_id
    WHERE lff.lecturer_id = $lecturer_id
    ORDER BY ff.created_at DESC
");

$forms = $query->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Filled Feedback</title>
    <link rel="stylesheet" href="lecturer_view_forms.css">
</head>
<body>
<h2>Feedback Forms with Responses</h2>

<?php if (empty($forms)): ?>
    <p>No feedback submissions yet.</p>
<?php else: ?>
    <ul>
        <?php foreach ($forms as $f): ?>
            <li>
                <?= htmlspecialchars($f['title']) ?> |
                <a href="summary_feedback.php?form_id=<?= $f['form_id'] ?>">View Summary</a>
            </li>
        <?php endforeach; ?>
         <a href="lecdash.php"><button class="back">Back</button></a>
    </ul>
<?php endif; ?>
</body>
</html>
