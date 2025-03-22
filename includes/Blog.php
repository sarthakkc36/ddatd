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