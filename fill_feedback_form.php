<?php
session_start();
include("include/dbconnect.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // ADD this function at the top of this file
    function analyze_feedback($message) {
        $url = "http://127.0.0.1:8000/feedback/analyze";

        $data = json_encode(["message" => $message]);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    // PROCESSING LOGIC BEGINS HERE
    $form_id = intval($_POST['form_id']);
    $unit_id = intval($_POST['unit_id']);
    $user_id = $_SESSION['user_id'];
    $answers = $_POST['answers'];

    foreach ($answers as $question_id => $response) {
        if (is_numeric($response)) {
            // Scale-type answer: save directly
            $stmt = $conn->prepare("INSERT INTO feedback_answers (form_id, unit_id, question_id, user_id, answer) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iiiis", $form_id, $unit_id, $question_id, $user_id, $response);
            $stmt->execute();
            continue;
        }

        // Text answer: analyze first
        $analysis = analyze_feedback($response);
        $cleaned = $analysis['cleaned_text'] ?? $response;
        $sentiment = $analysis['sentiment'] ?? 'neutral';
        $confidence = $analysis['confidence_score'] ?? 0.0;
        $has_profanity = $analysis['contains_profanity'] ?? false;

        // Save analyzed feedback
        $stmt = $conn->prepare("INSERT INTO feedback_answers (form_id, unit_id, question_id, user_id, answer, sentiment, confidence_score, contains_profanity) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiissdi", $form_id, $unit_id, $question_id, $user_id, $cleaned, $sentiment, $confidence, $has_profanity);
        $stmt->execute();
    }

    // Redirect or confirm submission
    echo "Feedback submitted successfully.";
}
?>
