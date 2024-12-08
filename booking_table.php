<?php
// Database connection
$dsn = "mysql:host=localhost;dbname=my_db"; // Corrected the DSN syntax
$username = "root";
$password = "";
$pdo = new PDO($dsn, $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Set error mode

// Example of DELETE with prepared statement
$sql = "DELETE FROM bookings WHERE booking_id = :booking_id"; // Correct table name to bookings
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':booking_id', $booking_id); // Bind the parameter with type
$stmt->execute();
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
                            <td>" . htmlspecialchars($row['room_id']) . "</td>
                            <td>" . htmlspecialchars($row['date']) . "</td>
                            <td>" . htmlspecialchars($row['timeslots']) . "</td>
                            <td>
                            <form method='post'>
                            <input type='hidden' name='booking_id' value='" . htmlspecialchars($row['booking_id']) . "'>
                            <button type='submit' class='btn btn-danger btn-sm' name='cancel_book'>Delete</button>
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