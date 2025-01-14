<?php
require_once 'db_config.php';

class ProcessOperations {
    // Create new process
    public static function createProcess($data) {
        try {
            $conn = Database::getConnection();
            // Implementation for creating new process
            // Return success/failure
        } catch (Exception $e) {
            throw $e;
        }
    }

    // Update existing process
    public static function updateProcess($id, $data) {
        try {
            $conn = Database::getConnection();
            // Implementation for updating process
            // Return success/failure
        } catch (Exception $e) {
            throw $e;
        }
    }

    // Delete process
    public static function deleteProcess($id) {
        try {
            $conn = Database::getConnection();
            // Implementation for deleting process
            // Return success/failure
        } catch (Exception $e) {
            throw $e;
        }
    }

    // Get single process
    public static function getProcess($id) {
        try {
            $conn = Database::getConnection();
            // Implementation for getting single process
            // Return process data
        } catch (Exception $e) {
            throw $e;
        }
    }
}
?>