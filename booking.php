<?php
// Database connection using PDO
try {
    $pdo = new PDO('mysql:host=localhost;dbname=my_db', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

// Fetch bookings for a specific room and date
if (isset($_GET['date']) && isset($_GET['room_id'])) {
    $date = $_GET['date'];
    $room_id = $_GET['room_id'];
    $stmt = $pdo->prepare("SELECT * FROM bookings WHERE date = :date AND room_id = :room_id");
    $stmt->execute(['date' => $date, 'room_id' => $room_id]);
    $booking = $stmt->fetchAll(PDO::FETCH_COLUMN, 2); // Assuming 'timeslot' is the third column
}

// Handle booking submission
if (isset($_POST['bookings'])) {
    $user_id = $_POST['user_id'];
    $room_id = $_POST['room_id'];
    $timeslot = $_POST['timeslot'];
    $date = $_POST['date'];
    $stmt = $pdo->prepare("INSERT INTO bookings (user_id, room_id, date, timeslot) VALUES (:user_id, :room_id, :date, :timeslot)");
    if ($stmt->execute(['user_id' => $user_id, 'room_id' => $room_id, 'date' => $date, 'timeslot' => $timeslot])) {
        $message = "<div class='alert alert-success'>Booking Successful</div>";
        $booking[] = $timeslot;
    }
}

// Function to generate available time slots
function timeslots($duration, $cleanup, $start, $end) {
    $start = new DateTime($start);
    $end = new DateTime($end);
    $interval = new DateInterval("PT" . $duration . "M");
    $cleanupinterval = new DateInterval("PT" . $cleanup . "M");
    $slots = array();

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
function build_calendar($month, $year) {
    // Days of the week
    $daysOfWeek = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
    
    // First day of the month
    $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);

    // Number of days in the month
    $numberDays = date('t', $firstDayOfMonth);

    // Get information about the first day of the month
    $dateComponents = getdate($firstDayOfMonth);

    // Get the name of the month
    $monthName = $dateComponents['month'];

    // Get the index of the first day of the month (0 = Sunday, 1 = Monday, ...)
    $dayOfWeek = $dateComponents['wday'];

    // Initialize the calendar table
    $calendar = "<table class='table table-bordered'>";
    $calendar .= "<center><h2>$monthName $year</h2></center>";

    // Previous Month Button (taking care of month/year boundaries)
    $previousMonth = $month - 1;
    $previousYear = $year;
    if ($previousMonth < 1) {
        $previousMonth = 12;
        $previousYear = $year - 1;
    }

    // Next Month Button (taking care of month/year boundaries)
    $nextMonth = $month + 1;
    $nextYear = $year;
    if ($nextMonth > 12) {
        $nextMonth = 1;
        $nextYear = $year + 1;
    }

    // Add navigation buttons with correct query parameters 
    $calendar .= "<div class='calendar-nav'>";
    $calendar .= "<a class='btn btn-xs btn-primary' href='?month=$previousMonth&year=$previousYear'>Previous Month</a>";
    $calendar .= "<a class='btn btn-xs btn-primary' href='?month=" . date('m') . "&year=" . date('Y') . "'>Current Month</a>";
    $calendar .= "<a class='btn btn-xs btn-primary' href='?month=$nextMonth&year=$nextYear'>Next Month</a>";
    $calendar .= "</div>";
    
    // Calendar header (days of the week)
    $calendar .= "<tr>";
    foreach ($daysOfWeek as $day) {
        $calendar .= "<th class='header'>$day</th>";
    }
    $calendar .= "</tr><tr>";

    // Add empty cells for the days before the first day of the month
    if ($dayOfWeek > 0) {
        for ($k = 0; $k < $dayOfWeek; $k++) {
            $calendar .= "<td class='empty'></td>";
        }
    }

    // Start filling in the days
    $currentDay = 1;
    $month = str_pad($month, 2, "0", STR_PAD_LEFT);  // Ensure month is always 2 digits

    while ($currentDay <= $numberDays) {
        // If we reached the 7th column (Saturday), start a new row
        if ($dayOfWeek == 7) {
            $dayOfWeek = 0;
            $calendar .= "</tr><tr>";  // Start a new row
        }

        // Format the date
        $currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT); // Add leading zero if needed
        $date = "$year-$month-$currentDayRel";

        // Determine if the date is today or past
        $todayClass = ($date == date('Y-m-d')) ? 'today' : '';
        if ($date < date('Y-m-d')) {
            $calendar .= "<td><h4>$currentDay</h4><button class='btn btn-danger btn-xs'>N/A</button></td>";
        } else {
            $calendar .= "<td class='$todayClass'><h4>$currentDay</h4><a href='?month=$month&year=$year&day=$currentDay' class='btn btn-success btn-xs'>Book</a></td>";
        }

        // Increment the day and dayOfWeek
        $currentDay++;
        $dayOfWeek++;
    }

    // Complete the last row with empty cells if necessary
    if ($dayOfWeek != 7) {
        $remainingDays = 7 - $dayOfWeek;
        for ($i = 0; $i < $remainingDays; $i++) {
            $calendar .= "<td class='empty'></td>";
        }
    }

    // Close the last row and table
    $calendar .= "</tr>";
    $calendar .= "</table>";

    return $calendar;
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar</title>
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
        $month = isset($_GET['month']) ? $_GET['month'] : date('m');
        $year = isset($_GET['year']) ? $_GET['year'] : date('Y');
        $room_id = isset($_GET['room_id']) ? $_GET['room_id'] : 1;
        $user_id = 1; // Replace with the actual user ID from your session or authentication system

        echo build_calendar($month, $year, $room_id);

        if (isset($_GET['day'])) {
            $selectedDay = $_GET['day'];
            $date = "$year-$month-" . str_pad($selectedDay, 2, '0', STR_PAD_LEFT);
            echo "<h3 class='text-center'>Available Time Slots for Room $room_id on $month/$selectedDay/$year</h3><hr>";
            $slots = timeslots($duration, $cleanup, $start, $end);

            echo "<form action='#' method='POST'>";
            echo "<input type='hidden' name='date' value='$date'>";
            echo "<input type='hidden' name='room_id' value='$room_id'>";
            echo "<input type='hidden' name='user_id' value='$user_id'>"; // Include user ID in the form
            echo '<div class="row">';
            foreach ($slots as $slot) {
                if (in_array($slot, $booking)) {
                    echo "<div class='col-md-3 mb-3'>";
                    echo "<div class='form-group'>";
                    echo "<button type='button' class='btn btn-danger btn-block' onclick='showBookedMessage()'>$slot</button>";
                    echo "</div>";
                    echo "</div>";
                } else {
                    echo "<div class='col-md-3 mb-3'>";
                    echo "<div class='form-group'>";
                    echo "<button type='submit' class='btn btn-success btn-block' name='timeslot' value='$slot'>$slot</button>";
                    echo "</div>";
                    echo "</div>";
                }
            }
            echo '</div>';
            echo "</form>";
        }
        ?>
    </div>
</body>
</html>