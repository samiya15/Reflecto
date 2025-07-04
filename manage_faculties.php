<?php
session_start();
include("include/dbconnect.php");

// Make sure a system admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 4) {
    header("Location: signin.php");
    exit();
}

// Fetch all faculties
$stmt = $conn->prepare("SELECT * FROM faculty ORDER BY faculty_name ASC");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Faculties</title>
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

<div class="container">
  <h2>Manage Faculties</h2>
  <table>
    <thead>
      <tr>
        <th>Faculty Name</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['faculty_name']) ?></td>
        <td>
          <a href="edit_faculty.php?id=<?= $row['faculty_id'] ?>" class="btn edit">Edit</a>
          <a href="delete_faculty.php?id=<?= $row['faculty_id'] ?>" class="btn delete" onclick="return confirm('Are you sure you want to delete this faculty?');">Delete</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <h3>Add New Faculty</h3>
  <form action="add_faculty.php" method="post">
    <input type="text" name="faculty_name" placeholder="Faculty Name" required>
    <button type="submit">Add Faculty</button>
  </form>
</div>
</body>
</html>
