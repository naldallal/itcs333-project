<?php
session_start();

$servername = "localhost";
$username = "your_username";
$password = "your_password";
$dbname = "your_database";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Register user
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    // Validate email
    if (substr($email, -12) !== '@edu.uob.bh') {
        echo "Invalid email address. Email must end with '@edu.uob.bh'.";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert data into database
        $sql = "INSERT INTO users (username, password, email) VALUES ('$username', '$hashed_password', '$email')";
        if ($conn->query($sql) === TRUE) {
            echo "Registration successful!";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Login user
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query database for user
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Get user data
        $row = $result->fetch_assoc();
        $hashed_password = $row['password'];

        // Check password
        if (password_verify($password, $hashed_password)) {
            // Login successful!
            $_SESSION['username'] = $username;
            echo "Login successful!";
        } else {
            echo "Invalid password";
        }
    } else {
        echo "Invalid username";
    }
}

// Logout user
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    echo "Logged out!";
}

// Check if user is logged in
if (isset($_SESSION['username'])) {
    echo "Welcome, " . $_SESSION['username'] . "!";
} else {
    echo "You are not logged in.";
}

$conn->close();
?>

<form action="" method="post">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required><br><br>
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required><br><br>
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required><br><br>
    <input type="submit" name="register" value="Register">
</form>

<form action="" method="post">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required><br><br>
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required><br><br>
    <input type="submit" name="login" value="Login">
</form>

<form action="" method="post">
    <input type="submit" name="logout" value="Logout">
</form>
