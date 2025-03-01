<?php
require_once 'db_config.php';

class AssignmentOperations
{
    // Create new assignment
    public static function createAssignment($data, $keepOpen = false)
    {
        try {
            $conn = Database::getConnection();

            if (!isset($data->combinations) || !is_array($data->combinations) || empty($data->combinations)) {
                throw new Exception("Missing or invalid combinations parameter.");
            }

            // Check for test mode
            if (isset($_GET['test']) && $_GET['test'] == 1) {
                $result = array(
                    "success" => true,
                    "status" => "test",
                    "message" => "Insert test successful.",
                    "rowsAffected" => count($data->combinations)
                );
            } else {
                $values = array();
                $params = array();

                foreach ($data->combinations as $combination) {
                    if (
                        !isset($combination->fk_RPA_Bankenuebersicht) ||
                        !isset($combination->fk_RPA_Prozesse) ||
                        !isset($combination->fk_RPA_Standort) ||
                        !isset($combination->ProduktionsStart)
                    ) {
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
                        self::updateAssignment($combination, true);
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

                $allProcessed = $rowsAffected === count($data->combinations);
                if (!$allProcessed) {
                    $message = "Not all records were inserted.";
                } else {
                    $message = "Records created/updated successfully.";
                }

                $result = array(
                    "success" => true,
                    "status" => $allProcessed ? "success" : "warning",
                    "message" => $message,
                    "rowsAffected" => $rowsAffected
                );
            }

            return $result;
        } catch (Exception $e) {
            throw $e;
        } finally {
            if (!$keepOpen) {
                // Close the connection
                Database::closeConnection();
            }
        }
    }

    // Update multiple assignments by foreign key combinations
    public static function updateAssignments($data, $keepOpen = false)
    {
        try {
            // Connection is a singleton, so we don't need to close it here
            // and it causes no overhead to call getConnection() multiple times
            $conn = Database::getConnection();

            if (!isset($data->combinations) || !is_array($data->combinations) || empty($data->combinations)) {
                throw new Exception("Missing or invalid combinations parameter.");
            }

            // Check for test mode
            if (isset($_GET['test']) && $_GET['test'] == 1) {
                return array(
                    "success" => true,
                    "status" => "test",
                    "message" => "Update test successful.",
                    "rowsAffected" => count($data->combinations)
                );
            }

            $rowsAffected = 0;

            foreach ($data->combinations as $combination) {
                if (
                    !isset($combination->fk_RPA_Bankenuebersicht) ||
                    !isset($combination->fk_RPA_Prozesse) ||
                    !isset($combination->fk_RPA_Standort) ||
                    !isset($combination->ProduktionsStart)
                ) {
                    continue;
                }

                $result = self::updateAssignment($combination, true);
                if ($result['status'] == 'success') {
                    $rowsAffected += $result['rowsAffected'];
                }
            }

            $allProcessed = $rowsAffected === count($data->combinations);
            if (!$allProcessed) {
                $message = "Not all records were updated.";
            } else {
                $message = "Records updated successfully.";
            }

            return array(
                "success" => true,
                "status" => $allProcessed ? "success" : "warning",
                "message" => $message,
                "rowsAffected" => $rowsAffected
            );
        } catch (Exception $e) {
            throw $e;
        } finally {
            if (!$keepOpen) {
                // Close the connection
                Database::closeConnection();
            }
        }
    }

    // Update multiple assignments by ID
    public static function updateAssignmentsById($data, $keepOpen = false)
    {
        try {
            // Connection is a singleton, so we don't need to close it here
            // and it causes no overhead to call getConnection() multiple times
            $conn = Database::getConnection();

            if (!is_array($data) || empty($data)) {
                throw new Exception("Missing or invalid data parameter.");
            }

            // Check for test mode
            if (isset($_GET['test']) && $_GET['test'] == 1) {
                return array(
                    "success" => true,
                    "status" => "test",
                    "message" => "Update test successful.",
                    "rowsAffected" => count($data)
                );
            }

            $rowsAffected = 0;

            foreach ($data as $entry) {
                if (
                    !isset($entry['id']) ||
                    !isset($entry['startDate'])
                ) {
                    continue;
                }

                $result = self::updateAssignmentById($entry['id'], $entry['startDate'], $keepOpen);
                if ($result['status'] == 'success') {
                    $rowsAffected += $result['rowsAffected'];
                }
            }

            $allProcessed = $rowsAffected === count($data);
            if (!$allProcessed) {
                $message = "Not all records were updated.";
            } else {
                $message = "Multiple Records updated successful by Id.";
            }

            return array(
                "success" => true,
                "status" => $allProcessed ? "success" : "warning",
                "message" => $message,
                "rowsAffected" => $rowsAffected
            );
        } catch (Exception $e) {
            throw $e;
        } finally {
            if (!$keepOpen) {
                // Close the connection
                Database::closeConnection();
            }
        }
    }

    // Update existing assignment
    public static function updateAssignment($combination, $keepOpen = false)
    {
        try {
            $conn = Database::getConnection();

            if (
                !isset($combination->fk_RPA_Bankenuebersicht) ||
                !isset($combination->fk_RPA_Prozesse) ||
                !isset($combination->fk_RPA_Standort) ||
                !isset($combination->ProduktionsStart)
            ) {
                throw new Exception("Missing or invalid combination parameter.");
            }

            // Check for test mode
            if (isset($_GET['test']) && $_GET['test'] == 1) {
                return array(
                    "success" => true,
                    "status" => "test",
                    "message" => "Update test successful.",
                    "rowsAffected" => 1
                );
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
                "success" => true,
                "status" => "success",
                "message" => "Record updated successfully.",
                "rowsAffected" => $rowsAffected
            );
        } catch (Exception $e) {
            throw $e;
        } finally {
            if (!$keepOpen) {
                // Close the connection
                Database::closeConnection();
            }
        }
    }

    // Update existing assignment by ID
    public static function updateAssignmentById($ids, $startDate, $keepOpen = false)
    {
        try {
            $conn = Database::getConnection();

            // Convert single ID to array if necessary
            if (!is_array($ids)) {
                $ids = [$ids];
            }

            if (empty($ids)) {
                throw new Exception("Missing or invalid IDs parameter.");
            }

            // Check for test mode
            if (isset($_GET['test']) && $_GET['test'] == 1) {
                return array(
                    "success" => true,
                    "status" => "test",
                    "message" => "Test update single ID successful.",
                    "rowsAffected" => count($ids),
                );
            }

            $idPlaceholders = implode(',', array_fill(0, count($ids), '?'));
            $params = array_merge([$startDate], $ids);

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

            $allProcessed = $rowsAffected === count($ids);
            if (!$allProcessed) {
                $message = "Not all records were updated.";
            } else {
                $message = "Records updated successfully.";
            }

            return array(
                "success" => true,
                "status" => $allProcessed ? "success" : "warning",
                "message" => $message,
                "rowsAffected" => $rowsAffected
            );
        } catch (Exception $e) {
            throw $e;
        } finally {
            if (!$keepOpen) {
                // Close the connection
                Database::closeConnection();
            }
        }
    }

    // Delete process
    public static function deleteAssignment($data, $keepOpen = false)
    {
        try {
            $conn = Database::getConnection();
            // Implementation for deleting assignment(s)

            // Validate required fields
            if (!isset($data->combinations) || !is_array($data->combinations) || !count($data->combinations) > 0) {
                throw new Exception("Missing or invalid combinations parameter.");
            }
            $combinations = $data->combinations;

            $whereClauses = [];

            foreach ($combinations as $combination) {
                if (isset($combination->fk_RPA_Bankenuebersicht) && isset($combination->fk_RPA_Standort)) {
                    $whereClauses[] = "(fk_RPA_Bankenuebersicht = ? AND fk_RPA_Standort = ?)";
                    $params[] = $combination->fk_RPA_Bankenuebersicht;
                    $params[] = $combination->fk_RPA_Standort;
                }
            }

            if (empty($whereClauses)) {
                throw new Exception("No valid combinations provided.");
            }

            $whereClause = implode(' OR ', $whereClauses);

            // Check for the 'test' parameter
            if (isset($_GET['test']) && $_GET['test'] == 1) {
                // Use SELECT COUNT(*) to simulate the delete
                $countSql = "SELECT COUNT(*) AS count FROM USEAP_RPA_Prozess_Zuweisung WHERE $whereClause";
                $countStmt = sqlsrv_query($conn, $countSql, $params);
                if ($countStmt === false) {
                    $errors = sqlsrv_errors();
                    $error_message = "Error getting count: ";
                    foreach ($errors as $error) {
                        $error_message .= $error['message'] . " ";
                    }
                    $result = array(
                        "success" => false,
                        "status" => "error",
                        "message" => $error_message
                    );
                } else {
                    $row = sqlsrv_fetch_array($countStmt, SQLSRV_FETCH_ASSOC);
                    $count = $row['count'];
                    $result = array(
                        "success" => true,
                        "status" => "test",
                        "message" => "Test deletion successful.",
                        "rowsAffected" => $count,
                    );
                }
                sqlsrv_free_stmt($countStmt);
            } else {
                $sql = "DELETE FROM USEAP_RPA_Prozess_Zuweisung WHERE $whereClause";
                // Execute the query as usual
                $stmt = sqlsrv_query($conn, $sql, $params);
                if ($stmt === false) {
                    $errors = sqlsrv_errors();
                    $error_message = "Error deleting record: ";
                    foreach ($errors as $error) {
                        $error_message .= $error['message'] . " ";
                    }
                    $result =  array(
                        "success" => false,
                        "status" => "error",
                        "message" => $error_message
                    );
                } else {
                    $rowsAffected = sqlsrv_rows_affected($stmt);
                    $result = array(
                        "success" => true,
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
            if (!$keepOpen) {
                // Close the connection
                Database::closeConnection();
            }
        }
    }

    // Delete process by ID
    public static function deleteAssignmentById($ids, $keepOpen = false)
    {
        try {
            $conn = Database::getConnection();

            // Convert single ID to array if necessary
            if (!is_array($ids)) {
                $ids = [$ids];
            }

            // Validate input
            if (empty($ids)) {
                throw new Exception("Missing or invalid IDs parameter.");
            }

            $idPlaceholders = implode(',', array_fill(0, count($ids), '?'));
            $params = $ids;

            // Check for test mode
            if (isset($_GET['test']) && $_GET['test'] == 1) {
                // Use SELECT COUNT(*) to simulate the delete
                $countSql = "SELECT COUNT(*) AS count FROM USEAP_RPA_Prozess_Zuweisung WHERE pk_Prozess_Zuweisung IN ($idPlaceholders)";
                $countStmt = sqlsrv_query($conn, $countSql, $params);

                if ($countStmt === false) {
                    $errors = sqlsrv_errors();
                    $error_message = "Error getting count: ";
                    foreach ($errors as $error) {
                        $error_message .= $error['message'] . " ";
                    }
                    throw new Exception($error_message);
                }

                $row = sqlsrv_fetch_array($countStmt, SQLSRV_FETCH_ASSOC);
                sqlsrv_free_stmt($countStmt);

                return array(
                    "success" => true,
                    "status" => "test",
                    "message" => "Test deletion by ID successful.",
                    "rowsAffected" => $row['count'],
                );
            }

            // Perform actual deletion
            $sql = "DELETE FROM USEAP_RPA_Prozess_Zuweisung WHERE pk_Prozess_Zuweisung IN ($idPlaceholders)";
            $stmt = sqlsrv_query($conn, $sql, $params);

            if ($stmt === false) {
                $errors = sqlsrv_errors();
                $error_message = "Error deleting records: ";
                foreach ($errors as $error) {
                    $error_message .= $error['message'] . " ";
                }
                throw new Exception($error_message);
            }

            $rowsAffected = sqlsrv_rows_affected($stmt);
            sqlsrv_free_stmt($stmt);

            return array(
                "success" => true,
                "status" => "success",
                "message" => "Records deleted successfully.",
                "rowsAffected" => $rowsAffected
            );
        } catch (Exception $e) {
            throw $e;
        } finally {
            if (!$keepOpen) {
                // Close the connection
                Database::closeConnection();
            }
        }
    }
}
