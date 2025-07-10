<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>System Admin Dashboard</title>
  <link rel="stylesheet" href="systemadmin.css" />
</head>
<body>

  <!-- Navigation Bar -->
  <nav class="navbar">
    <div class="nav-left">
      <ul>
        <li><a href="sysadmin.php">Dashboard</a></li>
        <li><a href="manage_users.php">Manage Users</a></li>
      </ul>
    </div>
    <div class="nav-right">
      <a href="signin.php" class="logout-btn">Log Out</a>
    </div>
  </nav>

  <!-- Banner -->
  <div class="banner">
    <h2>Welcome, System Administrator</h2>
  </div>

  <!-- Dashboard Content -->
  <div class="dashboard-content">
    <!-- Card: Approve or Reject New Users -->
    <div class="card">
      <h3>Pending User Approvals</h3>
      <p>Review and approve/reject new users (students, lecturers, course admins).</p>
      <button onclick="location.href='manage_users.php'">Manage User Approvals</button>
    </div>
<div class="card">
  <h3>Manage Faculties</h3>
  <p>Create, edit, and delete faculties.</p>
  <button onclick="location.href='manage_faculties.php'">Manage Faculties</button>
</div>
<div class="card">
  <h3>Manage Courses</h3>
  <p>Create, edit, and delete courses under faculties.</p>
  <button onclick="location.href='manage_courses.php'">Manage Courses</button>
</div>
<div class="card">
  <h3>Manage Units</h3>
  <p>Create, edit, and delete units under courses.</p>
  <button onclick="location.href='manage_units.php'">Manage Units</button>
</div>
    <!-- Card: Approve Student Profile Updates -->
    <div class="card">
      <h3>Pending Student Profile Updates</h3>
      <p>Review and approve changes submitted by students.</p>
      <button onclick="location.href='manage_student_updates.php'">Review Updates</button>
    </div>
    <div class="card">
      <h3>Update Profanity Logs</h3>
      <p>Add profanity to the dictionary </p>
      <button onclick="location.href='http://127.0.0.1:8000/docs'">update Profanity</button>
    </div>


</body>
</html>
