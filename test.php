<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'ddatd_db');
define('DB_USER', 'root');
define('DB_PASS', '');

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

    public function query($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            
            if (is_array($params) && !empty($params)) {
                if (array_keys($params) === range(0, count($params) - 1)) {
                    $stmt->execute($params);
                } else {
                    foreach ($params as $key => $value) {
                        $stmt->bindValue(
                            is_numeric($key) ? $key + 1 : ':' . $key, 
                            $value
                        );
                    }
                    $stmt->execute();
                }
            } else {
                $stmt->execute();
            }
            
            return $stmt;
        } catch(PDOException $e) {
            error_log("Database query error: " . $e->getMessage());
            throw $e;
        }
    }

    public function update($table, $data, $where, $params) {
        $setParts = [];
        foreach ($data as $key => $value) {
            $setParts[] = "$key = :$key";
        }
        
        $sql = "UPDATE $table SET " . implode(', ', $setParts) . " WHERE $where";
        
        $allParams = array_merge($data, $params);
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($allParams);
    }
}

// Function to reset admin password
function resetAdminPassword($newPassword) {
    $db = Database::getInstance();
    
    // Generate secure password hash
    $passwordHash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
    
    try {
        // Update password for admin user
        $result = $db->update(
            'users', 
            ['password' => $passwordHash], 
            'username = :username', 
            ['username' => 'admin']
        );
        
        if ($result) {
            echo "Password successfully reset!\n";
            
            // Verify the new password
            $user = $db->query("SELECT * FROM users WHERE username = 'admin'")->fetch();
            $verifyResult = password_verify($newPassword, $user['password']);
            
            echo "New Password Verification: " . 
                 ($verifyResult ? "SUCCESSFUL" : "FAILED") . "\n";
        } else {
            echo "Failed to reset password.\n";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

// Reset password to 'cb6k_9nh2_m37'
resetAdminPassword('cb6k_9nh2_m37');