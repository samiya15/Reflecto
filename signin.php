
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
 <link rel="stylesheet" href="style.css">

</head>
<body>
     <div class="container" id="signin">
        <h1 class="form-title">Sign In</h1>
        <form method="post" action="register.php">
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
            <!--Submit button -->
            <a href="test.php">
            <button type="submit" name="signin">Sign In</button>
        </a>
        </form>
       <div class="links">
        <p>Don't have an account?</p>
        <a href="index.php">
            <button id="signup">Sign Up</button>
        </a>

       </div> 
        </div>
            
</body>
</html>