<?php
session_start();
include("include/dbconnect.php");

// Make sure logged in as Course Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 3) {
    header("Location: signin.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['faculty_id'])) {
        $faculty_id = intval($_POST['faculty_id']);
        
        // Update courseadmin table
        $update = $conn->prepare("UPDATE courseadmin SET faculty_id = ? WHERE email = ?");
        $update->bind_param("is", $faculty_id, $_SESSION['email']);
        $update->execute();
        
        // Store in session
        $_SESSION['faculty_id'] = $faculty_id;
        
        // Redirect to dashboard
        header("Location: courseadmin.php");
        exit();
    } else {
        $error = "Please select a faculty.";
    }
}

// Fetch faculties for dropdown
$faculties = $conn->query("SELECT faculty_id, faculty_name FROM faculty");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Complete Your Profile</title>
    <link rel="stylesheet" href="complete_profile.css" />
</head>
<body>
<div class="form-box">
    <h2>Select Your Faculty</h2>
    <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>
    <form method="post">
        <label for="faculty_id">Faculty:</label>
        <select name="faculty_id" required>
            <option value="">-- Select Faculty --</option>
            <?php while($f = $faculties->fetch_assoc()): ?>
                <option value="<?= $f['faculty_id'] ?>"><?= htmlspecialchars($f['faculty_name']) ?></option>
            <?php endwhile; ?>
        </select>
        <button type="submit">Save Faculty</button>
    </form>
</div>
</body>
</html>
