<?php
session_start();
include("include/dbconnect.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 4) {
    header("Location: signin.php");
    exit();
}

// Fetch faculties for dropdown
$faculties = $conn->query("SELECT * FROM faculty ORDER BY faculty_name ASC");

// Fetch courses with faculty name
$courses = $conn->query("SELECT c.*, f.faculty_name 
                         FROM course c 
                         JOIN faculty f ON c.faculty_id = f.faculty_id 
                         ORDER BY course_name ASC");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Manage Courses</title>
  <link rel="stylesheet" href="manage_faculties.css" />
</head>
<body>
    
<nav class="navbar">
  <div class="nav-left">
    <ul>
      <li><a href="sysadmin.php">Dashboard</a></li>
      <li><a href="manage_faculties.php">Manage Faculties</a></li>
    </ul>
  </div>
  <div class="nav-right">
    <a href="signin.php" class="logout-btn">Log Out</a>
  </div>
</nav>
  <h2>Manage Courses</h2>

  <table>
    <thead>
      <tr>
        <th>Course Name</th>
        <th>Faculty</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $courses->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['course_name']) ?></td>
        <td><?= htmlspecialchars($row['faculty_name']) ?></td>
        <td>
        <a href="edit_course.php?id=<?= $row['course_id'] ?>" class="btn edit">Edit</a>
          <a href="delete_course.php?id=<?= $row['course_id'] ?>" class="btn delete" onclick="return confirm('Are you sure you want to delete this faculty?');">Delete</a>
        </td>
       
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <h3>Add New Course</h3>
  <form action="add_course.php" method="post">
    <input type="text" name="course_name" placeholder="Course Name" required>
    <select name="faculty_id" required>
      <option value="">Select Faculty</option>
      <?php while ($f = $faculties->fetch_assoc()): ?>
        <option value="<?= $f['faculty_id'] ?>"><?= htmlspecialchars($f['faculty_name']) ?></option>
      <?php endwhile; ?>
    </select>
    <button type="submit">Add Course</button>
  </form>
</body>
</html>
