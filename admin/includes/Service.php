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
            
            // Handle image upload if present
            if (isset($_FILES['service_image']) && $_FILES['service_image']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = $this->handleImageUpload($_FILES['service_image']);
                if ($uploadResult['success']) {
                    $serviceData['image_path'] = $uploadResult['path'];
                }
            }

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
            
            // Handle image upload if present
            if (isset($_FILES['service_image']) && $_FILES['service_image']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = $this->handleImageUpload($_FILES['service_image']);
                if ($uploadResult['success']) {
                    // Get current image path to delete old image
                    $currentService = $this->getServiceById($id);
                    if ($currentService && !empty($currentService['image_path'])) {
                        $oldImagePath = $_SERVER['DOCUMENT_ROOT'] . '/ddatd/' . $currentService['image_path'];
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }
                    
                    $serviceData['image_path'] = $uploadResult['path'];
                }
            }

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
    
    /**
     * Handle image upload for services
     * 
     * @param array $file The uploaded file from $_FILES
     * @return array Result with success status and path or error message
     */
    private function handleImageUpload($file) {
        try {
            // Check if upload directory exists, create if not
            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/ddatd/uploads/services/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Validate file type
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($file['type'], $allowedTypes)) {
                throw new Exception("Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed.");
            }
            
            // Validate file size (5MB max)
            $maxSize = 5 * 1024 * 1024; // 5MB
            if ($file['size'] > $maxSize) {
                throw new Exception("File is too large. Maximum size is 5MB.");
            }
            
            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'service_' . uniqid() . '.' . $extension;
            $targetPath = $uploadDir . $filename;
            
            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
                throw new Exception("Failed to move uploaded file.");
            }
            
            // Return relative path for database storage
            return [
                'success' => true,
                'path' => 'uploads/services/' . $filename
            ];
        } catch (Exception $e) {
            log_error("Image upload error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
