<?php
require_once 'Database.php';

class Testimonials {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAllTestimonials() {
        return $this->db->select(
            "SELECT * FROM testimonials ORDER BY display_order ASC, created_at DESC"
        );
    }

    public function getAllActiveTestimonials() {
        return $this->db->select(
            "SELECT * FROM testimonials WHERE is_active = 1 ORDER BY display_order ASC, created_at DESC"
        );
    }

    public function getTestimonialById($id) {
        return $this->db->selectOne(
            "SELECT * FROM testimonials WHERE id = ?",
            [$id]
        );
    }

    public function createTestimonial($data) {
        try {
            // Sanitize data
            $testimonialData = [
                'name' => trim($data['name']),
                'position' => trim($data['position']),
                'content' => trim($data['content']),
                'rating' => isset($data['rating']) ? intval($data['rating']) : 5,
                'photo_path' => $data['photo_path'] ?? null,
                'is_active' => isset($data['is_active']) ? 1 : 0,
                'display_order' => isset($data['display_order']) ? intval($data['display_order']) : 0
            ];

            return $this->db->insert('testimonials', $testimonialData);
        } catch (Exception $e) {
            log_error("Error creating testimonial: " . $e->getMessage());
            throw $e;
        }
    }

    public function updateTestimonial($id, $data) {
        try {
            // Sanitize data
            $testimonialData = [
                'name' => trim($data['name']),
                'position' => trim($data['position']),
                'content' => trim($data['content']),
                'rating' => isset($data['rating']) ? intval($data['rating']) : 5,
                'is_active' => isset($data['is_active']) ? 1 : 0,
                'display_order' => isset($data['display_order']) ? intval($data['display_order']) : 0
            ];

            // Add photo path if provided
            if (isset($data['photo_path']) && !empty($data['photo_path'])) {
                $testimonialData['photo_path'] = $data['photo_path'];
            }

            $this->db->update('testimonials', $testimonialData, 'id = ?', [$id]);
            return true;
        } catch (Exception $e) {
            log_error("Error updating testimonial: " . $e->getMessage());
            throw $e;
        }
    }

    public function deleteTestimonial($id) {
        try {
            $this->db->delete('testimonials', 'id = ?', [$id]);
            return true;
        } catch (Exception $e) {
            log_error("Error deleting testimonial: " . $e->getMessage());
            throw $e;
        }
    }

    public function toggleTestimonialStatus($id) {
        try {
            $testimonial = $this->getTestimonialById($id);
            if (!$testimonial) {
                throw new Exception("Testimonial not found");
            }

            $this->db->update(
                'testimonials',
                ['is_active' => $testimonial['is_active'] ? 0 : 1],
                'id = ?',
                [$id]
            );
            return true;
        } catch (Exception $e) {
            log_error("Error toggling testimonial status: " . $e->getMessage());
            throw $e;
        }
    }
}