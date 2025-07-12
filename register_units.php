<?php
session_start();
include("include/dbconnect.php");

// Ensure student is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get student info including course name
$studentQuery = $conn->prepare("
    SELECT s.student_id, s.course_id, s.year_of_study, c.course_name 
    FROM students s
    JOIN course c ON s.course_id = c.course_id
    WHERE s.user_id = ?
");
$studentQuery->bind_param("i", $user_id);
$studentQuery->execute();
$student = $studentQuery->get_result()->fetch_assoc();

$student_id = $student['student_id'];
$course_id = $student['course_id'];
$course_name = $student['course_name'];
$year = $student['year_of_study'];

// Fetch available units for student's course and year
$unitQuery = $conn->prepare("SELECT unit_id, unit_name FROM units WHERE course_id = ? AND year_of_study = ?");
$unitQuery->bind_param("ii", $course_id, $year);
$unitQuery->execute();
$units = $unitQuery->get_result()->fetch_all(MYSQLI_ASSOC);

// Handle unit registration
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['unit_ids'])) {
    $unit_ids = $_POST['unit_ids'];

    // Delete previous registrations (optional)
    $conn->query("DELETE FROM student_units WHERE student_id = $student_id");

    // Register selected units
    $insert = $conn->prepare("INSERT INTO student_units (student_id, unit_id) VALUES (?, ?)");
    foreach ($unit_ids as $uid) {
        $uid = intval($uid);
        $insert->bind_param("ii", $student_id, $uid);
        $insert->execute();
    }

    echo "<script>alert('Units registered successfully.'); window.location.href = 'studentdash.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register for Units</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 30px; }
        h2 { color: #333; }
        .form-section { margin-top: 20px; }
        label { display: block; margin-bottom: 8px; }
        .unit-list { margin-bottom: 20px; }
        .unit-list label { display: block; margin-bottom: 4px; }
        button {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 10px 16px;
            cursor: pointer;
            border-radius: 4px;
            font-size: 16px;
        }
        button:hover { background: #45a049; }
    </style>
</head>
<body>

<h2>Register for Units</h2>

<p><strong>Course Name:</strong> <?= htmlspecialchars($course_name) ?> |
   <strong>Year:</strong> <?= htmlspecialchars($year) ?></p>

<form method="post">
    <div class="form-section">
        <label>Select Units to Register:</label>
        <div class="unit-list">
            <?php foreach ($units as $unit): ?>
                <label>
                    <input type="checkbox" name="unit_ids[]" value="<?= $unit['unit_id'] ?>">
                    <?= htmlspecialchars($unit['unit_name']) ?>
                </label>
            <?php endforeach; ?>
        </div>
    </div>

    <button type="submit">Submit Units</button>
</form>

</body>
</html>
