<?php
session_start();
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    echo $userId;
    global $pdo; 
    $pdo = new PDO('mysql:host=localhost;dbname=my_db;charset=utf8mb4', 'root');
    $stmt = $pdo->query("SELECT * FROM user WHERE id = '$userId'");
    $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($stmt->rowCount() == 0) {
        echo "You are not allowed to access this page.";
        exit;
    }

} else {
    echo "You need to log in first to access your profile.";
    exit;
}
// Database connection using PDO
try {
    global $pdo;
    $pdo = new PDO('mysql:host=localhost;dbname=my_db', "root");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

// Check if the 'cancel_book' button is pressed and delete the booking
if (isset($_POST['cancel_book'])) {
    if (isset($_POST['booking_id'])) {
        $booking_id = $_POST['booking_id'];
        $user_id = $_SESSION['user_id'];

        try {
            // Prepare the DELETE SQL statement with user_id check
            $sql = "DELETE FROM bookings WHERE booking_id = :booking_id AND user_id = :user_id";
            $stmt = $pdo->prepare($sql);

            // Bind the parameters
            $stmt->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

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

                // Get the logged-in user's ID
                $user_id = $_SESSION['user_id'];

                // SQL query to fetch data for the logged-in user
                $sql = "SELECT * FROM bookings WHERE user_id = :user_id";
                $stmt = $connection->prepare($sql);
                $stmt->execute(['user_id' => $user_id]);

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