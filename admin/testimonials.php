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

require_once '../includes/Testimonials.php';

// Initialize Testimonials handler
$testimonialHandler = new Testimonials();
$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['action'])) {
            $data = [
                'name' => $_POST['name'],
                'position' => $_POST['position'],
                'content' => $_POST['content'],
                'rating' => $_POST['rating'],
                'is_active' => $_POST['status'] === 'active',
                'display_order' => intval($_POST['display_order'] ?? 0)
            ];

            // Handle photo upload
            if (!empty($_FILES['client_photo']['name'])) {
                $targetDir = "../uploads/testimonials/";
                if (!file_exists($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }

                $fileExtension = strtolower(pathinfo($_FILES['client_photo']['name'], PATHINFO_EXTENSION));
                $fileName = uniqid('testimonial_') . '.' . $fileExtension;
                $targetPath = $targetDir . $fileName;

                if (move_uploaded_file($_FILES['client_photo']['tmp_name'], $targetPath)) {
                    $data['photo_path'] = 'uploads/testimonials/' . $fileName;
                } else {
                    throw new Exception("Failed to upload photo");
                }
            }

            if ($_POST['action'] === 'add') {
                $testimonialHandler->createTestimonial($data);
                $message = 'Testimonial added successfully!';
                $messageType = 'success';
            } elseif ($_POST['action'] === 'edit') {
                $testimonialHandler->updateTestimonial($_POST['id'], $data);
                $message = 'Testimonial updated successfully!';
                $messageType = 'success';
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
        $testimonialHandler->deleteTestimonial($_GET['id']);
        $message = 'Testimonial deleted successfully!';
        $messageType = 'success';
    } catch (Exception $e) {
        $message = $e->getMessage();
        $messageType = 'error';
    }
}

// Determine if we're adding, editing, or listing testimonials
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$testimonialId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get testimonial data for editing
$testimonialData = [];
if ($action === 'edit' && $testimonialId > 0) {
    $testimonialData = $testimonialHandler->getTestimonialById($testimonialId);
    
    if (!$testimonialData) {
        $message = 'Testimonial not found!';
        $messageType = 'error';
        $action = 'list';
    }
}

// Get all testimonials for listing
$testimonials = $testimonialHandler->getAllTestimonials();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Testimonials - HomeCare</title>
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
        
        /* Testimonials Table */
        .testimonials-table {
            width: 100%;
            background-color: var(--white);
            border-radius: 10px;
            box-shadow: var(--box-shadow);
            overflow: hidden;
        }
        
        .testimonials-table table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .testimonials-table th, .testimonials-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .testimonials-table th {
            background-color: var(--light-color);
            font-weight: 600;
        }
        
        .testimonials-table tr:last-child td {
            border-bottom: none;
        }
        
        .testimonials-table tr:hover {
            background-color: rgba(44, 123, 229, 0.05);
        }
        
        .testimonial-avatar {
            width: 50px;
            height: 50px;
            background-color: rgba(44, 123, 229, 0.1);
            color: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            overflow: hidden;
        }
        
        .testimonial-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .testimonial-status {
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
        
        .rating {
            color: #FFD700;
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
        
        /* Testimonial Form */
        .testimonial-form {
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
        
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="number"],
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            font-family: 'Poppins', sans-serif;
        }
        
        .form-group textarea {
            min-height: 150px;
            resize: vertical;
        }
        
        .rating-input {
            display: flex;
            gap: 10px;
        }
        
        .rating-input label {
            cursor: pointer;
            font-size: 24px;
            color: #ccc;
        }
        
        .rating-input input[type="radio"] {
            display: none;
        }
        
        .rating-input label:hover,
        .rating-input label:hover ~ label,
        .rating-input input[type="radio"]:checked ~ label {
            color: #FFD700;
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
        
        .text-center {
            text-align: center;
        }
        
        /* Preview current image */
        .current-image {
            margin-top: 10px;
            max-width: 150px;
            border-radius: 5px;
            overflow: hidden;
        }
        
        .current-image img {
            width: 100%;
            height: auto;
            display: block;
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
            
            .testimonials-table {
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
            <a href="services.php" class="menu-item">
                <i class="fas fa-hand-holding-medical"></i> Services
            </a>
            <a href="team.php" class="menu-item">
                <i class="fas fa-user-md"></i> Team Members
            </a>
            <a href="testimonials.php" class="menu-item active">
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
                    Add New Testimonial
                <?php elseif ($action === 'edit'): ?>
                    Edit Testimonial
                <?php else: ?>
                    Manage Testimonials
                <?php endif; ?>
            </h1>
            
            <?php if ($action === 'list'): ?>
                <a href="testimonials.php?action=add" class="add-new-btn">
                    <i class="fas fa-plus"></i> Add New Testimonial
                </a>
            <?php endif; ?>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($action === 'list'): ?>
            <!-- Testimonials List -->
            <div class="testimonials-table">
                <table>
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Position</th>
                            <th>Testimonial</th>
                            <th>Rating</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($testimonials)): ?>
                            <tr>
                                <td colspan="6" class="text-center">No testimonials found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($testimonials as $testimonial): ?>
                                <tr>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <div class="testimonial-avatar">
                                                <?php if (!empty($testimonial['photo_path'])): ?>
                                                    <img src="../<?php echo htmlspecialchars($testimonial['photo_path']); ?>" 
                                                         alt="<?php echo htmlspecialchars($testimonial['name']); ?>">
                                                <?php else: ?>
                                                    <i class="fas fa-user"></i>
                                                <?php endif; ?>
                                            </div>
                                            <?php echo htmlspecialchars($testimonial['name']); ?>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($testimonial['position']); ?></td>
                                    <td><?php echo htmlspecialchars(substr($testimonial['content'], 0, 100)) . '...'; ?></td>
                                    <td>
                                        <div class="rating">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star<?php echo $i <= $testimonial['rating'] ? '' : '-o'; ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="testimonial-status status-<?php echo $testimonial['is_active'] ? 'active' : 'inactive'; ?>">
                                            <?php echo $testimonial['is_active'] ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="testimonials.php?action=edit&id=<?php echo $testimonial['id']; ?>" class="btn-edit">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="#" class="btn-delete" onclick="confirmDelete(<?php echo $testimonial['id']; ?>, '<?php echo htmlspecialchars($testimonial['name']); ?>')">
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
            <!-- Testimonial Form -->
            <div class="testimonial-form">
                <form method="POST" action="testimonials.php" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="<?php echo $action; ?>">
                    <?php if ($action === 'edit'): ?>
                        <input type="hidden" name="id" value="<?php echo $testimonialData['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="name">Client Name</label>
                        <input type="text" id="name" name="name" value="<?php echo $action === 'edit' ? htmlspecialchars($testimonialData['name']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="position">Position/Title</label>
                        <input type="text" id="position" name="position" value="<?php echo $action === 'edit' ? htmlspecialchars($testimonialData['position']) : ''; ?>" required>
                        <small class="text-muted">E.g., Patient, Mother of two, Family Member</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="content">Testimonial Content</label>
                        <textarea id="content" name="content" required><?php echo $action === 'edit' ? htmlspecialchars($testimonialData['content']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="client_photo">Client Photo</label>
                        <input type="file" id="client_photo" name="client_photo" accept="image/*">
                        <small class="text-muted">Upload a photo (JPG, PNG, GIF). Max size: 5MB.</small>
                        <?php if ($action === 'edit' && !empty($testimonialData['photo_path'])): ?>
                            <div class="current-image">
                                <p>Current photo:</p>
                                <img src="../<?php echo htmlspecialchars($testimonialData['photo_path']); ?>" alt="Client photo">
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label>Rating</label>
                        <div class="rating-input">
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <input type="radio" name="rating" id="star<?php echo $i; ?>" value="<?php echo $i; ?>" <?php echo ($action === 'edit' && $testimonialData['rating'] == $i) ? 'checked' : ''; ?> <?php echo ($action === 'add' && $i === 5) ? 'checked' : ''; ?>>
                                <label for="star<?php echo $i; ?>"><i class="fas fa-star"></i></label>
                            <?php endfor; ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="display_order">Display Order</label>
                        <input type="number" id="display_order" name="display_order" value="<?php echo $action === 'edit' ? htmlspecialchars($testimonialData['display_order']) : '0'; ?>" min="0">
                        <small class="text-muted">Lower numbers appear first. Use 0 for automatic ordering.</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" required>
                            <option value="active" <?php echo ($action === 'edit' && $testimonialData['is_active']) ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo ($action === 'edit' && !$testimonialData['is_active']) ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                    
                    <div class="form-actions">
                        <a href="testimonials.php" class="btn-cancel">Cancel</a>
                        <button type="submit" class="btn-submit">
                            <?php echo $action === 'add' ? 'Add Testimonial' : 'Update Testimonial'; ?>
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
            if (confirm(`Are you sure you want to delete the testimonial from "${name}"?`)) {
                window.location.href = `testimonials.php?action=delete&id=${id}`;
            }
        }

        // Star rating
        const stars = document.querySelectorAll('.rating-input label');
        stars.forEach(star => {
            star.addEventListener('mouseover', function() {
                this.style.transform = 'scale(1.2)';
            });
            
            star.addEventListener('mouseout', function() {
                this.style.transform = 'scale(1)';
            });
        });
    </script>
</body>
</html>