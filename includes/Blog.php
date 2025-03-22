<?php
require_once 'Database.php';

class Blog {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Get published blog posts with pagination
     */
    public function getPosts($page = 1, $per_page = 10) {
        $offset = ($page - 1) * $per_page;
        
        return $this->db->select(
            "SELECT * FROM blog_posts 
            WHERE status = 'published' AND published_at <= NOW() 
            ORDER BY published_at DESC 
            LIMIT ? OFFSET ?",
            [$per_page, $offset]
        );
    }

    /**
     * Count total published posts
     */
    public function countPosts() {
        $result = $this->db->selectOne(
            "SELECT COUNT(*) as count FROM blog_posts 
            WHERE status = 'published' AND published_at <= NOW()"
        );
        
        return $result ? $result['count'] : 0;
    }
    
    /**
     * Get a single post by slug
     */
    public function getPostBySlug($slug) {
        return $this->db->selectOne(
            "SELECT * FROM blog_posts WHERE slug = ? AND status = 'published' AND published_at <= NOW()",
            [$slug]
        );
    }
    
    /**
     * Get a single post by ID
     */
    public function getPostById($id) {
        return $this->db->selectOne(
            "SELECT * FROM blog_posts WHERE id = ? AND status = 'published' AND published_at <= NOW()",
            [$id]
        );
    }
    
    /**
     * Get recent published posts
     */
    public function getRecentPosts($limit = 5) {
        return $this->db->select(
            "SELECT * FROM blog_posts 
            WHERE status = 'published' AND published_at <= NOW() 
            ORDER BY published_at DESC 
            LIMIT ?",
            [$limit]
        );
    }
    
    /**
     * Get posts by category with pagination
     */
    public function getPostsByCategory($category_slug, $page = 1, $per_page = 10) {
        $offset = ($page - 1) * $per_page;
        
        return $this->db->select(
            "SELECT p.* FROM blog_posts p
            JOIN post_categories pc ON p.id = pc.post_id
            JOIN blog_categories c ON pc.category_id = c.id
            WHERE p.status = 'published' 
            AND p.published_at <= NOW() 
            AND c.slug = ?
            ORDER BY p.published_at DESC
            LIMIT ? OFFSET ?",
            [$category_slug, $per_page, $offset]
        );
    }
    
    /**
     * Count posts in a category
     */
    public function countPostsByCategory($category_slug) {
        $result = $this->db->selectOne(
            "SELECT COUNT(p.id) as count FROM blog_posts p
            JOIN post_categories pc ON p.id = pc.post_id
            JOIN blog_categories c ON pc.category_id = c.id
            WHERE p.status = 'published' 
            AND p.published_at <= NOW() 
            AND c.slug = ?",
            [$category_slug]
        );
        
        return $result ? $result['count'] : 0;
    }
    
    /**
     * Get posts by tag with pagination
     */
    public function getPostsByTag($tag_slug, $page = 1, $per_page = 10) {
        $offset = ($page - 1) * $per_page;
        
        return $this->db->select(
            "SELECT p.* FROM blog_posts p
            JOIN post_tags pt ON p.id = pt.post_id
            JOIN blog_tags t ON pt.tag_id = t.id
            WHERE p.status = 'published' 
            AND p.published_at <= NOW() 
            AND t.slug = ?
            ORDER BY p.published_at DESC
            LIMIT ? OFFSET ?",
            [$tag_slug, $per_page, $offset]
        );
    }
    
    /**
     * Count posts with a tag
     */
    public function countPostsByTag($tag_slug) {
        $result = $this->db->selectOne(
            "SELECT COUNT(p.id) as count FROM blog_posts p
            JOIN post_tags pt ON p.id = pt.post_id
            JOIN blog_tags t ON pt.tag_id = t.id
            WHERE p.status = 'published' 
            AND p.published_at <= NOW() 
            AND t.slug = ?",
            [$tag_slug]
        );
        
        return $result ? $result['count'] : 0;
    }
    
    /**
     * Search posts with pagination
     */
    public function searchPosts($term, $page = 1, $per_page = 10) {
        $offset = ($page - 1) * $per_page;
        $like_term = "%$term%";
        
        return $this->db->select(
            "SELECT * FROM blog_posts 
            WHERE status = 'published' 
            AND published_at <= NOW() 
            AND (title LIKE ? OR content LIKE ? OR excerpt LIKE ?)
            ORDER BY published_at DESC
            LIMIT ? OFFSET ?",
            [$like_term, $like_term, $like_term, $per_page, $offset]
        );
    }
    
