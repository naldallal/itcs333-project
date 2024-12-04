<?php
try {
    // database connection
    $db = new PDO("mysql:host=localhost;dbname=roomsdetailsdatabase", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //room details and section names
    $query = "SELECT r.room_id, r.section_id, r.room_number, s.section_name 
              FROM rooms r
              JOIN sections s ON r.section_id = s.section_id 
              ORDER BY r.section_id, r.room_number";
    $stmt = $db->prepare($query);
    $stmt->execute(); 

    // Organize rooms by section and level
    $sections = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Determine the level based on the room_number
        $room_number = $row['room_number'];
        if (substr($room_number, 0, 1) == '2') {
            $level = 'upper';  // Upper level starts with '2'
        } elseif (substr($room_number, 0, 1) == '1') {
            $level = 'level2'; // Level 2 starts with '1'
        } else {
            $level = 'ground'; // Ground level starts with '0'
        }

        // Organize the rooms by section, level, and store section name
        $sections[$row['section_id']]['section_name'] = $row['section_name']; // Store section name
        $sections[$row['section_id']][$level][] = $row;  // Store the full row
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
            <a href="building_map.php" class="active">IT college Map</a>
            <a href="index.php">Filter</a>
            <a href="#services">Services</a>
            <a href="#contact">Contact</a>
        </nav>
    </header>

    <hr>
    <div class="change-view-container">
        <a href="index.php">change view</a>
    </div>
    <hr>

    <div class="building">
        <?php
        // Iterate over each section
        foreach ($sections as $section_id => $section_data) { 
            $section_name = $section_data['section_name']; // Get the section name
        ?>
            <div class="section section-<?=$section_id?>">
                <div class="section-title"> 
                    <p> <?=$section_name?> </p> <!-- Display section name -->
                </div>

                <div class="columns">
                    <!-- Column 1: Upper, Level 2, Ground (First Half of Rooms) -->
                    <div class="column">
                        <?php
                        // Upper level (first half)
                        if (isset($section_data['upper'])) {
                            $upperRooms = $section_data['upper'];
                            $half = ceil(count($upperRooms) / 2); //round number up to the nearest integer
                            $upperFirstHalf = array_slice($upperRooms, 0, $half);
                            foreach ($upperFirstHalf as $room) { ?>
                                <div class="room">
                                    <a href="single_room_details.php?room_id=<?= $room['room_id'] ?>" class="room-link">Room <?= $room['room_number'] ?></a>
                                </div>
                            <?php }
                        }
                        ?> <hr> <?php

                        // Level 2 (first half)
                        if (isset($section_data['level2'])) {
                            $level2Rooms = $section_data['level2'];
                            $half = ceil(count($level2Rooms) / 2); //round number up to the nearest integer
                            $level2FirstHalf = array_slice($level2Rooms, 0, $half);
                            foreach ($level2FirstHalf as $room) { ?>
                                <div class="room">
                                    <a href="single_room_details.php?room_id=<?= $room['room_id'] ?>" class="room-link">Room <?= $room['room_number'] ?></a>
                                </div>
                            <?php }
                        } ?> <hr> <?php

                        // Ground level (first half)
                        if (isset($section_data['ground'])) {
                            $groundRooms = $section_data['ground'];
                            $half = ceil(count($groundRooms) / 2); //round number up to the nearest integer
                            $groundFirstHalf = array_slice($groundRooms, 0, $half);
                            foreach ($groundFirstHalf as $room) { ?>
                                <div class="room">
                                    <a href="single_room_details.php?room_id=<?= $room['room_id'] ?>" class="room-link">Room <?= $room['room_number'] ?></a>
                                </div>
                            <?php }
                        } 

                        ?>
                    </div>

                    <!-- Column 2: Upper, Level 2, Ground (Second Half of Rooms) -->
                    <div class="column">
                        <?php
                        // Upper level (second half)
                        if (isset($section_data['upper'])) {
                            $upperRooms = $section_data['upper'];
                            $half = ceil(count($upperRooms) / 2); //round number up to the nearest integer
                            $upperSecondHalf = array_slice($upperRooms, $half);
                            foreach ($upperSecondHalf as $room) { ?>
                                <div class="room">
                                    <a href="single_room_details.php?room_id=<?= $room['room_id'] ?>" class="room-link">Room <?= $room['room_number'] ?></a>
                                </div>
                            <?php }
                        }?> <hr> <?php


                        // Level 2 (second half)
                        if (isset($section_data['level2'])) {
                            $level2Rooms = $section_data['level2'];
                            $half = ceil(count($level2Rooms) / 2); //round number up to the nearest integer
                            $level2SecondHalf = array_slice($level2Rooms, $half);
                            foreach ($level2SecondHalf as $room) { ?>
                                <div class="room">
                                    <a href="single_room_details.php?room_id=<?= $room['room_id'] ?>" class="room-link">Room <?= $room['room_number'] ?></a>
                                </div>
                            <?php }
                        }?> <hr> <?php


                        // Ground level (second half)
                        if (isset($section_data['ground'])) {
                            $groundRooms = $section_data['ground'];
                            $half = ceil(count($groundRooms) / 2); //round number up to the nearest integer
                            $groundSecondHalf = array_slice($groundRooms, $half);
                            foreach ($groundSecondHalf as $room) { ?>
                                <div class="room">
                                    <a href="single_room_details.php?room_id=<?= $room['room_id'] ?>" class="room-link">Room <?= $room['room_number'] ?></a>
                                </div>
                            <?php }
                        }?> 
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
