<?php
class Database {
    private $conn = null;
    private static $instance = null;

    private function __construct() {
        try {
            $this->conn = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
                DB_USER,
                DB_PASS
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }

    public function query($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            
            // Added debugging - comment this out in production
            /*
            echo "<pre>";
            echo "SQL: " . $sql . "\n";
            echo "Params: ";
            print_r($params);
            echo "</pre>";
            */
            
            // Fix for potential issue with numeric array keys in parameters
            if (is_array($params) && !empty($params)) {
                if (array_keys($params) === range(0, count($params) - 1)) {
                    // Sequential array - use positional parameters
                    $stmt->execute($params);
                } else {
                    // Associative array - bind each parameter separately
                    foreach ($params as $key => $value) {
                        // If the key is numeric, convert it to a positional parameter
                        if (is_numeric($key)) {
                            $stmt->bindValue($key + 1, $value);
                        } else {
                            $stmt->bindValue(':' . $key, $value);
                        }
                    }
                    $stmt->execute();
                }
            } else {
                $stmt->execute();
            }
            
            return $stmt;
        } catch(PDOException $e) {
            // Log the error with specifics
            error_log("Database query error: " . $e->getMessage());
            error_log("SQL: " . $sql);
            error_log("Params: " . print_r($params, true));
            
            // Throw the exception to be handled by the calling code
            throw $e;
        }
    }

    public function select($sql, $params = []) {
        try {
            return $this->query($sql, $params)->fetchAll();
        } catch(PDOException $e) {
            // Use a more graceful error handling approach
            error_log("SELECT query error: " . $e->getMessage());
            error_log("SQL: " . $sql);
            // Return empty array instead of crashing
            return [];
        }
    }

    public function selectOne($sql, $params = []) {
        try {
            $result = $this->query($sql, $params)->fetch();
            return $result !== false ? $result : null;
        } catch(PDOException $e) {
            error_log("SELECT ONE query error: " . $e->getMessage());
            error_log("SQL: " . $sql);
            return null;
        }
    }

    public function insert($table, $data) {
        $fields = array_keys($data);
        $placeholders = array_map(function($field) { return ":$field"; }, $fields);
        
        $sql = "INSERT INTO $table (" . implode(", ", $fields) . ") 
                VALUES (" . implode(", ", $placeholders) . ")";
        
        try {
            $this->query($sql, $data);
            return $this->conn->lastInsertId();
        } catch(PDOException $e) {
            error_log("INSERT query error: " . $e->getMessage());
            error_log("Table: " . $table);
            return false;
        }
    }

    public function update($table, $data, $where, $whereParams = []) {
        try {
            $setParts = [];
            $params = [];
            
            // Handle special case for expressions like "views + 1"
            foreach ($data as $field => $value) {
                if (is_array($value) && isset($value['expr'])) {
                    $setParts[] = "$field = " . $value['expr'];
                } else {
                    $setParts[] = "$field = :$field";
                    $params[$field] = $value;
                }
            }
            
            // Convert positional where parameters to named parameters
            $whereConditions = $where;
            foreach ($whereParams as $index => $value) {
                $paramName = ":where_param_$index";
                $whereConditions = preg_replace('/\?/', $paramName, $whereConditions, 1);
                $params[$paramName] = $value;
            }
            
            $sql = "UPDATE $table SET " . implode(", ", $setParts) . " WHERE $whereConditions";
            $this->query($sql, $params);
            return true;
        } catch(PDOException $e) {
            error_log("UPDATE query error: " . $e->getMessage());
            error_log("Table: " . $table);
            return false;
        }
    }

    public function delete($table, $where, $params = []) {
        try {
            $sql = "DELETE FROM $table WHERE $where";
            $this->query($sql, $params);
            return true;
        } catch(PDOException $e) {
            error_log("DELETE query error: " . $e->getMessage());
            error_log("Table: " . $table);
            return false;
        }
    }
}