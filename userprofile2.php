<?php
session_start();

$servername = "localhost";
$username = "your_username";
$password = "your_password";
$dbname = "your_database";

try {
    // database connection using PDO
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(" the Connection is failed: " . $e->getMessage());
}

// Registerd user
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    // email Validation
    if (substr($email, -12) !== '@edu.uob.bh') {
        echo "Invalid email address. Email must end with '@edu.uob.bh'.";
    } else {
       
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Inserting the  data into the database
        try {
            $stmt = $conn->prepare("INSERT INTO users (username, password, email) VALUES (:username, :password, :email)");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            echo "Registration successful!";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}

        // Login user
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

          // Query database for user
    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Login successful
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $user['id'];
            echo "Login is successful!";
        } else {
            echo "Invalid username or password.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

        // Logout user
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    echo "Logged out!";
}

      //  user profile editing / update 
if (isset($_POST['update_profile'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $user_id = $_SESSION['user_id'];

    // Update the user data
    try {
        $stmt = $conn->prepare("UPDATE users SET username = :username, email = :email WHERE id = :id");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':id', $user_id);
        $stmt->execute();
        echo "your Profile is updated successfully.";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

      // inserting the profile picture
if (isset($_POST['upload_picture'])) {
    $user_id = $_SESSION['user_id'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if its an image
    $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
    if ($check !== false) {
        // Move uploaded file
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            try {
                $stmt = $conn->prepare("UPDATE users SET profile_picture = :profile_picture WHERE id = :id");
                $stmt->bindParam(':profile_picture', $target_file);
                $stmt->bindParam(':id', $user_id);
                $stmt->execute();
                echo " your new Profile picture is uploaded successfully.";
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        } else {
            echo "Error uploading the file.";
        }
    } else {
        echo "File is not an image.";
    }
}

      // Check if user are logged in
if (isset($_SESSION['username'])) {
    echo "Welcome, " . $_SESSION['username'] . "!";
} else {
    echo "You are not logged in.";
}
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


<form action="" method="post">
    <label for="username">New Username:</label>
    <input type="text" id="username" name="username" required><br><br>
    <label for="email">New Email:</label>
    <input type="email" id="email" name="email" required><br><br>
    <input type="submit" name="update_profile" value="Update Profile">
</form>


<form action="" method="post" enctype="multipart/form-data">
    <label for="profile_picture">Upload Profile Picture:</label>
    <input type="file" id="profile_picture" name="profile_picture" required><br><br>
    <input type="submit" name="upload_picture" value="Upload Picture">
</form>