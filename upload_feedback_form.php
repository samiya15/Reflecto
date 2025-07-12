<?php
session_start();
include("include/dbconnect.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 3) { // Assuming 3 is admin
    header("Location: signin.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $uploaded_by = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO feedback_forms (title, description, uploaded_by) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $title, $description, $uploaded_by);
    if ($stmt->execute()) {
        echo "Form uploaded successfully!";
    } else {
        echo "Error uploading form.";
    }
}
?>

<form method="POST">
    <input type="text" name="title" placeholder="Form Title" required>
    <textarea name="description" placeholder="Form description..."></textarea>
    <button type="submit">Upload Form</button>
</form>
