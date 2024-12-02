<?php

//connaction
$servername = 'localhost';
$dbname = 'user_management';
$username = 'root'; // 
$password = '';     

   try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}   catch (PDOException $e) {
       die("Database connection failed: " . $e->getMessage());
}
?>