    /**
     * Count search results
     */
    public function countSearchResults($term) {
        $like_term = "%$term%";
        
        $result = $this->db->selectOne(
            "SELECT COUNT(*) as count FROM blog_posts 
            WHERE status = 'published' 
            AND published_at <= NOW() 
            AND (title LIKE ? OR content LIKE ? OR excerpt LIKE ?)",
            [$like_term, $like_term, $like_term]
        );
        
        return $result ? $result['count'] : 0;
    }
    
    /**
     * Get all blog categories with post count
     */
    public function getCategories() {
        return $this->db->select(
            "SELECT c.*, COUNT(pc.post_id) as post_count
            FROM blog_categories c
            LEFT JOIN post_categories pc ON c.id = pc.category_id
            LEFT JOIN blog_posts p ON pc.post_id = p.id AND p.status = 'published' AND p.published_at <= NOW()
            GROUP BY c.id
            ORDER BY c.display_order ASC, c.name ASC"
        );
    }
    
    /**
     * Get a category by slug
     */
    public function getCategoryBySlug($slug) {
        return $this->db->selectOne(
            "SELECT * FROM blog_categories WHERE slug = ?",
            [$slug]
        );
    }
    
    /**
     * Get all blog tags with post count
     */
    public function getTags() {
        return $this->db->select(
            "SELECT t.*, COUNT(pt.post_id) as post_count
            FROM blog_tags t
            LEFT JOIN post_tags pt ON t.id = pt.tag_id
            LEFT JOIN blog_posts p ON pt.post_id = p.id AND p.status = 'published' AND p.published_at <= NOW()
            GROUP BY t.id
            ORDER BY t.name ASC"
        );
    }
    
    /**
     * Get a tag by slug
     */
    public function getTagBySlug($slug) {
        return $this->db->selectOne(
            "SELECT * FROM blog_tags WHERE slug = ?",
            [$slug]
        );
    }
    
    /**
     * Get categories for a post
     */
    public function getPostCategories($post_id) {
        return $this->db->select(
            "SELECT c.* FROM blog_categories c
            JOIN post_categories pc ON c.id = pc.category_id
            WHERE pc.post_id = ?
            ORDER BY c.name ASC",
            [$post_id]
        );
    }
    
    /**
     * Get tags for a post
     */
    public function getPostTags($post_id) {
        return $this->db->select(
            "SELECT t.* FROM blog_tags t
            JOIN post_tags pt ON t.id = pt.tag_id
            WHERE pt.post_id = ?
            ORDER BY t.name ASC",
            [$post_id]
        );
    }
    
    /**
     * Get author information
     */
    public function getAuthor($author_id) {
        if (!$author_id) {
            return null;
        }
        
        return $this->db->selectOne(
            "SELECT id, username, email FROM users WHERE id = ?",
            [$author_id]
        );
    }
    
    /**
     * Get related posts based on categories and tags
     */
    public function getRelatedPosts($post_id, $limit = 3) {
        // Get current post's categories
        $categories = $this->getPostCategories($post_id);
        if (empty($categories)) {
            return [];
        }
        
        // Extract category IDs
        $category_ids = array_column($categories, 'id');
        $placeholders = implode(',', array_fill(0, count($category_ids), '?'));
        
        // Get related posts
        $params = array_merge($category_ids, [$post_id, $limit]);
        
        return $this->db->select(
            "SELECT DISTINCT p.* FROM blog_posts p
            JOIN post_categories pc ON p.id = pc.post_id
            WHERE pc.category_id IN ($placeholders)
            AND p.id != ?
            AND p.status = 'published'
            AND p.published_at <= NOW()
            ORDER BY p.published_at DESC
            LIMIT ?",
            $params
        );
    }
    
    /**
     * Get previous post
     */
    public function getPreviousPost($post_id) {
        $current_post = $this->getPostById($post_id);
        if (!$current_post) {
            return null;
        }
        
        return $this->db->selectOne(
            "SELECT * FROM blog_posts
            WHERE status = 'published'
            AND published_at <= NOW()
            AND published_at < ?
            ORDER BY published_at DESC
            LIMIT 1",
            [$current_post['published_at']]
        );
    }
    
    /**
     * Get next post
     */
    public function getNextPost($post_id) {
        $current_post = $this->getPostById($post_id);
        if (!$current_post) {
            return null;
        }
        
        return $this->db->selectOne(
            "SELECT * FROM blog_posts
            WHERE status = 'published'
            AND published_at <= NOW()
            AND published_at > ?
            ORDER BY published_at ASC
            LIMIT 1",
            [$current_post['published_at']]
        );
    }
    
