<?php
if (isset($_GET["id"])){
    $id= $_GET["id"];
}
// Database connection
$dns = "mysql:host=localhost;dbname:booking";
$username = "root";
$password = "";

// Create connection
$pdo = new PDO($dns, $username, $password);
// Example of DELETE with prepared statement
$sql = "DELETE FROM booking WHERE id = :id";
$stmt = $connection->prepare($sql);
$stmt->bindParam(':id', $booking_id);
$stmt->execute();

?>
