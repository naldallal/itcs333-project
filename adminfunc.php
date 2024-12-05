<?php
function get_table($department) {
    global $pdo;
    $result = $pdo->query("SELECT * FROM rooms where department='$department'");
    foreach ($result as $row) {
        echo '<tr>';
        echo '<td>'.$row['room_num'].'</td>';
        echo '<td>'.$row['type'].'</td>';
        echo '<td>'.$row['capacity'].'</td>';
        echo '<td>'.$row['equipment'].'</td>';
        echo "<td><button onclick=\"editRoom('IS',this)\">Edit Room</button>".
            "<button onclick=\"DeleteRoom('IS',this)\">Delete Room</button></td>";
        echo '</tr>';
    }
}

if (isset($_POST['add_room'])) {
    $room_num = $_POST['room_num'];
    $capacity = $_POST['capacity'];
    $department = $_POST['department'];
    $type = $_POST['type'];
    $equipment = isset($_POST['equipment']) ? implode(", ", $_POST['equipment']) : '';
    global $pdo;
    $result = $pdo->query("INSERT INTO rooms (room_num, department,capacity,equipment,type) VALUES ('$room_num', '$department', $capacity, '$equipment','$type')");
    echo "Room added successfully!";
    // get_table($department);
}
if (isset($_POST['edit_room'])) {
    $room_num = $_POST['room_num'];
    $capacity = $_POST['capacity'];
    // $department = $_POST['department'];
    $type = $_POST['type'];
    $equipment = isset($_POST['equipment']) ? implode(", ", $_POST['equipment']) : '';
    global $pdo;
    $statement = $pdo->prepare("UPDATE rooms SET  capacity = ?, equipment = ?, type = ? WHERE room_num = ?");
    $statement->execute([ $capacity, $equipment, $type, $room_num]);
    // echo "Room EDITED successfully!";
    // get_table($department);
}
if (isset($_POST['delete_room'])) {
    $room_num = $_POST['ddnumroom'];
    global $pdo;
    $statement = $pdo->prepare("DELETE FROM rooms WHERE room_num=? ;");
    $statement->execute([$room_num]);
    // echo "Room DELETED successfully!".$room_num."<br>";
    // echo "Room EDITED successfully!";
    // get_table($department);
}


function get_graph($department) {
    global $pdo;
    $stmt = $pdo->query("SELECT COUNT(*) FROM rooms WHERE department='$department'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $result['COUNT(*)'];
}
function get_total_rooms(){
    global $pdo;
    $stmt = $pdo->query("SELECT COUNT(*) FROM rooms");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['COUNT(*)'];
}
function get_pending_requests(){
    global $pdo;
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role='pending'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['COUNT(*)'];
}

?>
