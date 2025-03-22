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

// Get post ID from URL if editing
$postId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$action = $postId > 0 ? 'edit' : 'add';

// Get post data if editing
$postData = [];
if ($action === 'edit') {
    $postData = $blogHandler->getAdminPostById($postId);
    if (!$postData) {
        $message = 'Post not found!';
        $messageType = 'error';
        header('Location: blog-management.php');
        exit;
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = [
            'title' => trim($_POST['title']),
            'slug' => trim($_POST['slug'] ?? ''),
            'excerpt' => trim($_POST['excerpt']),
            'content' => $_POST['content'],
            'status' => $_POST['status'],
            'category_ids' => isset($_POST['categories']) ? $_POST['categories'] : [],
            'tag_ids' => isset($_POST['tags']) ? $_POST['tags'] : [],
            'author_id' => $_SESSION['admin_id'] ?? null,
        ];

        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = $blogHandler->generateSlug($data['title']);
        }

        // Handle featured image upload
        if (!empty($_FILES['featured_image']['name'])) {
            $targetDir = "../uploads/blog/";
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            $fileExtension = strtolower(pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION));
            $fileName = 'post_' . time() . '.' . $fileExtension;
            $targetPath = $targetDir . $fileName;

            if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $targetPath)) {
                $data['featured_image'] = 'uploads/blog/' . $fileName;
            } else {
                throw new Exception("Failed to upload featured image");
            }
        }

        // Set published_at date if needed
        if ($data['status'] === 'published') {
            $data['published_at'] = date('Y-m-d H:i:s');
        }

        if ($action === 'add') {
            $newPostId = $blogHandler->addPost($data);
            if ($newPostId) {
                $message = 'Post added successfully!';
                $messageType = 'success';
                header('Location: blog-management.php');
                exit;
            } else {
                throw new Exception("Failed to add post");
            }
        } else {
            $result = $blogHandler->updatePost($postId, $data);
            if ($result) {
                $message = 'Post updated successfully!';
                $messageType = 'success';
                // Refresh post data
                $postData = $blogHandler->getAdminPostById($postId);
            } else {
                throw new Exception("Failed to update post");
            }
        }
    } catch (Exception $e) {
        $message = $e->getMessage();
        $messageType = 'error';
    }
}

// Get categories and tags for dropdowns
$categories = $blogHandler->getCategories();
$tags = $blogHandler->getTags();

