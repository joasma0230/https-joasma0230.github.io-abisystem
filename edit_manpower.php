<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'users'); // Replace with your credentials
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve data from form
    $id = $_POST['id'];
    $name = $_POST['name'];
    $division = $_POST['division'];
    $group_department_branch_team = $_POST['group_department_branch_team'];
    $position = $_POST['position'];
    $date_started = $_POST['date_started'];
    $training_title = $_POST['training_title'];
    $training_date = $_POST['training_date'];
    $training_venue = $_POST['training_venue'];
    $training_provider = $_POST['training_provider'];
    $training_type = $_POST['training_type'];
    $grade_rating = $_POST['grade_rating'];
    $remarks = $_POST['remarks'];

    // Update query
    $query = "UPDATE manpower SET 
        name = ?, 
        division = ?, 
        group_department_branch_team = ?, 
        position = ?, 
        date_started = ?, 
        training_title = ?, 
        training_date = ?, 
        training_venue = ?, 
        training_provider = ?, 
        training_type = ?, 
        grade_rating = ?, 
        remarks = ? 
        WHERE id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        "ssssssssssssi", 
        $name, 
        $division, 
        $group_department_branch_team, 
        $position, 
        $date_started, 
        $training_title, 
        $training_date, 
        $training_venue, 
        $training_provider, 
        $training_type, 
        $grade_rating, 
        $remarks, 
        $id
    );

    if ($stmt->execute()) {
        header("Location: dashboard.php?msg=success");
    } else {
        header("Location: dashboard.php?msg=error");
    }

    $stmt->close();
}

$conn->close();
?>
