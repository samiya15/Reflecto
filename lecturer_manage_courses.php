<?php
session_start();
include("include/dbconnect.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) {
    header("Location: signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$lecturer_id = $conn->query("SELECT lecturer_id FROM lecturers WHERE user_id = $user_id")->fetch_assoc()['lecturer_id'] ?? 0;

// Handle add course
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_course_id'])) {
    $course_id = intval($_POST['add_course_id']);

    $check = $conn->prepare("SELECT 1 FROM lecturer_courses WHERE lecturer_id = ? AND course_id = ?");
    $check->bind_param("ii", $lecturer_id, $course_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows == 0) {
        $stmt = $conn->prepare("INSERT INTO lecturer_courses (lecturer_id, course_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $lecturer_id, $course_id);
        $stmt->execute();
    }
    header("Location: lecturer_manage_courses.php");
    exit();
}

// Handle remove course
if (isset($_GET['remove_course_id'])) {
    $remove_course_id = intval($_GET['remove_course_id']);
    $stmt = $conn->prepare("DELETE FROM lecturer_courses WHERE lecturer_id = ? AND course_id = ?");
    $stmt->bind_param("ii", $lecturer_id, $remove_course_id);
    $stmt->execute();
    header("Location: lecturer_manage_courses.php");
    exit();
}

// Get all courses
$all_courses = $conn->query("SELECT * FROM course")->fetch_all(MYSQLI_ASSOC);

// Get assigned courses
$assigned = $conn->query("
    SELECT lc.course_id, c.course_name 
    FROM lecturer_courses lc 
    JOIN course c ON lc.course_id = c.course_id 
    WHERE lc.lecturer_id = $lecturer_id
")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Courses</title>
    <link rel="stylesheet" href="lecturer_manage.css">
</head>
<body>
    <h2>Your Courses</h2>
    <ul>
        <?php foreach ($assigned as $c): ?>
            <li>
                <?= htmlspecialchars($c['course_name']) ?>
                <a href="?remove_course_id=<?= $c['course_id'] ?>">Remove</a>
            </li>
        <?php endforeach; ?>
    </ul>

    <h3>Add New Course</h3>
    <form method="post">
        <select name="add_course_id">
            <?php foreach ($all_courses as $c): ?>
                <option value="<?= $c['course_id'] ?>"><?= htmlspecialchars($c['course_name']) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Add Course</button>
    </form>
</body>
</html>
