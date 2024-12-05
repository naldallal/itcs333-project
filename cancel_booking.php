<?php
if (isset($_GET["booking_id"])){
    $id= $_GET["booking_id"];
}
// Database connection
$dns = "mysql:host=localhost;dbname:my_db";
$username = "root";
$password = "";

// Create connection
$pdo = new PDO($dns, $username, $password);
// Example of DELETE with prepared statement
$sql = "DELETE FROM booking WHERE id = :booking_id";
$stmt = $connection->prepare($sql);
$stmt->bindParam(':booking_id', $booking_id);
$stmt->execute();

?>
