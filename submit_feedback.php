<?php
session_start();
include("include/dbconnect.php");

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["error" => "Invalid request"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["lecturer_id"], $data["original_text"], $data["cleaned_text"],
          $data["sentiment"], $data["confidence_score"],
          $data["contains_profanity"], $data["is_anonymous"])) {
    echo json_encode(["error" => "Missing fields"]);
    exit();
}

$user_id = null;
if (!$data["is_anonymous"]) {
    if (!isset($_SESSION["user_id"], $_SESSION["role"]) || $_SESSION["role"] != 1) {
        echo json_encode(["error" => "Only students can submit identified feedback"]);
        exit();
    }
    $user_id = $_SESSION["user_id"];
}

$stmt = $conn->prepare("
    INSERT INTO feedback (
        user_id, lecturer_id, original_text, cleaned_text, sentiment,
        confidence_score, contains_profanity, is_anonymous, created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
");

$stmt->bind_param(
    "iisssdis",
    $user_id,
    $data["lecturer_id"],
    $data["original_text"],
    $data["cleaned_text"],
    $data["sentiment"],
    $data["confidence_score"],
    $data["contains_profanity"],
    $data["is_anonymous"]
);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["error" => $stmt->error]);
}
?>
