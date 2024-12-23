
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
// Database connection using PDO
try {
    global $pdo;
    $pdo = new PDO('mysql:host=localhost;dbname=my_db', "root");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

// Initialize booking array
$booking = [];

// Define variables
$user_id = $_SESSION['user_id'];
$room_num = isset($_GET['room_num']) ? $_GET['room_num'] : null; // Ensure room_num is set
$date = $timeslots = '';

// Fetch bookings for a specific room and date
if (isset($_GET['date']) && isset($_GET['room_num'])) {
    $date = $_GET['date'];
    $room_num = $_GET['room_num'];
    $stmt = $pdo->prepare("SELECT timeslots FROM bookings WHERE date = :date AND room_num = :room_num");
    $stmt->execute(['date' => $date, 'room_num' => $room_num]);
    $booking = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Handle booking submission
if (isset($_POST['bookings'])) {
    $user_id = $_POST['user_id'];
    $room_num = $_POST['room_num'];
    $timeslots = $_POST['timeslots'];
    $date = $_POST['date'];

    // Check if the timeslot is already booked
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE date = :date AND room_num = :room_num AND timeslots = :timeslots");
    $stmt->execute(['date' => $date, 'room_num' => $room_num, 'timeslots' => $timeslots]);
    $count = $stmt->fetchColumn();

    if ($count == 0) {
        // Insert the booking if the timeslot is not already booked
        $stmt = $pdo->prepare("INSERT INTO bookings (user_id, room_num, date, timeslots) VALUES (:user_id, :room_num, :date, :timeslots)");
        try {
            if ($stmt->execute(['user_id' => $user_id, 'room_num' => $room_num, 'date' => $date, 'timeslots' => $timeslots])) {
                $message = "<div class='alert alert-success'>Booking Successful</div>";
                $booking[] = $timeslots; // Add booked timeslot to the array
            }
        } catch (PDOException $e) {
            $message = "<div class='alert alert-danger'>Booking failed: " . $e->getMessage() . "</div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>This timeslot is already booked.</div>";
    }
}

// Function to generate available time slots
function timeslots($duration, $cleanup, $start, $end) {
    $start = new DateTime($start);
    $end = new DateTime($end);
    $interval = new DateInterval("PT" . $duration . "M");
    $cleanupinterval = new DateInterval("PT" . $cleanup . "M");
    $slots = [];

    while ($start < $end) {
        $endPeriod = clone $start;
        $endPeriod->add($interval);

        if ($endPeriod > $end) {
            break;
        }

        $slots[] = $start->format("H:IA") . "-" . $endPeriod->format("H:IA");
        $start->add($interval)->add($cleanupinterval);
    }

    return $slots;
}

// Function to generate the calendar
function build_calendar($month, $year, $room_num) {
    $daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
    $numberDays = date('t', $firstDayOfMonth);
    $dateComponents = getdate($firstDayOfMonth);
    $monthName = $dateComponents['month'];
    $dayOfWeek = $dateComponents['wday'];

    $calendar = "<table class='table table-bordered'>";
    $calendar .= "<center><h2>$monthName $year</h2></center>";

    // Navigation for previous and next month
    $previousMonth = $month - 1;
    $previousYear = ($previousMonth < 1) ? $year - 1 : $year;
    $previousMonth = ($previousMonth < 1) ? 12 : $previousMonth;

    $nextMonth = $month + 1;
    $nextYear = ($nextMonth > 12) ? $year + 1 : $year;
    $nextMonth = ($nextMonth > 12) ? 1 : $nextMonth;

    // Navigation buttons
    $calendar .= "<div class='calendar-nav'>";
    $calendar .= "<a class='btn btn-xs btn-primary' href='?month=$previousMonth&year=$previousYear&room_num=$room_num'>Previous Month</a>";
    $calendar .= "<a class='btn btn-xs btn-primary' href='?month=" . date('m') . "&year=" . date('Y') . "&room_num=$room_num'>Current Month</a>";
    $calendar .= "<a class='btn btn-xs btn-primary' href='?month=$nextMonth&year=$nextYear&room_num=$room_num'>Next Month</a>";
    $calendar .= "</div>";

    // Calendar header
    $calendar .= "<tr>";
    foreach ($daysOfWeek as $day) {
        $calendar .= "<th class='header'>$day</th>";
    }
    $calendar .= "</tr><tr>";

    // Empty cells before the first day of the month
    if ($dayOfWeek > 0) {
        for ($k = 0; $k < $dayOfWeek; $k++) {
            $calendar .= "<td class='empty'></td>";
        }
    }

    // Fill in the days
    $currentDay = 1;
    $month = str_pad($month, 2, "0", STR_PAD_LEFT);

    while ($currentDay <= $numberDays) {
        if ($dayOfWeek == 7) {
            $dayOfWeek = 0;
            $calendar .= "</tr><tr>";
        }

        $currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);
        $date = "$year-$month-$currentDayRel";
        $todayClass = ($date == date('Y-m-d')) ? 'today' : '';

        if ($date < date('Y-m-d')) {
            $calendar .= "<td><h4>$currentDay</h4><button class='btn btn-danger btn-xs'>N/A</button></td>";
        } else {
            $dayOfWeekName = date('l', strtotime($date));
            if ($dayOfWeekName == 'Friday') {
                $calendar .= "<td class='$todayClass'><h4>$currentDay</h4><button class='btn btn-danger btn-xs'>N/A</button></td>";
            } else {
                $calendar .= "<td class='$todayClass'><h4>$currentDay</h4><a href='?month=$month&year=$year&day=$currentDay&room_num=$room_num' class='btn btn-success btn-xs'>Book</a></td>";
            }
        }

        $currentDay++;
        $dayOfWeek++;
    }

    // Complete the last row with empty cells
    if ($dayOfWeek != 7) {
        $remainingDays = 7 - $dayOfWeek;
        for ($i = 0; $i < $remainingDays; $i++) {
            $calendar .= "<td class='empty'></td>";
        }
    }

    $calendar .= "</tr></table>";

    return $calendar;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking System</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script>
        function showBookedMessage() {
            alert("This time slot is already booked.");
        }
    </script>
</head>
<body>
<div class="container">
    <h1 class="text-center">Booking System</h1><hr>

    <?php
    $month = isset($_GET['month']) ? (int)$_GET['month'] : date('m');
    $year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
    $room_num = isset($_GET['room_num']) ? (int)$_GET['room_num'] : 1; // Ensure room_num is set
    $user_id = $_SESSION['user_id']; // Replace with actual user ID from your session or authentication

    echo build_calendar($month, $year, $room_num);

    if (isset($_GET['day'])) {
        $selectedDay = (int)$_GET['day'];
        $date = "$year-$month-" . str_pad($selectedDay, 2, '0', STR_PAD_LEFT);
        echo "<h3 class='text-center'>Available Time Slots for Room $room_num on $month/$selectedDay/$year</h3><hr>";

        // Define duration and cleanup times
        $duration = 60; // Duration of each slot in minutes
        $cleanup = 10; // Cleanup time in minutes
        $stmt = $pdo->query("SELECT available_from FROM rooms WHERE room_num = $room_num");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt = $pdo->query("SELECT available_from FROM rooms WHERE room_num = $room_num");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $start = $result['available_from']; // Start time

        $stmt = $pdo->query("SELECT available_to FROM rooms WHERE room_num = $room_num");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $end = $result['available_to']; // End time

        $slots = timeslots($duration, $cleanup, $start, $end);

        $slots = timeslots($duration, $cleanup, $start, $end);

        echo '<div class="row">';
        foreach ($slots as $slot) {
            echo "<form method='POST' class='col-md-3 mb-3' id='timeslot'>"; // Adjust column width as needed
            echo "<input type='hidden' id='date' name='date' value='$date'>";
            echo "<input type='hidden' id='room_num' name='room_num' value='$room_num'>";
            echo "<input type='hidden' id='user_id' name='user_id' value='$user_id'>";
            echo "<input type='hidden' id='timeslots' name='timeslots' value='$slot'>";

            if (in_array($slot, $booking)) {
                echo "<button type='button' class='btn btn-danger btn-block' onclick='showBookedMessage()'>$slot</button>";
            } else {
                echo "<button type='submit' class='btn btn-success btn-block' name='bookings' value='$slot'>$slot</button>";
            }
            echo "</form>";
        }
        echo '</div>';
    }

    // Display message if set
    if (isset($message)) {
        echo $message;
    }
    ?>
</div>
</body>
</html>