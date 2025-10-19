<?php
class Shop extends Controller {
    public function __construct() {
        // Check if user is logged in and has shop employee role
        requireAuth(['ShopEmployee']);
    }
    
    public function index() {
        redirect('shop/dashboard');
    }
    
    public function dashboard() {
        $data = [
            'title' => 'Shop Dashboard - Elite Cricket Academy',
            'user_name' => $_SESSION['user_name'] ?? 'Shop Employee',
            'stats' => $this->getDashboardStats()
        ];
        $this->view('shop/dashboard', $data);
    }
    
    // Order Management
    public function orders($status = 'all') {
        $data = [
            'title' => 'Order Management',
            'orders' => $this->getOrdersByStatus($status),
            'current_status' => $status
        ];
        $this->view('shop/orders', $data);
    }
    
    public function order_details($orderId) {
        $data = [
            'title' => 'Order Details',
            'order' => $this->getOrderById($orderId)
        ];
        $this->view('shop/order_details', $data);
    }
    
    public function update_order_status() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $orderId = $_POST['order_id'];
            $status = $_POST['status'];
            // Update order status logic here
            redirect('shop/orders');
        }
    }
    
    // Product Management
    public function products($category = 'all') {
        $data = [
            'title' => 'Product Management',
            'products' => $this->getProductsByCategory($category),
            'categories' => $this->getCategories(),
            'current_category' => $category
        ];
        $this->view('shop/products', $data);
    }
    
    public function add_product() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Add product logic
            redirect('shop/products');
        }
        $data = ['title' => 'Add Product'];
        $this->view('shop/add_product', $data);
    }
    
    public function edit_product($productId) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Update product logic
            redirect('shop/products');
        }
        $data = [
            'title' => 'Edit Product',
            'product' => $this->getProductById($productId)
        ];
        $this->view('shop/edit_product', $data);
    }
    
    public function delete_product($productId) {
        // Delete product logic
        redirect('shop/products');
    }
    
    // Inventory Management
    public function inventory() {
        $data = [
            'title' => 'Inventory Management',
            'inventory' => $this->getInventoryData(),
            'low_stock_items' => $this->getLowStockItems()
        ];
        $this->view('shop/inventory', $data);
    }
    
    public function update_stock() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Update stock logic
            redirect('shop/inventory');
        }
    }
    
    // Equipment Rental Management
    public function rentals() {
        $data = [
            'title' => 'Equipment Rentals',
            'rentals' => $this->getRentalData(),
            'equipment' => $this->getAvailableEquipment()
        ];
        $this->view('shop/rentals', $data);
    }
    
    public function update_rental_availability() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Update rental availability
            redirect('shop/rentals');
        }
    }
    
    // Reviews and Feedback
    public function reviews() {
        $data = [
            'title' => 'Reviews & Feedback',
            'reviews' => $this->getAllReviews(),
            'pending_reviews' => $this->getPendingReviews()
        ];
        $this->view('shop/reviews', $data);
    }
    
    public function moderate_review($reviewId, $action) {
        // Moderate review (approve/reject)
        redirect('shop/reviews');
    }
    
    // Supplement Prescriptions
    public function prescriptions() {
        $data = [
            'title' => 'Supplement Prescriptions',
            'prescription_requests' => $this->getPrescriptionRequests()
        ];
        $this->view('shop/prescriptions', $data);
    }
    
    public function request_prescription() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Submit prescription request
            redirect('shop/prescriptions');
        }
        $data = ['title' => 'Request Prescription'];
        $this->view('shop/request_prescription', $data);
    }
    
    // Sales Analytics
    public function analytics() {
        $data = [
            'title' => 'Sales Analytics',
            'sales_data' => $this->getSalesAnalytics(),
            'top_products' => $this->getTopProducts(),
            'revenue_trends' => $this->getRevenueTrends()
        ];
        $this->view('shop/analytics', $data);
    }
    
    // Facility Management
    public function facilities() {
        $data = [
            'title' => 'Facility Management',
            'facilities' => $this->getAllFacilities(),
            'bookings' => $this->getFacilityBookings()
        ];
        $this->view('shop/facilities', $data);
    }
    
    public function add_facility() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Add facility logic
            redirect('shop/facilities');
        }
        $data = ['title' => 'Add Facility'];
        $this->view('shop/add_facility', $data);
    }
    
    public function edit_facility($facilityId) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Update facility logic
            redirect('shop/facilities');
        }
        $data = [
            'title' => 'Edit Facility',
            'facility' => $this->getFacilityById($facilityId)
        ];
        $this->view('shop/edit_facility', $data);
    }
    
    // Helper Methods (Mock data for now - replace with actual database calls)
    private function getRentalData() {
        // Mock data - replace with database query
        return [
            ['id' => 156, 'equipment' => 'Professional Cricket Bat', 'player' => 'Ashen Perera', 'status' => 'active', 'due_date' => '2025-10-18 18:00:00'],
            ['id' => 155, 'equipment' => 'Bowling Machine', 'player' => 'Kavinda Silva', 'status' => 'overdue', 'due_date' => '2025-10-17 18:00:00'],
            ['id' => 154, 'equipment' => 'Cricket Helmet Set', 'player' => 'Nimal Fernando', 'status' => 'returned', 'due_date' => '2025-10-16 18:00:00'],
        ];
    }
    
    private function getAvailableEquipment() {
        // Mock data - replace with database query
        return [
            ['id' => 1, 'name' => 'Professional Cricket Bat #001', 'category' => 'Batting', 'condition' => 'Good', 'price' => 800, 'status' => 'available'],
            ['id' => 2, 'name' => 'Bowling Machine #002', 'category' => 'Training', 'condition' => 'Excellent', 'price' => 1500, 'status' => 'rented'],
            ['id' => 3, 'name' => 'Cricket Helmet #003', 'category' => 'Protective', 'condition' => 'New', 'price' => 600, 'status' => 'available'],
        ];
    }
    
    private function getAllFacilities() {
        // Mock data - replace with database query
        return [
            ['id' => 1, 'name' => 'Practice Net 1', 'capacity' => 8, 'hourly_rate' => 2000, 'status' => 'available'],
            ['id' => 2, 'name' => 'Practice Net 2', 'capacity' => 8, 'hourly_rate' => 2000, 'status' => 'occupied'],
            ['id' => 3, 'name' => 'Bowling Machine Area', 'capacity' => 4, 'hourly_rate' => 3500, 'status' => 'available'],
            ['id' => 4, 'name' => 'Main Ground', 'capacity' => 22, 'hourly_rate' => 5000, 'status' => 'maintenance'],
            ['id' => 5, 'name' => 'Indoor Training Hall', 'capacity' => 15, 'hourly_rate' => 2500, 'status' => 'available'],
            ['id' => 6, 'name' => 'Gymnasium', 'capacity' => 20, 'hourly_rate' => 4000, 'status' => 'occupied'],
        ];
    }
    
    private function getFacilityBookings() {
        // Mock data - replace with database query
        return [
            ['id' => 892, 'facility' => 'Practice Net 2', 'player' => 'Ashen Perera', 'start_time' => '14:00', 'end_time' => '15:30', 'status' => 'active'],
            ['id' => 891, 'facility' => 'Bowling Machine Area', 'player' => 'Kavinda Silva', 'start_time' => '16:00', 'end_time' => '17:00', 'status' => 'confirmed'],
            ['id' => 890, 'facility' => 'Indoor Training Hall', 'player' => 'Nimal Fernando', 'start_time' => '18:00', 'end_time' => '19:00', 'status' => 'confirmed'],
            ['id' => 889, 'facility' => 'Gymnasium', 'player' => 'Group Training', 'start_time' => '19:00', 'end_time' => '20:00', 'status' => 'active'],
        ];
    }
    
    private function getDashboardStats() {
        return [
            'total_orders' => 156,
            'pending_orders' => 8,
            'processing_orders' => 15,
            'completed_orders' => 133,
            'total_revenue' => 275000,
            'monthly_revenue' => 45000,
            'total_products' => 89,
            'low_stock_products' => 6,
            'active_rentals' => 12,
            'pending_reviews' => 4
        ];
    }
    
    private function getOrdersByStatus($status) {
        // Mock data - replace with database query
        return [
            ['id' => 1, 'customer' => 'John Smith', 'total' => 2500, 'status' => 'pending', 'date' => '2025-10-18'],
            ['id' => 2, 'customer' => 'Sarah Johnson', 'total' => 1200, 'status' => 'processing', 'date' => '2025-10-17'],
        ];
    }
    
    private function getProductsByCategory($category) {
        // Mock data - replace with database query
        return [
            ['id' => 1, 'name' => 'Cricket Bat Pro', 'category' => 'Batting', 'price' => 8500, 'stock' => 15, 'status' => 'active'],
            ['id' => 2, 'name' => 'Helmet Elite', 'category' => 'Protective', 'price' => 4200, 'stock' => 8, 'status' => 'active'],
        ];
    }
    
    private function getCategories() {
        return ['Batting', 'Bowling', 'Protective', 'Training', 'Merchandise', 'Accessories', 'Other'];
    }
    
    private function getOrderById($orderId) {
        // Mock data - replace with database query
        return [
            'id' => $orderId,
            'customer' => 'John Smith',
            'email' => 'john@example.com',
            'phone' => '+94771234567',
            'total' => 2500,
            'status' => 'pending',
            'date' => '2025-10-18',
            'items' => [
                ['product' => 'Cricket Bat', 'quantity' => 1, 'price' => 2500]
            ]
        ];
    }
    
    private function getProductById($productId) {
        // Mock data - replace with database query
        return [
            'id' => $productId,
            'name' => 'Cricket Bat Pro',
            'description' => 'Professional grade cricket bat',
            'category' => 'Batting',
            'price' => 8500,
            'stock' => 15,
            'brand' => 'MRF',
            'weight' => '1.2',
            'dimensions' => '96cm x 11cm x 7cm'
        ];
    }
    
    private function getInventoryData() {
        // Mock data - replace with database query
        return [
            ['product' => 'Cricket Bat Pro', 'current_stock' => 15, 'min_stock' => 10, 'status' => 'adequate'],
            ['product' => 'Helmet Elite', 'current_stock' => 3, 'min_stock' => 5, 'status' => 'low'],
        ];
    }
    
    private function getLowStockItems() {
        return [
            ['product' => 'Helmet Elite', 'current_stock' => 3, 'min_stock' => 5],
            ['product' => 'Gloves Pro', 'current_stock' => 2, 'min_stock' => 8],
        ];
    }
    
    private function getAllReviews() {
        return [
            ['id' => 1, 'product' => 'Cricket Bat Pro', 'customer' => 'John Smith', 'rating' => 5, 'status' => 'approved'],
            ['id' => 2, 'product' => 'Helmet Elite', 'customer' => 'Sarah Johnson', 'rating' => 4, 'status' => 'pending'],
        ];
    }
    
    private function getPendingReviews() {
        return [
            ['id' => 2, 'product' => 'Helmet Elite', 'customer' => 'Sarah Johnson', 'rating' => 4, 'review' => 'Good quality helmet'],
        ];
    }
    
    private function getPrescriptionRequests() {
        return [
            ['id' => 1, 'player' => 'Mike Wilson', 'supplement' => 'Protein Powder', 'status' => 'pending', 'date' => '2025-10-18'],
        ];
    }
    
    private function getSalesAnalytics() {
        return [
            'daily_sales' => 15000,
            'weekly_sales' => 85000,
            'monthly_sales' => 275000,
            'top_category' => 'Batting Equipment'
        ];
    }
    
    private function getTopProducts() {
        return [
            ['name' => 'Cricket Bat Pro', 'sales' => 45, 'revenue' => 382500],
            ['name' => 'Helmet Elite', 'sales' => 32, 'revenue' => 134400],
        ];
    }
    
    private function getRevenueTrends() {
        return [
            ['month' => 'August', 'revenue' => 245000],
            ['month' => 'September', 'revenue' => 289000],
            ['month' => 'October', 'revenue' => 275000],
        ];
    }
}
?>