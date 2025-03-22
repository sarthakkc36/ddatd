<?php
include 'includes/header.php';
require_once 'includes/Blog.php';

// Initialize Blog handler
$blogHandler = new Blog();

// Get pagination parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 6; // Number of posts per page

// Get query parameters for filtering
$category_slug = isset($_GET['category']) ? $_GET['category'] : null;
$tag_slug = isset($_GET['tag']) ? $_GET['tag'] : null;
$search_term = isset($_GET['search']) ? $_GET['search'] : null;

// Get posts based on filters
if ($category_slug) {
    $posts = $blogHandler->getPostsByCategory($category_slug, $page, $per_page);
    $total_posts = $blogHandler->countPostsByCategory($category_slug);
    $current_category = $blogHandler->getCategoryBySlug($category_slug);
    $page_title = "Category: " . ($current_category ? htmlspecialchars($current_category['name']) : 'Not Found');
} elseif ($tag_slug) {
    $posts = $blogHandler->getPostsByTag($tag_slug, $page, $per_page);
    $total_posts = $blogHandler->countPostsByTag($tag_slug);
    $current_tag = $blogHandler->getTagBySlug($tag_slug);
    $page_title = "Tag: " . ($current_tag ? htmlspecialchars($current_tag['name']) : 'Not Found');
} elseif ($search_term) {
    $posts = $blogHandler->searchPosts($search_term, $page, $per_page);
    $total_posts = $blogHandler->countSearchResults($search_term);
    $page_title = "Search Results for: " . htmlspecialchars($search_term);
} else {
    $posts = $blogHandler->getPosts($page, $per_page);
    $total_posts = $blogHandler->countPosts();
    $page_title = "Blog";
}

// Calculate total pages for pagination
$total_pages = ceil($total_posts / $per_page);

// Get categories and tags for sidebar
$categories = $blogHandler->getCategories();
$tags = $blogHandler->getTags();

// Get recent posts for sidebar
$recent_posts = $blogHandler->getRecentPosts(5);
?>

<!-- Hero Banner -->
<section class="page-hero blog-hero" data-aos="fade-up">
    <div class="container">
        <h1><?php echo $page_title; ?></h1>
        <?php if (isset($current_category) && !empty($current_category['description'])): ?>
            <p><?php echo htmlspecialchars($current_category['description']); ?></p>
        <?php elseif (!isset($current_category) && !isset($current_tag) && !isset($search_term)): ?>
            <p>Stay informed with our latest health tips, medical insights, and patient stories</p>
        <?php endif; ?>
    </div>
</section>

<!-- Blog Section -->
<section class="blog-section" data-aos="fade-up">
    <div class="container">
        <div class="blog-grid">
            <!-- Main Content -->
            <div class="blog-main">
                <?php if (empty($posts)): ?>
                    <div class="no-posts">
                        <h2>No posts found</h2>
                        <?php if ($search_term): ?>
                            <p>No results found for '<?php echo htmlspecialchars($search_term); ?>'. Please try another search term.</p>
                        <?php elseif ($category_slug || $tag_slug): ?>
                            <p>No posts found in this category or tag.</p>
                        <?php else: ?>
                            <p>No blog posts have been published yet. Please check back soon!</p>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <!-- Blog Posts Grid -->
                    <div class="blog-posts-grid">
                        <?php foreach ($posts as $post): ?>
                            <div class="blog-card" data-aos="fade-up">
                                <div class="blog-image">
                                    <?php if (!empty($post['featured_image'])): ?>
                                        <a href="blog-post.php?slug=<?php echo $post['slug']; ?>">
                                            <img src="<?php echo htmlspecialchars($post['featured_image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" loading="lazy">
                                        </a>
                                    <?php else: ?>
                                        <a href="blog-post.php?slug=<?php echo $post['slug']; ?>">
                                            <div class="placeholder-image">
                                                <i class="fas fa-newspaper"></i>
                                            </div>
                                        </a>
                                    <?php endif; ?>
                                </div>
                                <div class="blog-content">
                                    <div class="blog-meta">
                                        <span class="blog-date">
                                            <i class="fas fa-calendar"></i> <?php echo date('M j, Y', strtotime($post['published_at'])); ?>
                                        </span>
                                        <?php
                                        $post_categories = $blogHandler->getPostCategories($post['id']);
                                        if (!empty($post_categories)):
                                        ?>
                                            <span class="blog-category">
                                                <i class="fas fa-folder"></i>
                                                <?php 
                                                $category_links = [];
                                                foreach ($post_categories as $category) {
                                                    $category_links[] = '<a href="blog.php?category=' . $category['slug'] . '">' . htmlspecialchars($category['name']) . '</a>';
                                                }
                                                echo implode(', ', $category_links);
                                                ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <h2><a href="blog-post.php?slug=<?php echo $post['slug']; ?>"><?php echo htmlspecialchars($post['title']); ?></a></h2>
                                    <p><?php echo htmlspecialchars($post['excerpt']); ?></p>
                                    <a href="blog-post.php?slug=<?php echo $post['slug']; ?>" class="read-more">Read More <i class="fas fa-arrow-right"></i></a>
                                </div>
                            </div>
                        <?php endforeach; ?>
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

            <!-- Sidebar -->
            <div class="blog-sidebar">
                <!-- Search Box -->
                <div class="sidebar-widget search-widget">
                    <form action="blog.php" method="GET" class="search-form">
                        <input type="text" name="search" placeholder="Search blog..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>

                <!-- Categories -->
                <div class="sidebar-widget">
                    <h3>Categories</h3>
                    <ul class="category-list">
                        <?php foreach ($categories as $category): ?>
                            <li>
                                <a href="blog.php?category=<?php echo $category['slug']; ?>">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                    <span class="category-count">(<?php echo $category['post_count']; ?>)</span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Recent Posts -->
                <div class="sidebar-widget">
                    <h3>Recent Posts</h3>
                    <ul class="recent-posts">
                        <?php foreach ($recent_posts as $recent): ?>
                            <li>
                                <a href="blog-post.php?slug=<?php echo $recent['slug']; ?>">
                                    <?php if (!empty($recent['featured_image'])): ?>
                                        <div class="post-thumbnail">
                                            <img src="<?php echo htmlspecialchars($recent['featured_image']); ?>" alt="<?php echo htmlspecialchars($recent['title']); ?>" loading="lazy">
                                        </div>
                                    <?php endif; ?>
                                    <div class="post-info">
                                        <h4><?php echo htmlspecialchars($recent['title']); ?></h4>
                                        <span class="post-date"><?php echo date('M j, Y', strtotime($recent['published_at'])); ?></span>
                                    </div>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Tags -->
                <div class="sidebar-widget">
                    <h3>Tags</h3>
                    <div class="tag-cloud">
                        <?php foreach ($tags as $tag): ?>
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

<?php include 'includes/footer.php'; ?>