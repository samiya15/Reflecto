<?php
session_start();
include("include/dbconnect.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT 
        r.response_text,
        r.responded_at,
        u.firstName AS lecturer_name,
        f.cleaned_text,
        f.is_anonymous
    FROM feedback_responses r
    JOIN feedback f ON r.feedback_id = f.id
    JOIN lecturers l ON r.lecturer_id = l.lecturer_id
    JOIN users u ON l.user_id = u.user_id
    WHERE (f.user_id = ? OR f.is_anonymous = 1)
    AND r.response_text IS NOT NULL
    ORDER BY r.responded_at DESC
");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="student_view_responses.css">
</head>
<body>
 <nav class="navbar">
    <div class="nav-left">
      <ul>
        <a href="studentdash.php">Dashboard</a>
       
        </ul>
    </div>
    <div class="nav-right">
        <a href="signin.php" class="logout-btn">Log Out</a>
    </div>
    </nav>
<h2>Responses from Lecturers</h2>
<div class="response-container">
<?php if ($result->num_rows === 0): ?>
  <p>You have no responses yet.</p>
<?php else: ?>
 <?php while ($row = $result->fetch_assoc()): ?>
  <div class="response-card">
    <p><strong>Feedback:</strong> <?= htmlspecialchars($row['cleaned_text']) ?></p>
    <p><strong>Response:</strong> <?= nl2br(htmlspecialchars($row['response_text'])) ?></p>
    <p><strong>Lecturer:</strong> <?= htmlspecialchars($row['lecturer_name']) ?></p>
    <p><strong>Submitted:</strong> <?= $row['is_anonymous'] ? 'Anonymously' : 'With details' ?></p>
    <p><strong>Responded on:</strong> <?= htmlspecialchars(date("F j, Y, g:i a", strtotime($row['responded_at']))) ?></p>
  </div>
<?php endwhile; ?>

<?php endif; ?>
</div>
   
</body>
</html>