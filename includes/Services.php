<?php
require_once 'Database.php';

class Services {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAllActiveServices() {
        return $this->db->select(
            "SELECT * FROM services WHERE is_active = 1 ORDER BY id DESC"
        );
    }

    public function getServiceById($id) {
        return $this->db->selectOne(
            "SELECT * FROM services WHERE id = ? AND is_active = 1",
            [$id]
        );
    }

    public function formatPrice($price) {
        return 'NPR ' . number_format($price, 2);
    }

    public function formatDuration($minutes) {
        if ($minutes < 60) {
            return $minutes . ' minutes';
        }
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . 
               ($mins > 0 ? ' ' . $mins . ' minute' . ($mins > 1 ? 's' : '') : '');
    }
}
