<?php
if (isset($_GET["booking_id"])) {
    $booking_id = $_GET["booking_id"]; // Store booking_id from URL query
}
else {
    echo "No booking ID provided.";
    exit();
}

// Database connection
$dsn = "mysql:host=localhost;dbname=my_db"; // Corrected the DSN syntax
$username = "root";
$password = "";

try {
    // Create connection
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Set error mode

    // Example of DELETE with prepared statement
    $sql = "DELETE FROM bookings WHERE booking_id = :booking_id"; // Correct table name to bookings
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':booking_id', $booking_id, PDO::PARAM_INT); // Bind the parameter with type
    $stmt->execute();
    echo $booking_id;

    // Optional: Redirect or display a message after deletion
    echo "Booking cancelled successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage(); // Handle connection errors
}
?>