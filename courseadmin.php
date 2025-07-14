<?php
session_start();
include("include/dbconnect.php");

// Ensure only course admins can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 3) {
    header("Location: signin.php");
    exit;
}
$user_id = $_SESSION['user_id'];

// Get user data
$user_stmt = $conn->prepare("SELECT firstName, lastName, email FROM users WHERE user_id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user_data = $user_result->fetch_assoc();

// Get courseadmin data
$admin_stmt = $conn->prepare("SELECT * FROM courseadmin WHERE email = ?");
$admin_stmt->bind_param("s", $user_data['email']);
$admin_stmt->execute();
$admin_result = $admin_stmt->get_result();
$admin_data = $admin_result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Course Admin Dashboard</title>
  <link rel="stylesheet" href="courseadmin.css" />
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

  <!-- Banner -->
  <div class="banner">
    <h2>Welcome, <?= htmlspecialchars($user_data['firstName']) ?> <?= htmlspecialchars($user_data['lastName']) ?></h2>
  </div>

  <!-- Dashboard Content -->
  <div class="dashboard-content">
    <div class="card">
      <h3>Update Profile</h3><br>
      <p>Update your information</p>
      <button id="updateProfileBtn">Update Profile</button>
    </div>
    <div class="card">
      <h3>Pending Lecturer Approvals</h3><br>
      <p>Review and verify lecturer accounts.</p><br>
   <a href="manage_lecturers.php" class="button-link">Approve Lecturers</a>
    </div>
    <div class="card">
      <h3>Create Feedback Forms</h3><br>
      <p>Create and edit feedback forms</p><br>
        <a href="create_feedback_form.php" class="button-link">Create forms</a>
    </div>
   

    <div class="card">
      <h3>Reports</h3><br>
      <p>Generate and download activity reports.</p><br>
       <a href="courseadmin_view_summaries.php" class="button-link">View Reports</a>
    </div>
  </div>

  <!-- Modal Overlay -->
  <div id="profileModal" class="modal">
    <div class="modal-content">
      <h3>Update Your Profile</h3><br>
      <form method="post" action="update_cadmin_profile.php">
        <div class="input-group">
          <label>Name</label>
          <input type="text" name="name" value="<?= htmlspecialchars($admin_data['course_admin_name'] ?: ($user_data['firstName'] . ' ' . $user_data['lastName'])) ?>" required>
        </div>
        <div class="input-group">
          <label>Email</label>
          <input type="email" name="email" value="<?= htmlspecialchars($user_data['email']) ?>" readonly>
        </div>
        <div class="input-group">
          <label>Faculty</label>
          <input type="text" name="faculty_name" value="<?= htmlspecialchars($admin_data['faculty_name'] ?? '') ?>">
        </div><br>
        <button type="submit" class="submit-btn">Save Changes</button>
      </form>
    </div>
  </div>

  <!-- JavaScript for modal -->
  <script>
    const updateProfileBtn = document.getElementById("updateProfileBtn");
    const profileModal = document.getElementById("profileModal");

    updateProfileBtn.addEventListener("click", () => {
      profileModal.style.display = "flex";
    });

    profileModal.addEventListener("click", (e) => {
      if (e.target === profileModal) {
        profileModal.style.display = "none";
      }
    });
  </script>

</body>
</html>
