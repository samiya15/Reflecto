<?php
session_start();
include("include/dbconnect.php");

// Only system admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 4) {
    header("Location: signin.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "Invalid request.";
    exit();
}

$course_id = (int)$_GET['id'];

// Fetch current name
$stmt = $conn->prepare("SELECT course_name FROM course WHERE course_id = ?");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "course not found.";
    exit();
}

$faculty = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newName = trim($_POST['course_name']);
    if (empty($newName)) {
        echo "course name cannot be empty.";
        exit();
    }

    $update = $conn->prepare("UPDATE course SET course_name = ? WHERE course_id = ?");
    $update->bind_param("si", $newName, $course_id);
    if ($update->execute()) {
        header("Location: manage_courses.php");
        exit();
    } else {
        echo "Update failed: " . $update->error;
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Faculty</title>
  <link rel="stylesheet" href="manage_faculties.css" />
</head>
<body>
<div class="container">
  <h2>Edit Faculty</h2>
  <form method="post">
    <input type="text" name="course_name" value="<?= htmlspecialchars($faculty['course_name']) ?>" required>
    <button type="submit">Save Changes</button>
    <a href="manage_courses.php" class="btn cancel">Cancel</a>
  </form>
</div>
</body>
</html>
