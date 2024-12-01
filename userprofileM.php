<?php
// enable user to edit its profile after log in and got access 
// Check if user is logged in
if (!isset($_SESSION['username'])) {
    die("You are not logged in.");
}

//  logged-in user's ID
$user_id = $_SESSION['user_id']; 

// Fetch user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];

    // user img upload
    if ($_FILES['profile_picture']['name']) {
        $targetDir = "uploads/";
        $targetFile = $targetDir . basename($_FILES["profile_picture"]["name"]);
        
        // delete  uploaded file
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $targetFile)) {
            // Update user data with profile picture
            $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, profile_picture = ? WHERE id = ?");
            $stmt->bind_param("sssi", $username, $email, $targetFile, $user_id);
        } else {
            echo "Error uploading file.";
        }
    } else {
        // Update user data 
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $username, $email, $user_id);
    }

    if ($stmt->execute()) {
        header('Location: profile.php'); // Redirect the user to the profile page after updating the requireds
        exit();
    } else {
        echo "Error updating profile: " . $stmt->error;
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Edit Profile</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required><br><br>
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required><br><br>
        
        <label for="profile_picture">Profile Picture:</label>
        <input type="file" id="profile_picture" name="profile_picture"><br><br>
        
        <button type="submit">Update Profile</button>
    </form>

    <?php if ($user['profile_picture']): ?>
        <h3>Current Profile Picture:</h3>
        <img src="<?= htmlspecialchars($user['profile_picture']) ?>" alt="Profile Picture" style="width:100px;height:auto;">
    <?php endif; ?>
</body>
</html>