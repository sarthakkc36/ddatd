<?php
session_start();
require_once 'includes/auth.php';
require_admin();

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

// Include Settings class
require_once '../includes/Settings.php';
$settingsHandler = new Settings();

// Load default settings if needed
$settingsHandler->loadDefaultSettings();

// Message handling
$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['section'])) {
    $section = $_POST['section'];
    
    // Remove the section identifier from POST data before processing
    $formData = $_POST;
    unset($formData['section']);
    
    // Handle file uploads for logo/favicon
    if ($section === 'site') {
        // Process logo upload
        if (!empty($_FILES['logo']['name'])) {
            $targetDir = "../uploads/site/";
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            $logoFileName = 'logo_' . time() . '.' . strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
            $targetPath = $targetDir . $logoFileName;

            if (move_uploaded_file($_FILES['logo']['tmp_name'], $targetPath)) {
                $formData['logo'] = 'uploads/site/' . $logoFileName;
            }
        }
        
        // Process favicon upload
        if (!empty($_FILES['favicon']['name'])) {
            $targetDir = "../uploads/site/";
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            $faviconFileName = 'favicon_' . time() . '.' . strtolower(pathinfo($_FILES['favicon']['name'], PATHINFO_EXTENSION));
            $targetPath = $targetDir . $faviconFileName;

            if (move_uploaded_file($_FILES['favicon']['tmp_name'], $targetPath)) {
                $formData['favicon'] = 'uploads/site/' . $faviconFileName;
            }
        }
    }
    
    // Handle checkbox fields which don't get submitted when unchecked
    if ($section === 'booking') {
        $checkboxFields = ['allow_same_day_booking', 'require_phone', 'require_address'];
        foreach ($checkboxFields as $field) {
            if (!isset($formData[$field])) {
                $formData[$field] = '0';
            }
        }
    }
    
    if ($section === 'payment') {
        if (!isset($formData['enable_online_payment'])) {
            $formData['enable_online_payment'] = '0';
        }
    }
    
    if ($section === 'system') {
        $checkboxFields = ['enable_registration', 'enable_testimonials', 'maintenance_mode', 'debug_mode'];
        foreach ($checkboxFields as $field) {
            if (!isset($formData[$field])) {
                $formData[$field] = '0';
            }
        }
    }
    
    try {
        // Update settings in database
        $settingsHandler->updateSettings($section, $formData);
        $message = 'Settings updated successfully!';
        $messageType = 'success';
    } catch (Exception $e) {
        $message = 'An error occurred: ' . $e->getMessage();
        $messageType = 'error';
    }
}

// Get current section from URL
$currentSection = isset($_GET['section']) ? $_GET['section'] : 'site';
$validSections = ['site', 'social', 'booking', 'email', 'payment', 'system'];
if (!in_array($currentSection, $validSections)) {
    $currentSection = 'site';
}

