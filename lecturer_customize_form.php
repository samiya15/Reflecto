<?php
session_start();
include("include/dbconnect.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) {
    header("Location: signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get lecturer ID
$lecStmt = $conn->prepare("SELECT lecturer_id FROM lecturers WHERE user_id = ?");
$lecStmt->bind_param("i", $user_id);
$lecStmt->execute();
$lecResult = $lecStmt->get_result();
$lecRow = $lecResult->fetch_assoc();
$lecturer_id = $lecRow['lecturer_id'];

// Get faculty IDs linked to lecturer
$facResult = $conn->query("SELECT faculty_id FROM lecturer_faculties WHERE lecturer_id = $lecturer_id");
$facultyIds = [];
while ($row = $facResult->fetch_assoc()) {
    $facultyIds[] = $row['faculty_id'];
}
$facultyList = implode(',', $facultyIds);

// Get forms created by course admins in same faculty
$formQuery = $conn->query("
    SELECT * FROM feedback_forms 
    WHERE faculty_id IN ($facultyList)
");

// Get lecturer's courses and units
$courses = $conn->query("SELECT c.course_id, c.course_name 
                         FROM lecturer_courses lc 
                         JOIN course c ON lc.course_id = c.course_id 
                         WHERE lc.lecturer_id = $lecturer_id")->fetch_all(MYSQLI_ASSOC);

$units = $conn->query("SELECT u.unit_id, u.unit_name 
                       FROM lecturer_units lu 
                       JOIN units u ON lu.unit_id = u.unit_id 
                       WHERE lu.lecturer_id = $lecturer_id")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Customize Feedback Form</title>
<link rel="stylesheet" href="lec_customize_form.css">
    
</head>
<body>

<h2>Select a Feedback Form to Customize</h2>

<form method="POST" action="submit_custom_form.php">
    <label>Select Form:</label>
    <select name="form_id" required>
        <option value="">-- Choose a form --</option>
        <?php while ($form = $formQuery->fetch_assoc()): ?>
            <option value="<?= $form['form_id'] ?>"><?= htmlspecialchars($form['title']) ?></option>
        <?php endwhile; ?>
    </select>

    <h3>Add Custom Questions</h3>
    <div id="custom-questions"></div>
    <button type="button" onclick="addScale()">+ Scale Question</button>
    <button type="button" onclick="addText()">+ Text Question</button>

    <h3>Assign To:</h3>
    <label>Course</label>
    <select name="assigned_course_id" required>
        <option value="">-- Select Course --</option>
        <?php foreach ($courses as $c): ?>
            <option value="<?= $c['course_id'] ?>"><?= htmlspecialchars($c['course_name']) ?></option>
        <?php endforeach; ?>
    </select>

    <label>Unit</label>
    <select name="assigned_unit_id" required>
        <option value="">-- Select Unit --</option>
        <?php foreach ($units as $u): ?>
            <option value="<?= $u['unit_id'] ?>"><?= htmlspecialchars($u['unit_name']) ?></option>
        <?php endforeach; ?>
    </select>

    <br><br>
    <button type="submit">Submit Customized Form</button>
</form>

<script>
let qIndex = 0;

function addScale() {
    const container = document.getElementById("custom-questions");
    const div = document.createElement("div");
    div.className = "question-block";
    div.innerHTML = `
        <label>Scale Question (1–5):</label>
        <input type="text" name="questions[${qIndex}][text]" required>
        <input type="hidden" name="questions[${qIndex}][type]" value="scale">
    `;
    container.appendChild(div);
    qIndex++;
}

function addText() {
    const container = document.getElementById("custom-questions");
    const div = document.createElement("div");
    div.className = "question-block";
    div.innerHTML = `
        <label>Text Question:</label>
        <input type="text" name="questions[${qIndex}][text]" required>
        <input type="hidden" name="questions[${qIndex}][type]" value="text">
    `;
    container.appendChild(div);
    qIndex++;
}
</script>

</body>
</html>
