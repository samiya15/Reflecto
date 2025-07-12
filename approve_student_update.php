<?php
session_start();
include("include/dbconnect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_id'])) {
    $update_id = intval($_POST['update_id']);

    // Get update data
    $stmt = $conn->prepare("SELECT * FROM student_updates WHERE update_id = ?");
    $stmt->bind_param("i", $update_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $update = $result->fetch_assoc();

    if ($update) {
        // ✅ Update the students table with correct columns
        $updateStmt = $conn->prepare("
            UPDATE students
            SET faculty_id = ?, course_id = ?, status = 'approved'
            WHERE user_id = ?
        ");
        $updateStmt->bind_param("iii", $update['faculty_id'], $update['course_id'], $update['user_id']);
        $updateStmt->execute();

        // ✅ Delete the pending update from student_updates
        $deleteStmt = $conn->prepare("DELETE FROM student_updates WHERE update_id = ?");
        $deleteStmt->bind_param("i", $update_id);
        $deleteStmt->execute();
    }

    header("Location: manage_student_updates.php");
    exit();
}
?>
