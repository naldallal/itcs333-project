<?php
// Establish a connection to the database
$pdo = new PDO('mysql:host=localhost;dbname=my_db;charset=utf8mb4', 'root');

// Function to get the count of pending requests
function count_pending_requests(){
    global $pdo;
    $stmt = $pdo->query("SELECT COUNT(*) AS count FROM user WHERE role='pending'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'];
}

// Function to get pending requests
function get_pending_requests(){
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM user WHERE role='pending'");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$pending_count = count_pending_requests();
$pending_requests = get_pending_requests();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        .dashboard-item {
            cursor: pointer;
            text-decoration: underline;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 15px;
            text-align: left;
        }
    </style>
    <script>
        function showPendingRequests() {
            document.getElementById('rModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('rModal').style.display = 'none';
        }
    </script>
</head>
<body>
    <div class="dashboard-item" onclick="showPendingRequests()">
        Pending Requests:<br/> <?php echo $pending_count; ?>
    </div>

    <!-- Modal -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Pending Requests</h2>
            <table>
                <tr>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($pending_requests as $request): ?>
                <tr>
                    <td><?php echo $request['id']; ?></td>
                    <td><?php echo $request['username']; ?></td>
                    <td><?php echo $request['email']; ?></td>
                    <td>
                        <button onclick="approveRequest(<?php echo $request['id']; ?>)">Approve</button>
                        <button onclick="disapproveRequest(<?php echo $request['id']; ?>)">Disapprove</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

    <script>
        function approveRequest(userId) {
            // Handle the approval process
            alert('Approve request for user ID: ' + userId);
        }

        function disapproveRequest(userId) {
            // Handle the disapproval process
            alert('Disapprove request for user ID: ' + userId);
        }

        // Close modal when clicking outside of it
        window.onclick = function(event) {
            var modal = document.getElementById('myModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>


<form method='POST'  style='display:inline;'>
    <input type='hidden' name='id' value='$userId'>
    <input type='hidden' name='action' value='pending'>
    <button type='submit' name='pending_role'>Admin Request</button>
</form>

<?php
if (isset($_POST['pending_role'])) {
    $action = $_POST['action'];
    $userId = $_POST['id'];

    // Ensure the action is either 'admin' or 'user'
    
        global $pdo;
        $statement = $pdo->prepare("UPDATE user SET role = ? WHERE id = ?");
        $result = $statement->execute([$action, $userId]);
}
?>