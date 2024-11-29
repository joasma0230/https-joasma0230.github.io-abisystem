<?php 
session_start();
include('db_connection.php'); // Include your database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capture form data
    $name = $_POST['name'];
    $division = $_POST['division'];
    $group = $_POST['group_department_branch_team'];
    $position = $_POST['position'];
    $date_started = $_POST['date_started'];
    $training_title = $_POST['training_title'];
    $training_date = $_POST['training_date'];
    $training_venue = $_POST['training_venue'];
    $training_provider = $_POST['training_provider'];
    $training_type = $_POST['training_type'];
    $grade_rating = $_POST['grade_rating'];
    $remarks = $_POST['remarks'];

    // Validate inputs
    if (empty($name) || empty($division) || empty($group) || empty($position) || empty($date_started)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: dashboard.php");
        exit();
    }

    // Insert query without the certificate field
    $query = "INSERT INTO manpower (name, division, group_department_branch_team, position, date_started, training_title, training_date, training_venue, training_provider, training_type, grade_rating, remarks) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssssssssss", $name, $division, $group, $position, $date_started, $training_title, $training_date, $training_venue, $training_provider, $training_type, $grade_rating, $remarks);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Manpower added successfully!";
    } else {
        $_SESSION['error'] = "Failed to add manpower.";
    }

    $stmt->close();
    $conn->close();

    header("Location: dashboard.php");
    exit();
}
