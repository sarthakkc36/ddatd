<?php
require_once 'Database.php';

class Settings {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Get all settings by section
     * @param string|null $section Optional section name to filter by
     * @return array Settings data
     */
    public function getAllSettings($section = null) {
        if ($section) {
            return $this->db->select(
                "SELECT * FROM settings WHERE section = ? ORDER BY display_order ASC",
                [$section]
            );
        } else {
            return $this->db->select(
                "SELECT * FROM settings ORDER BY section, display_order ASC"
            );
        }
    }

    /**
     * Get grouped settings for all sections
     * @return array Settings grouped by section
     */
    public function getGroupedSettings() {
        $settings = $this->getAllSettings();
        $grouped = [];
        
        foreach ($settings as $setting) {
            if (!isset($grouped[$setting['section']])) {
                $grouped[$setting['section']] = [];
            }
            $grouped[$setting['section']][$setting['setting_key']] = $setting['setting_value'];
        }
        
        return $grouped;
    }

    /**
     * Get a specific setting value
     * @param string $key Setting key
     * @param string|null $default Default value if setting doesn't exist
     * @return string Setting value
     */
    public function get($key, $default = null) {
        $setting = $this->db->selectOne(
            "SELECT setting_value FROM settings WHERE setting_key = ?",
            [$key]
        );
        
        return $setting ? $setting['setting_value'] : $default;
    }

    /**
     * Update or create settings
     * @param string $section Section name
     * @param array $data Settings data to update
     * @return bool Success status
     */
    public function updateSettings($section, $data) {
        try {
            $this->db->getConnection()->beginTransaction();
            
            foreach ($data as $key => $value) {
                // Check if the setting exists
                $exists = $this->db->selectOne(
                    "SELECT id FROM settings WHERE section = ? AND setting_key = ?",
                    [$section, $key]
                );
                
                if ($exists) {
                    // Update existing setting
                    $this->db->update(
                        'settings',
                        ['setting_value' => $value],
                        'section = ? AND setting_key = ?',
                        [$section, $key]
                    );
                } else {
                    // Insert new setting
                    $this->db->insert('settings', [
                        'section' => $section,
                        'setting_key' => $key,
                        'setting_value' => $value,
                        'display_order' => 0
                    ]);
                }
            }
            
            $this->db->getConnection()->commit();
            return true;
        } catch (Exception $e) {
            $this->db->getConnection()->rollBack();
            log_error("Error updating settings: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Load default settings if no settings exist
     * @return bool Success status
     */
    public function loadDefaultSettings() {
        // Check if settings already exist
        $existingSettings = $this->db->selectOne("SELECT COUNT(*) as count FROM settings");
        
        if ($existingSettings && $existingSettings['count'] > 0) {
            return false; // Don't overwrite existing settings
        }
        
        $defaultSettings = [
            'site' => [
                'site_name' => 'Doctors At Door Step',
                'site_tagline' => 'Healthcare at your doorstep',
                'site_description' => 'We provide quality healthcare services at your home, making medical care accessible and convenient.',
                'contact_email' => 'doctorsatdoorstep@gmail.com',
                'contact_phone' => '+977 986-0102404',
                'address' => 'khursanitar marg, Kathmandu, Nepal',
                'working_hours' => '9:00 AM - 8:00 PM, All Days',
                'logo' => 'images/logo.png',
                'favicon' => 'images/favicon.ico'
            ],
            'social' => [
                'facebook' => 'https://facebook.com/doctorsatdoorstep',
                'twitter' => 'https://twitter.com/doctorsatdoorstep',
                'instagram' => 'https://instagram.com/doctorsatdoorstep',
                'linkedin' => 'https://linkedin.com/company/doctorsatdoorstep',
                'youtube' => ''
            ],
            'booking' => [
                'min_booking_notice' => '24',
                'max_booking_advance' => '30',
                'booking_interval' => '60',
                'working_days' => 'all',
                'working_hours_start' => '09:00',
                'working_hours_end' => '20:00',
                'allow_same_day_booking' => '1',
                'require_phone' => '1',
                'require_address' => '1'
            ],
            'email' => [
                'smtp_host' => 'smtp.example.com',
                'smtp_port' => '587',
                'smtp_username' => 'doctorsatdoorstep@gmail.com',
                'smtp_password' => '',
                'smtp_encryption' => 'tls',
                'from_email' => 'doctorsatdoorstep@gmail.com',
                'from_name' => 'Doctors At Door Step',
                'admin_notification_email' => 'admin@doctorsatdoorstep.com'
            ],
            'payment' => [
                'currency' => 'NPR',
                'currency_symbol' => 'Rs.',
                'payment_methods' => 'cash,esewa,khalti',
                'tax_rate' => '13',
                'enable_online_payment' => '1'
            ],
            'system' => [
                'timezone' => 'Asia/Kathmandu',
                'date_format' => 'Y-m-d',
                'time_format' => 'H:i',
                'enable_registration' => '1',
                'enable_testimonials' => '1',
                'maintenance_mode' => '0',
                'debug_mode' => '0'
            ]
        ];
        
        try {
            $this->db->getConnection()->beginTransaction();
            $displayOrder = 0;
            
            foreach ($defaultSettings as $section => $settings) {
                foreach ($settings as $key => $value) {
                    $this->db->insert('settings', [
                        'section' => $section,
                        'setting_key' => $key,
                        'setting_value' => $value,
                        'display_order' => $displayOrder++
                    ]);
                }
            }
            
            $this->db->getConnection()->commit();
            return true;
        } catch (Exception $e) {
            $this->db->getConnection()->rollBack();
            log_error("Error loading default settings: " . $e->getMessage());
            return false;
        }
    }
}