<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $sql = "INSERT INTO YourTable (column1, column2, column3) VALUES (?, ?, ?)";
    $params = array($data['column1'], $data['column2'], $data['column3']);
    
    $stmt = sqlsrv_query($conn, $sql, $params);
    
    if ($stmt === false) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to create record']);
    } else {
        echo json_encode(['success' => true]);
    }
}
?>