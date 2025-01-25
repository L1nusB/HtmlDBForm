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

                    // Check if the record already exists
                    $checkSql = "SELECT COUNT(*) AS count FROM USEAP_RPA_Prozess_Zuweisung 
                                 WHERE fk_RPA_Bankenuebersicht = ? AND fk_RPA_Prozesse = ? AND fk_RPA_Standort = ?";
                    $checkParams = array($combination->fk_RPA_Bankenuebersicht, $combination->fk_RPA_Prozesse, $combination->fk_RPA_Standort);
                    $checkStmt = sqlsrv_query($conn, $checkSql, $checkParams);

                    if ($checkStmt === false) {
                        $errors = sqlsrv_errors();
                        $error_message = "Error checking existing records: ";
                        foreach ($errors as $error) {
                            $error_message .= $error['message'] . " ";
                        }
                        throw new Exception($error_message);
                    }

                    $row = sqlsrv_fetch_array($checkStmt, SQLSRV_FETCH_ASSOC);
                    sqlsrv_free_stmt($checkStmt);

                    if ($row['count'] == 0) {
                        $values[] = "(?, ?, ?, ?)";
                        $params[] = $combination->fk_RPA_Bankenuebersicht;
                        $params[] = $combination->fk_RPA_Prozesse;
                        $params[] = $combination->fk_RPA_Standort;
                        $params[] = $combination->ProduktionsStart;
                    } else {
                        // Update existing record
                        self::updateAssignment($combination);
                    }
                }

                if (!empty($values)) {
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
                } else {
                    $rowsAffected = 0;
                }

                $result = array(
                    "status" => "success",
                    "message" => "Records created/updated successfully.",
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

    // Update existing assignment
    public static function updateAssignment($combination)
    {
        try {
            $conn = Database::getConnection();

            if (!isset($combination->fk_RPA_Bankenuebersicht) ||
                !isset($combination->fk_RPA_Prozesse) ||
                !isset($combination->fk_RPA_Standort) ||
                !isset($combination->ProduktionsStart)) {
                throw new Exception("Missing or invalid combination parameter.");
            }

            $sql = "UPDATE USEAP_RPA_Prozess_Zuweisung 
                    SET ProduktionsStart = ? 
                    WHERE fk_RPA_Bankenuebersicht = ? AND fk_RPA_Prozesse = ? AND fk_RPA_Standort = ?";
            $params = array($combination->ProduktionsStart, $combination->fk_RPA_Bankenuebersicht, $combination->fk_RPA_Prozesse, $combination->fk_RPA_Standort);

            $stmt = sqlsrv_query($conn, $sql, $params);

            if ($stmt === false) {
                $errors = sqlsrv_errors();
                $error_message = "Error updating record: ";
                foreach ($errors as $error) {
                    $error_message .= $error['message'] . " ";
                }
                throw new Exception($error_message);
            }

            $rowsAffected = sqlsrv_rows_affected($stmt);
            sqlsrv_free_stmt($stmt);

            return array(
                "status" => "success",
                "message" => "Record updated successfully.",
                "rowsAffected" => $rowsAffected
            );

        } catch (Exception $e) {
            throw $e;
        } finally {
            // Close the connection
            Database::closeConnection();
        }
    }

    // Update existing assignment by ID
    public static function updateAssignmentById($ids, $data)
    {
        try {
            $conn = Database::getConnection();

            if (!is_array($ids) || empty($ids)) {
                throw new Exception("Missing or invalid IDs parameter.");
            }

            if (!isset($data->ProduktionsStart)) {
                throw new Exception("Missing or invalid data parameter.");
            }

            $idPlaceholders = implode(',', array_fill(0, count($ids), '?'));
            $params = array_merge([$data->ProduktionsStart], $ids);

            $sql = "UPDATE USEAP_RPA_Prozess_Zuweisung 
                    SET ProduktionsStart = ? 
                    WHERE pk_Prozess_Zuweisung IN ($idPlaceholders)";

            $stmt = sqlsrv_query($conn, $sql, $params);

            if ($stmt === false) {
                $errors = sqlsrv_errors();
                $error_message = "Error updating records: ";
                foreach ($errors as $error) {
                    $error_message .= $error['message'] . " ";
                }
                throw new Exception($error_message);
            }

            $rowsAffected = sqlsrv_rows_affected($stmt);
            sqlsrv_free_stmt($stmt);

            return array(
                "status" => "success",
                "message" => "Records updated successfully.",
                "rowsAffected" => $rowsAffected
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
?>
