<?php
session_start();
include("include/dbconnect.php");

// Ensure course admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 3) {
    header("Location: signin.php");
    exit();
}

// Get the faculty from session
if (empty($_SESSION['faculty_id'])) {
    echo "Error: Faculty ID not set in session. Please set your faculty before managing lecturers.";
    exit();
}

$faculty_id = $_SESSION['faculty_id'];

// Fetch the faculty name for display
$facultyName = "Unknown";

if ($faculty_id) {
    $facultyQuery = $conn->prepare("SELECT faculty_name FROM faculty WHERE faculty_id = ?");
    $facultyQuery->bind_param("i", $faculty_id);
    $facultyQuery->execute();
    $facultyResult = $facultyQuery->get_result();
    if ($facultyResult->num_rows > 0) {
        $facultyRow = $facultyResult->fetch_assoc();
        $facultyName = $facultyRow['faculty_name'];
    }
}

// Fetch lecturers belonging to this faculty
$stmt = $conn->prepare("
    SELECT l.lecturer_id, l.faculty_name, l.course_taught, l.unit_taught, l.verification_status, 
           u.firstName, u.lastName, u.email 
    FROM lecturers l
    JOIN users u ON l.user_id = u.user_id
    JOIN lecturer_faculties lf ON l.lecturer_id = lf.lecturer_id
    WHERE lf.faculty_id = ?
");
if (!$stmt) {
    echo "Prepare failed: " . $conn->error;
    exit();
}

$stmt->bind_param("i", $faculty_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Lecturers</title>
  <link rel="stylesheet" href="manage_lecturers.css" />
</head>
<body>
<!-- Navigation Bar -->
<nav class="navbar">
  <div class="nav-left">
    <ul>
      <li><a href="courseadmin.php">Dashboard</a></li>
      <li><a href="manage_lecturers.php">Manage Lecturers</a></li>
      <li><a href="manage_courses.php">Manage Courses</a></li>
    </ul>
  </div>
  <div class="nav-right">
    <a href="signin.php" class="logout-btn">Log Out</a>
  </div>
</nav>

<h3>Manage Lecturers (Faculty Name: <?= htmlspecialchars($facultyName) ?>)</h3>

<?php if ($result->num_rows === 0): ?>
  <p>No lecturers found in this faculty.</p>
<?php else: ?>
<table>
  <thead>
    <tr>
      <th>Name</th>
      <th>Email</th>
      <th>Courses</th>
      <th>Units</th>
      <th>Status</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
  <?php while($lec = $result->fetch_assoc()): ?>
    <tr>
      <td><?= htmlspecialchars($lec['firstName'].' '.$lec['lastName']) ?></td>
      <td><?= htmlspecialchars($lec['email']) ?></td>
      <td><?= htmlspecialchars($lec['course_taught']) ?></td>
      <td><?= htmlspecialchars($lec['unit_taught']) ?></td>
      <td><?= htmlspecialchars($lec['verification_status']) ?></td>
      <td>
        <?php if($lec['verification_status'] != 'approved'): ?>
          <form action="approve_lecturer.php" method="post" style="display:inline;">
            <input type="hidden" name="lecturer_id" value="<?= $lec['lecturer_id'] ?>">
            <button type="submit" class="btn approve">Approve</button>
          </form>
          <form action="reject_lecturer.php" method="post" style="display:inline;">
            <input type="hidden" name="lecturer_id" value="<?= $lec['lecturer_id'] ?>">
            <button type="submit" class="btn reject">Reject</button>
          </form>
        <?php else: ?>
          <span class="approved">Approved</span>
        <?php endif; ?>
      </td>
    </tr>
  <?php endwhile; ?>
  </tbody>
</table>
<?php endif; ?>

</body>
</html>