    /**
     * Increment post view counter
     */
    public function incrementViews($post_id) {
        return $this->db->update(
            'blog_posts',
            ['views' => ['expr' => 'views + 1']],
            'id = ?',
            [$post_id]
        );
    }
    
    /**
     * Get approved comments for a post
     */
    public function getApprovedComments($post_id) {
        return $this->db->select(
            "SELECT * FROM blog_comments
            WHERE post_id = ?
            AND parent_id IS NULL
            AND status = 'approved'
            ORDER BY created_at ASC",
            [$post_id]
        );
    }
    
    /**
     * Get replies to a comment
     */
    public function getCommentReplies($comment_id) {
        return $this->db->select(
            "SELECT * FROM blog_comments
            WHERE parent_id = ?
            AND status = 'approved'
            ORDER BY created_at ASC",
            [$comment_id]
        );
    }
    
    /**
     * Add a new comment
     */
    public function addComment($data) {
        try {
            $comment_data = [
                'post_id' => $data['post_id'],
                'parent_id' => $data['parent_id'],
                'author_name' => trim($data['author_name']),
                'author_email' => trim($data['author_email']),
                'content' => trim($data['content']),
                'status' => 'pending' // All comments start as pending
            ];
            
            return $this->db->insert('blog_comments', $comment_data);
        } catch (Exception $e) {
            error_log("Error adding comment: " . $e->getMessage());
            return false;
        }
    }
}
/**
 * Generate a unique slug from a title or name
 */
public function generateSlug($text) {
    // Convert to lowercase
    $slug = strtolower($text);
    
    // Replace non-alphanumeric characters with hyphens
    $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
    
    // Replace spaces with hyphens
    $slug = preg_replace('/\s+/', '-', $slug);
    
    // Remove consecutive hyphens
    $slug = preg_replace('/-+/', '-', $slug);
    
    // Trim hyphens from beginning and end
    $slug = trim($slug, '-');
    
    // Check if slug already exists
    $original_slug = $slug;
    $count = 0;
    
    while ($this->slugExists($slug)) {
        $count++;
        $slug = $original_slug . '-' . $count;
    }
    
    return $slug;
}

/**
 * Check if a slug already exists
 */
private function slugExists($slug, $tableName = 'blog_posts', $excludeId = null) {
    $sql = "SELECT COUNT(*) as count FROM $tableName WHERE slug = ?";
    $params = [$slug];
    
    if ($excludeId !== null) {
        $sql .= " AND id != ?";
        $params[] = $excludeId;
    }
    
    $result = $this->db->selectOne($sql, $params);
    return $result && $result['count'] > 0;
}

/**
 * Add a new category
 */
public function addCategory($data) {
    try {
        // Check if slug exists
        if ($this->slugExists($data['slug'], 'blog_categories')) {
            throw new Exception("A category with this slug already exists.");
        }
        
        $categoryData = [
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'] ?? null,
            'display_order' => $data['display_order'] ?? 0
        ];
        
        return $this->db->insert('blog_categories', $categoryData);
    } catch (Exception $e) {
        error_log("Error adding category: " . $e->getMessage());
        throw $e;
    }
}

/**
 * Update a category
 */
public function updateCategory($id, $data) {
    try {
        // Check if slug exists for other categories
        if ($this->slugExists($data['slug'], 'blog_categories', $id)) {
            throw new Exception("A category with this slug already exists.");
        }
        
        $categoryData = [
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'] ?? null,
            'display_order' => $data['display_order'] ?? 0
        ];
        
        $this->db->update('blog_categories', $categoryData, 'id = ?', [$id]);
        return true;
    } catch (Exception $e) {
        error_log("Error updating category: " . $e->getMessage());
        throw $e;
    }
}

/**
 * Delete a category
 */
public function deleteCategory($id) {
    try {
        // Start transaction
        $this->db->getConnection()->beginTransaction();
        
        // Delete category associations with posts
        $this->db->delete('post_categories', 'category_id = ?', [$id]);
        
        // Delete the category
        $this->db->delete('blog_categories', 'id = ?', [$id]);
        
        $this->db->getConnection()->commit();
        return true;
    } catch (Exception $e) {
        $this->db->getConnection()->rollBack();
        error_log("Error deleting category: " . $e->getMessage());
        throw $e;
    }
}

/**
 * Get a category by ID
 */
