<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_GET['id'];
    
    $sql = "DELETE FROM YourTable WHERE id = ?";
    $params = array($id);
    
    $stmt = sqlsrv_query($conn, $sql, $params);
    
    if ($stmt === false) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to delete record']);
    } else {
        echo json_encode(['success' => true]);
    }
}
?>