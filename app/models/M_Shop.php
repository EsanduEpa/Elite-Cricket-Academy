<?php
class M_Shop {
    private $db;
    
    public function __construct() {
        $this->db = new Database;
    }

    // Get products by category
    public function getProductsByCategory($categoryId) {
        $this->db->query('SELECT * FROM products WHERE category_id = :category_id AND status = "active" ORDER BY created_at DESC');
        $this->db->bind(':category_id', $categoryId);
        
        return $this->db->resultSet();
    }

    // Get category information
    public function getCategoryById($categoryId) {
        $this->db->query('SELECT * FROM categories WHERE id = :id');
        $this->db->bind(':id', $categoryId);
        
        return $this->db->single();
    }

    // Get product details
    public function getProductById($productId) {
        $this->db->query('SELECT * FROM products WHERE id = :id AND status = "active"');
        $this->db->bind(':id', $productId);
        
        return $this->db->single();
    }

    // Get related products
    public function getRelatedProducts($productId, $limit = 4) {
        $this->db->query('
            SELECT p2.* FROM products p1 
            INNER JOIN products p2 ON p1.category_id = p2.category_id 
            WHERE p1.id = :product_id AND p2.id != :product_id 
            AND p2.status = "active" 
            ORDER BY RAND() 
            LIMIT :limit
        ');
        $this->db->bind(':product_id', $productId);
        $this->db->bind(':limit', $limit);
        
        return $this->db->resultSet();
    }

    // Search products
    public function searchProducts($searchTerm) {
        $searchTerm = '%' . $searchTerm . '%';
        $this->db->query('
            SELECT * FROM products 
            WHERE (name LIKE :search OR description LIKE :search OR brand LIKE :search) 
            AND status = "active" 
            ORDER BY name ASC
        ');
        $this->db->bind(':search', $searchTerm);
        
        return $this->db->resultSet();
    }

    // Get featured products
    public function getFeaturedProducts($limit = 8) {
        $this->db->query('SELECT * FROM products WHERE is_featured = 1 AND status = "active" ORDER BY created_at DESC LIMIT :limit');
        $this->db->bind(':limit', $limit);
        
        return $this->db->resultSet();
    }

    // Get all categories
    public function getCategories() {
        $this->db->query('SELECT * FROM categories WHERE status = "active" ORDER BY name ASC');
        
        return $this->db->resultSet();
    }

    // Add product to cart (session-based for now)
    public function addToCart($productId, $quantity = 1) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] += $quantity;
        } else {
            $product = $this->getProductById($productId);
            if ($product) {
                $_SESSION['cart'][$productId] = [
                    'product' => $product,
                    'quantity' => $quantity
                ];
            }
        }
        
        return true;
    }

    // Remove from cart
    public function removeFromCart($productId) {
        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
            return true;
        }
        return false;
    }

    // Update cart quantity
    public function updateCartQuantity($productId, $quantity) {
        if (isset($_SESSION['cart'][$productId])) {
            if ($quantity <= 0) {
                unset($_SESSION['cart'][$productId]);
            } else {
                $_SESSION['cart'][$productId]['quantity'] = $quantity;
            }
            return true;
        }
        return false;
    }

    // Get cart total
    public function getCartTotal() {
        $total = 0;
        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $item) {
                $total += $item['product']->price * $item['quantity'];
            }
        }
        return $total;
    }

    // Get cart item count
    public function getCartItemCount() {
        $count = 0;
        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $item) {
                $count += $item['quantity'];
            }
        }
        return $count;
    }
}
?>