<?php
// Database connection
$dsn = "mysql:host=localhost;dbname=my_db";
$username = "root";
$password = "";
$pdo = new PDO($dsn, $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Set error mode

// Check if the 'cancel_book' button is pressed and delete the booking
if (isset($_POST['cancel_book'])) {
    if (isset($_POST['booking_id'])) {
        $booking_id = $_POST['booking_id'];

        // Debugging: Check if booking_id is set
        echo "Booking ID: " . $booking_id . "<br>";

        try {
            // Prepare the DELETE SQL statement
            $sql = "DELETE FROM bookings WHERE booking_id = :booking_id";
            $stmt = $pdo->prepare($sql);

            // Bind the booking_id parameter
            $stmt->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);

            // Execute the query
            $stmt->execute();

            // Optional: Debugging to ensure it is executing
            echo "Booking with ID " . $booking_id . " deleted successfully.<br>";
        } catch (PDOException $e) {
            echo "Error executing DELETE query: " . $e->getMessage(); // Catch errors
        }
    } else {
        echo "Booking ID not set!<br>"; // Debugging if booking_id is not in POST data
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <title>Document</title>
</head>
<body>
    <h1 id="main_title">BOOKING LIST</h1>
    <br>
    <table class="table table-hover table-bordered table-striped">
        <thead>
            <tr id="head_titles">
                <th>BOOK ID</th>
                <th>USER ID</th>
                <th>ROOM NUM</th>
                <th>DATE</th>
                <th>TIME SLOTS</th>
                <th>ACTION</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Database connection using PDO
            $servername = "localhost";
            $username = "root";
            $password = "";
            $db = "my_db";

            try {
                $connection = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
                $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // SQL query to fetch data
                $sql = "SELECT * FROM bookings"; 
                $stmt = $connection->prepare($sql);
                $stmt->execute();

                // Loop through each row and display the data
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row['booking_id']) . "</td>
                            <td>" . htmlspecialchars($row['user_id']) . "</td>
                            <td>" . htmlspecialchars($row['room_num']) . "</td>
                            <td>" . htmlspecialchars($row['date']) . "</td>
                            <td>" . htmlspecialchars($row['timeslots']) . "</td>
                            <td>
                            <form method='post'>
                            <input type='hidden' name='booking_id' value='" . htmlspecialchars($row['booking_id']) . "'>
                            <button type='submit' class='btn btn-danger btn-sm' name='cancel_book'>Delete</button>
                            </form>
                            </td>
                          </tr>";
                }

            } catch (PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }

            $connection = null;
            ?>
        </tbody>
    </table>
</body>
</html>