// Get post categories and tags if editing
$postCategories = [];
$postTags = [];
if ($action === 'edit') {
    $postCategories = $blogHandler->getPostCategories($postId);
    $postTags = $blogHandler->getPostTags($postId);
    
    // Convert to arrays of IDs for easy checking in the form
    $postCategoryIds = array_column($postCategories, 'id');
    $postTagIds = array_column($postTags, 'id');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $action === 'add' ? 'Add New Post' : 'Edit Post'; ?> - Doctors At Door Step</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Include TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#content',
            height: 500,
            menubar: true,
            plugins: [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table paste code help wordcount'
            ],
            toolbar: 'undo redo | formatselect | ' +
                'bold italic backcolor | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'removeformat | help',
            content_style: 'body { font-family: Poppins, sans-serif; font-size: 16px; }'
        });
    </script>
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
        
        /* Post Form */
        .post-form-wrapper {
            display: grid;
            grid-template-columns: 7fr 3fr;
            gap: 20px;
        }
        
        .post-form-main, .post-form-sidebar {
            background-color: var(--white);
            border-radius: 10px;
            box-shadow: var(--box-shadow);
            padding: 20px;
        }
        
        .form-section {
            margin-bottom: 30px;
        }
        
        .form-section-title {
            font-size: 18px;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        .form-group input, 
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
        
        .form-group select[multiple] {
            height: 150px;
        }
        
        /* Featured Image Preview */
        .featured-image-preview {
            margin-top: 10px;
            max-width: 100%;
            border-radius: 5px;
            overflow: hidden;
        }
        
        .featured-image-preview img {
            max-width: 100%;
            height: auto;
            display: block;
        }
        
        /* Checkbox and Radio Styles */
        .checkbox-group {
            margin-top: 10px;
        }
        
        .checkbox-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .checkbox-item input[type="checkbox"] {
            width: auto;
            margin-right: 10px;
        }
        
        /* Form Actions */
        .form-actions {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-top: 20px;
        }
        
        .btn-group {
            display: flex;
            gap: 10px;
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
        
        .btn-draft {
            padding: 10px 20px;
            background-color: var(--secondary-color);
            color: var(--white);
            border: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-draft:hover {
            background-color: #5a6883;
        }
        
        .btn-publish {
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
        
        .btn-publish:hover {
            background-color: var(--dark-color);
        }
        
        /* Form Helpers */
        .text-muted {
            color: var(--secondary-color);
            font-size: 0.85rem;
            margin-top: 0.25rem;
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
            
            .post-form-wrapper {
                grid-template-columns: 1fr;
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
            <a href="blog-management.php" class="menu-item active">
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
            <h1 class="page-title"><?php echo $action === 'add' ? 'Add New Post' : 'Edit Post'; ?></h1>
            <div class="btn-group">
                <a href="blog-management.php" class="btn-cancel">
                    <i class="fas fa-arrow-left"></i> Back to Posts
                </a>
            </div>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] . ($action === 'edit' ? '?id=' . $postId : ''); ?>" enctype="multipart/form-data">
            <div class="post-form-wrapper">
                <!-- Main Content Section -->
                <div class="post-form-main">
                    <div class="form-section">
                        <div class="form-group">
                            <label for="title">Post Title</label>
                            <input type="text" id="title" name="title" value="<?php echo isset($postData['title']) ? htmlspecialchars($postData['title']) : ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="slug">Slug</label>
                            <input type="text" id="slug" name="slug" value="<?php echo isset($postData['slug']) ? htmlspecialchars($postData['slug']) : ''; ?>">
                            <small class="text-muted">Leave empty to generate automatically. Use only letters, numbers, and hyphens.</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="excerpt">Excerpt</label>
                            <textarea id="excerpt" name="excerpt" rows="3"><?php echo isset($postData['excerpt']) ? htmlspecialchars($postData['excerpt']) : ''; ?></textarea>
                            <small class="text-muted">A short summary of the post. If left empty, it will be generated from the content.</small>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3 class="form-section-title">Content</h3>
                        <div class="form-group">
                            <textarea id="content" name="content"><?php echo isset($postData['content']) ? $postData['content'] : ''; ?></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Sidebar Section -->
                <div class="post-form-sidebar">
                    <div class="form-section">
                        <h3 class="form-section-title">Publish</h3>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status">
                                <option value="draft" <?php echo (isset($postData['status']) && $postData['status'] === 'draft') ? 'selected' : ''; ?>>Draft</option>
                                <option value="published" <?php echo (isset($postData['status']) && $postData['status'] === 'published') ? 'selected' : ''; ?>>Published</option>
                                <option value="archived" <?php echo (isset($postData['status']) && $postData['status'] === 'archived') ? 'selected' : ''; ?>>Archived</option>
                            </select>
                        </div>
                        
                        <div class="form-actions">
                            <a href="blog-management.php" class="btn-cancel">Cancel</a>
                            <button type="submit" class="btn-publish">
                                <?php echo $action === 'add' ? 'Publish Post' : 'Update Post'; ?>
                            </button>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3 class="form-section-title">Categories</h3>
                        <div class="form-group">
                            <div class="checkbox-group">
                                <?php foreach ($categories as $category): ?>
                                    <div class="checkbox-item">
                                        <input type="checkbox" 
                                               id="category-<?php echo $category['id']; ?>" 
                                               name="categories[]" 
                                               value="<?php echo $category['id']; ?>"
                                               <?php echo (isset($postCategoryIds) && in_array($category['id'], $postCategoryIds)) ? 'checked' : ''; ?>>
                                        <label for="category-<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php if (empty($categories)): ?>
                                <p>No categories found. <a href="blog-categories.php?action=add">Add a category</a></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3 class="form-section-title">Tags</h3>
                        <div class="form-group">
                            <select id="tags" name="tags[]" multiple>
                                <?php foreach ($tags as $tag): ?>
                                    <option value="<?php echo $tag['id']; ?>"
                                            <?php echo (isset($postTagIds) && in_array($tag['id'], $postTagIds)) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($tag['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Hold Ctrl (Windows) or Command (Mac) to select multiple tags.</small>
                            <?php if (empty($tags)): ?>
                                <p>No tags found. <a href="blog-tags.php?action=add">Add a tag</a></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3 class="form-section-title">Featured Image</h3>
                        <div class="form-group">
                            <input type="file" id="featured_image" name="featured_image" accept="image/*">
                            <small class="text-muted">Select an image to display with your post. Recommended size: 1200×800 pixels.</small>
                            
                            <?php if (isset($postData['featured_image']) && !empty($postData['featured_image'])): ?>
                                <div class="featured-image-preview">
                                    <p>Current image:</p>
                                    <img src="../<?php echo htmlspecialchars($postData['featured_image']); ?>" alt="Featured image">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </form>
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
            
            // Auto-generate slug from title
            const titleInput = document.getElementById('title');
            const slugInput = document.getElementById('slug');
            
            if (titleInput && slugInput) {
                titleInput.addEventListener('blur', function() {
                    if (slugInput.value === '') {
                        const title = titleInput.value.trim();
                        const slug = title.toLowerCase()
                            .replace(/[^a-z0-9\s-]/g, '')  // Remove special chars
                            .replace(/\s+/g, '-')          // Replace spaces with hyphens
                            .replace(/-+/g, '-');          // Remove consecutive hyphens
                        
                        slugInput.value = slug;
                    }
                });
            }
        });
    </script>
</body>
</html>