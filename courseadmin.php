<?php
session_start();
include("include/dbconnect.php");

// Make sure the course admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 3) {
    header("Location: signin.php");
    exit;
}
$user_id = $_SESSION['user_id'];

// Fetch user basic info
$user_stmt = $conn->prepare("SELECT firstName, lastName, email FROM users WHERE user_id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user_data = $user_result->fetch_assoc();

// Fetch course admin info
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
  <style>
    /* You can move this to your CSS file */
    .profile-form-container {
      display: none;
      position: absolute;
      right: 10px;
      top: 60px;
      background: #fff;
      padding: 15px;
      border: 1px solid #ccc;
      box-shadow: 0 0 5px rgba(0,0,0,0.2);
      z-index: 100;
    }
    .profile-form-container form div {
      margin-bottom: 10px;
    }
    .dropdown {
      display: none;
      position: absolute;
      background: #fff;
      border: 1px solid #ccc;
      right: 10px;
      top: 40px;
      box-shadow: 0 0 5px rgba(0,0,0,0.2);
      z-index: 99;
    }
    .dropdown a {
      display: block;
      padding: 8px 12px;
      text-decoration: none;
      color: #333;
    }
    .dropdown a:hover {
      background: #f0f0f0;
    }
  </style>
</head>
<body>

  <!-- Navigation Bar -->
  <nav class="navbar">
    <div class="nav-left">
      <li><a href="courseadmindash.php">Dashboard</a></li>
      <li><a href="manage_lecturers.php">Manage Lecturers</a></li>
    </div>
    <div class="nav-right">
      <div class="profile" id="profileArea">
        <div class="profile-icon" id="profileIcon"></div>
        <div class="dropdown" id="dropdownMenu">
          <a href="#" id="viewProfileLink">View Profile</a>
          <a href="signin.php">Log Out</a>
        </div>
      </div>
    </div>
  </nav>

  <!-- Banner -->
  <div class="banner">
    <h2>Welcome, <?= htmlspecialchars($user_data['firstName']) ?></h2>
  </div>

  <!-- Profile View/Edit Form -->
  <div class="profile-form-container" id="profileFormContainer">
    <form method="post" action="update_courseadmin_profile.php">
      <input type="hidden" name="email" value="<?= htmlspecialchars($user_data['email']) ?>">
      <div>
        <label>Name:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($admin_data['course_admin_name'] ?: ($user_data['firstName'].' '.$user_data['lastName'])) ?>" required>
      </div>
      <div>
        <label>Email:</label>
        <input type="email" value="<?= htmlspecialchars($user_data['email']) ?>" disabled>
      </div>
      <div>
        <label>Faculty:</label>
        <input type="text" name="faculty_name" value="<?= htmlspecialchars($admin_data['faculty_name'] ?? '') ?>">
      </div>
      <button type="submit">Update Profile</button>
    </form>
  </div>

  <!-- Dashboard Content -->
  <div class="dashboard-content">
    <div class="card">
      <h3>Pending Lecturers</h3>
      <p>View and verify new lecturer accounts.</p>
      <button onclick="window.location.href='manage_lecturers.php'">View Lecturers</button>
    </div>
    <div class="card">
      <h3>Manage Courses</h3>
      <p>Add or edit courses and assign lecturers.</p>
      <button>Manage Courses</button>
    </div>
  </div>

  <!-- JavaScript for Dropdown and Profile Panel -->
  <script>
    const profileIcon = document.getElementById("profileIcon");
    const dropdownMenu = document.getElementById("dropdownMenu");
    const profileArea = document.getElementById("profileArea");
    const viewProfileLink = document.getElementById("viewProfileLink");
    const profileFormContainer = document.getElementById("profileFormContainer");

    // Toggle dropdown menu
    profileIcon.addEventListener("click", (e) => {
      e.stopPropagation();
      dropdownMenu.style.display = dropdownMenu.style.display === "block" ? "none" : "block";
      profileFormContainer.style.display = "none";
    });

    // Show profile form
    viewProfileLink.addEventListener("click", (e) => {
      e.preventDefault();
      dropdownMenu.style.display = "none";
      profileFormContainer.style.display = profileFormContainer.style.display === "block" ? "none" : "block";
    });

    // Close both when clicking outside
    document.addEventListener("click", function(event) {
      if (!profileArea.contains(event.target)) {
        dropdownMenu.style.display = "none";
        profileFormContainer.style.display = "none";
      }
    });
  </script>

</body>
</html>
