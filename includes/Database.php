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
            $stmt->execute($params);
            return $stmt;
        } catch(PDOException $e) {
            die("Query failed: " . $e->getMessage());
        }
    }

    public function select($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }

    public function selectOne($sql, $params = []) {
        return $this->query($sql, $params)->fetch();
    }

    public function insert($table, $data) {
        $fields = array_keys($data);
        $placeholders = array_map(function($field) { return ":$field"; }, $fields);
        
        $sql = "INSERT INTO $table (" . implode(", ", $fields) . ") 
                VALUES (" . implode(", ", $placeholders) . ")";
        
        $this->query($sql, $data);
        return $this->conn->lastInsertId();
    }

    public function update($table, $data, $where, $whereParams = []) {
        $setParts = array_map(function($field) { return "$field = :$field"; }, array_keys($data));
        
        // Convert positional where parameters to named parameters
        $whereConditions = $where;
        foreach ($whereParams as $index => $value) {
            $paramName = ":where_param_$index";
            $whereConditions = preg_replace('/\?/', $paramName, $whereConditions, 1);
            $whereParams[$paramName] = $value;
            unset($whereParams[$index]);
        }
        
        $sql = "UPDATE $table SET " . implode(", ", $setParts) . " WHERE $whereConditions";
        $params = array_merge($data, $whereParams);
        $this->query($sql, $params);
    }

    public function delete($table, $where, $params = []) {
        $sql = "DELETE FROM $table WHERE $where";
        $this->query($sql, $params);
    }
}
