<?php
session_start();
include("include/dbconnect.php");

// Make sure only system admins can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 4) {
    header("Location: signin.php");
    exit;
}

// Fetch all pending users
$stmt = $conn->prepare("SELECT user_id, firstName, lastName, email, role FROM users WHERE status = 'pending'");
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Users</title>
  <link rel="stylesheet" href="manage_users.css">
</head>
<body>

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

  <div class="container">
    <h2>Pending User Approvals</h2>
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Role</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <!-- PHP will loop through users and put rows here -->
          <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
         <td><?= htmlspecialchars($row['firstName'] . " " . $row['lastName']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
          <td>
            <?php
                switch($row['role']) {
                  case 1: echo "Student"; break;
                  case 2: echo "Lecturer"; break;
                  case 3: echo "Course Admin"; break;
                  case 4: echo "System Admin"; break;
                  default: echo "Unknown";
                }
              ?>
          </td>
          <td>
                <form action="approve_user.php" method="post" style="display:inline;">
                <input type="hidden" name="user_id" value="<?= $row['user_id'] ?>">
                <button type="submit" class="approve-btn">Approve</button>
              </form>
              <form action="reject_user.php" method="post" style="display:inline;">
                <input type="hidden" name="user_id" value="<?= $row['user_id'] ?>">
                <button type="submit" class="reject-btn">Reject</button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr>
          <td colspan="4">No pending users.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

</body>
</html>