public function getCategoryById($id) {
    return $this->db->selectOne(
        "SELECT * FROM blog_categories WHERE id = ?",
        [$id]
    );
}

/**
 * Add a new tag
 */
public function addTag($data) {
    try {
        // Check if slug exists
        if ($this->slugExists($data['slug'], 'blog_tags')) {
            throw new Exception("A tag with this slug already exists.");
        }
        
        $tagData = [
            'name' => $data['name'],
            'slug' => $data['slug']
        ];
        
        return $this->db->insert('blog_tags', $tagData);
    } catch (Exception $e) {
        error_log("Error adding tag: " . $e->getMessage());
        throw $e;
    }
}

/**
 * Update a tag
 */
public function updateTag($id, $data) {
    try {
        // Check if slug exists for other tags
        if ($this->slugExists($data['slug'], 'blog_tags', $id)) {
            throw new Exception("A tag with this slug already exists.");
        }
        
        $tagData = [
            'name' => $data['name'],
            'slug' => $data['slug']
        ];
        
        $this->db->update('blog_tags', $tagData, 'id = ?', [$id]);
        return true;
    } catch (Exception $e) {
        error_log("Error updating tag: " . $e->getMessage());
        throw $e;
    }
}

/**
 * Delete a tag
 */
public function deleteTag($id) {
    try {
        // Start transaction
        $this->db->getConnection()->beginTransaction();
        
        // Delete tag associations with posts
        $this->db->delete('post_tags', 'tag_id = ?', [$id]);
        
        // Delete the tag
        $this->db->delete('blog_tags', 'id = ?', [$id]);
        
        $this->db->getConnection()->commit();
        return true;
    } catch (Exception $e) {
        $this->db->getConnection()->rollBack();
        error_log("Error deleting tag: " . $e->getMessage());
        throw $e;
    }
}

/**
 * Get a tag by ID
 */
public function getTagById($id) {
    return $this->db->selectOne(
        "SELECT * FROM blog_tags WHERE id = ?",
        [$id]
    );
}

/**
 * Add a new blog post
 */
