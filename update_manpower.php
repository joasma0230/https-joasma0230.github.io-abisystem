<?php
session_start();
include('db_connection.php'); // Include your database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
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
    $certificate = $_FILES['certificate']['name']; // File upload handling

    // Move the uploaded certificate file to a directory if it's new
    if ($_FILES['certificate']['name']) {
        move_uploaded_file($_FILES['certificate']['tmp_name'], "uploads/" . $certificate);
    } else {
        $certificate = $_POST['old_certificate']; // Keep the old certificate if not uploading a new one
    }

    // Validate inputs
    if (empty($name) || empty($division) || empty($group) || empty($position) || empty($date_started)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: dashboard.php");
        exit();
    }

    // Update query
    $query = "UPDATE manpower SET name = ?, division = ?, group_department_branch_team = ?, position = ?, date_started = ?, training_title = ?, training_date = ?, training_venue = ?, training_provider = ?, training_type = ?, grade_rating = ?, remarks = ?, certificate = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssssssssssi", $name, $division, $group, $position, $date_started, $training_title, $training_date, $training_venue, $training_provider, $training_type, $grade_rating, $remarks, $certificate, $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Manpower updated successfully!";
    } else {
        $_SESSION['error'] = "Failed to update manpower.";
    }

    $stmt->close();
    $conn->close();

    header("Location: dashboard.php");
    exit();
}
