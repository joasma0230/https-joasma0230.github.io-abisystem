<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include 'db_connection.php';

$user_id = $_SESSION['user_id'];
$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];

// Handle profile picture upload
$profile_picture = $_FILES['profile_picture']['name'];
$target_dir = "uploads/profile_pictures/";
$target_file = $target_dir . basename($profile_picture);

if ($profile_picture) {
    // Check if the file is a valid image
    $check = getimagesize($_FILES['profile_picture']['tmp_name']);
    if ($check === false) {
        echo "File is not an image.";
        exit();
    }
    
    // Move the uploaded file to the target directory
    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
        echo "The file " . htmlspecialchars(basename($profile_picture)) . " has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
        exit();
    }
}

// If password is provided, hash it
if (!empty($password)) {
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    $query = "UPDATE users SET username = ?, email = ?, password = ?, profile_picture = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssi", $username, $email, $hashed_password, $target_file, $user_id);
} else {
    // Update only username, email, and profile picture if no password
    $query = "UPDATE users SET username = ?, email = ?, profile_picture = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssi", $username, $email, $target_file, $user_id);
}

$stmt->execute();

// Redirect back to profile page after saving changes
header('Location: profile.php?update=success');
exit();
