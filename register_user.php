<?php
$conn = new mysqli('localhost', 'root', '', 'users'); // Replace with your database credentials
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed.']));
}

// Handle user registration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $position = $_POST['position'];
    $contact = $_POST['contact'];

    $query = "INSERT INTO manpower (name, position, contact) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sss', $name, $position, $contact);

    if ($stmt->execute()) {
        $last_id = $conn->insert_id;
        echo json_encode([
            'success' => true,
            'data' => [
                'id' => $last_id,
                'name' => $name,
                'position' => $position,
                'contact' => $contact
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error registering user.']);
    }

    $stmt->close();
}

$conn->close();
?>
