<?php
session_start();
include("include/dbconnect.php");

// Ensure only course admins can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 3) { // assuming role 4 is course admin
    header("Location: signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get course admin's faculty ID
$stmt = $conn->prepare("SELECT faculty_id FROM courseadmin WHERE email = ?");
$stmt->bind_param("s", $_SESSION['email']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user && isset($user['faculty_id'])) {
    $faculty_id = $user['faculty_id'];
} else {
    echo "Faculty not found. Please ensure your profile is complete.";
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Create Feedback Form</title>
  <link rel="stylesheet" href="form.css" />
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    .form-group { margin-bottom: 15px; }
    .question-block { border: 1px solid #ccc; padding: 10px; margin-top: 10px; }
    button { margin-right: 10px; }
  </style>
</head>
<body>
  <h2>Create Feedback Form</h2>
  <form method="POST" action="process_feedback_form.php">
    <div class="form-group">
      <label for="form_title">Form Title</label>
      <input type="text" name="form_title" id="form_title" required />
    </div>

    <div id="questions-container">
      <!-- Questions will be added here -->
    </div>

    <div class="form-group">
      <button type="button" onclick="addScaleQuestion()">+ Add Scale Question</button>
      <button type="button" onclick="addTextQuestion()">+ Add Text Question</button>
    </div>

    <input type="hidden" name="faculty_id" value="<?= htmlspecialchars($faculty_id) ?>" />
    <button type="submit">Submit Form</button>
  </form>

  <script>
    let questionCount = 0;

    function addScaleQuestion() {
      const container = document.getElementById("questions-container");
      const block = document.createElement("div");
      block.className = "question-block";

      block.innerHTML = `
        <label>Scale Question (1–5):</label>
        <input type="text" name="questions[${questionCount}][text]" required />
        <input type="hidden" name="questions[${questionCount}][type]" value="scale" />
      `;
      container.appendChild(block);
      questionCount++;
    }

    function addTextQuestion() {
      const container = document.getElementById("questions-container");
      const block = document.createElement("div");
      block.className = "question-block";

      block.innerHTML = `
        <label>Text Question:</label>
        <input type="text" name="questions[${questionCount}][text]" required />
        <input type="hidden" name="questions[${questionCount}][type]" value="text" />
      `;
      container.appendChild(block);
      questionCount++;
    }
  </script>
</body>
</html>
