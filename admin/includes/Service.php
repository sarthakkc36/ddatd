<?php
require_once __DIR__ . '/../../includes/Database.php';

class Service {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAllServices() {
        return $this->db->select("SELECT * FROM services ORDER BY id DESC");
    }

    public function getServiceById($id) {
        return $this->db->selectOne(
            "SELECT * FROM services WHERE id = ?",
            [$id]
        );
    }

    public function createService($data) {
        try {
            // Sanitize data
            $serviceData = [
                'title' => trim($data['title']),
                'description' => trim($data['description']),
                'image' => $data['image'] ?? '',
                'price' => $data['price'] ?? 0.00,
                'duration' => $data['duration'] ?? 60,
                'is_active' => $data['status'] === 'active' ? 1 : 0
            ];

            return $this->db->insert('services', $serviceData);
        } catch (Exception $e) {
            log_error("Error creating service: " . $e->getMessage());
            throw $e;
        }
    }

    public function updateService($id, $data) {
        try {
            // Sanitize data
            $serviceData = [
                'title' => trim($data['title']),
                'description' => trim($data['description']),
                'image' => $data['image'] ?? '',
                'price' => $data['price'] ?? 0.00,
                'duration' => $data['duration'] ?? 60,
                'is_active' => $data['status'] === 'active' ? 1 : 0
            ];

            $this->db->update('services', $serviceData, 'id = ?', [$id]);
            return true;
        } catch (Exception $e) {
            log_error("Error updating service: " . $e->getMessage());
            throw $e;
        }
    }

    public function deleteService($id) {
        try {
            // Check if service is being used in bookings
            $bookings = $this->db->selectOne(
                "SELECT COUNT(*) as count FROM bookings WHERE service_id = ?",
                [$id]
            );

            if ($bookings['count'] > 0) {
                throw new Exception("Cannot delete service as it has associated bookings");
            }

            $this->db->delete('services', 'id = ?', [$id]);
            return true;
        } catch (Exception $e) {
            log_error("Error deleting service: " . $e->getMessage());
            throw $e;
        }
    }

    public function toggleServiceStatus($id) {
        try {
            $service = $this->getServiceById($id);
            if (!$service) {
                throw new Exception("Service not found");
            }

            $this->db->update(
                'services',
                ['is_active' => $service['is_active'] ? 0 : 1],
                'id = ?',
                [$id]
            );
            return true;
        } catch (Exception $e) {
            log_error("Error toggling service status: " . $e->getMessage());
            throw $e;
        }
    }

    public function searchServices($term) {
        return $this->db->select(
            "SELECT * FROM services WHERE title LIKE ? OR description LIKE ? ORDER BY id DESC",
            ["%$term%", "%$term%"]
        );
    }
    
}
