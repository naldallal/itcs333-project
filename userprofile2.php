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
    echo "You need to log in first to access your profile.";
    exit;
}

// Updating the Profile (Name, Email)
if (isset($_POST['update_profile'])) {
    $fname = $_POST['Fname'];
    $lname = $_POST['Lname'];
    $email = $_POST['email'];
    $userId = $_SESSION['user_id'];

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

// Upload Profile Picture
if (isset($_POST['upload_picture'])) {
    if (isset($_FILES["profile_picture"]) && $_FILES["profile_picture"]["error"] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if it's an image
        $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
        if ($check !== false) {
            // Check file size (optional - max size: 5MB)
            if ($_FILES["profile_picture"]["size"] <= 5000000) {
                // Allow certain file formats (optional)
                if ($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" || $imageFileType == "gif") {
                    // Move uploaded file to the target directory
                    if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                        // Update database with the new profile picture path
                        try {
                            $stmt = $conn->prepare("UPDATE user SET profil_pic = :profile_picture WHERE id = :id");
                            $stmt->bindParam(':profile_picture', $target_file);
                            $stmt->bindParam(':id', $userId);
                            $stmt->execute();
                            echo "Profile picture uploaded successfully.";
                        } catch (PDOException $e) {
                            echo "Error updating database: " . $e->getMessage();
                        }
                    } else {
                        echo "Sorry, there was an error uploading your file.";
                    }
                } else {
                    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                }
            } else {
                echo "Sorry, your file is too large.";
            }
        } else {
            echo "File is not an image.";
        }
    } else {
        echo "No file uploaded or there was an error with the upload.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>

    <!-- Custom Css -->
    <link rel="stylesheet" href="userprofile2.css">

    <!-- FontAwesome 5 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css">
    <style>
        .editable {
            display: none;
        }
        .visible {
            display: block;
        }
    </style>
</head>
<body>
    <!-- Navbar top -->
    <div class="navbar-top">
        <div class="title">
            <h1>Profile</h1>
        </div>
        <!-- Navbar -->
        <ul>
            <li>
                <a href="#message">
                    <span class="icon-count">29</span>
                    <i class="fa fa-envelope fa-2x"></i>
                </a>
            </li>
            <li>
                <a href="#notification">
                    <span class="icon-count">59</span>
                    <i class="fa fa-bell fa-2x"></i>
                </a>
            </li>
            <li>
                <a href="#sign-out">
                    <i class="fa fa-sign-out-alt fa-2x"></i>
                </a>
            </li>
        </ul>
    </div>
    <!-- End -->

    <!-- Sidenav -->
    <div class="sidenav">
        <div class="profile">
            <img id="profileImage" src="<?= htmlspecialchars($userData['profil_pic']) ?: 'https://static-00.iconduck.com/assets.00/avatar-default-icon-2048x2048-h6w375ur.png' ?>" alt="Profile Picture" width="100" height="100">
            <div class="name" id="profileName">
                <?= htmlspecialchars($userData['Fname']) . " " .  htmlspecialchars($userData['Lname']) ?>
            </div>
        </div>
        <div class="sidenav-url">
            <div class="url">
                <a href="#profile" class="active">Profile</a>
                <hr align="center">
            </div>
            <div class="url">
                <a href="booking_table.php">Booking</a>
                <hr align="center">
            </div>
        </div>
    </div>
    <!-- End -->

    <!-- Main -->
    <div class="main">
        <h2>IDENTITY</h2>
        <div class="card">
            <div class="card-body">
                <i class="fa fa-pen fa-xs edit" id="editButton" onclick="toggleEdit()"></i>
                <table>
                    <tbody>
                        <tr>
                            <td>Name</td>
                            <td>:</td>
                            <td id="displayName"><?= htmlspecialchars($userData['Fname']) . " " .  htmlspecialchars($userData['Lname']) ?></td>
                            <td class="editable">
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
                            </td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td>:</td>
                            <td><?= htmlspecialchars($userData['email']) ?></td>
                        </tr>
                        <tr>
                            <td>College</td>
                            <td>:</td>
                            <td>Information Technology</td>
                        </tr>
                    </tbody>
                </table>
                <div class="editable">
                    <label for="imageUpload">Change Profile Image:</label>
                    <input type="file" id="imageUpload" accept="image/*" onchange="previewImage(event)">
                </div>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="id" value="<?= $userId ?>">
                    <input type="hidden" name="action" value="pending">
                    <button type="submit" name="pending_role">Admin Request</button>
                </form>
            </div>
        </div>
    </div>
    <!-- End -->

    <script>
        function toggleEdit() {
            const editButton = document.getElementById('editButton');
            const displayName = document.getElementById('displayName');
            const editName = document.getElementById('editName');
            const editableFields = document.querySelectorAll('.editable');

            editableFields.forEach(field => {
                field.classList.toggle('visible');
            });

            if (editButton.classList.contains('fa-pen')) {
                editButton.classList.remove('fa-pen');
                editButton.classList.add('fa-save');
            } else {
                displayName.textContent = editName.value;
                editButton.classList.remove('fa-save');
                editButton.classList.add('fa-pen');
            }
        }

        function previewImage(event) {
            const profileImage = document.getElementById('profileImage');
            profileImage.src = URL.createObjectURL(event.target.files[0]);
        }
    </script>
</body>
</html>
