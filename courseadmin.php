<?php
include ("include/dbconnect.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session only if not already started
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>WELCOME course ADMIN</h1>
     <a href="signin.php">
       <button id="logout">Log Out</button>
    </a>
</body>
</html>