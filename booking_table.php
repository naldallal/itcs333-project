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
    <table class="table table-hover table-border table-striped">
        <thead>
            <tr id="head_titles">
                <th>BOOK ID</th>
                <th>USER ID</th>
                <th>ROOM NUM</th>
                <th>SLOT TIME</th>
                <th>ACTION</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Database connection
            $servername = "localhost";
            $username = "root";
            $password = "";
            $db = "booking";

            // Create connection
            $connection = new mysqli($servername, $username, $password, $db);

            // Error check
            if ($connection->connect_error) {
                die("Connection failed: " . $connection->connect_error);
            }

            // SQL query to fetch data
            $sql = "SELECT * FROM my_booking"; // assuming 'booking' is the correct table name
            $result = $connection->query($sql);

            // Error handling for query
            if (!$result) {
                die("Invalid query: " . $connection->error);
            }

            // Loop through each row and display the data
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . $row['id'] . "</td>
                        <td>" . $row['user_id'] . "</td>
                        <td>" . $row['room_id'] . "</td>
                        <td>" . $row['timeslot'] . "</td>
                        <td>
        
                         <a class='btn btn-primary btn-sm' href='cancel_booking.php?id=" . $row['id'] . "'>Cancel</a>
                        </td>
                      </tr>";
            }

            // Close the database connection
            $connection->close();
            ?>

        
        </tbody>
    </table>
</body>
</html>
