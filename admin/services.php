<?php
session_start();
require_once 'includes/auth.php';
require_once 'includes/Service.php';
require_admin();

// Initialize Service class
$serviceHandler = new Service();

// Handle form submission
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['action'])) {
            if ($_POST['action'] === 'add') {
                $serviceHandler->createService($_POST);
                $message = 'Service added successfully!';
                $messageType = 'success';
                header('Location: services.php');
                exit;
            } elseif ($_POST['action'] === 'edit') {
                $serviceHandler->updateService($_POST['id'], $_POST);
                $message = 'Service updated successfully!';
                $messageType = 'success';
                header('Location: services.php');
                exit;
            }
        }
    } catch (Exception $e) {
        $message = $e->getMessage();
        $messageType = 'error';
    }
}

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    try {
        $serviceHandler->deleteService($_GET['id']);
        $message = 'Service deleted successfully!';
        $messageType = 'success';
    } catch (Exception $e) {
        $message = $e->getMessage();
        $messageType = 'error';
    }
}

// Determine if we're adding, editing, or listing services
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$serviceId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get service data
if ($action === 'edit' && $serviceId > 0) {
    $serviceData = $serviceHandler->getServiceById($serviceId);
    if (!$serviceData) {
        $message = 'Service not found!';
        $messageType = 'error';
        $action = 'list';
    }
}

// Get all services for listing
$services = $action === 'list' ? $serviceHandler->getAllServices() : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Services - Doctors At Door Step</title>
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
        
        .add-new-btn {
            padding: 8px 16px;
            background-color: var(--primary-color);
            color: var(--white);
            border: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s;
        }
        
        .add-new-btn:hover {
            background-color: var(--dark-color);
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
        
        /* Services Table */
        .services-table {
            width: 100%;
            background-color: var(--white);
            border-radius: 10px;
            box-shadow: var(--box-shadow);
            overflow: hidden;
        }
        
        .services-table table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .services-table th, .services-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .services-table th {
            background-color: var(--light-color);
            font-weight: 600;
        }
        
        .services-table tr:last-child td {
            border-bottom: none;
        }
        
        .services-table tr:hover {
            background-color: rgba(44, 123, 229, 0.05);
        }
        
        .service-icon {
            width: 40px;
            height: 40px;
            background-color: rgba(44, 123, 229, 0.1);
            color: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .service-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .status-active {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }
        
        .status-inactive {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--error-color);
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        
        .btn-edit, .btn-delete {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .btn-edit {
            background-color: rgba(44, 123, 229, 0.1);
            color: var(--primary-color);
        }
        
        .btn-edit:hover {
            background-color: var(--primary-color);
            color: var(--white);
        }
        
        .btn-delete {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--error-color);
        }
        
        .btn-delete:hover {
            background-color: var(--error-color);
            color: var(--white);
        }
        
        /* Service Form */
        .service-form {
            background-color: var(--white);
            border-radius: 10px;
            box-shadow: var(--box-shadow);
            padding: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        
        .form-group input, .form-group textarea, .form-group select {
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

        .form-group input[type="number"] {
            -moz-appearance: textfield;
        }

        .form-group input[type="number"]::-webkit-outer-spin-button,
        .form-group input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .text-center {
            text-align: center;
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
            
            .services-table {
                overflow-x: auto;
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
            <a href="services.php" class="menu-item active">
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
            <a href="settings.php" class="menu-item">
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
            <h1 class="page-title">
                <?php if ($action === 'add'): ?>
                    Add New Service
                <?php elseif ($action === 'edit'): ?>
                    Edit Service
                <?php else: ?>
                    Manage Services
                <?php endif; ?>
            </h1>
            
            <?php if ($action === 'list'): ?>
                <a href="services.php?action=add" class="add-new-btn">
                    <i class="fas fa-plus"></i> Add New Service
                </a>
            <?php endif; ?>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($action === 'list'): ?>
            <!-- Services List -->
            <div class="services-table">
                <table>
                    <thead>
                        <tr>
                            <th>Icon</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($services)): ?>
                            <tr>
                                <td colspan="5" class="text-center">No services found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($services as $service): ?>
                                <tr>
                                    <td>
                                        <div class="service-icon">
                                            <i class="fas <?php echo htmlspecialchars($service['image']); ?>"></i>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($service['title']); ?></td>
                                    <td><?php echo htmlspecialchars($service['description']); ?></td>
                                    <td>
                                        <span class="service-status status-<?php echo $service['is_active'] ? 'active' : 'inactive'; ?>">
                                            <?php echo $service['is_active'] ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="services.php?action=edit&id=<?php echo $service['id']; ?>" class="btn-edit">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="#" class="btn-delete" onclick="confirmDelete(<?php echo $service['id']; ?>, '<?php echo htmlspecialchars($service['title']); ?>')">
                                                <i class="fas fa-trash"></i> Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <!-- Service Form -->
            <div class="service-form">
                <form method="POST" action="services.php" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="<?php echo $action; ?>">
                    <?php if ($action === 'edit'): ?>
                        <input type="hidden" name="id" value="<?php echo $serviceData['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="title">Service Name</label>
                        <input type="text" id="title" name="title" value="<?php echo $action === 'edit' ? htmlspecialchars($serviceData['title']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" required><?php echo $action === 'edit' ? htmlspecialchars($serviceData['description']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="icon">Icon (Font Awesome Class)</label>
                        <input type="text" id="icon" name="image" value="<?php echo $action === 'edit' ? htmlspecialchars($serviceData['image']) : ''; ?>" required>
                        <small class="text-muted">Example: fa-user-nurse, fa-heartbeat, etc.</small>
                    </div>
                
                    
                    <div class="form-group">
                        <label for="price">Price (NPR)</label>
                        <input type="number" id="price" name="price" step="0.01" min="0" value="<?php echo $action === 'edit' ? $serviceData['price'] : '0.00'; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="duration">Duration (minutes)</label>
                        <input type="number" id="duration" name="duration" min="0" value="<?php echo $action === 'edit' ? $serviceData['duration'] : '60'; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" required>
                            <option value="active" <?php echo ($action === 'edit' && $serviceData['is_active']) ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo ($action === 'edit' && !$serviceData['is_active']) ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                    
                    <div class="form-actions">
                        <a href="services.php" class="btn-cancel">Cancel</a>
                        <button type="submit" class="btn-submit">
                            <?php echo $action === 'add' ? 'Add Service' : 'Update Service'; ?>
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
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
        
        // Confirm delete
        function confirmDelete(id, name) {
            if (confirm(`Are you sure you want to delete the service "${name}"?`)) {
                // In a real app, this would submit a form or make an AJAX request
                window.location.href = `services.php?action=delete&id=${id}`;
            }
        }
    </script>
</body>
</html>
