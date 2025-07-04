<?php
session_start();
include("include/dbconnect.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) {
    header("Location: signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user basic info
$getUser = $conn->prepare("SELECT firstName, lastName, email FROM users WHERE user_id = ?");
$getUser->bind_param("i", $user_id);
$getUser->execute();
$user_data = $getUser->get_result()->fetch_assoc();

$getLecturer = $conn->prepare("SELECT course_taught, unit_taught FROM lecturers WHERE user_id = ?");
if (!$getLecturer) {
    die("Prepare failed: " . $conn->error);
}
$getLecturer->bind_param("i", $user_id);
$getLecturer->execute();
$lecturer_data = $getLecturer->get_result()->fetch_assoc();

// Fetch faculties (optional)
$getFaculties = $conn->prepare("
    SELECT f.faculty_name
    FROM lecturer_faculties lf
    JOIN faculty f ON lf.faculty_id = f.faculty_id
    WHERE lf.lecturer_id = (SELECT lecturer_id FROM lecturers WHERE user_id = ?)
");
$getFaculties->bind_param("i", $user_id);
$getFaculties->execute();
$faculties = $getFaculties->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lecturer Dashboard</title>
  <link rel="stylesheet" href="lecdash.css" />

</head>
<body>

  <!-- Navigation Bar -->
  <nav class="navbar">
    <div class="nav-left">
      <ul>
        <li><a href="lecdash.php">Dashboard</a></li>
        <li><a href="lec_profile.php">My Profile</a></li>
        <li><a href="signin.php">Log Out</a></li>
      </ul>
    </div>
  </nav>

  <!-- Banner -->
  <div class="banner">
    <h2>Welcome, Lecturer</h2>
  </div>

  <!-- Dashboard Content -->
  <div class="dashboard-content">
    <div class="card">
      <h3>My Profile</h3>
      <p>View and update your profile information.</p>
<button id="updateProfileBtn">View Profile</button>
    </div>

    <div class="card">
      <h3>Course and Unit Details</h3>
      <p>View the courses and units you are assigned to.</p>
      <button>View Assignments</button>
    </div>

    <div class="card">
      <h3>Status</h3>
      <p>Your account status and approvals.</p>
      <button>View Status</button>
    </div>
  </div>
  <!-- Modal Overlay -->
<div id="profileModal" class="modal">
  <div class="modal-content">
    <h3>Update Your Profile</h3><br>
    <form method="post" action="update_lec_profile.php">
      <div class="input-group">
        <label>Name</label>
        <input type="text" name="name" 
          value="<?= htmlspecialchars($user_data['firstName'] . ' ' . $user_data['lastName']) ?>" 
          required>
      </div>

      <div class="input-group">
        <label>Email</label>
        <input type="email" name="email" 
          value="<?= htmlspecialchars($user_data['email']) ?>" 
          readonly>
      </div>

      <div class="input-group">
        <label>Course Taught</label>
        <input type="text" name="course_taught" 
          value="<?= htmlspecialchars($lecturer_data['course_taught'] ?? '') ?>" 
          required>
      </div>

      <div class="input-group">
        <label>Unit Taught</label>
        <input type="text" name="unit_taught" 
          value="<?= htmlspecialchars($lecturer_data['unit_taught'] ?? '') ?>" 
          required>
      </div>

      <!-- Optional: Faculties could be shown as a readonly field or checkboxes -->
      <div class="input-group">
        <label>Faculty</label>
        <input type="text" name="faculty_names"
          value="<?php
            if (!empty($faculties)) {
              $names = array_column($faculties, 'faculty_name');
              echo htmlspecialchars(implode(', ', $names));
            }
          ?>"
          readonly>
      </div><br>

      <button type="submit" class="submit-btn">Save Changes</button>
    </form>
  </div>
</div>

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