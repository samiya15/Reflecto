<?php
session_start();
include("include/dbconnect.php");

// Ensure only system admins can perform this
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 4) {
    header("Location: signin.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);

    $stmt = $conn->prepare("UPDATE users SET status = 'rejected' WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        header("Location: manage_users.php?message=rejected");
        exit;
    } else {
        echo "Error rejecting user: " . $stmt->error;
    }
} else {
    header("Location: manage_users.php");
    exit;
}
?>
