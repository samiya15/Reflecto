<?php
include("include/dbconnect.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session only if not already started
}
$signupError = ""; // to store and display error messages

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup'])) {
    // Check for empty fields
    if (
        empty($_POST['fname']) || empty($_POST['lname']) || empty($_POST['email']) ||
        empty($_POST['password']) || empty($_POST['confirm_password']) || empty($_POST['role'])
    ) {
        $signupError = "All fields are required.";
    } else {
        $firstName = $_POST['fname'];
        $lastName = $_POST['lname'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];
        $role = $_POST['role'];

        if ($password !== $confirmPassword) {
            $signupError = "Passwords do not match.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Check if email exists
            $check = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $check->bind_param("s", $email);
            $check->execute();
            $result = $check->get_result();

            if ($result->num_rows > 0) {
                $signupError = "Email already exists.";
            } else {
                // Insert into database
                $stmt = $conn->prepare("INSERT INTO users (firstName, lastName, email, password, role) VALUES (?, ?, ?, ?, ?)");
                if ($stmt) {
                    $stmt->bind_param("ssssi", $firstName, $lastName, $email, $hashedPassword, $role);
                    if ($stmt->execute()) {
                        header("Location: signin.php");
                        exit();
                    } else {
                        $signupError = "Insert failed: " . $stmt->error;
                    }
                } else {
                    $signupError = "Insert prepare failed: " . $conn->error;
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">

</head>
<body>
    <div class="container" id="signup">
        <h1 class="form-title">Register</h1><br>
       <?php if (!empty($signupError)) : ?>
    <div style="color: red; margin-bottom: 15px;">
        <?= htmlspecialchars($signupError) ?>
    </div>
<?php endif; ?>
        <form method="post" action="">
        <!-- First name -->
            <div class="input-group">
                
                                <i class="fas fa-user"></i>
                <input type="text" name="fname" id="fname" placeholder="First Name" required>
                <label for="fname">First Name</label>

                 
            </div>
             <!-- Last name -->
            <div class="input-group">
                 
                 <i class="fas fa-user"></i>
                <input type="text" name="lname" id="lname" placeholder="Last Name" required>
               <label for="lname">Last Name</label>
            </div>
             
               <!-- Select Role -->
                  <div class="input-group">
                <select id="role" name="role" required>
                    <option value="">Select Role</option>
                    <option value="1">Student</option>
                    <option value="2">Lecturer</option>
                    <option value="3">Course Administrator</option>
                    <option value="4">System Administrator</option>
                </select>
               <label for="role">Select Role</label>
            </div><br>
             <!-- Email -->
                 <div class="input-group">
                   
                <i class="fas fa-envelope"></i>
                <input type="email" id="email" name="email" placeholder="E-mail" required>
                  <label for="email">E-mail Address</label>
            </div>
             <!-- Password -->
                 <div class="input-group">
                       
                <i class="fas fa-lock"></i>
                <input type="password" id="password" name="password" placeholder="Password" required>
              <label for="password">Enter Password</label>
            </div>
             
             <!--Confirm Password -->
                 <div class="input-group">
                <i class="fas fa-eye"></i>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
                <label for="confirm_password">Confirm Password</label>
            </div>

            <!--Submit button -->
          <button type="submit" name="signup">Sign Up</button>
        
        </form>
        <div class="links">
            
             <p>Already have an account? </p>
             <a href="signin.php">
             <button id="signin">  Sign in </button>
        </a>
        </div>
    </div>

</body>
</html>