<?php
require 'vendor/autoload.php'; // Load PhpSpreadsheet library

use PhpOffice\PhpSpreadsheet\IOFactory;

$conn = new mysqli('localhost', 'root', '', 'users'); // Replace with your database credentials
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed.']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel-file'])) {
    $file = $_FILES['excel-file']['tmp_name'];

    try {
        // Load the Excel file
        $spreadsheet = IOFactory::load($file);
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        $updatedData = [];
        foreach ($sheetData as $index => $row) {
            // Skip the header row (index 1)
            if ($index === 1) continue;

            $name = $row['A'];
            $position = $row['B'];
            $contact = $row['C'];

            // Check if this record already exists (you can use the contact or any unique field to check)
            $stmt = $conn->prepare("SELECT id FROM manpower WHERE contact = ?");
            $stmt->bind_param("s", $contact);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // If the record exists, update it
                $stmt = $conn->prepare("UPDATE manpower SET name = ?, position = ? WHERE contact = ?");
                $stmt->bind_param("sss", $name, $position, $contact);
            } else {
                // If the record doesn't exist, insert a new one
                $stmt = $conn->prepare("INSERT INTO manpower (name, position, contact) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $name, $position, $contact);
            }

            $stmt->execute();

            // Store updated data for the response
            $updatedData[] = [
                'id' => $stmt->insert_id ?: $result->fetch_assoc()['id'],
                'name' => $name,
                'position' => $position,
                'contact' => $contact,
            ];
        }

        // Send success response with the updated data
        echo json_encode(['success' => true, 'data' => $updatedData]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error processing the file: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No file uploaded.']);
}
?>
