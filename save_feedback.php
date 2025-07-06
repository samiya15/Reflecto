<?php
session_start();
include("include/dbconnect.php");

header("Content-Type: application/json");

// Check POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["error" => "Invalid request"]);
    exit();
}

// Read JSON body
$data = json_decode(file_get_contents("php://input"), true);

// Validate fields
if (!isset($data["lecturer_id"], $data["original_text"], $data["cleaned_text"], $data["sentiment"], $data["confidence_score"], $data["contains_profanity"], $data["is_anonymous"])) {
    echo json_encode(["error" => "Missing fields"]);
    exit();
}

$user_id = null;
if (!$data["is_anonymous"]) {
    if (!isset($_SESSION["user_id"])) {
        echo json_encode(["error" => "User not logged in"]);
        exit();
    }
    $user_id = $_SESSION["user_id"];
}

// Prepare insert
$stmt = $conn->prepare("
    INSERT INTO feedback (
        user_id,
        lecturer_id,
        original_text,
        cleaned_text,
        sentiment,
        confidence_score,
        contains_profanity
    ) VALUES (?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "isssddi",
    $user_id,
    $data["lecturer_id"],
    $data["original_text"],
    $data["cleaned_text"],
    $data["sentiment"],
    $data["confidence_score"],
    $data["contains_profanity"]
);

$stmt->execute();

echo json_encode(["success" => true]);
?>
