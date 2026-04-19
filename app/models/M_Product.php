<?php
class M_Product {
    private $db;
    
    public function __construct() {
        $this->db = new Database;
    }

    // Get all products
    public function getAllProducts() {
        $this->db->query('SELECT * FROM Product ORDER BY AddedDate DESC');
        return $this->db->resultSet();
    }

    // Get product by ID
    public function getProductById($productId) {
        $this->db->query('SELECT * FROM Product WHERE ProductID = :id');
        $this->db->bind(':id', $productId);
        return $this->db->single();
    }

    // Get products by category
    public function getProductsByCategory($category) {
        $this->db->query('SELECT * FROM Product WHERE Category = :category AND Status = "active" ORDER BY AddedDate DESC');
        $this->db->bind(':category', $category);
        return $this->db->resultSet();
    }

    // Get products by status
    public function getProductsByStatus($status) {
        $this->db->query('SELECT * FROM Product WHERE Status = :status ORDER BY AddedDate DESC');
        $this->db->bind(':status', $status);
        return $this->db->resultSet();
    }

    // Search products
    public function searchProducts($searchTerm) {
        $searchTerm = '%' . $searchTerm . '%';
        $this->db->query('
            SELECT * FROM Product 
            WHERE (Name LIKE :search OR Description LIKE :search OR Brand LIKE :search OR SKU LIKE :search) 
            ORDER BY Name ASC
        ');
        $this->db->bind(':search', $searchTerm);
        return $this->db->resultSet();
    }

    // Create new product
    public function createProduct($data) {
        $this->db->query('INSERT INTO Product (Name, Description, Category, Brand, Price, StockQuantity, Status, SKU, Weight, Dimensions, ProductImage) 
                          VALUES (:name, :description, :category, :brand, :price, :stock, :status, :sku, :weight, :dimensions, :image)');
        
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':category', $data['category']);
        $this->db->bind(':brand', $data['brand']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':stock', $data['stock']);
        $this->db->bind(':status', $data['status'] ?? 'active');
        $this->db->bind(':sku', $data['sku'] ?? null);
        $this->db->bind(':weight', $data['weight'] ?? null);
        $this->db->bind(':dimensions', $data['dimensions'] ?? null);
        $this->db->bind(':image', $data['image'] ?? null);
        
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    // Update product
    public function updateProduct($productId, $data) {
        $this->db->query('UPDATE Product SET 
                          Name = :name, 
                          Description = :description, 
                          Category = :category, 
                          Brand = :brand, 
                          Price = :price, 
                          StockQuantity = :stock, 
                          Status = :status, 
                          SKU = :sku, 
                          Weight = :weight, 
                          Dimensions = :dimensions
                          WHERE ProductID = :id');
        
        $this->db->bind(':id', $productId);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':category', $data['category']);
        $this->db->bind(':brand', $data['brand']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':stock', $data['stock']);
        $this->db->bind(':status', $data['status'] ?? 'active');
        $this->db->bind(':sku', $data['sku'] ?? null);
        $this->db->bind(':weight', $data['weight'] ?? null);
        $this->db->bind(':dimensions', $data['dimensions'] ?? null);
        
        return $this->db->execute();
    }

    // Delete product
    public function deleteProduct($productId) {
        $this->db->query('DELETE FROM Product WHERE ProductID = :id');
        $this->db->bind(':id', $productId);
        return $this->db->execute();
    }

    // Update product stock
    public function updateStock($productId, $quantity) {
        $this->db->query('UPDATE Product SET StockQuantity = :quantity WHERE ProductID = :id');
        $this->db->bind(':id', $productId);
        $this->db->bind(':quantity', $quantity);
        return $this->db->execute();
    }

    // Get low stock products
    public function getLowStockProducts($threshold = 10) {
        $this->db->query('SELECT * FROM Product WHERE StockQuantity <= :threshold AND Status = "active" ORDER BY StockQuantity ASC');
        $this->db->bind(':threshold', $threshold);
        return $this->db->resultSet();
    }

    // Get product statistics
    public function getProductStats() {
        $stats = [];
        
        // Total active products
        $this->db->query('SELECT COUNT(*) as total FROM Product WHERE Status = "active"');
        $result = $this->db->single();
        $stats['total_products'] = $result->total ?? 0;
        
        // Low stock count (≤ 10 units)
        $this->db->query('SELECT COUNT(*) as total FROM Product WHERE StockQuantity <= 10 AND Status = "active"');
        $result = $this->db->single();
        $stats['low_stock'] = $result->total ?? 0;
        
        // Products with average rating >= 4.5.
        // The subquery first finds qualifying products; the outer query counts them.
        $this->db->query('
            SELECT COUNT(*) as total
            FROM (
                SELECT p.ProductID
                FROM product p
                JOIN productreview pr ON p.ProductID = pr.ProductID AND pr.Status = "approved"
                WHERE p.Status = "active"
                GROUP BY p.ProductID
                HAVING AVG(pr.Rating) >= 4.5
            ) rated_products
        ');
        $result = $this->db->single();
        $stats['top_rated'] = $result->total ?? 0;
        
        // New products added this month
        $this->db->query('
            SELECT COUNT(*) as total 
            FROM Product 
            WHERE MONTH(AddedDate) = MONTH(CURRENT_DATE()) 
            AND YEAR(AddedDate) = YEAR(CURRENT_DATE())
        ');
        $result = $this->db->single();
        $stats['new_this_month'] = $result->total ?? 0;
        
        return $stats;
    }
    
    // Get products with category and review stats
    public function getAllProductsWithDetails() {
        $this->db->query('
            SELECT 
                p.ProductID,
                p.Name,
                p.Description,
                p.Category,
                p.Brand,
                p.Price,
                p.StockQuantity,
                p.Status,
                p.ProductImage,
                COALESCE(AVG(pr.Rating), 0) as avg_rating,
                COUNT(pr.ReviewID) as review_count
            FROM product p
            LEFT JOIN productreview pr ON p.ProductID = pr.ProductID AND pr.Status = "approved"
            GROUP BY p.ProductID
            ORDER BY p.AddedDate DESC
        ');
        return $this->db->resultSet();
    }

    // ===== PRODUCT IMAGE METHODS =====
    
    // Update product image path
    public function updateProductImage($productId, $imagePath) {
        try {
            $this->db->query('UPDATE Product SET ProductImage = :imagePath WHERE ProductID = :productId');
            $this->db->bind(':productId', $productId);
            $this->db->bind(':imagePath', $imagePath);
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error updating product image: " . $e->getMessage());
            return false;
        }
    }
    
    // Get product image path
    public function getProductImage($productId) {
        try {
            $this->db->query('SELECT ProductImage FROM Product WHERE ProductID = :productId');
            $this->db->bind(':productId', $productId);
            $result = $this->db->single();
            return $result ? $result->ProductImage : null;
        } catch (Exception $e) {
            error_log("Error getting product image: " . $e->getMessage());
            return null;
        }
    }
    
    // Delete product image (remove from database)
    public function deleteProductImage($productId) {
        try {
            $this->db->query('UPDATE Product SET ProductImage = NULL WHERE ProductID = :productId');
            $this->db->bind(':productId', $productId);
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error deleting product image: " . $e->getMessage());
            return false;
        }
    }
}
