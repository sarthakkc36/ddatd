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

// Placeholder for blog functionality
$message = '';
$messageType = '';

if (isset($_GET['action']) && $_GET['action'] === 'add') {
    $action = 'add';
} elseif (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $action = 'edit';
    $postId = (int)$_GET['id'];
    // In a real app, you would fetch the post data here
} else {
    $action = 'list';
}

// Sample blog posts for demonstration
$blogPosts = [
    [
        'id' => 1,
        'title' => 'The Importance of Regular Health Check-ups',
        'excerpt' => 'Regular health check-ups are essential for maintaining good health and detecting potential issues early.',
        'author' => 'Dr. Smith',
        'date' => '2025-03-15',
        'status' => 'published'
    ],
    [
        'id' => 2,
        'title' => 'Understanding Telemedicine: Benefits and Limitations',
        'excerpt' => 'Telemedicine has revolutionized healthcare delivery, especially during the pandemic.',
        'author' => 'Dr. Johnson',
        'date' => '2025-03-10',
        'status' => 'published'
    ],
    [
        'id' => 3,
        'title' => 'Nutrition Tips for a Stronger Immune System',
        'excerpt' => 'Your diet plays a crucial role in maintaining a healthy immune system.',
        'author' => 'Dr. Patel',
        'date' => '2025-03-05',
        'status' => 'published'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Blog Posts - Doctors At Door Step</title>
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
        
        /* Blog Posts Table */
        .blog-table {
            width: 100%;
            background-color: var(--white);
            border-radius: 10px;
            box-shadow: var(--box-shadow);
            overflow: hidden;
        }
        
        .blog-table table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .blog-table th, .blog-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .blog-table th {
            background-color: var(--light-color);
            font-weight: 600;
        }
        
        .blog-table tr:last-child td {
            border-bottom: none;
        }
        
        .blog-table tr:hover {
            background-color: rgba(44, 123, 229, 0.05);
        }
        
        .post-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .status-published {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }
        
        .status-draft {
            background-color: rgba(107, 122, 153, 0.1);
            color: var(--secondary-color);
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        
        .btn-edit, .btn-delete, .btn-view {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .btn-view {
            background-color: rgba(107, 122, 153, 0.1);
            color: var(--secondary-color);
        }
        
        .btn-view:hover {
            background-color: var(--secondary-color);
            color: var(--white);
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
        
        /* Blog Post Form */
        .blog-form {
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
            min-height: 200px;
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
            
            .blog-table {
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h1>Doctors At Door Step</h1>
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
            <a href="blog.php" class="menu-item active">
                <i class="fas fa-blog"></i> Blog Posts
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
                    Add New Blog Post
                <?php elseif ($action === 'edit'): ?>
                    Edit Blog Post
                <?php else: ?>
                    Manage Blog Posts
                <?php endif; ?>
            </h1>
            
            <?php if ($action === 'list'): ?>
                <a href="blog.php?action=add" class="add-new-btn">
                    <i class="fas fa-plus"></i> Add New Post
                </a>
            <?php endif; ?>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($action === 'list'): ?>
            <!-- Blog Posts List -->
            <div class="blog-table">
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Excerpt</th>
                            <th>Author</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($blogPosts)): ?>
                            <tr>
                                <td colspan="6" class="text-center">No blog posts found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($blogPosts as $post): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($post['title']); ?></td>
                                    <td><?php echo htmlspecialchars($post['excerpt']); ?></td>
                                    <td><?php echo htmlspecialchars($post['author']); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($post['date'])); ?></td>
                                    <td>
                                        <span class="post-status status-<?php echo $post['status']; ?>">
                                            <?php echo ucfirst($post['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="../blog.php?post=<?php echo $post['id']; ?>" class="btn-view" target="_blank">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a href="blog.php?action=edit&id=<?php echo $post['id']; ?>" class="btn-edit">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="#" class="btn-delete" onclick="confirmDelete(<?php echo $post['id']; ?>, '<?php echo htmlspecialchars($post['title']); ?>')">
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
            <!-- Blog Post Form -->
            <div class="blog-form">
                <form method="POST" action="blog.php">
                    <input type="hidden" name="action" value="<?php echo $action; ?>">
                    <?php if ($action === 'edit'): ?>
                        <input type="hidden" name="id" value="<?php echo $postId; ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="title">Post Title</label>
                        <input type="text" id="title" name="title" value="<?php echo $action === 'edit' ? htmlspecialchars($blogPosts[0]['title']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="excerpt">Excerpt</label>
                        <textarea id="excerpt" name="excerpt" rows="2" required><?php echo $action === 'edit' ? htmlspecialchars($blogPosts[0]['excerpt']) : ''; ?></textarea>
                        <small class="text-muted">A short summary of the post (max 160 characters)</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="content">Content</label>
                        <textarea id="content" name="content" required><?php echo $action === 'edit' ? 'Sample content for this blog post. This would be replaced with actual content in a real application.' : ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="author">Author</label>
                        <input type="text" id="author" name="author" value="<?php echo $action === 'edit' ? htmlspecialchars($blogPosts[0]['author']) : 'Dr. Admin'; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" required>
                            <option value="published" <?php echo ($action === 'edit' && $blogPosts[0]['status'] === 'published') ? 'selected' : ''; ?>>Published</option>
                            <option value="draft" <?php echo ($action === 'edit' && $blogPosts[0]['status'] === 'draft') ? 'selected' : ''; ?>>Draft</option>
                        </select>
                    </div>
                    
                    <div class="form-actions">
                        <a href="blog.php" class="btn-cancel">Cancel</a>
                        <button type="submit" class="btn-submit">
                            <?php echo $action === 'add' ? 'Add Post' : 'Update Post'; ?>
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
        function confirmDelete(id, title) {
            if (confirm(`Are you sure you want to delete the blog post "${title}"?`)) {
                // In a real app, this would submit a form or make an AJAX request
                window.location.href = `blog.php?action=delete&id=${id}`;
            }
        }
    </script>
</body>
</html>
