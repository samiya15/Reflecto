<?php
session_start();
include("include/dbconnect.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 4) {
    header("Location: signin.php");
    exit();
}

$course = $conn->query("SELECT * FROM course ORDER BY course_name ASC");

$units = $conn->query("SELECT u.*, c.course_name 
                       FROM units u 
                       JOIN course c ON u.course_id = c.course_id 
                       ORDER BY unit_name ASC");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Manage Units</title>
  <link rel="stylesheet" href="manage_faculties.css" />
</head>
<body>
  <h2>Manage Units</h2>

  <table>
    <thead>
      <tr>
        <th>Unit Name</th>
        <th>Course</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $units->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['unit_name']) ?></td>
        <td><?= htmlspecialchars($row['course_name']) ?></td>
        <td>
          <!-- Optional: Edit/Delete -->
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
<h3>Add New Unit</h3>
<form action="add_unit.php" method="post">
  <input type="text" name="unit_name" placeholder="Unit Name" required>

  <select name="course_id" required>
    <option value="">Select Course</option>
    <?php while ($c = $course->fetch_assoc()): ?>
      <option value="<?= $c['course_id'] ?>"><?= htmlspecialchars($c['course_name']) ?></option>
    <?php endwhile; ?>
  </select>

  <select name="year_of_study" required>
    <option value="">Select Year</option>
    <option value="1">Year 1</option>
    <option value="2">Year 2</option>
    <option value="3">Year 3</option>
    <option value="4">Year 4</option>
  </select>

  <button type="submit">Add Unit</button>
</form>

</body>
</html>