// Get all settings for the current view
$allSettings = $settingsHandler->getGroupedSettings();
$currentSectionSettings = $allSettings[$currentSection] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Doctors At Door Step</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2C7BE5;
            --secondary-color: #6B7A99;
            --dark-color: #1A2B3C;
            --light-color: #F8FAFC;
            --white: #FFFFFF;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --sidebar-width: 250px;
            --success-color: #10B981;
            --error-color: #EF4444;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-color);
            color: var(--dark-color);
        }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background-color: var(--dark-color);
            color: var(--white);
            padding: 20px 0;
            overflow-y: auto;
            z-index: 1000;
            transition: all 0.3s;
        }
        
        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
        }
        
        .sidebar-header h1 {
            font-size: 20px;
            margin-bottom: 5px;
            color: var(--primary-color);
        }
        
        .sidebar-header p {
            font-size: 14px;
            opacity: 0.8;
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .menu-item {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            color: var(--white);
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .menu-item:hover, .menu-item.active {
            background-color: rgba(44, 123, 229, 0.2);
            color: var(--primary-color);
        }
        
        .menu-item i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            position: absolute;
            bottom: 0;
            width: 100%;
        }
        
        .logout-btn {
            display: block;
            padding: 10px;
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--white);
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s;
        }
        
        .logout-btn:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
        
        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            min-height: 100vh;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .page-title {
            font-size: 24px;
            font-weight: 600;
        }
        
        /* Alert Messages */
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background-color: rgba(16, 185, 129, 0.1);
            border: 1px solid var(--success-color);
            color: var(--success-color);
        }
        
        .alert-error {
            background-color: rgba(239, 68, 68, 0.1);
            border: 1px solid var(--error-color);
            color: var(--error-color);
        }
        
        /* Settings Layout */
        .settings-container {
            display: flex;
            gap: 20px;
        }
        
        .settings-sidebar {
            width: 250px;
            background-color: var(--white);
            border-radius: 10px;
            box-shadow: var(--box-shadow);
            padding: 15px;
        }
        
        .settings-nav {
            list-style: none;
        }
        
        .settings-nav-item {
            margin-bottom: 5px;
        }
        
        .settings-nav-link {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            border-radius: 5px;
            color: var(--dark-color);
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .settings-nav-link:hover {
            background-color: rgba(44, 123, 229, 0.05);
        }
        
        .settings-nav-link.active {
            background-color: rgba(44, 123, 229, 0.1);
            color: var(--primary-color);
            font-weight: 500;
        }
        
        .settings-nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .settings-content {
            flex: 1;
            background-color: var(--white);
            border-radius: 10px;
            box-shadow: var(--box-shadow);
            padding: 20px;
        }
        
        .settings-section {
            margin-bottom: 30px;
        }
        
        .settings-section-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        /* Form Styles */
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"],
        .form-group input[type="number"],
        .form-group input[type="url"],
        .form-group input[type="tel"],
        .form-group input[type="time"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            font-family: 'Poppins', sans-serif;
        }
        
        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        .form-group .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .form-group .checkbox-group input[type="checkbox"] {
            width: auto;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }
        
        .btn-cancel {
            padding: 10px 20px;
            background-color: var(--light-color);
            color: var(--dark-color);
            border: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .btn-cancel:hover {
            background-color: #e0e0e0;
        }
        
        .btn-submit {
            padding: 10px 20px;
            background-color: var(--primary-color);
            color: var(--white);
            border: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-submit:hover {
            background-color: var(--dark-color);
        }
        
        /* Form Helpers */
        .text-muted {
            color: var(--secondary-color);
            font-size: 0.85rem;
            margin-top: 0.25rem;
        }
        
        /* Current Image Display */
        .current-image {
            margin-top: 10px;
            margin-bottom: 10px;
            max-width: 200px;
        }
        
        .current-image img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
            border: 1px solid #e0e0e0;
        }
        
        /* Responsive */
        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .toggle-sidebar {
                display: block;
            }
            
            .settings-container {
                flex-direction: column;
            }
            
            .settings-sidebar {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
        <h1>                <img src="../images/logo.png" alt="Doctors At Door Step"width="100px" height="100px"  />
        </h1>
            <p>Admin Panel</p>
        </div>
        
        <div class="sidebar-menu">
            <a href="dashboard.php" class="menu-item">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="services.php" class="menu-item">
                <i class="fas fa-hand-holding-medical"></i> Services
            </a>
            <a href="team.php" class="menu-item">
                <i class="fas fa-user-md"></i> Team Members
            </a>
            <a href="testimonials.php" class="menu-item">
                <i class="fas fa-quote-right"></i> Testimonials
            </a>
            <a href="blog-management.php" class="menu-item">
                <i class="fas fa-blog"></i> Blog
            </a>
            <a href="blog-categories.php" class="menu-item">
                <i class="fas fa-folder"></i> Blog Categories
            </a>
            <a href="blog-tags.php" class="menu-item">
                <i class="fas fa-tags"></i> Blog Tags
            </a>

            <a href="inquiries.php" class="menu-item">
                <i class="fas fa-envelope"></i> Inquiries
            </a>
            <a href="bookings.php" class="menu-item">
                <i class="fas fa-calendar-check"></i> Bookings
            </a>
            <a href="settings.php" class="menu-item active">
                <i class="fas fa-cog"></i> Settings
            </a>
        </div>
        
        <div class="sidebar-footer">
            <a href="dashboard.php?logout=true" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">Settings</h1>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <div class="settings-container">
            <div class="settings-sidebar">
                <ul class="settings-nav">
                    <li class="settings-nav-item">
                        <a href="settings.php?section=site" class="settings-nav-link <?php echo $currentSection === 'site' ? 'active' : ''; ?>">
                            <i class="fas fa-globe"></i> Site Settings
                        </a>
                    </li>
                    <li class="settings-nav-item">
                        <a href="settings.php?section=social" class="settings-nav-link <?php echo $currentSection === 'social' ? 'active' : ''; ?>">
                            <i class="fas fa-share-alt"></i> Social Media
                        </a>
                    </li>
                    <li class="settings-nav-item">
                        <a href="settings.php?section=booking" class="settings-nav-link <?php echo $currentSection === 'booking' ? 'active' : ''; ?>">
                            <i class="fas fa-calendar-alt"></i> Booking Settings
                        </a>
                    </li>
                    <li class="settings-nav-item">
                        <a href="settings.php?section=email" class="settings-nav-link <?php echo $currentSection === 'email' ? 'active' : ''; ?>">
                            <i class="fas fa-envelope"></i> Email Settings
                        </a>
                    </li>
                    <li class="settings-nav-item">
                        <a href="settings.php?section=payment" class="settings-nav-link <?php echo $currentSection === 'payment' ? 'active' : ''; ?>">
                            <i class="fas fa-credit-card"></i> Payment Settings
                        </a>
                    </li>
                    <li class="settings-nav-item">
                        <a href="settings.php?section=system" class="settings-nav-link <?php echo $currentSection === 'system' ? 'active' : ''; ?>">
                            <i class="fas fa-cogs"></i> System Settings
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="settings-content">
                <?php if ($currentSection === 'site'): ?>
                    <div class="settings-section">
                        <h2 class="settings-section-title">Site Settings</h2>
                        <form method="POST" action="settings.php?section=site" enctype="multipart/form-data">
                            <input type="hidden" name="section" value="site">
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="site_name">Site Name</label>
                                    <input type="text" id="site_name" name="site_name" value="<?php echo htmlspecialchars($currentSectionSettings['site_name'] ?? 'Doctors At Door Step'); ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="site_tagline">Site Tagline</label>
                                    <input type="text" id="site_tagline" name="site_tagline" value="<?php echo htmlspecialchars($currentSectionSettings['site_tagline'] ?? 'Healthcare at your doorstep'); ?>">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="site_description">Site Description</label>
                                <textarea id="site_description" name="site_description"><?php echo htmlspecialchars($currentSectionSettings['site_description'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="contact_email">Contact Email</label>
                                    <input type="email" id="contact_email" name="contact_email" value="<?php echo htmlspecialchars($currentSectionSettings['contact_email'] ?? ''); ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="contact_phone">Contact Phone</label>
                                    <input type="tel" id="contact_phone" name="contact_phone" value="<?php echo htmlspecialchars($currentSectionSettings['contact_phone'] ?? ''); ?>" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="address">Address</label>
                                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($currentSectionSettings['address'] ?? ''); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="working_hours">Working Hours</label>
                                <input type="text" id="working_hours" name="working_hours" value="<?php echo htmlspecialchars($currentSectionSettings['working_hours'] ?? ''); ?>">
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="logo">Logo</label>
                                    <input type="file" id="logo" name="logo" accept="image/*">
                                    <?php if (!empty($currentSectionSettings['logo'])): ?>
                                        <div class="current-image">
                                            <p>Current logo:</p>
                                            <img src="../<?php echo htmlspecialchars($currentSectionSettings['logo']); ?>" alt="Site Logo">
                                        </div>
                                    <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>
        // Toggle sidebar on mobile
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.querySelector('.toggle-sidebar');
            const sidebar = document.querySelector('.sidebar');
            
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                });
            }
        });
    </script>
