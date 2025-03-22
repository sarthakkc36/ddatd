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

require_once '../includes/Blog.php';

// Initialize Blog handler
$blogHandler = new Blog();
$message = '';
$messageType = '';

// Handle post status change
if (isset($_GET['action']) && $_GET['action'] === 'change_status' && isset($_GET['id']) && isset($_GET['status'])) {
    $post_id = (int)$_GET['id'];
    $new_status = $_GET['status'];
    
    try {
        if ($blogHandler->updatePostStatus($post_id, $new_status)) {
            $message = "Post status updated successfully!";
            $messageType = 'success';
        } else {
            $message = "Failed to update post status.";
            $messageType = 'error';
        }
    } catch (Exception $e) {
        $message = $e->getMessage();
        $messageType = 'error';
    }
}

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $post_id = (int)$_GET['id'];
    
    try {
        if ($blogHandler->deletePost($post_id)) {
            $message = "Post deleted successfully!";
            $messageType = 'success';
        } else {
            $message = "Failed to delete post.";
            $messageType = 'error';
        }
    } catch (Exception $e) {
        $message = $e->getMessage();
        $messageType = 'error';
    }
}

// Get search parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$category = isset($_GET['category']) ? (int)$_GET['category'] : 0;

// Get pagination parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;

// Get posts based on filters
$posts = $blogHandler->getAdminPosts($search, $status, $category, $page, $per_page);
$total_posts = $blogHandler->countAdminPosts($search, $status, $category);
$total_pages = ceil($total_posts / $per_page);

