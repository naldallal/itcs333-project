<?php 
session_start();
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    
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
try {
    // Database connection
    $db = new PDO("mysql:host=localhost;dbname=my_db", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Room details and section names
    $query = "SELECT * FROM rooms";
    $stmt = $db->prepare($query);
    $stmt->execute(); 

    // Initialize the array to group rooms by department and level
    $rooms_by_section = [
        'IS' => ['upper' => [], 'level2' => [], 'ground' => []],
        'CS' => ['upper' => [], 'level2' => [], 'ground' => []],
        'CE' => ['upper' => [], 'level2' => [], 'ground' => []],
    ];

    // Fetch rooms and organize by department and level
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $room_department = $row['department'];
        $room_number = $row['room_num'];
    
        // Check if the room number has less than 4 digits
        if (strlen($room_number) < 4) {
            // Pad the room number with leading zeros to make it 4 digits
            $room_number = str_pad($room_number, 4, "0", STR_PAD_LEFT);
        }
    
        // Determine the level based on the room number
        if (substr($room_number, 0, 1) == '2') {
            $level = 'upper';
        } elseif (substr($room_number, 0, 1) == '1') {
            $level = 'level2';
        } else {
            $level = 'ground';
        }
    
        // Organize rooms by department and level
        $rooms_by_section[$room_department][$level][] = ['room_num' => $room_number];
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
    <link rel="stylesheet" href="css_building_map.css">
    <title>IT College Map</title>
</head>
<body>
    <header>
        <h1>Room Booking System</h1>
        <nav>
            <a href="building_map.php?id=<?= $_SESSION['user_id'] ?>" class="active">IT College Map</a>
            <a href="filter_page.php?id=<?= $_SESSION['user_id'] ?>">Filter</a>
            <a href="userprofile2.php?id=<?= $_SESSION['user_id'] ?>">User Profile</a>
            <a href="logout.php">Log out</a>
        </nav>
    </header>

    <hr>
    <div class="change-view-container">
    <a href="filter_page.php?id=<?= $_SESSION['user_id'] ?>">Change View</a>
    </div>
    <hr>

    <div class="building">
        <?php
        // Iterate over each section
        foreach ($rooms_by_section as $section_id => $section_data) {
            $section_name = strtoupper($section_id); // Get the section name (IS, CS, CE)
            ?>
            <div class="section section-<?= strtolower($section_id) ?>">
                <div class="section-title">
                    <p><?= $section_name ?></p> <!-- Display section name -->
                </div>
    
                <div class="columns">
                    <!-- Column 1: Upper, Level 2, Ground (First Half of Rooms) -->
                    <div class="column">
                        <!-- Upper level (first half) -->
                        <?php if (isset($section_data['upper']) && count($section_data['upper']) > 0) {
                            $upperRooms = $section_data['upper'];
                            $half = ceil(count($upperRooms) / 2); // Round number up to the nearest integer
                            $upperFirstHalf = array_slice($upperRooms, 0, $half);
                            foreach ($upperFirstHalf as $room) { ?>
                                <div class="room">
                                    <a href="single_room_details.php?room_num=<?= $room['room_num'] ?>" class="room-link">Room <?= $room['room_num'] ?></a>
                                </div>
                            <?php }
                        } else { ?>
                            <p>No rooms available in Upper level</p>
                        <?php } ?>
                        <hr> 
                        
                        <!-- Level 2 (first half) -->
                        <?php if (isset($section_data['level2']) && count($section_data['level2']) > 0) {
                            $level2Rooms = $section_data['level2'];
                            $half = ceil(count($level2Rooms) / 2);
                            $level2FirstHalf = array_slice($level2Rooms, 0, $half);
                            foreach ($level2FirstHalf as $room) { ?>
                                <div class="room">
                                    <a href="single_room_details.php?room_num=<?= $room['room_num'] ?>" class="room-link">Room <?= $room['room_num'] ?></a>
                                </div>
                            <?php }
                        } else { ?>
                            <p>No rooms available in Level 2</p>
                        <?php } ?>
                        <hr> 
                        
                        <!-- Ground level (first half) -->
                        <?php if (isset($section_data['ground']) && count($section_data['ground']) > 0) {
                            $groundRooms = $section_data['ground'];
                            $half = ceil(count($groundRooms) / 2);
                            $groundFirstHalf = array_slice($groundRooms, 0, $half);
                            foreach ($groundFirstHalf as $room) { ?>
                                <div class="room">
                                    <a href="single_room_details.php?room_num=<?= $room['room_num'] ?>" class="room-link">Room <?= $room['room_num'] ?></a>
                                </div>
                            <?php }
                        } else { ?>
                            <p>No rooms available in Ground level</p>
                        <?php } ?>
                    </div>
    
                    <!-- Column 2: Upper, Level 2, Ground (Second Half of Rooms) -->
                    <div class="column">
                        <!-- Upper level (second half) -->
                        <?php if (isset($section_data['upper']) && count($section_data['upper']) > 0) {
                            $upperRooms = $section_data['upper'];
                            $half = ceil(count($upperRooms) / 2);
                            $upperSecondHalf = array_slice($upperRooms, $half);
                            foreach ($upperSecondHalf as $room) { ?>
                                <div class="room">
                                    <a href="single_room_details.php?room_num=<?= $room['room_num'] ?>" class="room-link">Room <?= $room['room_num'] ?></a>
                                </div>
                            <?php }
                            if (count($upperRooms) % 2 != 0) {
                                echo '<div class="empty-room"></div>';  // Empty space to balance layout
                            }
                        } else { ?>
                            <p>No rooms available in Upper level</p>
                        <?php } ?>
                        <hr> 
                        
                        <!-- Level 2 (second half) -->
                        <?php if (isset($section_data['level2']) && count($section_data['level2']) > 0) {
                            $level2Rooms = $section_data['level2'];
                            $half = ceil(count($level2Rooms) / 2);
                            $level2SecondHalf = array_slice($level2Rooms, $half);
                            foreach ($level2SecondHalf as $room) { ?>
                                <div class="room">
                                    <a href="single_room_details.php?room_num=<?= $room['room_num'] ?>" class="room-link">Room <?= $room['room_num'] ?></a>
                                </div>
                            <?php }
                            if (count($level2Rooms) % 2 != 0) {
                                echo '<div class="empty-room"></div>';  // Empty space to balance layout
                            }
                
                        } else { ?>
                            <p>No rooms available in Level 2</p>
                        <?php } ?>
                        <hr> 
                        
                        <!-- Ground level (second half) -->
                        <?php if (isset($section_data['ground']) && count($section_data['ground']) > 0) {
                            $groundRooms = $section_data['ground'];
                            $half = ceil(count($groundRooms) / 2);
                            $groundSecondHalf = array_slice($groundRooms, $half);
                            foreach ($groundSecondHalf as $room) { ?>
                                <div class="room">
                                    <a href="single_room_details.php?room_num=<?= $room['room_num'] ?>" class="room-link">Room <?= $room['room_num'] ?></a>
                                </div>
                            <?php }
                            if (count($groundRooms) % 2 != 0) {
                                echo '<div class="empty-room"></div>';  // Empty space to balance layout
                            }
                        } else { ?>
                            <p>No rooms available in Ground level</p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>

</body>


<footer>
    <nav>
        <a href="#privacy">Privacy Policy</a>
        <a href="#terms">Terms of Service</a>
        <a href="#support">Support</a>
    </nav>
    <p>&copy; 2024 Room Booking System. All rights reserved.</p>
</footer>

</html>
