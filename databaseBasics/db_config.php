<?php
class Database {
    private static $conn = null;
    private static $serverName = "DESKTOP-38K7GFG";
    private static $connectionInfo = array(
        "Database" => "Serviscope",
        "TrustServerCertificate" => true,
        "CharacterSet" => "UTF-8"
    );

    // Get database connection
    public static function getConnection() {
        if (self::$conn === null) {
            try {
                self::$conn = sqlsrv_connect(self::$serverName, self::$connectionInfo);
                
                if (!self::$conn) {
                    throw new Exception("Database connection failed: " . print_r(sqlsrv_errors(), true));
                }
            } catch (Exception $e) {
                // Log error or handle it as needed
                throw $e;
            }
        }
        return self::$conn;
    }

    // Close connection if it exists
    public static function closeConnection() {
        if (self::$conn !== null) {
            sqlsrv_close(self::$conn);
            self::$conn = null;
        }
    }
}
?>