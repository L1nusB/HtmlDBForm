<?php
require_once 'db_config.php';

class AssignmentOperations
{
    // Create new assignment
    public static function createAssignment($data)
    {
        try {
            $conn = Database::getConnection();

            if (!isset($data->combinations) || !is_array($data->combinations) || empty($data->combinations)) {
                throw new Exception("Missing or invalid combinations parameter.");
            }

            // Check for test mode
            if (isset($_GET['test']) && $_GET['test'] == 1) {
                $result = array(
                    "status" => "test",
                    "count" => count($data->combinations)
                );
            } else {
                $values = array();
                $params = array();

                foreach ($data->combinations as $combination) {
                    if (!isset($combination->fk_RPA_Bankenuebersicht) ||
                        !isset($combination->fk_RPA_Prozesse) ||
                        !isset($combination->fk_RPA_Standort) ||
                        !isset($combination->ProduktionsStart)) {
                        continue;
                    }

                    $values[] = "(?, ?, ?, ?)";
                    $params[] = $combination->fk_RPA_Bankenuebersicht;
                    $params[] = $combination->fk_RPA_Prozesse;
                    $params[] = $combination->fk_RPA_Standort;
                    $params[] = $combination->ProduktionsStart;
                }

                if (empty($values)) {
                    throw new Exception("No valid combinations provided.");
                }

                $sql = "INSERT INTO USEAP_RPA_Prozess_Zuweisung 
                        (fk_RPA_Bankenuebersicht, fk_RPA_Prozesse, fk_RPA_Standort, ProduktionsStart)
                        VALUES " . implode(", ", $values);

                $stmt = sqlsrv_query($conn, $sql, $params);

                if ($stmt === false) {
                    $errors = sqlsrv_errors();
                    $error_message = "Error inserting records: ";
                    foreach ($errors as $error) {
                        $error_message .= $error['message'] . " ";
                    }
                    throw new Exception($error_message);
                }

                $rowsAffected = sqlsrv_rows_affected($stmt);
                sqlsrv_free_stmt($stmt);

                $result = array(
                    "status" => "success",
                    "message" => "Records created successfully.",
                    "rowsAffected" => $rowsAffected
                );
            }

            return $result;

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
            if (!isset($data->combinations) || !is_array($data->combinations) || !count($data->combinations) > 0) {
                throw new Exception("Missing or invalid combinations parameter.");
            }
            $combinations = $data->combinations;

            // Build the IN clauses and parameters
            $institutePlaceholders = [];
            $locationPlaceholders = [];
            $params = [];

            foreach ($combinations as $combination) {
                if (isset($combination->fk_RPA_Bankenuebersicht) && isset($combination->fk_RPA_Standort)) {
                    $institutePlaceholders[] = '?';
                    $locationPlaceholders[] = '?';
                    $params[] = $combination->fk_RPA_Bankenuebersicht;
                    $params[] = $combination->fk_RPA_Standort;
                }
            }

            if (empty($institutePlaceholders)) {
                throw new Exception("No valid combinations provided.");
            }

            $instituteInClause = implode(',', $institutePlaceholders);
            $locationInClause = implode(',', $locationPlaceholders);

            // Check for the 'test' parameter
            if (isset($_GET['test']) && $_GET['test'] == 1) {
                // Use SELECT COUNT(*) to simulate the delete
                $countSql = "SELECT COUNT(*) AS count FROM USEAP_RPA_Prozess_Zuweisung WHERE (fk_RPA_Bankenuebersicht IN ($instituteInClause)) AND (fk_RPA_Standort IN ($locationInClause))";
                $countStmt = sqlsrv_query($conn, $countSql, $params);
                if ($countStmt === false) {
                    $errors = sqlsrv_errors();
                    $error_message = "Error getting count: ";
                    foreach ($errors as $error) {
                        $error_message .= $error['message'] . " ";
                    }
                    $result = array(
                        "status" => "error",
                        "message" => $error_message
                    );
                } else {
                    $row = sqlsrv_fetch_array($countStmt, SQLSRV_FETCH_ASSOC);
                    $count = $row['count'];
                    $result = array(
                        "status" => "test",
                        "count" => $count
                    );
                }
                sqlsrv_free_stmt($countStmt);
            } else {
                $sql = "DELETE FROM USEAP_RPA_Prozess_Zuweisung WHERE (fk_RPA_Bankenuebersicht IN ($instituteInClause)) AND (fk_RPA_Standort IN ($locationInClause))";
                // Execute the query as usual
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
            }
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
