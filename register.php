<?php
include("include/dbconnect.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session only if not already started
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // SIGNUP BLOCK
    if (isset($_POST['signup'])) {
        // Validate input
        if (
            empty($_POST['fname']) || empty($_POST['lname']) || empty($_POST['email']) ||
            empty($_POST['password']) || empty($_POST['confirm_password']) || empty($_POST['role'])
        ) {
            echo "All fields are required.";
            exit();
        }

        $firstName = $_POST['fname'];
        $lastName = $_POST['lname'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];
        $role = $_POST['role'];

        if ($password !== $confirmPassword) {
            echo "Passwords do not match.";
            exit();
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Check if email exists
        $check = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            echo "Email already exists.";
            exit();
        }

        // Insert user (use correct column names: firstName and lastName)
        $stmt = $conn->prepare("INSERT INTO users (firstName, lastName, email, password, role) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt) {
            echo "Insert prepare failed: " . $conn->error;
            exit();
        }

        $stmt->bind_param("ssssi", $firstName, $lastName, $email, $hashedPassword, $role);

        if ($stmt->execute()) {
            echo "Account created successfully.";
            header("Location: signin.php");
            exit();
        } else {
            echo "Insert failed: " . $stmt->error;
            exit();
        }
    }

    // SIGNIN BLOCK
    if (isset($_POST['signin'])) {
        if (empty($_POST['email']) || empty($_POST['password'])) {
            echo "Email and password are required.";
            exit();
        }

        $email = $_POST['email'];
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();

            if (password_verify($password, $row['password'])) {
                $_SESSION['email'] = $row['email'];
                $_SESSION['role'] = $row['role'];
                $_SESSION['user_id'] = $row['user_id'];


                // Redirect based on role
                switch ($row['role']) {
                    case 1:
                        header("Location: studentdash.php");
                        break;
                    case 2:
                        header("Location: lecdash.php");
                        break;
                    case 3:
                        header("Location: courseadmin.php");
                        break;
                    case 4:
                        header("Location: sysadmin.php");
                        break;
                    default:
                        echo "Unknown role.";
                        break;
                }
                exit();
            } else {
                echo "Incorrect password.";
            }
        } else {
            echo "No account found with that email.";
        }
    }
}
?>
