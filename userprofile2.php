<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "my_db";

try {
    // Database connection 
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// fetch user data
function getUserData($conn, $userId) {
    try {
        $stmt = $conn->prepare("SELECT * FROM user WHERE id = :id");
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return null;
    }
}

// User Profile Page
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $userData = getUserData($conn, $userId);
} else {
    echo "You need to log in first,  to access your profile.";
    exit;
}

// Updating the  Profile
if (isset($_POST['update_profile'])) {
    $fname = $_POST['Fname'];
    $lname = $_POST['Lname'];
    $email = $_POST['email'];

    // Update user data
    try {
        $stmt = $conn->prepare("UPDATE user SET Fname = :fname, Lname = :lname, email = :email WHERE id = :id");
        $stmt->bindParam(':fname', $fname);
        $stmt->bindParam(':lname', $lname);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        echo "Profile updated successfully.";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Upload PPicture
if (isset($_POST['upload_picture'])) {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if it's an picture..

    $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
    if ($check !== false) {
        // Move uploaded file
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            try {
                $stmt = $conn->prepare("UPDATE user SET profil_pic = :profile_picture WHERE id = :id");
                $stmt->bindParam(':profile_picture', $target_file);
                $stmt->bindParam(':id', $userId);
                $stmt->execute();
                echo "Profile image uploaded successfully.";
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        } else {
            echo "Error uploading the picture ..";
        }
    } else {
        echo "File is not an image.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>

</head>
<body>
    <div class="profile-container">
        <h2>User Profile</h2>
        <img src="<?= $userData['profil_pic'] ?: 'default.png' ?>" alt="Profile Picture">
        <p><strong>First Name:</strong> <?= htmlspecialchars($userData['Fname']) ?></p>
        <p><strong>Last Name:</strong> <?= htmlspecialchars($userData['Lname']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($userData['email']) ?></p>
        <hr>

        <!-- Update Profile Form -->
        <form action="" method="post">
            <div class="form-group">
                <label for="Fname">First Name:</label>
                <input type="text" id="Fname" name="Fname" value="<?= htmlspecialchars($userData['Fname']) ?>" required>
            </div>
            <div class="form-group">
                <label for="Lname">Last Name:</label>
                <input type="text" id="Lname" name="Lname" value="<?= htmlspecialchars($userData['Lname']) ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($userData['email']) ?>" required>
            </div>
            <button type="submit" name="update_profile">Update Profile</button>
        </form>
        <hr>

        <!-- Upload Profile Picture Form -->
        <form action="" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="profile_picture">Upload Profile Picture:</label>
                <input type="file" id="profile_picture" name="profile_picture" required>
            </div>
            <button type="submit" name="upload_picture">Upload Picture</button>
        </form>
    </div>
</body>
</html>