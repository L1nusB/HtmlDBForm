<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $_GET['id'];
    
    $sql = "UPDATE YourTable SET column1 = ?, column2 = ?, column3 = ? WHERE id = ?";
    $params = array($data['column1'], $data['column2'], $data['column3'], $id);
    
    $stmt = sqlsrv_query($conn, $sql, $params);
    
    if ($stmt === false) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update record']);
    } else {
        echo json_encode(['success' => true]);
    }
}
?>