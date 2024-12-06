<?php
// Configuration
$db_host = '127.0.0.1';
$db_username = 'your_username';
$db_password = 'your_password';
$db_name = 'my_db';

// Connect to the database
$conn = new mysqli($db_host, $db_username, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to authenticate user
function authenticate_user($email, $password) {
    global $conn;
    $query = "SELECT * FROM user WHERE email = '$email' AND pass = '$password'";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        return true;
    } else {
        return false;
    }
}

// Function to check if user is admin
function is_admin($email) {
    global $conn;
    $query = "SELECT role FROM user WHERE email = '$email'";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['role'] == 'admin') {
            return true;
        }
    }
    return false;
}

// Handle login form submission
if (isset($_POST['login'])) {
    $email = $_POST['login-email'];
    $password = $_POST['login-password'];
    if (authenticate_user($email, $password)) {
        if (is_admin($email)) {
            // Redirect to admin dashboard
            header('Location: admin_dashboard.php');
            exit;
        } else {
            // Redirect to user dashboard
            header('Location: user_dashboard.php');
            exit;
        }
    } else {
        // Display error message
        echo 'Invalid email or password';
    }
}

// Handle registration form submission
if (isset($_POST['signup'])) {
    $email = $_POST['signup-email'];
    $password = $_POST['signup-password'];
    $confirm_password = $_POST['signup-password-confirm'];
    if ($password == $confirm_password) {
        // Insert new user into database
        $query = "INSERT INTO user (email, pass) VALUES ('$email', '$password')";
        if ($conn->query($query) === TRUE) {
            // Redirect to login page
            header('Location: login.php');
            exit;
        } else {
            // Display error message
            echo 'Error creating user';
        }
    } else {
        // Display error message
        echo 'Passwords do not match';
    }
}

// Close database connection
$conn->close();
?>
