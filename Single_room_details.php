<?php
try {
    // Database connection
    $db = new PDO("mysql:host=localhost;dbname=my_db", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Room number to fetch the single room details (could be passed via GET)
    $room_number = isset($_GET['room_num']) ? (int)$_GET['room_num'] : null; // Sanitize the input

    // Check if the room number is provided
    if (!$room_number) {
        echo "Room number is missing.";
        exit();
    }

    // Query to fetch room details based on the room number
    $query = "SELECT * FROM rooms WHERE room_num = :room_num"; // Add WHERE clause to filter by room_num
    $stmt = $db->prepare($query);
    $stmt->bindParam(':room_num', $room_number, PDO::PARAM_INT);  // Bind room_number parameter to prevent SQL injection
    $stmt->execute();

    // Fetch the room data
    $room = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$room) {
        echo "Room not found.";
        exit();
    }

    // Split the equipment list into an array by commas
    $equipmentList = explode(",", $room['equipment']); // Convert string to array based on commas
    // Trim any extra spaces around equipment names
    $equipmentList = array_map('trim', $equipmentList);

    // Function to generate equipment descriptions
    function EquipmentDescription($equipmentList) {
        $descriptions = [];

        // Check for specific equipment and display descriptions
        if (in_array("Computers", $equipmentList)) {
            $descriptions[] = "<strong>Computers: </strong> Each seat is equipped with a computer, providing students with easy access to digital resources during class activities. <br>";
        }

        if (in_array("Projector", $equipmentList)) {
            $descriptions[] = "<strong>Projector:</strong> The room is fitted with a high-quality projector, perfect for displaying presentations, videos, or slideshows during lessons. <br>";
        }

        if (in_array("Whiteboard", $equipmentList)) {
            $descriptions[] = "<strong>Whiteboard:</strong> A spacious whiteboard is available for writing or drawing to assist in teaching and discussions. <br>";
        }

        // Return the descriptions as a string
        return implode('<br>', $descriptions);
    }

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css_single_room_details.css">
    <title>Room Details</title>
</head>

<body>
    <header>
        <h1>Room Booking System</h1>
        <nav>
            <a href="building_map.php?id=<?= $_SESSION['user_id'] ?>">IT college Map</a>
            <a href="filter_page.php?id=<?= $_SESSION['user_id'] ?>">Filter</a>
            <a href="#room_details" class="active">Room</a>
            <a href="userprofile2.php">User Profile</a>
            <a href="logout.php">Log out</a>
        </nav>
    </header>

    <main>
        <div class="container">
            <div class="card">
                <!-- Back to Filter Button -->
                <div class="room-banner">
                    <a href="javascript:history.back()">Go Back</a>
                </div>

                <div class="card-content">
                    <!-- Room Image Section (on the right) -->
                    <div class="room-image">
                        <img src="images/try_image.jpg" alt="Room Image">
                    </div>

                    <!-- Room Description Section (on the left) -->
                    <div class="card-body">
                        <div class="desc-container">
                            <h2 class="card-title"><?php echo htmlspecialchars($room['room_num']); ?> - Room Details</h2>
                            <p class="card-text"><strong>Capacity:</strong> <?php echo htmlspecialchars($room['capacity']); ?> persons</p>
                            <p class="card-text"><strong>Department:</strong> <?php echo htmlspecialchars($room['department']); ?></p>
                            <p class="card-text"><strong>Room Type:</strong> <?php echo htmlspecialchars($room['type']); ?></p>
                            <p class="card-text"><strong>Equipments Included:</strong></p>
                            <p><?php echo EquipmentDescription($equipmentList); ?></p>
                        </div>
                        <!-- Book Now Button -->
                        <div class="btn-desc">
                            <?php
                            $_GET["room_num"]=$room['room_num'];
                            ?>
                            <a href="booking.php?room_num=<?php echo $room['room_num']; ?>" class="btn-primary" target="_blank">Book Now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <nav>
            <a href="#privacy">Privacy Policy</a>
            <a href="#terms">Terms of Service</a>
            <a href="#support">Support</a>
        </nav>
        <p>&copy; 2024 Room Booking System. All rights reserved.</p>
    </footer>

</body>
</html>
