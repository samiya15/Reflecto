<?php
session_start();
include("include/dbconnect.php");

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(["error" => "Not logged in"]);
    exit;
}

// Fetch from users
$stmt = $conn->prepare("SELECT firstName, lastName, email FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$userResult = $stmt->get_result();
$user = $userResult->fetch_assoc();
$stmt->close();

// Fetch from courseadmin
$stmt = $conn->prepare("SELECT faculty_name FROM courseadmin WHERE course_admin_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$adminResult = $stmt->get_result();
$admin = $adminResult->fetch_assoc();
$stmt->close();

// Combine
echo json_encode([
    "firstName" => $user["firstName"],
    "lastName" => $user["lastName"],
    "email" => $user["email"],
    "faculty_name" => $admin["faculty_name"] ?? ""
]);
