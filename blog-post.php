<?php
include 'includes/header.php';
require_once 'includes/Blog.php';

// Initialize Blog handler
$blogHandler = new Blog();

// Get post slug from URL
$slug = isset($_GET['slug']) ? $_GET['slug'] : null;

// Get the post data
$post = $blogHandler->getPostBySlug($slug);

// If post not found or not published, redirect to blog page
if (!$post || $post['status'] !== 'published') {
    header('Location: blog.php');
    exit;
}

// Increment post view counter
$blogHandler->incrementViews($post['id']);

// Get post categories and tags
$categories = $blogHandler->getPostCategories($post['id']);
$tags = $blogHandler->getPostTags($post['id']);

// Get author information
$author = $blogHandler->getAuthor($post['author_id']);

// Get related posts
$related_posts = $blogHandler->getRelatedPosts($post['id'], 3);

// Get comments if enabled
$comments_enabled = true; // You can make this dynamic from settings
$comments = $comments_enabled ? $blogHandler->getApprovedComments($post['id']) : [];

// Handle comment submission
$comment_error = '';
$comment_success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    $comment_data = [
        'post_id' => $post['id'],
        'parent_id' => isset($_POST['parent_id']) ? (int)$_POST['parent_id'] : null,
        'author_name' => $_POST['author_name'] ?? '',
        'author_email' => $_POST['author_email'] ?? '',
        'content' => $_POST['content'] ?? ''
    ];
    
    // Basic validation
    if (empty($comment_data['author_name']) || empty($comment_data['author_email']) || empty($comment_data['content'])) {
        $comment_error = 'Please fill in all required fields.';
    } elseif (!filter_var($comment_data['author_email'], FILTER_VALIDATE_EMAIL)) {
        $comment_error = 'Please enter a valid email address.';
    } else {
        // Save comment
        $comment_id = $blogHandler->addComment($comment_data);
        if ($comment_id) {
            $comment_success = 'Your comment has been submitted and is awaiting moderation.';
            // Clear form data
            $_POST = [];
        } else {
            $comment_error = 'There was an error submitting your comment. Please try again.';
        }
    }
}
?>

<!-- Hero Banner with Post Title -->
<section class="page-hero blog-post-hero" data-aos="fade-up">
    <div class="container">
        <div class="post-meta">
            <span class="post-date">
                <i class="fas fa-calendar"></i> <?php echo date('F j, Y', strtotime($post['published_at'])); ?>
            </span>
            <?php if (!empty($categories)): ?>
                <span class="post-category">
                    <i class="fas fa-folder"></i>
                    <?php 
                    $category_links = [];
                    foreach ($categories as $category) {
                        $category_links[] = '<a href="blog.php?category=' . $category['slug'] . '">' . htmlspecialchars($category['name']) . '</a>';
                    }
                    echo implode(', ', $category_links);
                    ?>
                </span>
            <?php endif; ?>
            <span class="post-views">
                <i class="fas fa-eye"></i> <?php echo number_format($post['views']); ?> views
            </span>
        </div>
        <h1><?php echo htmlspecialchars($post['title']); ?></h1>
    </div>
</section>

