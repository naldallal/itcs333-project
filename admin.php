<?php 
 global $pdo; 
 $pdo = new PDO('mysql:host=localhost;dbname=my_db;charset=utf8mb4', 'root'); 
 // Include the functions file 
 include 'adminfunc.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="adminstyles.css">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            padding-top: 100px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }
    </style>
    <script>
        function renderPieChart(data) {
            var ctx = document.getElementById('myPieChart').getContext('2d');
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Information System', 'Computer Science', 'Computer Engineering'],
                    datasets: [{
                        label: '# of classes',
                        data: data,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(54, 162, 235, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(54, 162, 235, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                }
            });
        }
        function fetchAndRenderPieChart() {
            var data = [
                <?php echo get_graph('IS'); ?>,
                <?php echo get_graph('CS'); ?>,
                <?php echo get_graph('CE'); ?>
            ];
            renderPieChart(data);
        }
        document.addEventListener('DOMContentLoaded', fetchAndRenderPieChart);
        function showAddForm(department) {
            document.getElementById('modal-' + department).style.display = 'block';
        }
        function closeModal(department) {
            document.getElementById('modal-' + department).style.display = 'none';
        }
        function editRoom(department, button) {
            document.getElementById('edit-' + department).style.display = 'block'; 
            var row = button.parentNode.parentNode;
            var roomId = row.cells[0].innerText;
            var roomType = row.cells[1].innerText;
            var capacity = row.cells[2].innerText;
            var equipment = row.cells[3].innerText;
            var available_from = row.cells[4].innerText;
            var available_to = row.cells[5].innerText;

            document.getElementById('eroom_num').value = roomId;
            document.getElementById('room_num').value = roomId; // Ensure hidden field is also set

            if (roomType === "Lecture") {
                document.getElementById('Lecture').checked = true;
            } else if (roomType === "Lab") {
                document.getElementById('Lab').checked = true;
            } else if (roomType === "Seminar") {
                document.getElementById('Seminar').checked = true;
            } else {
                document.getElementById('Meeting').checked = true;
            }

            document.getElementById('capacity').value = capacity;

            const itemsArray = equipment.split(', ');
            document.getElementById('projector').checked = itemsArray.includes("Projector");
            document.getElementById('whiteboard').checked = itemsArray.includes("Whiteboard");
            document.getElementById('computers').checked = itemsArray.includes("Computers");
            document.getElementById('available_from').value = available_from;
            document.getElementById('available_to').value = available_to;
        }
        function closeEdit(department){
            document.getElementById('edit-' + department).style.display = 'none';
        }
        function DeleteRoom(department,button){
            console.log("infunc: "+"delete");
            document.getElementById('delete-' + department).style.display = 'block';
            var row = button.parentNode.parentNode;
            console.log("infunc: "+"ppp");
            var roomId = row.cells[0].innerText;
            console.log("infunc: "+roomId);
            document.getElementById('delete-room_num').value = roomId;
            document.getElementById('ddnumroom').value = roomId;
            console.log("room_num: "+roomId);
            // document.getElementById('delete-room_num').value = roomId;
        }
        function closeDelete(department){
            document.getElementById('delete-' + department).style.display = 'none';
        }
       
    </script>
</head>
<body>
    <header>
        <h1>Admin Dashboard</h1>
        <nav>
            <ul>
                <li><a href="#dashboard">Dashboard</a></li>
                <li><a href="#rooms">Room Management</a></li>
                <li><a href="#schedule">Schedule Management</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <section id="dashboard">
            <h2>Dashboard</h2>
            <!-- Add charts, graphs, and key metrics here -->
             <div class="dashboard-container">
                <span class="dashboard-item pie-container">
                    <canvas id="myPieChart"></canvas>
                </span>
                <span class="dashboard-item info-container">
                <span class="dashboard-item">
                    <h3>
                        Total Rooms:<br/> <?php echo get_total_rooms(); ?>
                    </h3>
                </span>
                <span class="dashboard-item">
                    <h3>
                        Requests Pending:<br/> <?php echo get_pending_requests(); ?>
                    </h3>
                </span>
                </span>
             </div>


        </section>
        <section id="rooms">
            <h2>Room Management</h2>
            <div class="room-category">
                <div class="category-header">
                    <h3 onclick="toggleTable('IS')">Information System Department</h3>
                    <button onclick="showAddForm('IS')">Add Room</button>
                </div>
                <table id="IS" class="room-table">
                    <thead>
                        <tr>
                            <th>Room ID</th>
                            <th>Room Type</th>
                            <th>Capacity</th>
                            <th>Equipment</th>
                            <th>Available From</th>
                            <th>Available To</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        get_table('IS');
                    ?>
                    </tbody>
                </table>
            </div>

            <!-- Editing Modal for IS Department -->
            <div id="edit-IS" class="modal">
                <div class="modal-content">
                    <span onclick="closeEdit('IS',this)" style="float:right; cursor:pointer;">&times;</span>
                    <h2>Edit Room in IS Department </h2>
                    <form method="post">
                        <label for="room_num">Room Number:</label>
                        <input type="number" id="eroom_num" name="room_num" disabled><br>
                        <input type="hidden" id="room_num" name="room_num">
                        <label for="type">Room Type:</label><br/>
                            <input type="radio" id="Lecture" name="type" value="Lecture" required>
                            <label for="Lecture">Lecture</label><br>
                            <input type="radio" id="Lab" name="type" value="Lab" required>
                            <label for="Lab">Lab</label><br>
                            <input type="radio" id="Seminar" name="type" value="Seminar" required>
                            <label for="Seminar">Seminar</label><br>
                            <input type="radio" id="Meeting" name="type" value="Meeting" required>
                            <label for="Meeting">Meeting</label><br>
                            </radio>
                        <label for="capacity">Capacity:</label>
                        <input type="number" id="capacity" name="capacity" required><br>
                        <label for="equipment">Equipment:</label><br/>
                        <checkbox>
                            <input type="checkbox" id="projector" name="equipment[]" value="Projector">Projector<br/>
                            <input type="checkbox" id="whiteboard" name="equipment[]" value="Whiteboard">Whiteboard<br/>
                            <input type="checkbox" id="computers" name="equipment[]" value="Computers">Computers<br/>
                        </checkbox>
                        <label for="available_from">Available From  </label>
                        <input type="time" id="available_from" name = "available_from" requierd><br/>
                        <label for="available_to">Available To  </label>
                        <input type="time" id="available_to" name = "available_to" requierd> <br/>
                        <input type="hidden" name="department" value="IS">
                        <input type="submit" name="edit_room" value="Edit Room">
                    </form>
                </div>
            </div>
            
            <!-- Deleting Modal for IS Department -->
            <div id="delete-IS" class="modal">
                <div class="modal-content">
                    <span onclick="closeDelete('IS',this)" style="float:right; cursor:pointer;">&times;</span>
                    <h2>Delete Room in IS Department </h2>
                    <form method="post">
                    <label for="delete-room_num">Room Number:</label>
                    <input type="hidden" id="ddnumroom" name="ddnumroom">
                    <input type="number" id="delete-room_num" name="room_num" disabled><br>
                    <input type="hidden" name="department" value="IS">
                    <input type="submit" name="delete_room" value="Delete Room">
                    </form>
                </div>
            </div>
            <div id="modal-IS" class="modal">
                <div class="modal-content">
                    <span onclick="closeModal('IS')" style="float:right; cursor:pointer;">&times;</span>
                    <h2>Add Room for IS Department</h2>
                    <form method="post">
                    <label for="room_num">Room Number:</label>
                        <input type="number" id="room_num" name="room_num" required><br>
                        <label for="type">Room Type:</label><br/>
                            <input type="radio" id="Lecture" name="type" value="Lecture" required>
                            <label for="Lecture">Lecture</label><br>
                            <input type="radio" id="Lab" name="type" value="Lab" required>
                            <label for="Lab">Lab</label><br>
                            <input type="radio" id="Seminar" name="type" value="Seminar" required>
                            <label for="Seminar">Seminar</label><br>
                            <input type="radio" id="Meeting" name="type" value="Meeting" required>
                            <label for="Meeting">Meeting</label><br>
                            </radio>
                        <label for="capacity">Capacity:</label>
                        <input type="number" id="capacity" name="capacity" required><br>
                        <label for="equipment">Equipment:</label><br/>
                        <checkbox>
                            <input type="checkbox" id="equipment" name="equipment[]" value="Projector">Projector<br/>
                            <input type="checkbox" id="equipment" name="equipment[]" value="Whiteboard">Whiteboard<br/>
                            <input type="checkbox" id="equipment" name="equipment[]" value="Computers">Computers<br/>
                        </checkbox>   
                        <label for="available_from">Available From  </label>
                        <input type="time" name="available_from" value="08:00" requierd>
                        <label for="available_to">Available To  </label>
                        <input type="time" name="available_to" value="18:00" requierd>                               
                        <input type="hidden" name="department" value="IS">
                        <input type="submit" name="add_room" value="Add Room">
                    </form>
                </div>
            </div>

            <div class="room-category">
                <div class="category-header">
                    <h3 onclick="toggleTable('CS')">Computer Science Department</h3>
                    <button onclick="showAddForm('CS')">Add Room</button>
                </div>
                <table id="CS" class="room-table">
                    <thead>
                        <tr>
                            <th>Room ID</th>
                            <th>Room Type</th>
                            <th>Capacity</th>
                            <th>Equipment</th>
                            <th>Available From</th>
                            <th>Available To</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        get_table('CS');
                    ?>
                    </tbody>
                </table>
            </div>

            <!-- Editing Modal for CS Department -->
            <div id="edit-CS" class="modal">
                <div class="modal-content">
                    <span onclick="closeEdit('CS',this)" style="float:right; cursor:pointer;">&times;</span>
                    <h2>Edit Room in CS Department </h2>
                    <form method="post">
                        <label for="room_num">Room Number:</label>
                        <input type="number" id="eroom_num" name="room_num" disabled><br>
                        <input type="hidden" id="room_num" name="room_num">
                        <label for="type">Room Type:</label><br/>
                            <input type="radio" id="Lecture" name="type" value="Lecture" required>
                            <label for="Lecture">Lecture</label><br>
                            <input type="radio" id="Lab" name="type" value="Lab" required>
                            <label for="Lab">Lab</label><br>
                            <input type="radio" id="Seminar" name="type" value="Seminar" required>
                            <label for="Seminar">Seminar</label><br>
                            <input type="radio" id="Meeting" name="type" value="Meeting" required>
                            <label for="Meeting">Meeting</label><br>
                            </radio>
                        <label for="capacity">Capacity:</label>
                        <input type="number" id="capacity" name="capacity" required><br>
                        <label for="equipment">Equipment:</label><br/>
                        <checkbox>
                            <input type="checkbox" id="projector" name="equipment[]" value="Projector">Projector<br/>
                            <input type="checkbox" id="whiteboard" name="equipment[]" value="Whiteboard">Whiteboard<br/>
                            <input type="checkbox" id="computers" name="equipment[]" value="Computers">Computers<br/>
                        </checkbox>
                                               
                        <input type="hidden" name="department" value="CS">
                        <input type="submit" name="edit_room" value="Edit Room">
                    </form>
                </div>
            </div>

            <!-- Deleting Modal for CS Department -->
            <div id="delete-CS" class="modal">
                <div class="modal-content">
                    <span onclick="closeDelete('CS',this)" style="float:right; cursor:pointer;">&times;</span>
                    <h2>Delete Room in CS Department </h2>
                    <form method="post">
                    <label for="delete-room_num">Room Number:</label>
                    <input type="hidden" id="ddnumroom" name="ddnumroom">
                    <input type="number" id="delete-room_num" name="room_num" disabled><br>
                    <input type="hidden" name="department" value="CS">
                    <input type="submit" name="delete_room" value="Delete Room">
                    </form>
                </div>
            </div>

            <!-- Adding Modal for CS Department -->
            <div id="modal-CS" class="modal">
                <div class="modal-content">
                    <span onclick="closeModal('CS')" style="float:right; cursor:pointer;">&times;</span>
                    <h2>Add Room for CS Department</h2>
                    <form method="post">
                    <label for="room_num">Room Number:</label>
                        <input type="number" id="room_num" name="room_num" required><br>
                        <label for="type">Room Type:</label><br/>
                            <input type="radio" id="Lecture" name="type" value="Lecture" required>
                            <label for="Lecture">Lecture</label><br>
                            <input type="radio" id="Lab" name="type" value="Lab" required>
                            <label for="Lab">Lab</label><br>
                            <input type="radio" id="Seminar" name="type" value="Seminar" required>
                            <label for="Seminar">Seminar</label><br>
                            <input type="radio" id="Meeting" name="type" value="Meeting" required>
                            <label for="Meeting">Meeting</label><br>
                            </radio>
                        <label for="capacity">Capacity:</label>
                        <input type="number" id="capacity" name="capacity" required><br>
                        <label for="equipment">Equipment:</label><br/>
                        <checkbox>
                            <input type="checkbox" id="equipment" name="equipment[]" value="Projector">Projector<br/>
                            <input type="checkbox" id="equipment" name="equipment[]" value="Whiteboard">Whiteboard<br/>
                            <input type="checkbox" id="equipment" name="equipment[]" value="Computers">Computers<br/>
                        </checkbox>                                  
                        <input type="hidden" name="department" value="CS">
                        <input type="submit" name="add_room" value="Add Room">
                    </form>
                </div>
            </div>

            <div class="room-category">
                <div class="category-header">
                    <h3 onclick="toggleTable('CE')">Computer Engineering Department</h3>
                    <button onclick="showAddForm('CE')">Add Room</button>
                </div>
                <table id="CE" class="room-table">
                    <thead>
                        <tr>
                            <th>Room ID</th>
                            <th>Room Type</th>
                            <th>Capacity</th>
                            <th>Equipment</th>
                            <th>Available From</th>
                            <th>Available To</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        get_table('CE');
                    ?>
                    </tbody>
                </table>
            </div>

            <!-- Editing Modal for CE Department -->
            <div id="edit-CE" class="modal">
                <div class="modal-content">
                    <span onclick="closeEdit('CE',this)" style="float:right; cursor:pointer;">&times;</span>
                    <h2>Edit Room in CE Department </h2>
                    <form method="post">
                        <label for="room_num">Room Number:</label>
                        <input type="number" id="eroom_num" name="room_num" disabled><br>
                        <input type="hidden" id="room_num" name="room_num">
                        <label for="type">Room Type:</label><br/>
                            <input type="radio" id="Lecture" name="type" value="Lecture" required>
                            <label for="Lecture">Lecture</label><br>
                            <input type="radio" id="Lab" name="type" value="Lab" required>
                            <label for="Lab">Lab</label><br>
                            <input type="radio" id="Seminar" name="type" value="Seminar" required>
                            <label for="Seminar">Seminar</label><br>
                            <input type="radio" id="Meeting" name="type" value="Meeting" required>
                            <label for="Meeting">Meeting</label><br>
                            </radio>
                        <label for="capacity">Capacity:</label>
                        <input type="number" id="capacity" name="capacity" required><br>
                        <label for="equipment">Equipment:</label><br/>
                        <checkbox>
                            <input type="checkbox" id="projector" name="equipment[]" value="Projector">Projector<br/>
                            <input type="checkbox" id="whiteboard" name="equipment[]" value="Whiteboard">Whiteboard<br/>
                            <input type="checkbox" id="computers" name="equipment[]" value="Computers">Computers<br/>
                        </checkbox>                       
                        <input type="hidden" name="department" value="CE">
                        <input type="submit" name="edit_room" value="Edit Room">
                    </form>
                </div>
            </div>

            <!-- Deleting Modal for CE Department -->
            <div id="delete-CE" class="modal">
                <div class="modal-content">
                    <span onclick="closeDelete('CE',this)" style="float:right; cursor:pointer;">&times;</span>
                    <h2>Delete Room in Computer Engineering Department </h2>
                    <form method="post">
                    <label for="delete-room_num">Room Number:</label>
                    <input type="hidden" id="ddnumroom" name="ddnumroom">
                    <input type="number" id="delete-room_num" name="room_num" disabled><br>
                    <input type="hidden" name="department" value="CE">
                    <input type="submit" name="delete_room" value="Delete Room">
                    </form>
                </div>
            </div>

            <!-- Modal for CE Department -->
            <div id="modal-CE" class="modal">
                <div class="modal-content">
                    <span onclick="closeModal('CE')" style="float:right; cursor:pointer;">&times;</span>
                    <h2>Add Room for Computer Engineering Department</h2>
                    <form method="post">
                    <label for="room_num">Room Number:</label>
                        <input type="number" id="room_num" name="room_num" required><br>
                        <label for="type">Room Type:</label><br/>
                            <input type="radio" id="Lecture" name="type" value="Lecture" required>
                            <label for="Lecture">Lecture</label><br>
                            <input type="radio" id="Lab" name="type" value="Lab" required>
                            <label for="Lab">Lab</label><br>
                            <input type="radio" id="Seminar" name="type" value="Seminar" required>
                            <label for="Seminar">Seminar</label><br>
                            <input type="radio" id="Meeting" name="type" value="Meeting" required>
                            <label for="Meeting">Meeting</label><br>
                            </radio>
                        <label for="capacity">Capacity:</label>
                        <input type="number" id="capacity" name="capacity" required><br>
                        <label for="equipment">Equipment:</label><br/>
                        <checkbox>
                            <input type="checkbox" id="equipment" name="equipment[]" value="Projector">Projector<br/>
                            <input type="checkbox" id="equipment" name="equipment[]" value="Whiteboard">Whiteboard<br/>
                            <input type="checkbox" id="equipment" name="equipment[]" value="Computers">Computers<br/>
                        </checkbox>                                
                        <input type="hidden" name="department" value="CE">
                        <input type="submit" name="add_room" value="Add Room">
                    </form>
                </div>
            </div>
        </section>
        <section id="schedule">
            <h2>Schedule Management</h2>
            <div id='calendar'></div>
        </section>
    </main>
    <script src="adminscripts.js"></script>
</body>
</html>
