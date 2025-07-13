<?php
session_start();
include("include/dbconnect.php");

// Ensure system admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 4) {
    header("Location: signin.php");
    exit();
}

// Fetch pending updates
$stmt = $conn->prepare(" SELECT su.update_id, su.user_id, su.faculty_id, su.student_course, su.year_of_study,
           u.firstName, u.lastName, u.email,
           f.faculty_name
    FROM student_updates su
    JOIN users u ON su.user_id = u.user_id
    LEFT JOIN faculty f ON su.faculty_id = f.faculty_id
    ORDER BY su.created_at ASC
");
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Student Updates</title>
    <link rel="stylesheet" href="manage_student_updates.css">
</head>
<body>
<h2>Pending Student Profile Updates</h2>

<?php if ($result->num_rows > 0): ?>
<table border="1" cellpadding="6" cellspacing="0">
    <tr>
        <th>Student Name</th>
        <th>Email</th>
        <th>New Faculty</th>
        <th>New Course</th>
        <th>New Year</th>
        <th>Action</th>
    </tr>
    <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['firstName'] . ' ' . $row['lastName']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['faculty_name']) ?></td>
            <td><?= htmlspecialchars($row['student_course']) ?></td>
            <td><?= htmlspecialchars("Year " . $row['year_of_study']) ?></td>
            <td>
                <form action="approve_student_update.php" method="post" style="display:inline;">
                    <input type="hidden" name="update_id" value="<?= $row['update_id'] ?>">
                    <button type="submit">Approve</button>
                </form>
                <form action="reject_student_update.php" method="post" style="display:inline;">
                    <input type="hidden" name="update_id" value="<?= $row['update_id'] ?>">
                    <button type="submit">Reject</button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
</table>
<?php else: ?>
    <p>No student update requests at the moment.</p>
<?php endif; ?>

</body>
</html>
