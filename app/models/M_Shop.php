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
    
    // Dashboard Statistics Methods
    
    public function getTotalOrders() {
        $this->db->query('SELECT COUNT(*) as total FROM productorder');
        $result = $this->db->single();
        return $result->total ?? 0;
    }
    
    public function getPendingOrders() {
        $this->db->query('SELECT COUNT(*) as total FROM productorder WHERE Status = "pending"');
        $result = $this->db->single();
        return $result->total ?? 0;
    }
    
    public function getMonthlyRevenue() {
        $this->db->query('
            SELECT COALESCE(SUM(TotalAmount), 0) as revenue 
            FROM productorder 
            WHERE MONTH(OrderDate) = MONTH(CURRENT_DATE()) 
            AND YEAR(OrderDate) = YEAR(CURRENT_DATE())
            AND Status IN ("completed", "processing")
        ');
        $result = $this->db->single();
        return $result->revenue ?? 0;
    }
    
    public function getTotalProducts() {
        $this->db->query('SELECT COUNT(*) as total FROM product WHERE Status = "active"');
        $result = $this->db->single();
        return $result->total ?? 0;
    }
    
    public function getLowStockCount() {
        $this->db->query('SELECT COUNT(*) as total FROM product WHERE StockQuantity <= 5 AND Status = "active"');
        $result = $this->db->single();
        return $result->total ?? 0;
    }
    
    public function getLowStockItems() {
        $this->db->query('SELECT Name, StockQuantity FROM product WHERE StockQuantity <= 5 AND Status = "active" ORDER BY StockQuantity ASC LIMIT 5');
        return $this->db->resultSet();
    }
    
    public function getPendingReviewsCount() {
        $this->db->query('SELECT COUNT(*) as total FROM productreview WHERE Status = "pending"');
        $result = $this->db->single();
        return $result->total ?? 0;
    }
    
    public function getPendingReviewItems() {
        $this->db->query('
            SELECT pr.Rating, p.Name as product_name 
            FROM productreview pr
            JOIN product p ON pr.ProductID = p.ProductID
            WHERE pr.Status = "pending"
            ORDER BY pr.ReviewDate DESC
            LIMIT 5
        ');
        return $this->db->resultSet();
    }
    
    public function getActiveRentalsCount() {
        $this->db->query('SELECT COUNT(*) as total FROM equipmentrental WHERE Status = "active"');
        $result = $this->db->single();
        return $result->total ?? 0;
    }
    
    public function getActiveRentalItems() {
        $this->db->query('
            SELECT e.Name as equipment_name, er.EndTime 
            FROM equipmentrental er
            JOIN equipment e ON er.EquipmentID = e.EquipmentID
            WHERE er.Status = "active"
            ORDER BY er.EndTime ASC
            LIMIT 5
        ');
        return $this->db->resultSet();
    }
    
    public function getTopSellingProducts() {
        $this->db->query('
            SELECT p.Name, COUNT(poi.ProductID) as total_sales, SUM(poi.SubTotal) as total_revenue
            FROM productorderitem poi
            JOIN product p ON poi.ProductID = p.ProductID
            JOIN productorder po ON poi.OrderID = po.OrderID
            WHERE MONTH(po.OrderDate) = MONTH(CURRENT_DATE())
            AND YEAR(po.OrderDate) = YEAR(CURRENT_DATE())
            GROUP BY poi.ProductID, p.Name
            ORDER BY total_sales DESC
            LIMIT 5
        ');
        return $this->db->resultSet();
    }
    
    // Order Management Methods
    
    public function getAllOrders() {
        $this->db->query('
            SELECT 
                po.OrderID,
                po.OrderDate,
                po.TotalAmount,
                po.PaymentMethod,
                po.Status,
                u.Name as CustomerName,
                u.Email,
                COUNT(poi.OrderItemID) as item_count
            FROM productorder po
            JOIN user u ON po.PlayerID = u.UserID
            LEFT JOIN productorderitem poi ON po.OrderID = poi.OrderID
            GROUP BY po.OrderID
            ORDER BY po.OrderDate DESC
        ');
        return $this->db->resultSet();
    }
    
    public function getOrdersByStatus($status) {
        $this->db->query('
            SELECT 
                po.OrderID,
                po.OrderDate,
                po.TotalAmount,
                po.PaymentMethod,
                po.Status,
                u.Name as CustomerName,
                u.Email,
                COUNT(poi.OrderItemID) as item_count
            FROM productorder po
            JOIN user u ON po.PlayerID = u.UserID
            LEFT JOIN productorderitem poi ON po.OrderID = poi.OrderID
            WHERE po.Status = :status
            GROUP BY po.OrderID
            ORDER BY po.OrderDate DESC
        ');
        $this->db->bind(':status', $status);
        return $this->db->resultSet();
    }
    
    public function getOrderStats() {
        $stats = [
            'total' => 0,
            'pending' => 0,
            'processing' => 0,
            'completed' => 0,
            'cancelled' => 0
        ];
        
        // Get total orders
        $this->db->query('SELECT COUNT(*) as total FROM productorder');
        $result = $this->db->single();
        $stats['total'] = $result->total ?? 0;
        
        // Get pending orders
        $this->db->query('SELECT COUNT(*) as total FROM productorder WHERE Status = "pending"');
        $result = $this->db->single();
        $stats['pending'] = $result->total ?? 0;
        
        // Get processing orders
        $this->db->query('SELECT COUNT(*) as total FROM productorder WHERE Status = "processing"');
        $result = $this->db->single();
        $stats['processing'] = $result->total ?? 0;
        
        // Get completed orders
        $this->db->query('SELECT COUNT(*) as total FROM productorder WHERE Status = "completed"');
        $result = $this->db->single();
        $stats['completed'] = $result->total ?? 0;
        
        // Get cancelled orders
        $this->db->query('SELECT COUNT(*) as total FROM productorder WHERE Status = "cancelled"');
        $result = $this->db->single();
        $stats['cancelled'] = $result->total ?? 0;
        
        return $stats;
    }
    
    public function updateOrderStatus($orderId, $status) {
        $this->db->query('UPDATE productorder SET Status = :status WHERE OrderID = :order_id');
        $this->db->bind(':status', $status);
        $this->db->bind(':order_id', $orderId);
        
        return $this->db->execute();
    }
    
    // Inventory Management Methods
    
    public function getInventoryStats() {
        $stats = [];
        
        // Total stock value
        $this->db->query('SELECT SUM(Price * StockQuantity) as total_value FROM product WHERE Status = "active"');
        $result = $this->db->single();
        $stats['total_stock_value'] = $result->total_value ?? 0;
        
        // Low stock count (≤ 10)
        $this->db->query('SELECT COUNT(*) as total FROM product WHERE StockQuantity <= 10 AND Status = "active"');
        $result = $this->db->single();
        $stats['low_stock_count'] = $result->total ?? 0;
        
        // Out of stock count
        $this->db->query('SELECT COUNT(*) as total FROM product WHERE StockQuantity = 0 AND Status = "active"');
        $result = $this->db->single();
        $stats['out_of_stock_count'] = $result->total ?? 0;
        
        // Items in stock
        $this->db->query('SELECT COUNT(*) as total FROM product WHERE StockQuantity > 10 AND Status = "active"');
        $result = $this->db->single();
        $stats['in_stock_count'] = $result->total ?? 0;
        
        return $stats;
    }
    
    public function getAllInventoryItems() {
        $this->db->query('
            SELECT 
                ProductID,
                Name,
                Description,
                Category,
                Brand,
                Price,
                StockQuantity,
                Status,
                SKU
            FROM product
            ORDER BY Name ASC
        ');
        return $this->db->resultSet();
    }
}
?>