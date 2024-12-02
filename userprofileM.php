<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'] ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $user['password'];
    $profile_picture = $user['profile_picture'];

    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'upload/';
        $filename = uniqid() . '-' . $_FILES['profile_picture']['name'];
        $filepath = $upload_dir . $filename;

        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $filepath)) {
            $profile_picture = $filename;
        }
    }

    $stmt = $pdo->prepare("UPDATE users SET username = :username, password = :password, profile_picture = :profile_picture WHERE id = :id");
    $stmt->execute([
        'username' => $username,
        'password' => $password,
        'profile_picture' => $profile_picture,
        'id' => $user_id
    ]);

    header("Location: profile.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile</title>
</head>
<body>
    <h1>Edit your Profile</h1>
    <form method="POST" enctype="multipart/form-data">
        <label>New Username: <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required></label><br>
        <label>New Password  <input type="password" name="password"></label><br>
        <?php if ($user['profile_picture']): ?>
            <img src="upload/<?= htmlspecialchars($user['profile_picture']) ?>" alt="Profile Picture" width="100"><br>
        <?php endif; ?>
        <label> Profile Picture: <input type="file" name="profile_picture"></label><br>
        <button type="submit">Save Changes</button>
    </form>
    <a href="logout.php">Logout</a>
</body>
</html>