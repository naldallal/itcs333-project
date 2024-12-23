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
        echo '<td>'.$row['available_from'].'</td>';
        echo '<td>'.$row['available_to'].'</td>';
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
    $available_from = $_POST['available_from']; 
    $available_to = $_POST['available_to'];
    if ($available_from > $available_to) {
        $temp = $available_from;
        $available_from = $available_to;
        $available_to = $temp;
    }
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM rooms WHERE room_num = ?"); 
    $stmt->execute([$room_num]); 
    $result = $stmt->fetchColumn(); // Fetch the count 
    if ($result > 0) { // Room already exists, show alert and stop the script echo 
        echo "<script>
        alert('Room already exists!'); 
        window.history.back();
        </script>"; 
        return; 
    }
    $result = $pdo->query("INSERT INTO rooms (room_num, department,capacity,equipment,type,available_from,available_to) VALUES ('$room_num', '$department', $capacity, '$equipment','$type','$available_from','$available_to')");
    // echo "Room added successfully!";
    // get_table($department);
}
if (isset($_POST['edit_room'])) {
    $room_num = $_POST['room_num'];
    $capacity = $_POST['capacity'];
    $type = $_POST['type'];
    $equipment = isset($_POST['equipment']) ? implode(", ", $_POST['equipment']) : '';
    $available_from = $_POST['available_from']; 
    $available_to = $_POST['available_to'];
    if ($available_from > $available_to) {
        $temp = $available_from;
        $available_from = $available_to;
        $available_to = $temp;
    }
    global $pdo;
    $statement = $pdo->prepare("UPDATE rooms SET  capacity = ?, equipment = ?, type = ?, available_from = ?,available_to = ? WHERE room_num = ?");
    $statement->execute([ $capacity, $equipment, $type, $available_from, $available_to, $room_num]);
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
function get_total_requests(){
    global $pdo;
    $stmt = $pdo->query("SELECT COUNT(*) FROM user WHERE role='pending'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['COUNT(*)'];
}
function get_pending_requests(){
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM user WHERE role='pending'");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
if (isset($_POST['edit_role'])) {
    $action = $_POST['action'];
    $userId = $_POST['id'];

    // Ensure the action is either 'admin' or 'user'
    if (in_array($action, ['admin', 'user'])) {
        // Database connection
        global $pdo;
        $statement = $pdo->prepare("UPDATE user SET role = ? WHERE id = ?");
        $result = $statement->execute([$action, $userId]);
}
}
?>
