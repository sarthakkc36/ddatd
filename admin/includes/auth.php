<?php
define('ALLOWED_ACCESS', true);
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/Database.php';

function require_admin() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: index.php');
        exit;
    }
}

function get_dashboard_stats() {
    try {
        $db = Database::getInstance();
        
        $stats = [
            'total_services' => $db->selectOne("SELECT COUNT(*) as count FROM services")['count'] ?? 0,
            'total_team_members' => $db->selectOne("SELECT COUNT(*) as count FROM team_members")['count'] ?? 0,
            'total_testimonials' => $db->selectOne("SELECT COUNT(*) as count FROM testimonials")['count'] ?? 0,
            'total_bookings' => $db->selectOne("SELECT COUNT(*) as count FROM bookings")['count'] ?? 0,
            'pending_bookings' => $db->selectOne("SELECT COUNT(*) as count FROM bookings WHERE status = 'pending'")['count'] ?? 0,
            'total_inquiries' => $db->selectOne("SELECT COUNT(*) as count FROM contact_messages")['count'] ?? 0,
            'new_messages' => $db->selectOne("SELECT COUNT(*) as count FROM contact_messages WHERE status = 'new'")['count'] ?? 0
        ];
        
        return $stats;
    } catch (Exception $e) {
        log_error("Error getting dashboard stats: " . $e->getMessage());
        return [
            'total_services' => 0,
            'total_bookings' => 0,
            'pending_bookings' => 0,
            'total_inquiries' => 0,
            'total_team_members' => 0,
            'total_testimonials' => 0,
            'new_messages' => 0
        ];
    }
}

function get_recent_activity($limit = 5) {
    try {
        $db = Database::getInstance();
        $limit = (int)$limit; // Ensure limit is an integer
        
        // Get recent bookings
        $bookings = $db->select(
            "SELECT b.*, s.title as service_name 
             FROM bookings b 
             LEFT JOIN services s ON b.service_id = s.id 
             ORDER BY b.created_at DESC 
             LIMIT $limit"
        );
        
        // Get recent contact messages
        $messages = $db->select(
            "SELECT * FROM contact_messages 
             ORDER BY created_at DESC 
             LIMIT $limit"
        );
        
        // Combine and sort by created_at
        $activity = [];
        foreach ($bookings as $booking) {
            $activity[] = [
                'type' => 'booking',
                'data' => $booking,
                'time' => strtotime($booking['created_at'])
            ];
        }
        foreach ($messages as $message) {
            $activity[] = [
                'type' => 'message',
                'data' => $message,
                'time' => strtotime($message['created_at'])
            ];
        }
        
        // Sort by time descending
        usort($activity, function($a, $b) {
            return $b['time'] - $a['time'];
        });
        
        // Take only the first $limit items
        return array_slice($activity, 0, $limit);
        
    } catch (Exception $e) {
        log_error("Error getting recent activity: " . $e->getMessage());
        return [];
    }
}

// Helper function to format time difference
function time_elapsed_string($datetime) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    if ($diff->d > 7) {
        return $ago->format('M j, Y');
    }
    if ($diff->d > 0) {
        return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
    }
    if ($diff->h > 0) {
        return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
    }
    if ($diff->i > 0) {
        return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
    }
    return 'just now';
}

// Note: generate_csrf_token() and log_error() functions are already defined in includes/config.php