<!-- Blog Post Content -->
<section class="blog-post-section" data-aos="fade-up">
    <div class="container">
        <div class="blog-post-grid">
            <!-- Main Content -->
            <div class="blog-post-main">
                <!-- Featured Image -->
                <?php if (!empty($post['featured_image'])): ?>
                    <div class="post-featured-image">
                        <img src="<?php echo htmlspecialchars($post['featured_image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" loading="lazy">
                    </div>
                <?php endif; ?>

                <!-- Content -->
                <div class="post-content">
                    <?php echo $post['content']; ?>
                </div>

                <!-- Tags -->
                <?php if (!empty($tags)): ?>
                    <div class="post-tags">
                        <span class="tags-label">Tags:</span>
                        <div class="tag-list">
                            <?php foreach ($tags as $tag): ?>
                                <a href="blog.php?tag=<?php echo $tag['slug']; ?>" class="tag"><?php echo htmlspecialchars($tag['name']); ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Author Info -->
                <?php if ($author): ?>
                    <div class="post-author">
                        <div class="author-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="author-info">
                            <h3><?php echo htmlspecialchars($author['username']); ?></h3>
                            <p>Author</p>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Post Navigation -->
                <div class="post-navigation">
                    <?php
                    $previous_post = $blogHandler->getPreviousPost($post['id']);
                    $next_post = $blogHandler->getNextPost($post['id']);
                    ?>
                    
                    <?php if ($previous_post): ?>
                        <a href="blog-post.php?slug=<?php echo $previous_post['slug']; ?>" class="post-nav-link prev-post">
                            <i class="fas fa-arrow-left"></i>
                            <span class="post-nav-label">Previous Post</span>
                            <span class="post-nav-title"><?php echo htmlspecialchars($previous_post['title']); ?></span>
                        </a>
                    <?php endif; ?>
                    
                    <?php if ($next_post): ?>
                        <a href="blog-post.php?slug=<?php echo $next_post['slug']; ?>" class="post-nav-link next-post">
                            <span class="post-nav-label">Next Post</span>
                            <span class="post-nav-title"><?php echo htmlspecialchars($next_post['title']); ?></span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Comments Section -->
                <?php if ($comments_enabled): ?>
                    <div class="comments-section">
                        <h2>Comments (<?php echo count($comments); ?>)</h2>
                        
                        <?php if (!empty($comments)): ?>
                            <div class="comment-list">
                                <?php foreach ($comments as $comment): ?>
                                    <div class="comment" id="comment-<?php echo $comment['id']; ?>">
                                        <div class="comment-avatar">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="comment-content">
                                            <div class="comment-meta">
                                                <h4 class="comment-author"><?php echo htmlspecialchars($comment['author_name']); ?></h4>
                                                <span class="comment-date">
                                                    <i class="fas fa-calendar"></i> <?php echo date('M j, Y', strtotime($comment['created_at'])); ?>
                                                </span>
                                            </div>
                                            <div class="comment-text">
                                                <?php echo nl2br(htmlspecialchars($comment['content'])); ?>
                                            </div>
                                            <div class="comment-actions">
                                                <button class="reply-button" data-comment-id="<?php echo $comment['id']; ?>">
                                                    <i class="fas fa-reply"></i> Reply
                                                </button>
                                            </div>
                                            
                                            <!-- Reply Form (hidden by default) -->
                                            <div class="reply-form-container" id="reply-form-<?php echo $comment['id']; ?>" style="display: none;">
                                                <form method="POST" action="#comment-<?php echo $comment['id']; ?>" class="comment-form reply-form">
                                                    <input type="hidden" name="parent_id" value="<?php echo $comment['id']; ?>">
                                                    
                                                    <div class="form-group">
                                                        <label for="reply-name-<?php echo $comment['id']; ?>">Name *</label>
                                                        <input type="text" id="reply-name-<?php echo $comment['id']; ?>" name="author_name" required>
                                                    </div>
                                                    
                                                    <div class="form-group">
                                                        <label for="reply-email-<?php echo $comment['id']; ?>">Email *</label>
                                                        <input type="email" id="reply-email-<?php echo $comment['id']; ?>" name="author_email" required>
                                                    </div>
                                                    
                                                    <div class="form-group">
                                                        <label for="reply-content-<?php echo $comment['id']; ?>">Comment *</label>
                                                        <textarea id="reply-content-<?php echo $comment['id']; ?>" name="content" rows="4" required></textarea>
                                                    </div>
                                                    
                                                    <div class="form-actions">
                                                        <button type="button" class="btn-cancel-reply">Cancel</button>
                                                        <button type="submit" name="submit_comment" class="btn-submit">Submit Reply</button>
                                                    </div>
                                                </form>
                                            </div>
                                            
                                            <!-- Child Comments (Replies) -->
                                            <?php 
                                            $replies = $blogHandler->getCommentReplies($comment['id']);
                                            if (!empty($replies)):
                                            ?>
                                                <div class="comment-replies">
                                                    <?php foreach ($replies as $reply): ?>
                                                        <div class="comment reply" id="comment-<?php echo $reply['id']; ?>">
                                                            <div class="comment-avatar">
                                                                <i class="fas fa-user"></i>
                                                            </div>
                                                            <div class="comment-content">
                                                                <div class="comment-meta">
                                                                    <h4 class="comment-author"><?php echo htmlspecialchars($reply['author_name']); ?></h4>
                                                                    <span class="comment-date">
                                                                        <i class="fas fa-calendar"></i> <?php echo date('M j, Y', strtotime($reply['created_at'])); ?>
                                                                    </span>
                                                                </div>
                                                                <div class="comment-text">
                                                                    <?php echo nl2br(htmlspecialchars($reply['content'])); ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="no-comments">
                                <p>No comments yet. Be the first to comment!</p>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Comment Form -->
                        <div class="comment-form-container">
                            <h3>Leave a Comment</h3>
                            
                            <?php if ($comment_success): ?>
                                <div class="comment-success">
                                    <?php echo $comment_success; ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($comment_error): ?>
                                <div class="comment-error">
                                    <?php echo $comment_error; ?>
                                </div>
                            <?php endif; ?>
                            
                            <form method="POST" action="#comments" class="comment-form">
                                <div class="form-group">
                                    <label for="author_name">Name *</label>
                                    <input type="text" id="author_name" name="author_name" value="<?php echo $_POST['author_name'] ?? ''; ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="author_email">Email *</label>
                                    <input type="email" id="author_email" name="author_email" value="<?php echo $_POST['author_email'] ?? ''; ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="content">Comment *</label>
                                    <textarea id="content" name="content" rows="6" required><?php echo $_POST['content'] ?? ''; ?></textarea>
                                </div>
                                
                                <div class="form-actions">
                                    <button type="submit" name="submit_comment" class="btn-submit">Post Comment</button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="blog-sidebar">
                <!-- Search Box -->
                <div class="sidebar-widget search-widget">
                    <form action="blog.php" method="GET" class="search-form">
                        <input type="text" name="search" placeholder="Search blog...">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>

                <!-- Related Posts -->
                <?php if (!empty($related_posts)): ?>
                    <div class="sidebar-widget related-posts-widget">
                        <h3>Related Posts</h3>
                        <ul class="related-posts">
                            <?php foreach ($related_posts as $related): ?>
                                <li>
                                    <a href="blog-post.php?slug=<?php echo $related['slug']; ?>">
                                        <?php if (!empty($related['featured_image'])): ?>
                                            <div class="post-thumbnail">
                                                <img src="<?php echo htmlspecialchars($related['featured_image']); ?>" alt="<?php echo htmlspecialchars($related['title']); ?>" loading="lazy">
                                            </div>
                                        <?php endif; ?>
                                        <div class="post-info">
                                            <h4><?php echo htmlspecialchars($related['title']); ?></h4>
                                            <span class="post-date"><?php echo date('M j, Y', strtotime($related['published_at'])); ?></span>
                                        </div>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- Categories -->
                <div class="sidebar-widget">
                    <h3>Categories</h3>
                    <ul class="category-list">
                        <?php 
                        $all_categories = $blogHandler->getCategories();
                        foreach ($all_categories as $category): 
                        ?>
                            <li>
                                <a href="blog.php?category=<?php echo $category['slug']; ?>">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                    <span class="category-count">(<?php echo $category['post_count']; ?>)</span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Tags -->
                <div class="sidebar-widget">
                    <h3>Tags</h3>
                    <div class="tag-cloud">
                        <?php 
                        $all_tags = $blogHandler->getTags();
                        foreach ($all_tags as $tag): 
                        ?>
                            <a href="blog.php?tag=<?php echo $tag['slug']; ?>" class="tag"><?php echo htmlspecialchars($tag['name']); ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="blog-cta" data-aos="fade-up">
    <div class="container">
        <div class="cta-content">
            <h2>Need Professional Healthcare Services?</h2>
            <p>Our team of qualified doctors and healthcare professionals is ready to provide personalized care at your doorstep.</p>
            <div class="cta-buttons">
                <a href="booking.php" class="btn btn-primary">Book an Appointment</a>
                <a href="contact.php" class="btn btn-outline">Contact Us</a>
            </div>
        </div>
    </div>
</section>

<!-- JavaScript for reply functionality -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle reply button clicks
    const replyButtons = document.querySelectorAll('.reply-button');
    const cancelButtons = document.querySelectorAll('.btn-cancel-reply');
    
    replyButtons.forEach(button => {
        button.addEventListener('click', function() {
            const commentId = this.getAttribute('data-comment-id');
            const replyForm = document.getElementById(`reply-form-${commentId}`);
            
            // Hide all other reply forms first
            document.querySelectorAll('.reply-form-container').forEach(form => {
                form.style.display = 'none';
            });
            
            // Show this reply form
            replyForm.style.display = 'block';
            
            // Scroll to the form
            replyForm.scrollIntoView({ behavior: 'smooth', block: 'center' });
        });
    });
    
    cancelButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Find the parent form container and hide it
            const formContainer = this.closest('.reply-form-container');
            formContainer.style.display = 'none';
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>