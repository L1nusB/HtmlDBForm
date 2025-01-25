<?php
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../assignment_operations.php';

try {
    // Check if it's a POST request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        // If the request method is not POST, return an error
        http_response_code(405); // Set HTTP status code to 405 Method Not Allowed
        echo json_encode(array(
            "status" => "error",
            "message" => "Method not allowed. Only POST requests are accepted."
        ));
    }

    $json = file_get_contents('php://input');
    $data = json_decode($json);

    if ($data === null) {
        throw new Exception(message: 'Invalid JSON data provided');
    }

    $inserts = [];
    $updates = [];
    $deletions = [];

    if (isset($data->updateSummary)) {
        if (isset($data->updateSummary->insert)) {
            // Insert requires objects not associative arrays
            // Alternatively one could use stdClass directly
            $inserts = (object)['combinations' => $data->updateSummary->insert];
        }
        if (isset($data->updateSummary->update)) {
            $updates = array_map(function ($item) {
                return [
                    'pk_Prozess_Zuweisung' => $item->pk_Prozess_Zuweisung,
                    'startDate' => $item->startDate
                ];
            }, $data->updateSummary->update);
        }
        if (isset($data->updateSummary->delete)) {
            $deletions = array_map(function ($item) {
                return $item->pk_Prozess_Zuweisung;
            }, $data->updateSummary->delete);
        }
    } else {
        if (isset($data->insert)) {
            // Insert requires objects not associative arrays
            // Alternatively one could use stdClass directly
            $inserts = (object)['combinations' => $data->insert];
        }
        if (isset($data->update)) {
            $updates = array_map(function ($item) {
                return [
                    'id' => $item->pk_Prozess_Zuweisung,
                    'startDate' => $item->startDate
                ];
            }, $data->update);
        }
        if (isset($data->delete)) {
            $deletions = array_map(function ($item) {
                return $item->pk_Prozess_Zuweisung;
            }, $data->delete);
        }
    }

    // If there are no inserts, updates or deletions interpret the data as update entries
    if (empty($inserts) && empty($updates) && empty($deletions)) {
        $updates = $data;
    }

    $finalResult = array('inserts' => 0, 'updates' => 0, 'deletions' => 0);
    // Handle insertions
    if (!empty($inserts)) {
        $result = AssignmentOperations::createAssignment($inserts);
        if (!$result['success']) {
            throw new Exception($result['message']);
        }
        $finalResult['inserts'] = $result['rowsAffected'];
    }
    // Handle updates
    if (!empty($updates)) {
        $result = AssignmentOperations::updateAssignmentsById($updates);
        if (!$result['success']) {
            throw new Exception($result['message']);
        }
        $finalResult['updates'] = $result['rowsAffected'];
    }
    // Handle deletions
    if (!empty($deletions)) {
        $result = AssignmentOperations::deleteAssignmentById($deletions);
        if (!$result['success']) {
            throw new Exception($result['message']);
        }
        $finalResult['deletions'] = $result['rowsAffected'];
    }

    $finalResult['success'] = true;
    $finalResult['status'] = (isset($_GET['test']) && $_GET['test'] == 1) ? 'test' : 'success';
    $finalResult['total'] = $finalResult['inserts'] + $finalResult['updates'] + $finalResult['deletions'];

    // Return success response
    echo json_encode($finalResult);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'success' => false,
        'message' => $e->getMessage(),
        'error' => $e->getMessage()
    ]);
}
