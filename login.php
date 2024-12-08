<?php
// Start a new session
session_start();

// Configuration
$db_host = '127.0.0.1';
$db_username = 'root';
$db_password = '';
$db_name = 'my_db';

// Connect to the database using PDO
$dsn = "mysql:host=$db_host;dbname=$db_name";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $db = new PDO($dsn, $db_username, $db_password, $options); // Only one PDO connection
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

// Function to authenticate user
function authenticate_user($email, $password) {
    global $db;
    $stmt = $db->prepare('SELECT * FROM user WHERE email = :email');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['pass'])) {
        return $user; // Return the user data
    }
    return false; // Invalid credentials
}

// Function to check if user is admin
function is_admin($email) {
    global $db;
    $stmt = $db->prepare('SELECT role FROM user WHERE email = :email');
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
        // Start session and store user info
        $_SESSION['user_id'] = $user['id'];  // Store user ID in session
        $_SESSION['email'] = $user['email']; // Store email in session
        
        // Redirect to booking page with user ID
        header('Location: filter_page.php?id=' . $_SESSION['user_id']);
        exit;
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

    // Validate email and password
    if ($password == $confirm_password) {
        // Check if email already exists
        $stmt = $db->prepare('SELECT * FROM user WHERE email = :email');
        $stmt->execute(['email' => $email]);
        if ($stmt->fetch()) {
            echo 'Email already in use';
        } else {
            // Hash the password before storing it
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new user into database
            $stmt = $db->prepare('INSERT INTO user (email, pass) VALUES (:email, :password)');
            $stmt->execute(['email' => $email, 'password' => $hashed_password]);
            
            // Redirect to login page after successful registration
            header('Location: login.php');
            exit;
        }
    } else {
        // Display error message if passwords do not match
        echo 'Passwords do not match';
    }
}

// Close database connection
$db = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <title>Registration and Login</title>
</head>
<body>
    <section class="forms-section">
        <h1 class="section-title">Login / Registration</h1>
        <div class="forms">
            <!-- Login Form -->
            <div class="form-wrapper is-active">
                <button type="button" class="switcher switcher-login">
                    Login
                    <span class="underline"></span>
                </button>
                <form class="form form-login" method="POST">
                    <fieldset>
                        <legend>Please, enter your email and password for login.</legend>
                        <div class="input-block">
                            <label for="login-email">E-mail (e.g., example@uob.edu.bh)</label>
                            <input id="login-email" type="email" name="login-email" required>
                        </div>
                        <div class="input-block">
                            <label for="login-password">Password</label>
                            <input id="login-password" type="password" name="login-password" required>
                        </div>
                    </fieldset>
                    <button type="submit" name="login" class="btn-login">Login</button>
                    <div class="btn-desc">
                        
                    </div>
                </form>
            </div>

            <!-- Sign Up Form -->
            <div class="form-wrapper">
                <button type="button" class="switcher switcher-signup">
                    Sign Up
                    <span class="underline"></span>
                </button>
                <form class="form form-signup" method="POST">
                    <fieldset>
                        <legend>Please, enter your email, password and password confirmation for sign up.</legend>
                        <div class="input-block">
                            <label for="signup-email">E-mail (e.g., example@uob.edu.bh)</label>
                            <input id="signup-email" type="email" name="signup-email" required>
                            <p style="color: red;">Please use a valid University of Bahrain email address.</p>
                        </div>
                        <div class="input-block">
                            <label for="signup-password">Password</label>
                            <input id="signup-password" type="password" name="signup-password" required>
                        </div>
                        <div class="input-block">
                            <label for="signup-password-confirm">Confirm password</label>
                            <input id="signup-password-confirm" type="password" name="signup-password-confirm" required>
                        </div>
                    </fieldset>
                    <button type="submit" name="signup" class="btn-signup">Continue</button>
                </form>
            </div>
        </div>
    </section>

    <script>
        const switchers = [...document.querySelectorAll('.switcher')];

        switchers.forEach(item => {
            item.addEventListener('click', function() {
                switchers.forEach(item => item.parentElement.classList.remove('is-active'));
                this.parentElement.classList.add('is-active');
            });
        });
    </script>
</body>
</html>
