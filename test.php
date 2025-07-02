<?php
session_start();
include("include/dbconnect.php");

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: signin.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Course Admin Dashboard</title>
  <link rel="stylesheet" href="dashboard.css" />
  <style>
    .profile-dropdown {
      display: none;
      position: absolute;
      right: 0;
      top: 40px;
      background: #fff;
      border: 1px solid #ccc;
      padding: 1em;
      width: 300px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      z-index: 10;
    }
    .profile-dropdown a {
      display: block;
      margin: 0.5em 0;
      text-decoration: none;
      color: #333;
      cursor: pointer;
    }
    .profile-form {
      display: none;
    }
    .profile-form form {
      display: flex;
      flex-direction: column;
    }
    .profile-form label {
      margin-top: 0.5em;
      font-weight: bold;
    }
    .profile-form input {
      padding: 0.3em;
      margin-top: 0.2em;
    }
    .profile-form button {
      margin-top: 0.8em;
      padding: 0.5em;
    }
  </style>
</head>
<body>

<!-- NAVIGATION -->
<nav>
  <div class="navigation">
    <div class="nav-left">
      <ul>
        <li><a href="courseadmindash.php">Dashboard</a></li>
        <li><a href="manage_lecturers.php">Manage Lecturers</a></li>
      </ul>
    </div>
    <div class="profile" id="profileArea">
      <div class="profile-icon" id="profileIcon"></div>
      <div class="profile-dropdown" id="dropdownMenu">
        <a id="viewProfileBtn">View Profile</a>
        <a href="logout.php">Log Out</a>
        <div class="profile-form" id="profileFormContainer"></div>
      </div>
    </div>
  </div>
</nav>

<div class="banner">
  <h1>Welcome to the Course Admin Dashboard</h1>
</div>

<script>
// Toggle dropdown
const profileIcon = document.getElementById("profileIcon");
const dropdownMenu = document.getElementById("dropdownMenu");
const profileArea = document.getElementById("profileArea");
const profileFormContainer = document.getElementById("profileFormContainer");

profileIcon.addEventListener("click", () => {
  dropdownMenu.style.display = dropdownMenu.style.display === "block" ? "none" : "block";
  profileFormContainer.style.display = "none"; // hide profile form when reopening dropdown
});

// Close dropdown if clicked outside
document.addEventListener("click", function(event) {
  if (!profileArea.contains(event.target)) {
    dropdownMenu.style.display = "none";
    profileFormContainer.style.display = "none";
  }
});

// Load profile info on click
document.getElementById("viewProfileBtn").addEventListener("click", function(e) {
  e.preventDefault();
  fetch("get_admin_profile.php")
    .then(response => response.json())
    .then(data => {
      profileFormContainer.style.display = "block";
      profileFormContainer.innerHTML = `
        <form id="updateProfileForm">
          <label>First Name</label>
          <input type="text" name="firstName" value="${data.firstName}" required>
          <label>Last Name</label>
          <input type="text" name="lastName" value="${data.lastName}" required>
          <label>Email</label>
          <input type="email" name="email" value="${data.email}" required>
          <label>Faculty</label>
          <input type="text" name="faculty_name" value="${data.faculty_name || ''}">
          <button type="submit">Save Changes</button>
        </form>
      `;
    });
});

// Handle profile update
document.addEventListener("submit", function(e) {
  if (e.target && e.target.id === "updateProfileForm") {
    e.preventDefault();
    const formData = new FormData(e.target);
    fetch("update_admin_profile.php", {
      method: "POST",
      body: formData
    })
    .then(response => response.text())
    .then(msg => {
  alert(msg);
  // Re-fetch updated profile to refresh fields
  return fetch("get_admin_profile.php");
})
.then(response => response.json())
.then(data => {
  profileFormContainer.innerHTML = `
    <form id="updateProfileForm">
      <label>First Name</label>
      <input type="text" name="firstName" value="${data.firstName}" required>
      <label>Last Name</label>
      <input type="text" name="lastName" value="${data.lastName}" required>
      <label>Email</label>
      <input type="email" name="email" value="${data.email}" required>
      <label>Faculty</label>
      <input type="text" name="faculty_name" value="${data.faculty_name || ''}">
      <button type="submit">Save Changes</button>
    </form>
  `;
});

  }
});


</script>
</body>
</html>
