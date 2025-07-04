<?php
session_start();
include("include/dbconnect.php");

// Make sure only course admins can approve
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 3) {
    header("Location: signin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lecturer_id'])) {
    $lecturer_id = intval($_POST['lecturer_id']);

    // Update verification status
    $stmt = $conn->prepare("UPDATE lecturers SET verification_status = 'approved' WHERE lecturer_id = ?");
    $stmt->bind_param("i", $lecturer_id);
    if ($stmt->execute()) {
        header("Location: manage_lecturers.php");
        exit();
    } else {
        echo "Error approving lecturer.";
    }
} else {
    echo "Invalid request.";
}
?>
