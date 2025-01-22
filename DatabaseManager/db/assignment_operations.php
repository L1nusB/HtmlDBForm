<?php
require_once 'db_config.php';

class AssignmentOperations
{
    // Create new assignment
    public static function createAssignment($data)
    {
        try {
            $conn = Database::getConnection();
            // Implementation for creating new assignment(s)
            // Return success/failure
            return array(
                'success' => true,
                'message' => 'Process created successfully',
                // 'id' => $row['NewId']
            );
        } catch (Exception $e) {
            throw $e;
        } finally {
            // Close the connection
            Database::closeConnection();
        }
    }

    // Update existing process
    public static function updateAssignment($id, $data)
    {
        try {
            $conn = Database::getConnection();
            // Implementation for updating assignment(s)
            // Return success/failure
            return array(
                'success' => true,
                'message' => 'Process created successfully',
                // 'id' => $row['NewId']
            );
        } catch (Exception $e) {
            throw $e;
        } finally {
            // Close the connection
            Database::closeConnection();
        }
    }

    // Delete process
    public static function deleteAssignment($data)
    {
        try {
            $conn = Database::getConnection();
            // Implementation for deleting assignment(s)

            // Validate required fields
            $requiredFields = ['fk_RPA_Bankenuebersicht', 'fk_RPA_Standort'];
            foreach ($requiredFields as $field) {
                if (!isset($data->$field) || empty($data->$field)) {
                    throw new Exception("Missing required field: $field");
                }
            }

            $fk_RPA_Bankenuebersicht = $data->fk_RPA_Bankenuebersicht;
            $fk_RPA_Standort = $data->fk_RPA_Standort;

            $sql = "DELETE FROM USEAP_RPA_Prozess_Zuweisung WHERE fk_RPA_Bankenuebersicht = ? AND fk_RPA_Standort = ?";
            $params = array($fk_RPA_Bankenuebersicht, $fk_RPA_Standort);

            $stmt = sqlsrv_query($conn, $sql, $params);

            if ($stmt === false) {
                $errors = sqlsrv_errors();
                $error_message = "Error deleting record: ";
                foreach ($errors as $error) {
                    $error_message .= $error['message'] . " ";
                }
                $result =  array(
                    "status" => "error",
                    "message" => $error_message
                );
            } else {
                $rowsAffected = sqlsrv_rows_affected($stmt);
                $result = array(
                    "status" => "success",
                    "message" => "Record(s) deleted successfully.",
                    "rowsAffected" => $rowsAffected
                );
            }
            sqlsrv_free_stmt($stmt);

            // Return success/failure
            return $result;
        } catch (Exception $e) {
            throw $e;
        } finally {
            // Close the connection
            Database::closeConnection();
        }
    }
}
