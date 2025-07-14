<?php
session_start();
include("include/dbconnect.php");

// Ensure logged in as Course Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 3) {
    header("Location: signin.php");
    exit();
}

$error = "";
$success = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $faculty_id = isset($_POST['faculty_id']) ? intval($_POST['faculty_id']) : 0;
    $course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;

    if ($faculty_id && $course_id) {
        if (isset($_SESSION['email'])) {
            $email = $_SESSION['email'];

            // Update courseadmin table
            $stmt = $conn->prepare("UPDATE courseadmin SET faculty_id = ?, course_id = ? WHERE email = ?");
            if ($stmt) {
                $stmt->bind_param("iis", $faculty_id, $course_id, $email);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    $_SESSION['faculty_id'] = $faculty_id;
                    $_SESSION['course_id'] = $course_id;

                    header("Location: courseadmin.php");
                    exit();
                } else {
                    $error = "No changes made or record not found.";
                }

                $stmt->close();
            } else {
                $error = "Database error: " . $conn->error;
            }
        } else {
            $error = "Session error: Email not found. Please log in again.";
        }
    } else {
        $error = "Please select both a faculty and a course.";
    }
}

// Fetch all faculties
$faculties = $conn->query("SELECT faculty_id, faculty_name FROM faculty");

// If a faculty is selected, fetch its courses
$selectedFaculty = isset($_POST['faculty_id']) ? intval($_POST['faculty_id']) : 0;
$courses = [];

if ($selectedFaculty > 0) {
    $stmt = $conn->prepare("SELECT course_id, course_name FROM course WHERE faculty_id = ?");
    $stmt->bind_param("i", $selectedFaculty);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Complete Your Profile</title>
    <link rel="stylesheet" href="complete_profile.css" />
</head>
<body>
<div class="form-box">
    <h2>Select Your Faculty and Course</h2>
    <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>
    <form method="post">
        <!-- Faculty Dropdown -->
        <label for="faculty_id">Faculty:</label>
        <select name="faculty_id" id="faculty_id" onchange="this.form.submit()" required>
            <option value="">-- Select Faculty --</option>
            <?php foreach ($faculties as $f): ?>
                <option value="<?= $f['faculty_id'] ?>" <?= ($selectedFaculty == $f['faculty_id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($f['faculty_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <!-- Course Dropdown -->
        <?php if (!empty($courses)): ?>
            <label for="course_id">Course:</label>
            <select name="course_id" id="course_id" required>
                <option value="">-- Select Course --</option>
                <?php foreach ($courses as $c): ?>
                    <option value="<?= $c['course_id'] ?>"><?= htmlspecialchars($c['course_name']) ?></option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>

        <button type="submit">Save</button>
    </form>
</div>
</body>
</html>