// Get all categories for filter dropdown
$categories = $blogHandler->getCategories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Blog Posts - HomeCare</title>
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
            --warning-color: #F59E0B;
            --info-color: #3B82F6;
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
        
        /* Filter Section */
        .filter-section {
            background-color: var(--white);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: var(--box-shadow);
        }
        
        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .filter-form .form-group {
            flex: 1;
            min-width: 200px;
            margin-bottom: 0;
        }
        
        .filter-form .form-actions {
            margin-top: 0;
            align-self: flex-end;
        }
        
        /* Blog Posts Table */
        .posts-table {
            width: 100%;
            background-color: var(--white);
            border-radius: 10px;
            box-shadow: var(--box-shadow);
            overflow: hidden;
        }
        
        .posts-table table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .posts-table th, .posts-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .posts-table th {
            background-color: var(--light-color);
            font-weight: 600;
        }
        
        .posts-table tr:last-child td {
            border-bottom: none;
        }
        
        .posts-table tr:hover {
            background-color: rgba(44, 123, 229, 0.05);
        }
        
        .post-title {
            font-weight: 500;
        }
        
        .post-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }
        
        .status-published {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }
        
        .status-draft {
            background-color: rgba(107, 122, 153, 0.1);
            color: var(--secondary-color);
        }
        
        .status-archived {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--error-color);
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        
        .btn-view, .btn-edit, .btn-delete, .btn-status {
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
        
        .btn-status {
            background-color: rgba(245, 158, 11, 0.1);
            color: var(--warning-color);
        }
        
        .btn-status:hover {
            background-color: var(--warning-color);
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
        
        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 30px;
        }
        
        .pagination-item {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 35px;
            height: 35px;
            padding: 0 10px;
            background-color: var(--white);
            color: var(--dark-color);
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            box-shadow: var(--box-shadow-sm);
            transition: all 0.3s;
        }
        
        .pagination-item:hover {
            background-color: var(--light-color);
        }
        
        .pagination-item.active {
            background-color: var(--primary-color);
            color: var(--white);
        }
        
        .pagination-ellipsis {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 35px;
            height: 35px;
        }
        
        /* Form Elements */
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        
        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            font-family: 'Poppins', sans-serif;
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-start;
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
        
        /* Empty State */
        .empty-state {
            padding: 50px 20px;
            text-align: center;
            background-color: var(--white);
            border-radius: 10px;
            box-shadow: var(--box-shadow);
        }
        
        .empty-state i {
            font-size: 48px;
            color: var(--secondary-color);
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .empty-state h3 {
            font-size: 20px;
            margin-bottom: 10px;
        }
        
        .empty-state p {
            color: var(--secondary-color);
            margin-bottom: 20px;
        }
        
        /* Dropdown styles */
        .dropdown {
            position: relative;
            display: inline-block;
        }
        
        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            background-color: var(--white);
            min-width: 160px;
            box-shadow: var(--box-shadow);
            border-radius: 5px;
            z-index: 1;
        }
        
        .dropdown-menu.show {
            display: block;
        }
        
        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 15px;
            color: var(--dark-color);
            text-decoration: none;
            font-size: 14px;
            transition: all 0.2s;
        }
        
        .dropdown-item:hover {
            background-color: var(--light-color);
        }
        
        .dropdown-item i {
            width: 16px;
            text-align: center;
        }
        
        .text-danger {
            color: var(--error-color) !important;
        }
        
        .text-danger:hover {
            background-color: rgba(239, 68, 68, 0.1) !important;
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
            
            .posts-table {
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h1><img src="../images/logo.png" alt="Doctors At Door Step" width="100px" height="100px" /></h1>
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
                <i class="fas fa-blog"></i> Blog
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
            <h1 class="page-title">Manage Blog Posts</h1>
            <a href="blog-edit.php" class="add-new-btn">
                <i class="fas fa-plus"></i> Add New Post
            </a>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <!-- Filter Section -->
        <div class="filter-section">
            <form method="GET" action="blog.php" class="filter-form">
                <div class="form-group">
                    <label for="search">Search</label>
                    <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by title...">
                </div>
                
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="published" <?php echo $status === 'published' ? 'selected' : ''; ?>>Published</option>
                        <option value="draft" <?php echo $status === 'draft' ? 'selected' : ''; ?>>Draft</option>
                        <option value="archived" <?php echo $status === 'archived' ? 'selected' : ''; ?>>Archived</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="category">Category</label>
                    <select id="category" name="category">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo $category == $cat['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn-submit">Filter</button>
                    <a href="blog.php" class="btn-cancel">Reset</a>
                </div>
            </form>
        </div>
        
        <!-- Posts Table -->
        <?php if (empty($posts)): ?>
            <div class="empty-state">
                <i class="fas fa-newspaper"></i>
                <h3>No posts found</h3>
                <p>Start creating engaging content for your audience.</p>
                <a href="blog-edit.php" class="btn-submit">Add First Post</a>
            </div>
        <?php else: ?>
            <div class="posts-table">
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Categories</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Views</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($posts as $post): ?>
                            <tr>
                                <td class="post-title"><?php echo htmlspecialchars($post['title']); ?></td>
                                <td>
                                    <?php
                                    if ($post['author_id']) {
                                        $author = $blogHandler->getAuthor($post['author_id']);
                                        echo $author ? htmlspecialchars($author['username']) : 'Unknown';
                                    } else {
                                        echo 'Unknown';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $post_categories = $blogHandler->getPostCategories($post['id']);
                                    $category_names = array_map(function($cat) {
                                        return htmlspecialchars($cat['name']);
                                    }, $post_categories);
                                    echo implode(', ', $category_names);
                                    ?>
                                </td>
                                <td>
                                    <span class="post-status status-<?php echo $post['status']; ?>">
                                        <?php echo ucfirst($post['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($post['published_at']): ?>
                                        <?php echo date('M j, Y', strtotime($post['published_at'])); ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td><?php echo number_format($post['views']); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <?php if ($post['status'] === 'published'): ?>
                                            <a href="../blog-post.php?slug=<?php echo $post['slug']; ?>" target="_blank" class="btn-view">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        <?php endif; ?>
                                        
                                        <a href="blog-edit.php?id=<?php echo $post['id']; ?>" class="btn-edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        
                                        <div class="dropdown">
                                            <button class="btn-status" onclick="toggleDropdown(<?php echo $post['id']; ?>)">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <div class="dropdown-menu" id="dropdown-<?php echo $post['id']; ?>">
                                                <?php if ($post['status'] !== 'published'): ?>
                                                    <a href="blog.php?action=change_status&id=<?php echo $post['id']; ?>&status=published" class="dropdown-item">
                                                        <i class="fas fa-check-circle"></i> Publish
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <?php if ($post['status'] !== 'draft'): ?>
                                                    <a href="blog.php?action=change_status&id=<?php echo $post['id']; ?>&status=draft" class="dropdown-item">
                                                        <i class="fas fa-pencil-alt"></i> Mark as Draft
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <?php if ($post['status'] !== 'archived'): ?>
                                                    <a href="blog.php?action=change_status&id=<?php echo $post['id']; ?>&status=archived" class="dropdown-item">
                                                        <i class="fas fa-archive"></i> Archive
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <a href="#" onclick="confirmDelete(<?php echo $post['id']; ?>, '<?php echo htmlspecialchars($post['title']); ?>')" class="dropdown-item text-danger">
                                                    <i class="fas fa-trash"></i> Delete
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" class="pagination-item">
                            <i class="fas fa-chevron-left"></i> Previous
                        </a>
                    <?php endif; ?>

                    <?php
                    $start_page = max(1, $page - 2);
                    $end_page = min($total_pages, $page + 2);

                    if ($start_page > 1) {
                        echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => 1])) . '" class="pagination-item">1</a>';
                        if ($start_page > 2) {
                            echo '<span class="pagination-ellipsis">...</span>';
                        }
                    }

                    for ($i = $start_page; $i <= $end_page; $i++) {
                        $active = $i == $page ? 'active' : '';
                        echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => $i])) . '" class="pagination-item ' . $active . '">' . $i . '</a>';
                    }

                    if ($end_page < $total_pages) {
                        if ($end_page < $total_pages - 1) {
                            echo '<span class="pagination-ellipsis">...</span>';
                        }
                        echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => $total_pages])) . '" class="pagination-item">' . $total_pages . '</a>';
                    }
                    ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" class="pagination-item">
                            Next <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <script>
        // Toggle dropdown menu for post actions
        function toggleDropdown(postId) {
            const dropdown = document.getElementById(`dropdown-${postId}`);
            
            // Close all other dropdowns first
            document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                if (menu.id !== `dropdown-${postId}`) {
                    menu.classList.remove('show');
                }
            });
            
            // Toggle this dropdown
            dropdown.classList.toggle('show');
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function closeDropdown(e) {
                if (!e.target.closest('.dropdown')) {
                    dropdown.classList.remove('show');
                    document.removeEventListener('click', closeDropdown);
                }
            });
        }
        
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
            if (confirm(`Are you sure you want to delete the post "${title}"?`)) {
                window.location.href = `blog.php?action=delete&id=${id}`;
            }
        }
    </script>
</body>
</html>