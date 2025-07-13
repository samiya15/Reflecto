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

        // Insert user
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

            if ($row['status'] == 'rejected') {
                echo "<script>alert('Your account has been rejected. You cannot log in.');window.location.href='signin.php';</script>";
                exit();
            }

            if ($row['status'] == 'pending') {
                echo "<script>alert('Your account is pending approval. Please wait for admin approval.');window.location.href='signin.php';</script>";
                exit();
            }

            if (password_verify($password, $row['password'])) {
                $_SESSION['email'] = $row['email'];
                $_SESSION['role'] = $row['role'];
                $_SESSION['user_id'] = $row['user_id'];

// If Student
if ($row['role'] == 1) {
    // Check if student record exists
    $checkStudent = $conn->prepare("SELECT student_id FROM students WHERE user_id = ?");
    $checkStudent->bind_param("i", $row['user_id']);
    $checkStudent->execute();
    $studentResult = $checkStudent->get_result();

    if ($studentResult->num_rows === 0) {
        // Insert a record with no faculty and pending status
       $insertStudent = $conn->prepare("INSERT INTO students (user_id, faculty_id, student_course, unit_id, status)
    VALUES (?, NULL, '', NULL, 'pending')
");
         if (!$insertStudent) {
        die("Prepare failed: " . $conn->error);
    }
        $insertStudent->bind_param("i", $row['user_id']);
        $insertStudent->execute();
    }

    // Now fetch the record to see if profile is complete
    $profileCheck = $conn->prepare("SELECT faculty_id
        FROM students
        WHERE user_id = ?
    ");
    $profileCheck->bind_param("i", $row['user_id']);
    $profileCheck->execute();
    $profileResult = $profileCheck->get_result();
    $profileData = $profileResult->fetch_assoc();

    if (empty($profileData['faculty_id'])) {
        header("Location: student_complete_profile.php");
        exit();
    } else {
        header("Location: studentdash.php");
        exit();
    }
}


                // If Lecturer
                if ($row['role'] == 2) {
                    // Check if lecturer record exists
                    $checkLecturer = $conn->prepare("SELECT lecturer_id FROM lecturers WHERE user_id = ?");
                    $checkLecturer->bind_param("i", $row['user_id']);
                    $checkLecturer->execute();
                    $lecturerResult = $checkLecturer->get_result();

                    if ($lecturerResult->num_rows === 0) {
                        // Insert a new record with basic info
                        $insertLecturer = $conn->prepare("INSERT INTO lecturers (user_id, faculty_name, course_taught, unit_taught, profile_completed, verification_status)
                            VALUES (?, '', '', '', 0, 'pending')");
                        $insertLecturer->bind_param("i", $row['user_id']);
                        $insertLecturer->execute();
                    }

                    // Now fetch the record to see if profile is complete
                    $lecturerCheck = $conn->prepare("
                        SELECT profile_completed
                        FROM lecturers
                        WHERE user_id = ?
                    ");
                    $lecturerCheck->bind_param("i", $row['user_id']);
                    $lecturerCheck->execute();
                    $lecturerResult = $lecturerCheck->get_result();
                    $lecturerData = $lecturerResult->fetch_assoc();

                    if ($lecturerData['profile_completed'] == 0) {
                        header("Location: lec_complete_profile.php");
                        exit();
                    } else {
                        header("Location: lecdash.php");
                        exit();
                    }
                }

                // If Course Admin
               if ($row['role'] == 3) {
    // Check if courseadmin record exists
    $checkAdmin = $conn->prepare("SELECT course_admin_id, faculty_id FROM courseadmin WHERE email = ?");
    $checkAdmin->bind_param("s", $row['email']);
    $checkAdmin->execute();
    $adminResult = $checkAdmin->get_result();

    if ($adminResult->num_rows === 0) {
        // Insert a record with no faculty yet
        $insertAdmin = $conn->prepare("INSERT INTO courseadmin (course_admin_name, email) VALUES (?, ?)");
        $fullName = $row['firstName'] . ' ' . $row['lastName'];
        $insertAdmin->bind_param("ss", $fullName, $row['email']);
        $insertAdmin->execute();
         // Since no faculty, force to complete profile
    header("Location: courseadmin_complete_profile.php");
    exit();
} else {
    $adminData = $adminResult->fetch_assoc();
    
    if (empty($adminData['faculty_id'])) {
        // No faculty yet, force to complete profile
        header("Location: courseadmin_complete_profile.php");
        exit();
    }
    
    $_SESSION['faculty_id'] = $adminData['faculty_id'];
}
}

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