</body>
</html>
                                    <small class="text-muted">Recommended size: 200x80px, JPG/PNG formats</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="favicon">Favicon</label>
                                    <input type="file" id="favicon" name="favicon" accept="image/*">
                                    <?php if (!empty($currentSectionSettings['favicon'])): ?>
                                        <div class="current-image">
                                            <p>Current favicon:</p>
                                            <img src="../<?php echo htmlspecialchars($currentSectionSettings['favicon']); ?>" alt="Site Favicon">
                                        </div>
                                    <?php endif; ?>
                                    <small class="text-muted">Recommended size: 32x32px or 16x16px, ICO/PNG formats</small>
                                </div>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" class="btn-submit">Save Changes</button>
                            </div>
                        </form>
                    </div>
                <?php elseif ($currentSection === 'social'): ?>
                    <div class="settings-section">
                        <h2 class="settings-section-title">Social Media Settings</h2>
                        <form method="POST" action="settings.php?section=social">
                            <input type="hidden" name="section" value="social">
                            
                            <div class="form-group">
                                <label for="facebook">Facebook URL</label>
                                <input type="url" id="facebook" name="facebook" value="<?php echo htmlspecialchars($currentSectionSettings['facebook'] ?? ''); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="twitter">Twitter URL</label>
                                <input type="url" id="twitter" name="twitter" value="<?php echo htmlspecialchars($currentSectionSettings['twitter'] ?? ''); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="instagram">Instagram URL</label>
                                <input type="url" id="instagram" name="instagram" value="<?php echo htmlspecialchars($currentSectionSettings['instagram'] ?? ''); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="linkedin">LinkedIn URL</label>
                                <input type="url" id="linkedin" name="linkedin" value="<?php echo htmlspecialchars($currentSectionSettings['linkedin'] ?? ''); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="youtube">YouTube URL</label>
                                <input type="url" id="youtube" name="youtube" value="<?php echo htmlspecialchars($currentSectionSettings['youtube'] ?? ''); ?>">
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" class="btn-submit">Save Changes</button>
                            </div>
                        </form>
                    </div>
                <?php elseif ($currentSection === 'booking'): ?>
                    <div class="settings-section">
                        <h2 class="settings-section-title">Booking Settings</h2>
                        <form method="POST" action="settings.php?section=booking">
                            <input type="hidden" name="section" value="booking">
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="min_booking_notice">Minimum Booking Notice (hours)</label>
                                    <input type="number" id="min_booking_notice" name="min_booking_notice" value="<?php echo htmlspecialchars($currentSectionSettings['min_booking_notice'] ?? '24'); ?>" min="0" required>
                                    <small class="text-muted">Minimum time in advance a booking can be made</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="max_booking_advance">Maximum Booking Advance (days)</label>
                                    <input type="number" id="max_booking_advance" name="max_booking_advance" value="<?php echo htmlspecialchars($currentSectionSettings['max_booking_advance'] ?? '30'); ?>" min="1" required>
                                    <small class="text-muted">How far in advance bookings can be made</small>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="booking_interval">Booking Interval (minutes)</label>
                                    <input type="number" id="booking_interval" name="booking_interval" value="<?php echo htmlspecialchars($currentSectionSettings['booking_interval'] ?? '60'); ?>" min="15" step="15" required>
                                    <small class="text-muted">Time interval between available booking slots</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="working_days">Working Days</label>
                                    <select id="working_days" name="working_days">
                                        <option value="all" <?php echo ($currentSectionSettings['working_days'] ?? 'all') === 'all' ? 'selected' : ''; ?>>All Days</option>
                                        <option value="weekdays" <?php echo ($currentSectionSettings['working_days'] ?? '') === 'weekdays' ? 'selected' : ''; ?>>Weekdays Only</option>
                                        <option value="custom" <?php echo ($currentSectionSettings['working_days'] ?? '') === 'custom' ? 'selected' : ''; ?>>Custom</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="working_hours_start">Working Hours Start</label>
                                    <input type="time" id="working_hours_start" name="working_hours_start" value="<?php echo htmlspecialchars($currentSectionSettings['working_hours_start'] ?? '09:00'); ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="working_hours_end">Working Hours End</label>
                                    <input type="time" id="working_hours_end" name="working_hours_end" value="<?php echo htmlspecialchars($currentSectionSettings['working_hours_end'] ?? '20:00'); ?>" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="checkbox-group">
                                    <input type="checkbox" id="allow_same_day_booking" name="allow_same_day_booking" value="1" <?php echo ($currentSectionSettings['allow_same_day_booking'] ?? '1') == '1' ? 'checked' : ''; ?>>
                                    <label for="allow_same_day_booking">Allow Same Day Booking</label>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="checkbox-group">
                                    <input type="checkbox" id="require_phone" name="require_phone" value="1" <?php echo ($currentSectionSettings['require_phone'] ?? '1') == '1' ? 'checked' : ''; ?>>
                                    <label for="require_phone">Require Phone Number</label>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="checkbox-group">
                                    <input type="checkbox" id="require_address" name="require_address" value="1" <?php echo ($currentSectionSettings['require_address'] ?? '1') == '1' ? 'checked' : ''; ?>>
                                    <label for="require_address">Require Address</label>
                                </div>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" class="btn-submit">Save Changes</button>
                            </div>
                        </form>
                    </div>
                <?php elseif ($currentSection === 'email'): ?>
                    <div class="settings-section">
                        <h2 class="settings-section-title">Email Settings</h2>
                        <form method="POST" action="settings.php?section=email">
                            <input type="hidden" name="section" value="email">
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="smtp_host">SMTP Host</label>
                                    <input type="text" id="smtp_host" name="smtp_host" value="<?php echo htmlspecialchars($currentSectionSettings['smtp_host'] ?? 'smtp.gmail.com'); ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="smtp_port">SMTP Port</label>
                                    <input type="text" id="smtp_port" name="smtp_port" value="<?php echo htmlspecialchars($currentSectionSettings['smtp_port'] ?? '587'); ?>">
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="smtp_username">SMTP Username</label>
                                    <input type="text" id="smtp_username" name="smtp_username" value="<?php echo htmlspecialchars($currentSectionSettings['smtp_username'] ?? 'noreplybloodbridge@gmail.com'); ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="smtp_password">SMTP Password</label>
                                    <input type="password" id="smtp_password" name="smtp_password" value="<?php echo htmlspecialchars($currentSectionSettings['smtp_password'] ?? 'bnjq miad atej kfot'); ?>">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="smtp_encryption">SMTP Encryption</label>
                                <select id="smtp_encryption" name="smtp_encryption">
                                    <option value="none" <?php echo ($currentSectionSettings['smtp_encryption'] ?? '') === 'none' ? 'selected' : ''; ?>>None</option>
                                    <option value="ssl" <?php echo ($currentSectionSettings['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                                    <option value="tls" <?php echo ($currentSectionSettings['smtp_encryption'] ?? 'tls') === 'tls' ? 'selected' : ''; ?>>TLS</option>
                                </select>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="from_email">From Email</label>
                                    <input type="email" id="from_email" name="from_email" value="<?php echo htmlspecialchars($currentSectionSettings['from_email'] ?? 'noreplybloodbridge@gmail.com'); ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="from_name">From Name</label>
                                    <input type="text" id="from_name" name="from_name" value="<?php echo htmlspecialchars($currentSectionSettings['from_name'] ?? 'Doctorsss'); ?>">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="admin_notification_email">Admin Notification Email</label>
                                <input type="email" id="admin_notification_email" name="admin_notification_email" value="<?php echo htmlspecialchars($currentSectionSettings['admin_notification_email'] ?? 'sarthakkc2072@gmail.com'); ?>">
                                <small class="text-muted">Email address to receive admin notifications</small>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" class="btn-submit">Save Changes</button>
                            </div>
                        </form>
                    </div>
                <?php elseif ($currentSection === 'payment'): ?>
                    <div class="settings-section">
                        <h2 class="settings-section-title">Payment Settings</h2>
                        <form method="POST" action="settings.php?section=payment">
                            <input type="hidden" name="section" value="payment">
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="currency">Currency</label>
                                    <input type="text" id="currency" name="currency" value="<?php echo htmlspecialchars($currentSectionSettings['currency'] ?? 'NPR'); ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="currency_symbol">Currency Symbol</label>
                                    <input type="text" id="currency_symbol" name="currency_symbol" value="<?php echo htmlspecialchars($currentSectionSettings['currency_symbol'] ?? 'Rs.'); ?>" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="payment_methods">Payment Methods</label>
                                <input type="text" id="payment_methods" name="payment_methods" value="<?php echo htmlspecialchars($currentSectionSettings['payment_methods'] ?? 'cash,esewa,khalti'); ?>">
                                <small class="text-muted">Comma-separated list of payment methods (e.g., cash,esewa,khalti)</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="tax_rate">Tax Rate (%)</label>
                                <input type="number" id="tax_rate" name="tax_rate" value="<?php echo htmlspecialchars($currentSectionSettings['tax_rate'] ?? '13'); ?>" min="0" step="0.01">
                            </div>
                            
                            <div class="form-group">
                                <div class="checkbox-group">
                                    <input type="checkbox" id="enable_online_payment" name="enable_online_payment" value="1" <?php echo ($currentSectionSettings['enable_online_payment'] ?? '1') == '1' ? 'checked' : ''; ?>>
                                    <label for="enable_online_payment">Enable Online Payment</label>
                                </div>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" class="btn-submit">Save Changes</button>
                            </div>
                        </form>
                    </div>
                <?php elseif ($currentSection === 'system'): ?>
                    <div class="settings-section">
                        <h2 class="settings-section-title">System Settings</h2>
                        <form method="POST" action="settings.php?section=system">
                            <input type="hidden" name="section" value="system">
                            
                            <div class="form-group">
                                <label for="timezone">Timezone</label>
                                <select id="timezone" name="timezone">
                                    <option value="Asia/Kathmandu" <?php echo ($currentSectionSettings['timezone'] ?? 'Asia/Kathmandu') === 'Asia/Kathmandu' ? 'selected' : ''; ?>>Asia/Kathmandu</option>
                                    <option value="UTC" <?php echo ($currentSectionSettings['timezone'] ?? '') === 'UTC' ? 'selected' : ''; ?>>UTC</option>
                                    <option value="Asia/Kolkata" <?php echo ($currentSectionSettings['timezone'] ?? '') === 'Asia/Kolkata' ? 'selected' : ''; ?>>Asia/Kolkata</option>
                                    <!-- Add more timezone options as needed -->
                                </select>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="date_format">Date Format</label>
                                    <select id="date_format" name="date_format">
                                        <option value="Y-m-d" <?php echo ($currentSectionSettings['date_format'] ?? 'Y-m-d') === 'Y-m-d' ? 'selected' : ''; ?>>YYYY-MM-DD</option>
                                        <option value="d/m/Y" <?php echo ($currentSectionSettings['date_format'] ?? '') === 'd/m/Y' ? 'selected' : ''; ?>>DD/MM/YYYY</option>
                                        <option value="m/d/Y" <?php echo ($currentSectionSettings['date_format'] ?? '') === 'm/d/Y' ? 'selected' : ''; ?>>MM/DD/YYYY</option>
                                        <option value="d-m-Y" <?php echo ($currentSectionSettings['date_format'] ?? '') === 'd-m-Y' ? 'selected' : ''; ?>>DD-MM-YYYY</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="time_format">Time Format</label>
                                    <select id="time_format" name="time_format">
                                        <option value="H:i" <?php echo ($currentSectionSettings['time_format'] ?? 'H:i') === 'H:i' ? 'selected' : ''; ?>>24-hour (14:30)</option>
                                        <option value="h:i A" <?php echo ($currentSectionSettings['time_format'] ?? '') === 'h:i A' ? 'selected' : ''; ?>>12-hour (02:30 PM)</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="checkbox-group">
                                    <input type="checkbox" id="enable_registration" name="enable_registration" value="1" <?php echo ($currentSectionSettings['enable_registration'] ?? '1') == '1' ? 'checked' : ''; ?>>
                                    <label for="enable_registration">Enable User Registration</label>
                                </div>
                            </div>                            
                            <div class="form-group">
                                <div class="checkbox-group">
                                    <input type="checkbox" id="enable_testimonials" name="enable_testimonials" value="1" <?php echo ($currentSectionSettings['enable_testimonials'] ?? '1') == '1' ? 'checked' : ''; ?>>
                                    <label for="enable_testimonials">Enable Testimonials</label>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="checkbox-group">
                                    <input type="checkbox" id="maintenance_mode" name="maintenance_mode" value="1" <?php echo ($currentSectionSettings['maintenance_mode'] ?? '0') == '1' ? 'checked' : ''; ?>>
                                    <label for="maintenance_mode">Maintenance Mode</label>
                                </div>
                                <small class="text-muted">When enabled, the site will display a maintenance message to visitors</small>
                            </div>
                            
                            <div class="form-group">
                                <div class="checkbox-group">
                                    <input type="checkbox" id="debug_mode" name="debug_mode" value="1" <?php echo ($currentSectionSettings['debug_mode'] ?? '0') == '1' ? 'checked' : ''; ?>>
                                    <label for="debug_mode">Debug Mode</label>
                                </div>
                                <small class="text-muted">When enabled, detailed error messages will be displayed</small>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" class="btn-submit">Save Changes</button>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>