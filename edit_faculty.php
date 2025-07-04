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

$faculty_id = (int)$_GET['id'];

// Fetch current name
$stmt = $conn->prepare("SELECT faculty_name FROM faculty WHERE faculty_id = ?");
$stmt->bind_param("i", $faculty_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Faculty not found.";
    exit();
}

$faculty = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newName = trim($_POST['faculty_name']);
    if (empty($newName)) {
        echo "Faculty name cannot be empty.";
        exit();
    }

    $update = $conn->prepare("UPDATE faculty SET faculty_name = ? WHERE faculty_id = ?");
    $update->bind_param("si", $newName, $faculty_id);
    if ($update->execute()) {
        header("Location: manage_faculties.php");
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
    <input type="text" name="faculty_name" value="<?= htmlspecialchars($faculty['faculty_name']) ?>" required>
    <button type="submit">Save Changes</button>
    <a href="manage_faculties.php" class="btn cancel">Cancel</a>
  </form>
</div>
</body>
</html>
