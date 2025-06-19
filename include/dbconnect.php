<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session only if not already started
}

$servername= "localhost";
$username ="root";
$password = "";
$dbname="reflecto";

//create connection
 $conn =new mysqli($servername, $username, $password, $dbname);

 //check connection
 if ($conn->connect_error){
    die("Connection failed: ".$conn->connect_error);

 }
 //echo "Connected successfully";
?>