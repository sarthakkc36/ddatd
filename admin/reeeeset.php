<?php
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/config.php';

class AdminPasswordReset {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function resetAdminPassword($new_password) {
        try {
            // Hash the new password using Argon2 (most secure method)
            $password_hash = password_hash($new_password, PASSWORD_ARGON2ID, [
                'memory_cost' => PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
                'time_cost' => PASSWORD_ARGON2_DEFAULT_TIME_COST,
                'threads' => 4
            ]);

            // Update the admin user's password
            $result = $this->db->update(
                'users', 
                [
                    'password' => $password_hash,
                    'password_reset_required' => 0  // Optional: remove reset requirement
                ], 
                'username = ?', 
                ['admin']
            );

            if ($result) {
                echo "Admin password successfully reset!\n";
                return true;
            } else {
                echo "Failed to reset admin password. User not found or update failed.\n";
                return false;
            }

        } catch (Exception $e) {
            echo "Error resetting password: " . $e->getMessage() . "\n";
            return false;
        }
    }
}

// Instantiate and run
$adminReset = new AdminPasswordReset();
$adminReset->resetAdminPassword('cb6k_9nh2_m37');