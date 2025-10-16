<?php
class Shop extends Controller {
    public function __construct() {
        // Simple constructor for shop dashboard
    }
    
    public function index() {
        // Redirect to dashboard by default
        redirect('shop/dashboard');
    }
    
    public function dashboard() {
        // Basic shop dashboard
        $data = [
            'title' => 'Shop Dashboard - Elite Cricket Academy',
            'user_name' => $_SESSION['user_name'] ?? 'Shop Employee',
            'total_orders' => 15,
            'pending_orders' => 3,
            'total_revenue' => '₨ 45,000',
            'top_products' => [
                ['name' => 'Cricket Bat', 'sales' => 12],
                ['name' => 'Helmet', 'sales' => 8],
                ['name' => 'Gloves', 'sales' => 15]
            ]
        ];

        $this->view('shop/dashboard', $data);
    }
}
?>