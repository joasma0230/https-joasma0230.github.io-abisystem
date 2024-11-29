<?php
require 'vendor/autoload.php'; // Make sure to include PhpSpreadsheet library

use PhpOffice\PhpSpreadsheet\IOFactory;

$response = ['success' => false, 'data' => []];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel-file']) && $_FILES['excel-file']['error'] == 0) {
    $file = $_FILES['excel-file']['tmp_name'];

    try {
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $data = [];

        foreach ($sheet->getRowIterator(2) as $row) { // Skipping header row
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $rowData = [];
            foreach ($cellIterator as $cell) {
                $rowData[] = $cell->getValue();
            }

            // Assuming the Excel file has columns for ID, Name, Position, Contact
            $data[] = [
                'id' => $rowData[0], 
                'name' => $rowData[1], 
                'position' => $rowData[2], 
                'contact' => $rowData[3]
            ];
        }

        // Return data to frontend
        $response['success'] = true;
        $response['data'] = $data;

    } catch (Exception $e) {
        $response['message'] = 'Error reading Excel file: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'No file uploaded or file error.';
}

echo json_encode($response);
?>
