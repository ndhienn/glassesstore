<?php
namespace App\Services;
use mysqli;
use Exception;
class database_connection {
    private $connection = null;
    private static $instance;
    private static $host = "localhost";
    private static $port = "3306";
    private static $dbname = "glassesstore";
    private static $user = "root";
    private static $pass = "276951438";

    public function __construct() {
        self::$host = env('DB_HOST');
        self::$user = env('DB_USERNAME');
        self::$pass = env('DB_PASSWORD');
        self::$dbname = env('DB_DATABASE');
        self::$port = env('DB_PORT', 3306); // Mặc định 3306 nếu không có
    }

    public static function getInstance() {
        if(self::$instance == null) {
            self::$instance = new database_connection();
        }
        return self::$instance;
    }

    public function getConnection() {
        try {
            if ($this->connection == null || !$this->connection->ping()) {
                $this->connection = mysqli_init();
                
                // THIẾT LẬP SSL - Bắt buộc cho TiDB Cloud
                // Lưu ý: Đường dẫn này là mặc định trên môi trường Linux của Render
                $this->connection->ssl_set(NULL, NULL, '/etc/ssl/certs/ca-certificates.crt', NULL, NULL);
                
                // Sử dụng hàm env() để lấy dữ liệu từ Render Environment
                $success = @$this->connection->real_connect(
                    env('DB_HOST'),
                    env('DB_USERNAME'),
                    env('DB_PASSWORD'),
                    env('DB_DATABASE'),
                    env('DB_PORT', 4000), // TiDB mặc định là 4000
                    NULL,
                    MYSQLI_CLIENT_SSL
                );

                if (!$success) {
                    throw new Exception("Connect Error: " . $this->connection->connect_error);
                }
            }
            return $this->connection;
        } catch (Exception $e) {
            // Ghi log lỗi để bạn có thể xem trong mục Logs của Render
            error_log("Database Connection Failed: " . $e->getMessage());
            return null; 
        }
    }

    public static function getPreparedStatement($sql,...$args) {
        try {
            $preparedStatement = self::getInstance()->getConnection()->prepare($sql);
            $types = "";
            foreach($args as $arg) {
                if(is_int($arg)) {
                    $types .= "i";
                } else if(is_float($arg)) {
                    $types .= "d";
                } else if(is_string($arg)) {
                    $types .= "s";
                } else {
                    $types .= "b";
                }
            }
            if (!empty($types) && !empty($args)) {
                $preparedStatement->bind_param($types,...$args);
            }
            return $preparedStatement;
        } catch (Exception $e) {
            error_log("Error preparing statement: " . $e->getMessage());
        }
    }
    public static function executeQuery($sql, ...$args)
    {
        $preparedStatement = self::getPreparedStatement($sql, ...$args);
        $preparedStatement->execute();
        return $preparedStatement->get_result();
    }

    public static function executeUpdate($sql, ...$args)
    {
        try {
            $preparedStatement = self::getPreparedStatement($sql, ...$args);
            $preparedStatement->execute();
            $affectedRows = $preparedStatement->affected_rows;
            error_log("Executed query: $sql, affected rows: $affectedRows");
            return $affectedRows;
        } catch (Exception $e) {
            error_log("Error executing update: " . $e->getMessage());
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