public function addPost($data) {
    try {
        // Start transaction
        $this->db->getConnection()->beginTransaction();
        
        // Check if slug exists
        if ($this->slugExists($data['slug'])) {
            throw new Exception("A post with this slug already exists.");
        }
        
        // Prepare post data
        $postData = [
            'title' => $data['title'],
            'slug' => $data['slug'],
            'excerpt' => $data['excerpt'],
            'content' => $data['content'],
            'status' => $data['status'],
            'featured_image' => $data['featured_image'] ?? null,
            'author_id' => $data['author_id'],
            'views' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        // Add published_at date if status is published
        if ($data['status'] === 'published') {
            $postData['published_at'] = $data['published_at'] ?? date('Y-m-d H:i:s');
        }
        
        // Insert post and get new post ID
        $postId = $this->db->insert('blog_posts', $postData);
        
        // Add categories
        if (!empty($data['category_ids'])) {
            foreach ($data['category_ids'] as $categoryId) {
                $this->db->insert('post_categories', [
                    'post_id' => $postId,
                    'category_id' => $categoryId
                ]);
            }
        }
        
        // Add tags
        if (!empty($data['tag_ids'])) {
            foreach ($data['tag_ids'] as $tagId) {
                $this->db->insert('post_tags', [
                    'post_id' => $postId,
                    'tag_id' => $tagId
                ]);
            }
        }
        
        $this->db->getConnection()->commit();
        return $postId;
    } catch (Exception $e) {
        $this->db->getConnection()->rollBack();
        error_log("Error adding post: " . $e->getMessage());
        throw $e;
    }
}

/**
 * Update a blog post
 */
public function updatePost($id, $data) {
    try {
        // Start transaction
        $this->db->getConnection()->beginTransaction();
        
        // Check if slug exists for other posts
        if ($this->slugExists($data['slug'], 'blog_posts', $id)) {
            throw new Exception("A post with this slug already exists.");
        }
        
        // Prepare post data
        $postData = [
            'title' => $data['title'],
            'slug' => $data['slug'],
            'excerpt' => $data['excerpt'],
            'content' => $data['content'],
            'status' => $data['status'],
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        // Add featured image if provided
        if (isset($data['featured_image'])) {
            $postData['featured_image'] = $data['featured_image'];
        }
        
        // Update published_at date if status is published
        if ($data['status'] === 'published') {
            $currentPost = $this->getAdminPostById($id);
            
            // If post is being published for the first time
            if ($currentPost['status'] !== 'published' || $currentPost['published_at'] === null) {
                $postData['published_at'] = date('Y-m-d H:i:s');
            }
        }
        
        // Update post
        $this->db->update('blog_posts', $postData, 'id = ?', [$id]);
        
        // Delete existing categories and tags
        $this->db->delete('post_categories', 'post_id = ?', [$id]);
        $this->db->delete('post_tags', 'post_id = ?', [$id]);
        
        // Add categories
        if (!empty($data['category_ids'])) {
            foreach ($data['category_ids'] as $categoryId) {
                $this->db->insert('post_categories', [
                    'post_id' => $id,
                    'category_id' => $categoryId
                ]);
            }
        }
        
        // Add tags
        if (!empty($data['tag_ids'])) {
            foreach ($data['tag_ids'] as $tagId) {
                $this->db->insert('post_tags', [
                    'post_id' => $id,
                    'tag_id' => $tagId
                ]);
            }
        }
        
        $this->db->getConnection()->commit();
        return true;
    } catch (Exception $e) {
        $this->db->getConnection()->rollBack();
        error_log("Error updating post: " . $e->getMessage());
        throw $e;
    }
}

/**
 * Delete a blog post
 */
public function deletePost($id) {
    try {
        // Start transaction
        $this->db->getConnection()->beginTransaction();
        
        // Delete post associations (categories, tags, comments)
        $this->db->delete('post_categories', 'post_id = ?', [$id]);
        $this->db->delete('post_tags', 'post_id = ?', [$id]);
        $this->db->delete('blog_comments', 'post_id = ?', [$id]);
        
        // Delete the post
        $this->db->delete('blog_posts', 'id = ?', [$id]);
        
        $this->db->getConnection()->commit();
        return true;
    } catch (Exception $e) {
        $this->db->getConnection()->rollBack();
        error_log("Error deleting post: " . $e->getMessage());
        throw $e;
    }
}

/**
 * Get a post by ID for admin (includes all statuses)
 */
public function getAdminPostById($id) {
    return $this->db->selectOne(
        "SELECT * FROM blog_posts WHERE id = ?",
        [$id]
    );
}

/**
 * Get admin posts with filters and pagination
 */
public function getAdminPosts($search = '', $status = '', $category = 0, $page = 1, $per_page = 10) {
    $offset = ($page - 1) * $per_page;
    
    $sql = "SELECT DISTINCT p.* FROM blog_posts p";
    $params = [];
    
    // Join with categories if needed
    if ($category > 0) {
        $sql .= " JOIN post_categories pc ON p.id = pc.post_id";
    }
    
    $sql .= " WHERE 1=1";
    
    // Add search condition
    if ($search) {
        $sql .= " AND (p.title LIKE ? OR p.content LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    // Add status condition
    if ($status) {
        $sql .= " AND p.status = ?";
        $params[] = $status;
    }
    
    // Add category condition
    if ($category > 0) {
        $sql .= " AND pc.category_id = ?";
        $params[] = $category;
    }
    
    $sql .= " ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
    $params[] = $per_page;
    $params[] = $offset;
    
    return $this->db->select($sql, $params);
}

/**
 * Count admin posts with filters
 */
public function countAdminPosts($search = '', $status = '', $category = 0) {
    $sql = "SELECT COUNT(DISTINCT p.id) as count FROM blog_posts p";
    $params = [];
    
    // Join with categories if needed
    if ($category > 0) {
        $sql .= " JOIN post_categories pc ON p.id = pc.post_id";
    }
    
    $sql .= " WHERE 1=1";
    
    // Add search condition
    if ($search) {
        $sql .= " AND (p.title LIKE ? OR p.content LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    // Add status condition
    if ($status) {
        $sql .= " AND p.status = ?";
        $params[] = $status;
    }
    
    // Add category condition
    if ($category > 0) {
        $sql .= " AND pc.category_id = ?";
        $params[] = $category;
    }
    
    $result = $this->db->selectOne($sql, $params);
    return $result ? $result['count'] : 0;
}

/**
 * Update post status
 */
public function updatePostStatus($postId, $status) {
    try {
        $postData = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        // If publishing, set published_at date
        if ($status === 'published') {
            $post = $this->getAdminPostById($postId);
            
            // If not previously published
            if ($post['published_at'] === null) {
                $postData['published_at'] = date('Y-m-d H:i:s');
            }
        }
        
        $this->db->update('blog_posts', $postData, 'id = ?', [$postId]);
        return true;
    } catch (Exception $e) {
        error_log("Error updating post status: " . $e->getMessage());
        throw $e;
    }
}