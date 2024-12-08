<?php
session_start();
try {
    $db = new PDO("mysql:host=localhost;dbname=my_db", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = "SELECT * FROM rooms WHERE 1=1";  // Start with a generic query

    $filters = [];

    if (isset($_GET['id'])) {
        $user_id = $_GET['id'];
    } else {
        // Handle the case where user_id is not provided (maybe redirect to login page)
        header("Location: login_page.php");
        exit();
    }
    
    // Max Capacity Filter
    if (isset($_GET['capacity']) && is_array($_GET['capacity'])) {
        $selectedCapacities = $_GET['capacity'];
        $myList = implode(',', array_fill(0, count($selectedCapacities), '?'));
        $query .= " AND rooms.capacity IN ($myList)";
        $filters = array_merge($filters, $selectedCapacities); 
    }

    // Department Filter
    if (isset($_GET['department']) && is_array($_GET['department'])) {
        $selectedDepartments = $_GET['department'];
        $myList = implode(',', array_fill(0, count($selectedDepartments), '?'));
        $query .= " AND department IN ($myList)";
        $filters = array_merge($filters, $selectedDepartments); 
    }

    // Room Type Filter
    if (isset($_GET['type']) && is_array($_GET['type'])) {
        $selectedRoomTypes = $_GET['type'];
        $myList = implode(',', array_fill(0, count($selectedRoomTypes), '?'));
        $query .= " AND rooms.type IN ($myList)";
        $filters = array_merge($filters, $selectedRoomTypes); 
    }

    // Equipment List Filter (Ensuring all selected equipment is present in the room)
    if (isset($_GET['equipment']) && is_array($_GET['equipment'])) {
        $selectedEquipments = $_GET['equipment'];
        $query .= " AND (";  // Start a new condition group for equipment filters
        $equipmentFilters = [];
        foreach ($selectedEquipments as $equipment) {
            // Check if each equipment is present in the room's equipment
            $equipmentFilters[] = "rooms.equipment LIKE ?";
            $filters[] = "%$equipment%";
        }
        $query .= implode(' AND ', $equipmentFilters);  // Ensure all selected equipment is in the room
        $query .= ")";  // Close the condition group
    }

    $stmt = $db->prepare($query);

    foreach ($filters as $index => $filter) {
        $stmt->bindValue($index + 1, $filter, PDO::PARAM_STR);
    }

    $stmt->execute();

    $roomlist = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <title>Filter Rooms</title>
</head>
<style>
    
html, body{
    background-color:  #e4ceabcb;
}

body {
    font-family: serif;
    background-color:  #e4ceabcb;
}

header {
    background-color: #6f4712; /* Green */
    color: white;
    padding: 20px 0;
    text-align: center;
    margin: 3% 0;
}

nav {
    background-color: #333; /* Dark background for nav */
    display: flex;
    justify-content: center;
    padding: 10px;
}
nav a {
    color: white;
    text-decoration: none;
    padding: 14px 20px;
    margin: 0 10px;
    border-radius: 5px;
    transition: background 0.3s;
}
nav a:hover {
    background-color: #575757; /* Darker on hover */
}

nav a.active {
    background-color: #8a5c12; /* Active link color */
}

a{
    color: black;
    text-decoration:none;
    
}

hr { 
  display: block;
  margin-top: 0.5em;
  margin-bottom: 0.5em;
  margin-left: auto;
  margin-right: auto;
  border-style: inset;
  border-width: 1px;
} 

button{
    background-color: #6f4712 !important;
    transition: background-color 0.3s ease !important;
}

button:hover{
    background-color: #9a641df0 !important;
}

/* Base Card Styling */
.card {
    display: flex;
    flex-direction: column;
    padding: 20px;
    border-radius: 12px;
    background-color: #fff; /* Default background color */
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    transition: all 3.0s ease; /* Smooth transition for all properties */
    cursor: pointer; /* Indicate interactivity */
}

/* Hover Styling with Color Change */
.card:hover {
    background: linear-gradient(135deg, #a88e6e, #c89f77); /* Beautiful gradient color on hover */
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); /* Slightly stronger shadow */
    transform: translateY(-5px);
}

/* Optional: Card Content Styling */
.card-body {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    color: #333; /* Default text color */
    transition: color 0.5s ease; /* Smooth text color change */
}

/* Change text color on hover */
.card:hover .card-body {
    color: #fff; /* Change text color to white on hover for contrast */
}


/* Flexbox for Filter Categories */
.card-body-filter {
    display: flex;
    flex-direction: row; /* Ensure content inside the card is stacked horizontally */
    justify-content: space-between;
}

/* Container for All Filter Dropdowns (Next to each other) */
.filter-dropdowns-container {
    display: flex;
    flex-wrap: wrap; /* Allow wrapping if space is insufficient */
    gap: 20px; /* Space between filter dropdowns */
    margin-bottom: 20px; /* Space below the filter section */
}

/* Filter Dropdown Styling */
.filter-dropdown {
    flex: 1 1 ; /* Allow each dropdown to take up about 22% of the space */
    max-width: 22%; /* Set the max width for the dropdowns */
    box-sizing: border-box; /* Prevent overflow */
}

/* Button Styling */
.dropdown-btn {
    padding: 10px;
    font-size: 14px;
    cursor: pointer;
    background-color: #f4f4f4;
    border: 1px solid #ccc;
    border-radius: 5px;
    width: 100%;
    text-align: left;
    display: block;
    transition: background-color 0.3s ease; /* Smooth transition for hover effect */
}

/* Hover effect for dropdown button */
.dropdown-btn:hover {
    background-color: #e0e0e0;
}

/* Dropdown Content (Hidden by default) */
.dropdown-content {
    display: none; /* Initially hidden */
    position: absolute;
    background-color: white;
    box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
    z-index: 1;
    width: 21%;
    border: 1px solid #ccc;
    border-radius: 5px;
    padding: 10px;
    margin-top: 5px;
}

/* Show dropdown content when hovering over the container */
.filter-dropdown:hover .dropdown-content,
.filter-dropdown .dropdown-btn:focus + .dropdown-content {
    display: block;  /* Show dropdown when hovered or button is focused */
}

/* Label and Checkbox Styling */
.dropdown-content label {
    display: block;
    padding: 5px;
    font-size: 14px;
}

.dropdown-content input[type="checkbox"] {
    margin-right: 8px;
}

/* Optional: Hover effect for dropdown content items */
.dropdown-content label:hover {
    background-color: #f0f0f0;  /* Add hover effect to dropdown items */
}


/* Card Layout */
.card-row {
    display: flex;
    flex-wrap: wrap; /* Allow cards to wrap */
    gap: 20px; /* Add space between cards */
}

.card-col {
    flex: 1 1 calc(33.333% - 20px); /* Display 3 cards per row by default */
    max-width: calc(33.333% - 20px); /* Ensure that 3 cards per row fit */
    box-sizing: border-box;
}

.change-view-container {
    text-align: center;
}

.dropdown-container button{
    background-color: #dbbba4 !important;
    transition: background-color 0.7s ease !important;
}
.dropdown-container button:hover{
    background-color: #e9d5c7 !important;
}

footer {
    background-color: #6f4712; 
    color: white;
    padding: 20px 0;
    text-align: center;
    margin: 3% 0;
}

footer nav {
    display: flex;
    justify-content: center;
    padding: 10px;
    flex-wrap: wrap; /* Allow wrapping */
}
footer nav a {
    color: white;
    text-decoration: none;
    padding: 10px 15px;
    margin: 5px; /* Margin for spacing */
    border-radius: 5px;
    transition: background 0.3s;
}
footer nav a:hover {
    background-color: #575757; /* Darker on hover */
}
footer p {
    margin: 10px 0 0; /* Space above the paragraph */
}
.card-exclude-card {
    /* Apply styles for the excluded card */
    border-radius: 10px;
    background-color: #f4f4f4; /* Example: Change the background color */
    box-shadow: none; /* Example: Remove the shadow */
    padding: 30px; /* Example: Change padding */
    transition: none; /* Disable transition effect */
}

.no-room{
    background-color: white;
    border-radius: 10px;
    padding:10px;
}
/* Medium screens (769px to 1024px) - 2 cards per row */
@media (min-width: 769px) and (max-width: 1024px) {
    .card-col {
        flex: 1 1 48%; /* Make sure the cards fit side by side */
        max-width: 48%; /* Enforce width */
    }

    .filter-dropdown h3{
        font-size: 20px;;
    }
}

/* Small screens (max-width: 768px) - 1 card per row */
@media (max-width: 768px) {
    .card-col {
        flex: 1 1 100%; /* 1 card per row */
        max-width: 100%;
    }

    /* Stack the filter dropdowns vertically on small screens */
    .filter-dropdowns-container {
        flex-direction: column; /* Stack filter dropdowns vertically on small screens */
        gap: 10px; /* Smaller gap between stacked dropdowns */
    }

    .filter-dropdown {
        flex: 1 1 100%; /* Allow dropdowns to take full width */
        max-width: 100%; /* Prevent overflow */
        min-width: 100%; /* Ensure no width issues on smaller screens */
    }

    /* Stack filter categories vertically */
    .card-body-filter {
        flex-direction: column;
    }
    h1 {
        font-size: 2em; /* Smaller title on small screens */
    }

    nav a {
        padding: 10px 5px; /* Smaller padding for buttons */
        font-size: 1.2em; /* Smaller font size */
    }
}
</style>
<body>
<header>
    <h1>Room Booking System</h1>
    <nav>
    <a href="building_map.php?id=<?= $_SESSION['user_id'] ?>">IT college Map</a>
    <a href="filter_page.php?id=<?= $_SESSION['user_id'] ?>" class="active">Filter</a>
        <a href="userprofile2.php?id=<?= $_SESSION['user_id'] ?>">User Profile</a>
        <a href="logout.php">Log out</a>
    </nav>
</header>

<hr>
<div class="change-view-container">
    <a href="building_map.php?id=<?= $_SESSION['user_id'] ?>">Change View</a>
</div>
<hr>

<main>
<form action="" method="GET">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card-exclude-card shadow mt-3">
                <div class="card-header d-flex justify-content-between">
                        <h5>Filter</h5>
                        <button type="submit" class="btn btn-primary btn-sm float-end">Search</button>
                    </div>
                    <div class="card-body-filter">
                    <div class="filter-dropdown">
                            <h6>Max Capacity</h6>
                            <div class="dropdown-container">
                                <button type="button" class="dropdown-btn">Select Max Capacity</button>
                                <div class="dropdown-content">
                                    <label><input type="checkbox" name="capacity[]" value="20" <?= in_array('20', $_GET['capacity'] ?? []) ? 'checked' : '' ?>> 20 persons</label><br>
                                    <label><input type="checkbox" name="capacity[]" value="30" <?= in_array('30', $_GET['capacity'] ?? []) ? 'checked' : '' ?>> 30 persons</label><br>
                                    <label><input type="checkbox" name="capacity[]" value="40" <?= in_array('40', $_GET['capacity'] ?? []) ? 'checked' : '' ?>> 40 persons</label><br>
                                    <label><input type="checkbox" name="capacity[]" value="50" <?= in_array('50', $_GET['capacity'] ?? []) ? 'checked' : '' ?>> 50 persons</label><br>
                                </div>
                            </div>
                        </div>

                        <!-- Department Filter -->
                        <div class="filter-dropdown">
                            <h6>Department</h6>
                            <div class="dropdown-container">
                                <button type="button" class="dropdown-btn">Select Department</button>
                                <div class="dropdown-content">
                                    <label><input type="checkbox" name="department[]" value="IS" <?= in_array('IS', $_GET['department'] ?? []) ? 'checked' : '' ?>> Information Systems</label><br>
                                    <label><input type="checkbox" name="department[]" value="CS" <?= in_array('CS', $_GET['department'] ?? []) ? 'checked' : '' ?>> Computer Science</label><br>
                                    <label><input type="checkbox" name="department[]" value="CE" <?= in_array('CE', $_GET['department'] ?? []) ? 'checked' : '' ?>> Computer Engineering</label><br>
                                </div>
                            </div>
                        </div>

                        <!-- Room Type Filter -->
                        <div class="filter-dropdown">
                            <h6>Room Type</h6>
                            <div class="dropdown-container">
                                <button type="button" class="dropdown-btn">Select Room Type</button>
                                <div class="dropdown-content">
                                    <label><input type="checkbox" name="type[]" value="lecture" <?= in_array('lecture', $_GET['type'] ?? []) ? 'checked' : '' ?>> Lecture</label><br>
                                    <label><input type="checkbox" name="type[]" value="seminar" <?= in_array('seminar', $_GET['type'] ?? []) ? 'checked' : '' ?>> Seminar</label><br>
                                    <label><input type="checkbox" name="type[]" value="lab" <?= in_array('lab', $_GET['type'] ?? []) ? 'checked' : '' ?>> Lab</label><br>
                                    <label><input type="checkbox" name="type[]" value="meeting" <?= in_array('meeting', $_GET['type'] ?? []) ? 'checked' : '' ?>> Meeting</label><br>
                                </div>
                            </div>
                        </div>

                        <!-- Equipment List Filter -->
                        <div class="filter-dropdown">
                            <h6>Equipment List</h6>
                            <div class="dropdown-container">
                                <button type="button" class="dropdown-btn">Select Equipment</button>
                                <div class="dropdown-content">
                                    <label><input type="checkbox" name="equipment[]" value="Whiteboard" <?= in_array('Whiteboard', $_GET['equipment'] ?? []) ? 'checked' : '' ?>> Whiteboard</label><br>
                                    <label><input type="checkbox" name="Eequipment[]" value="Projector" <?= in_array('Projector', $_GET['equipment'] ?? []) ? 'checked' : '' ?>> Projector</label><br>
                                    <label><input type="checkbox" name="equipment[]" value="Computers" <?= in_array('Computers', $_GET['equipment'] ?? []) ? 'checked' : '' ?>> Computers</label><br>
                                    </div>
                            </div>
                        </div>

                    </div> <!-- end of card-body -->
                </div>
            </div>
        </div>
    </div>
</form>

    <div class="container mt-4">
        <h3>Rooms:</h3>
        <div class="row">
            <?php
            if (count($roomlist) > 0) {
                foreach ($roomlist as $room) {
                    echo "<div class='col-lg-4 col-md-6 col-12 mb-4'>
                            <div class='card'>
                                <a href='single_room_details.php?room_num={$room['room_num']}' class='text-decoration-none'>
                                    <div class='card-body'>
                                        <h5 class='card-title'>{$room['room_num']}</h5> 
                                        <p class='card-text'><strong>Max Capacity:</strong> {$room['capacity']} persons</p> 
                                        <p class='card-text'><strong>Department:</strong> {$room['department']}</p> 
                                        <p class='card-text'><strong>Room Type:</strong> {$room['type']}</p> 
                                        <p class='card-text'><strong>Equipment:</strong> {$room['equipment']}</p> 
                                    </div>
                                </a>
                            </div>
                          </div>";
                }
            } else {
                echo "<div class='no-room'><div class='col-12'>No rooms found matching the selected criteria.</div></div>";
            }
            ?>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    function updateDropdownText(dropdown) {
        const selectedValues = [];
        const checkboxes = dropdown.querySelectorAll('input[type="checkbox"]:checked');
        checkboxes.forEach(checkbox => {
            selectedValues.push(checkbox.parentElement.textContent.trim());
        });
        const button = dropdown.querySelector('.dropdown-btn');
        button.textContent = selectedValues.length > 0 ? selectedValues.join(', ') : 'Select options';
    }

    document.querySelectorAll('.dropdown-content input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateDropdownText(checkbox.closest('.dropdown-container'));
        });
    });

    document.querySelectorAll('.dropdown-container').forEach(dropdown => {
        updateDropdownText(dropdown);
    });
});
</script>
</body>
</html>
