<?php
// Configuration
$db_host = '127.0.0.1';
$db_username = 'your_username';
$db_password = 'your_password';
$db_name = 'my_db';

// Connect to the database using PDO
$dsn = "mysql:host=$db_host;dbname=$db_name";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
    $pdo = new PDO($dsn, $db_username, $db_password, $options);
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}

// Function to authenticate user
function authenticate_user($email, $password) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM user WHERE email = :email AND pass = :password');
    $stmt->execute(['email' => $email, 'password' => $password]);
    return $stmt->fetch();
}

// Function to check if user is admin
function is_admin($email) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT role FROM user WHERE email = :email');
    $stmt->execute(['email' => $email]);
    $row = $stmt->fetch();
    return $row['role'] == 'admin';
}

// Handle login form submission
if (isset($_POST['login'])) {
    $email = $_POST['login-email'];
    $password = $_POST['login-password'];
    $user = authenticate_user($email, $password);
    if ($user) {
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
        $stmt = $pdo->prepare('INSERT INTO user (email, pass) VALUES (:email, :password)');
        $stmt->execute(['email' => $email, 'password' => $password]);
        // Redirect to login page
        header('Location: login.php');
        exit;
    } else {
        // Display error message
        echo 'Passwords do not match';
    }
}

// Close database connection
$pdo = null;
?>
