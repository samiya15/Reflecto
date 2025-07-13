<?php
session_start();
include("include/dbconnect.php");

// Make sure student is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch student details, including course ID and course name
$stmt = $conn->prepare("
    SELECT s.faculty_id, s.course_id, s.status, s.year_of_study,
           u.firstName, u.lastName, u.email,
           c.course_name
    FROM students s
    JOIN users u ON s.user_id = u.user_id
    JOIN course c ON s.course_id = c.course_id
    WHERE s.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

// If no faculty yet (first login), force profile completion
if (empty($student['faculty_id'])) {
    header("Location: student_complete_profile.php");
    exit();
}

// Get faculty name
$facultyName = "Unknown";
if (!empty($student['faculty_id'])) {
    $fstmt = $conn->prepare("SELECT faculty_name FROM faculty WHERE faculty_id = ?");
    $fstmt->bind_param("i", $student['faculty_id']);
    $fstmt->execute();
    $fresult = $fstmt->get_result();
    if ($frow = $fresult->fetch_assoc()) {
        $facultyName = $frow['faculty_name'];
    }
}

// Fetch units based on student's course ID and year
$courseId = $student['course_id'];
$year = $student['year_of_study'];

$unitStmt = $conn->prepare("SELECT u.unit_name
    FROM units u
    WHERE u.course_id = ? AND u.year_of_study = ?
");
$unitStmt->bind_param("ii", $courseId, $year);
$unitStmt->execute();
$unitResult = $unitStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Dashboard</title>
  <link rel="stylesheet" href="studentdash.css" />
</head>
<body>
  <nav class="navbar">
    <div class="nav-left">
      <ul>
        <li><a href="studentdash.php">Dashboard</a></li>
      </ul>
    </div>
    <div class="nav-right">
      <a href="signin.php" class="logout-btn">Log Out</a>
    </div>
  </nav>

  <div class="banner">
    <h2>Welcome, <?= htmlspecialchars($student['firstName']) ?>!</h2>
  </div>

  <p>Faculty: <?= htmlspecialchars($facultyName) ?> | Course: <?= htmlspecialchars($student['course_name']) ?> | Year: <?= htmlspecialchars($year) ?></p>
  <p>Status: <?= htmlspecialchars($student['status']) ?></p>

  <div class="cards-container">
    <!-- View Profile Card -->
    <div class="card">
      <h3>View and Update Profile</h3>
      <button id="openProfileBtn">View Profile</button>
    </div>

     <div class="card">
      <h3>Register Units</h3>
      <a href="register_units.php" class="card-btn">Register</a>
    </div>

    <!-- Submit Personalized Feedback -->
    <div class="card">
      <h3>Submit Personalized Feedback</h3>
      <a href="student_feedback.php" class="card-btn">Submit</a>
    </div>

    <!-- Fill Feedback Form -->
    <div class="card">
      <h3>Fill Feedback Form</h3>
      <a href="student_view_forms.php" class="card-btn">Fill Form</a>
    </div>

    <!-- View Lecturer Responses -->
    <div class="card">
      <h3>View Feedback Responses</h3>
      <a href="student_view_responses.php" class="card-btn">Go</a>
    </div>

    <!-- Display My Units -->
    <div class="card full-width">
      <h3>My Units (Year <?= htmlspecialchars($year) ?>)</h3>
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Unit Name</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $count = 1;
          while ($unit = $unitResult->fetch_assoc()): ?>
            <tr>
              <td><?= $count++ ?></td>
              <td><?= htmlspecialchars($unit['unit_name']) ?></td>
            </tr>
          <?php endwhile; ?>
          <?php if ($count === 1): ?>
            <tr><td colspan="2">No units found for your year and course.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal Popup -->
  <div id="profileModal" class="modal">
    <div class="modal-content">
      <span class="closeBtn">&times;</span>
      <h3>Update Profile</h3>
      <form action="update_student_profile.php" method="post">
        <div class="input-group">
          <label>First Name</label>
          <input type="text" name="firstName" value="<?= htmlspecialchars($student['firstName']) ?>" required>
        </div>
        <div class="input-group">
          <label>Last Name</label>
          <input type="text" name="lastName" value="<?= htmlspecialchars($student['lastName']) ?>" required>
        </div>
        <div class="input-group">
          <label>Email</label>
          <input type="email" name="email" value="<?= htmlspecialchars($student['email']) ?>" readonly>
        </div>

        <div class="input-group">
          <label>Faculty</label>
          <select name="faculty_id" required>
            <option value="">Select Faculty</option>
            <?php
            $facQuery = $conn->query("SELECT faculty_id, faculty_name FROM faculty ORDER BY faculty_name");
            while ($fac = $facQuery->fetch_assoc()):
            ?>
              <option value="<?= $fac['faculty_id'] ?>" <?= $student['faculty_id'] == $fac['faculty_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($fac['faculty_name']) ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="input-group">
  <label>Course</label>
  <select name="course_id" required>
    <option value="">Select Course</option>
    <?php
    $coQuery = $conn->query("SELECT course_id, course_name FROM course ORDER BY course_name");
    while ($course = $coQuery->fetch_assoc()):
    ?>
      <option value="<?= $course['course_id'] ?>" <?= $student['course_id'] == $course['course_id'] ? 'selected' : '' ?>>
        <?= htmlspecialchars($course['course_name']) ?>
      </option>
    <?php endwhile; ?>
  </select>
</div>

        <div class="input-group">
          <label>Year of Study</label>
          <select name="year_of_study" required>
            <option value="1" <?= $year == 1 ? 'selected' : '' ?>>Year 1</option>
            <option value="2" <?= $year == 2 ? 'selected' : '' ?>>Year 2</option>
            <option value="3" <?= $year == 3 ? 'selected' : '' ?>>Year 3</option>
            <option value="4" <?= $year == 4 ? 'selected' : '' ?>>Year 4</option>
          </select>
        </div>

        <button type="submit">Update Profile</button>
      </form>
      <p class="note">*Updates will require system admin approval.</p>
    </div>
  </div>

  <script>
    const modal = document.getElementById("profileModal");
    const btn = document.getElementById("openProfileBtn");
    const span = document.getElementsByClassName("closeBtn")[0];
    btn.onclick = () => modal.style.display = "block";
    span.onclick = () => modal.style.display = "none";
    window.onclick = (event) => {
      if (event.target == modal) modal.style.display = "none";
    };
  </script>
</body>
</html>
