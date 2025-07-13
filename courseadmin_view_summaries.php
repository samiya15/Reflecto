<?php
session_start();
include("include/dbconnect.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 3) { // role 3 = course admin
    header("Location: signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$query = $conn->prepare("
    SELECT ff.form_id, ff.title, ff.created_at
    FROM feedback_forms ff
    WHERE ff.created_by = ?
");
$query->bind_param("i", $user_id);
$query->execute();
$forms = $query->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Form Summaries</title>
    <link rel="stylesheet" href="courseadmin_view.css">
</head>
<body>
<h2>Feedback Forms You've Created</h2>

<?php if (empty($forms)): ?>
    <p>No forms created yet.</p>
<?php else: ?>
    <ul>
        <?php foreach ($forms as $form): ?>
            <li>
                <?= htmlspecialchars($form['title']) ?> |
                <a href="cadmin_summary_feedback.php?form_id=<?= $form['form_id'] ?>">View Summary</a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
</body>
</html>
