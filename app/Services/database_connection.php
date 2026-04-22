<?php
namespace App\Services;
use mysqli;
use Exception;
class database_connection {
    private $connection = null;
    private static $instance;
    private static $host = "localhost";
    private static $port = "3306";
    private static $dbname = "laravel";
    private static $user = "root";
    private static $pass = "";

    private function __construct(){}

    public static function getInstance() {
        if(self::$instance == null) {
            self::$instance = new database_connection();
        }
        return self::$instance;
    }

    // public function getConnection(){
    //     try {
    //         if($this->connection == null || $this->connection->connect_error) {
    //             $this->connection = new mysqli(self::$host, self::$user, self::$pass, self::$dbname, self::$port);
    //         }
    //         return $this->connection;
    //     } catch (Exception $e) {
    //         error_log("Error connecting to database: " . $e->getMessage());
    //         throw $e;
    //     }
    //     return null;
    // }
    public function getConnection(){
            try {
                // Kiểm tra nếu chưa có kết nối hoặc kết nối đã chết (ping thất bại)
                if($this->connection == null || !$this->connection->ping()) {
                    $this->connection = new mysqli(self::$host, self::$user, self::$pass, self::$dbname, self::$port);
                    
                    if ($this->connection->connect_error) {
                        throw new Exception("Kết nối MySQL thất bại: " . $this->connection->connect_error);
                    }
                    // Thiết lập charset để tránh lỗi font tiếng Việt
                    $this->connection->set_charset("utf8mb4");
                }
                return $this->connection;
            } catch (Exception $e) {
                error_log("Database Connection Error: " . $e->getMessage());
                throw $e;
            }
        }
    // public static function getPreparedStatement($sql,...$args) {
    //     try {
    //         $preparedStatement = self::getInstance()->getConnection()->prepare($sql);
    //         $types = "";
    //         foreach($args as $arg) {
    //             if(is_int($arg)) {
    //                 $types .= "i";
    //             } else if(is_float($arg)) {
    //                 $types .= "d";
    //             } else if(is_string($arg)) {
    //                 $types .= "s";
    //             } else {
    //                 $types .= "b";
    //             }
    //         }
    //         if (!empty($types) && !empty($args)) {
    //             $preparedStatement->bind_param($types,...$args);
    //         }
    //         return $preparedStatement;
    //     } catch (Exception $e) {
    //         error_log("Error preparing statement: " . $e->getMessage());
    //     }
    // }

    public static function getPreparedStatement($sql, ...$args) {
        try {
            $conn = self::getInstance()->getConnection();
            $stmt = $conn->prepare($sql);
            
            if ($stmt === false) {
                throw new Exception("Prepare statement thất bại: " . $conn->error);
            }

            // --- PHẦN QUAN TRỌNG: Bind dữ liệu vào câu lệnh ---
            if (!empty($args)) {
                $types = "";
                foreach ($args as $arg) {
                    if (is_int($arg)) $types .= "i";
                    else if (is_float($arg)) $types .= "d";
                    else if (is_string($arg)) $types .= "s";
                    else $types .= "b";
                }
                $stmt->bind_param($types, ...$args);
            }

            return $stmt;
        } catch (Exception $e) {
            error_log("SQL Prepare Error: " . $e->getMessage());
            echo "<div style='color:red; border:1px solid red; padding:10px;'><h3>Lỗi SQL:</h3>" . $e->getMessage() . "</div>";
            throw $e;
        }
    }
    public static function executeQuery($sql, ...$args) {
        try {
            $stmt = self::getPreparedStatement($sql, ...$args);
            if (!$stmt->execute()) {
                throw new Exception("Execute thất bại: " . $stmt->error);
            }
            return $stmt->get_result();
        } catch (Exception $e) {
            error_log("Query Execution Error: " . $e->getMessage());
            throw $e;
        }
    }

    // public static function executeUpdate($sql, ...$args)
    // {
    //     try {
    //         $preparedStatement = self::getPreparedStatement($sql, ...$args);
    //         $preparedStatement->execute();
    //         $affectedRows = $preparedStatement->affected_rows;
    //         error_log("Executed query: $sql, affected rows: $affectedRows");
    //         return $affectedRows;
    //     } catch (Exception $e) {
    //         error_log("Error executing update: " . $e->getMessage());
    //     }
    // }
    public static function executeUpdate($sql, ...$args) {
        try {
            $stmt = self::getPreparedStatement($sql, ...$args);
            if (!$stmt->execute()) {
                throw new Exception("Update thất bại: " . $stmt->error);
            }
            return $stmt->affected_rows;
        } catch (Exception $e) {
            error_log("Update Execution Error: " . $e->getMessage());
            return 0;
        }
    }

    public static function closeConnection()
    {
        try {
            $instance = self::getInstance();
            if ($instance->connection != null && !$instance->connection->connect_error) {
                $instance->connection->close();
            }
        } catch (Exception $e) {
            error_log("Error closing connection: " . $e->getMessage());
        }
    }

    public function checkConnection()
    {
        $this->getConnection();
        try {
            return $this->connection != null && !$this->connection->connect_error;
        } catch (Exception $e) {
            error_log("Error checking connection: " . $e->getMessage());
        }
        return false;
    }

    public function beginTransaction()
    {
        try {
            $this->getConnection()->begin_transaction();
        } catch (Exception $e) {
            error_log("Error beginning transaction: " . $e->getMessage());
        }
    }

    public function endTransaction()
    {
        try {
            $this->getConnection()->commit();
        } catch (Exception $e) {
            error_log("Error ending transaction: " . $e->getMessage());
        }
    }

    public function rollbackTransaction()
    {
        try {
            $this->getConnection()->rollback();
        } catch (Exception $e) {
            error_log("Error rolling back transaction: " . $e->getMessage());
        }
    }

    public static function getLastInsertId() {
        try {
            $connection = self::getInstance()->getConnection();
            return $connection->insert_id;
        } catch (Exception $e) {
            error_log("Error getting last insert ID: " . $e->getMessage());
        }
        return null; // Trả về null nếu không lấy được ID
    }
}
?>