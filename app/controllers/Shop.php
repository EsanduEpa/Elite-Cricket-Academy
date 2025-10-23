<?php
class Shop extends Controller {
    private $shopModel;
    private $productModel;
    
    public function __construct() {
        $this->shopModel = $this->model('M_Shop');
        $this->productModel = $this->model('M_Product');
    }

    public function index() {
        // Check if user is logged in as shop employee
        if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'Shop') {
            // Redirect to dashboard for shop employees
            redirect('shop/dashboard');
        }
        
        // For public users, show products page
        $data = [
            'category' => 'Cricket Bats',
            'rating' => 4.5,
            'image' => 'cricket-bat-pro.svg',
            'stock' => 30,
            'title' => 'Elite Cricket Accessories - Premium Cricket Equipment',
            'featured_products' => $this->getFeaturedProducts(),
            'categories' => $this->getCategories(),
            'all_products' => $this->getAllProducts(),
            'deals' => $this->getCurrentDeals(),
            'testimonials' => $this->getTestimonials()
        ];
        
        $this->view('shop/products', $data);
    }

    public function dashboard() {
        // Check authentication for shop employees
        requireAuth(['Shop']);
        
        $data = [
            'title' => 'Shop Dashboard - Elite Cricket Gear',
            'user_name' => $_SESSION['user_name'] ?? 'Shop Manager',
            'stats' => $this->getDashboardStats()
        ];
        
        $this->view('shop/dashboard', $data);
    }

    public function orders() {
        // Check authentication for shop employees
        requireAuth(['Shop']);
        
        $data = [
            'title' => 'Order Management - Elite Cricket Gear',
            'user_name' => $_SESSION['user_name'] ?? 'Shop Manager',
            'orders' => $this->getOrdersData()
        ];
        
        $this->view('shop/orders', $data);
    }

    public function inventory() {
        // Check authentication for shop employees
        requireAuth(['Shop']);
        
        $data = [
            'title' => 'Inventory Management - Elite Cricket Gear',
            'user_name' => $_SESSION['user_name'] ?? 'Shop Manager',
            'inventory' => $this->getInventoryData()
        ];
        
        $this->view('shop/inventory', $data);
    }

    public function rentals() {
        // Check authentication for shop employees
        requireAuth(['Shop']);
        
        $data = [
            'title' => 'Equipment Rentals - Elite Cricket Gear',
            'user_name' => $_SESSION['user_name'] ?? 'Shop Manager',
            'rentals' => $this->getRentalsData()
        ];
        
        $this->view('shop/rentals', $data);
    }

    public function reviews() {
        // Check authentication for shop employees
        requireAuth(['Shop']);
        
        $data = [
            'title' => 'Reviews & Feedback - Elite Cricket Gear',
            'user_name' => $_SESSION['user_name'] ?? 'Shop Manager',
            'reviews' => $this->getReviewsData()
        ];
        
        $this->view('shop/reviews', $data);
    }

    public function prescriptions() {
        // Check authentication for shop employees
        requireAuth(['Shop']);
        
        $data = [
            'title' => 'Prescriptions - Elite Cricket Gear',
            'user_name' => $_SESSION['user_name'] ?? 'Shop Manager',
            'prescriptions' => $this->getPrescriptionsData()
        ];
        
        $this->view('shop/prescriptions', $data);
    }

    public function facilities() {
        // Check authentication for shop employees
        requireAuth(['Shop']);
        
        $data = [
            'title' => 'Facility Management - Elite Cricket Gear',
            'user_name' => $_SESSION['user_name'] ?? 'Shop Manager',
            'facilities' => $this->getFacilitiesData()
        ];
        
        $this->view('shop/facilities', $data);
    }

    public function products() {
        // Check authentication for shop employees
        requireAuth(['Shop']);
        
        // Get product statistics
        $stats = $this->productModel->getProductStats();
        
        $data = [
            'title' => 'Product Management - Elite Cricket Gear',
            'user_name' => $_SESSION['user_name'] ?? 'Shop Manager',
            'products' => $this->productModel->getAllProducts(),
            'categories' => $this->getCategories(),
            'stats' => $stats
        ];
        
        $this->view('shop/products', $data);
    }

    public function category($categoryId = null) {
        if (!$categoryId) {
            redirect('shop/dashboard');
        }
        
        $data = [
            'title' => 'Shop by Category - Elite Cricket Gear',
            'products' => $this->shopModel->getProductsByCategory($categoryId),
            'category' => $this->shopModel->getCategoryById($categoryId)
        ];
        
        $this->view('shop/products', $data);
    }

    public function product($productId = null) {
        if (!$productId) {
            redirect('shop/dashboard');
        }
        
        $data = [
            'title' => 'Product Details - Elite Cricket Gear',
            'product' => $this->shopModel->getProductById($productId),
            'related_products' => $this->shopModel->getRelatedProducts($productId)
        ];
        
        $this->view('shop/v_product_detail', $data);
    }

    public function shop() {
        $data = [
            'title' => 'Shop All Products - Elite Cricket Gear',
            'all_products' => $this->getAllProducts(),
            'categories' => $this->getCategories(),
            'featured_products' => $this->getFeaturedProducts()
        ];
        
        $this->view('shop/shopping', $data);
    }

    public function accessories() {
        $data = [
            'title' => 'Accessories - Elite Cricket Gear',
            'accessories' => $this->getAccessories(),
            'categories' => $this->getAccessoryCategories()
        ];
        
        $this->view('shop/v_shop_accessories', $data);
    }

    public function cart() {
        $data = [
            'title' => 'Shopping Cart - Elite Cricket Gear',
            'cart_items' => $this->getCartItems()
        ];
        
        $this->view('shop/v_cart', $data);
    }

    public function search() {
        $searchTerm = $_GET['q'] ?? '';
        
        $data = [
            'title' => 'Search Results - Elite Cricket Gear',
            'search_term' => $searchTerm,
            'products' => $this->shopModel->searchProducts($searchTerm)
        ];
        
        $this->view('shop/v_search_results', $data);
    }

    // Helper methods for demo data
    private function getFeaturedProducts() {
        return [
            [
                'id' => 1,
                'name' => 'Pro Series Cricket Bat',
                'brand' => 'Elite Pro',
                'price' => 299.99,
                'original_price' => 349.99,
                'rating' => 4.8,
                'image' => 'cricket-bat-pro.svg',
                'is_featured' => true,
                'stock' => 15,
                'category' => 'Bats',
                'description' => 'Professional grade cricket bat made from premium English willow'
            ],
            [
                'id' => 2,
                'name' => 'Premium Leather Cricket Ball',
                'brand' => 'Elite Match',
                'price' => 45.99,
                'original_price' => null,
                'rating' => 4.9,
                'image' => 'cricket-ball-leather.svg',
                'is_featured' => true,
                'stock' => 50,
                'category' => 'Balls',
                'description' => 'Hand-stitched leather cricket ball for professional matches'
            ],
            [
                'id' => 3,
                'name' => 'Professional Batting Gloves',
                'brand' => 'Elite Guard',
                'price' => 89.99,
                'original_price' => 109.99,
                'rating' => 4.7,
                'image' => 'batting-gloves.svg',
                'is_featured' => true,
                'stock' => 25,
                'category' => 'Gloves',
                'description' => 'Superior protection and grip for serious batsmen'
            ],
            [
                'id' => 4,
                'name' => 'Elite Cricket Helmet',
                'brand' => 'Elite Safety',
                'price' => 159.99,
                'original_price' => null,
                'rating' => 4.8,
                'image' => 'cricket-helmet.svg',
                'is_featured' => true,
                'stock' => 12,
                'category' => 'Helmets',
                'description' => 'Advanced protection helmet with superior ventilation'
            ],
            [
                'id' => 5,
                'name' => 'Cricket Leg Pads',
                'brand' => 'Elite Guard',
                'price' => 129.99,
                'original_price' => 149.99,
                'rating' => 4.6,
                'image' => 'cricket-leg-pads.svg',
                'is_featured' => true,
                'stock' => 18,
                'category' => 'Pads',
                'description' => 'Lightweight and durable leg protection for batsmen'
            ],
            [
                'id' => 6,
                'name' => 'Cricket Training Jersey',
                'brand' => 'Elite Wear',
                'price' => 59.99,
                'original_price' => 79.99,
                'rating' => 4.5,
                'image' => 'training-jersey.svg',
                'is_featured' => true,
                'stock' => 35,
                'category' => 'Clothing',
                'description' => 'Moisture-wicking performance jersey for training and matches'
            ]
        ];
    }

    private function getCategories() {
        return [
            [
                'id' => 1,
                'name' => 'Cricket Bats',
                'icon' => 'fas fa-baseball-bat',
                'image' => 'cricket-bats.svg',
                'product_count' => 4,
                'description' => 'Professional and recreational cricket bats from top brands'
            ],
            [
                'id' => 2,
                'name' => 'Cricket Balls',
                'icon' => 'fas fa-baseball-ball',
                'image' => 'cricket-balls.svg',
                'product_count' => 3,
                'description' => 'Match-quality cricket balls for all levels of play'
            ],
            [
                'id' => 3,
                'name' => 'Batting Gloves',
                'icon' => 'fas fa-hand-paper',
                'image' => 'gloves-pads.svg',
                'product_count' => 3,
                'description' => 'Protective batting and wicket keeping gloves'
            ],
            [
                'id' => 4,
                'name' => 'Cricket Helmets',
                'icon' => 'fas fa-hard-hat',
                'image' => 'helmets.svg',
                'product_count' => 2,
                'description' => 'Safety helmets for batting and fielding'
            ],
            [
                'id' => 5,
                'name' => 'Cricket Pads',
                'icon' => 'fas fa-shield-alt',
                'image' => 'pads.svg',
                'product_count' => 2,
                'description' => 'Leg pads and protective equipment'
            ],
            [
                'id' => 6,
                'name' => 'Cricket Shoes',
                'icon' => 'fas fa-running',
                'image' => 'shoes.svg',
                'product_count' => 2,
                'description' => 'Professional cricket spikes and training shoes'
            ],
            [
                'id' => 7,
                'name' => 'Cricket Clothing',
                'icon' => 'fas fa-tshirt',
                'image' => 'clothing.svg',
                'product_count' => 2,
                'description' => 'Cricket jerseys, trousers and team wear'
            ],
            [
                'id' => 8,
                'name' => 'Bags & Accessories',
                'icon' => 'fas fa-suitcase',
                'image' => 'accessories.svg',
                'product_count' => 2,
                'description' => 'Safety helmets with advanced protection technology'
            ],
            [
                'id' => 5,
                'name' => 'Apparel',
                'icon' => '👕',
                'image' => 'apparel.svg',
                'product_count' => 67,
                'description' => 'Cricket jerseys, trousers, and training wear'
            ],
            [
                'id' => 6,
                'name' => 'Footwear',
                'icon' => '👟',
                'image' => 'footwear.svg',
                'product_count' => 24,
                'description' => 'Cricket shoes and spikes for optimal performance'
            ],
            [
                'id' => 7,
                'name' => 'Training Equipment',
                'icon' => '🎯',
                'image' => 'training-equipment.svg',
                'product_count' => 32,
                'description' => 'Practice nets, cones, and training accessories'
            ],
            [
                'id' => 8,
                'name' => 'Accessories',
                'icon' => '🎒',
                'image' => 'accessories.svg',
                'product_count' => 41,
                'description' => 'Kit bags, water bottles, and cricket accessories'
            ]
        ];
    }

    private function getAllProducts() {
        return [
            // Cricket Bats
            [
                'id' => 1,
                'name' => 'Pro Series Cricket Bat',
                'brand' => 'Elite Pro',
                'price' => 299.99,
                'sale_price' => 249.99,
                'category_id' => 1,
                'category' => 'Cricket Bats',
                'rating' => 4.8,
                'review_count' => 124,
                'image' => 'cricket-bat-pro.svg',
                'stock' => 15,
                'featured' => true,
                'features' => ['English Willow', 'Professional Grade', 'Balanced Weight']
            ],
            [
                'id' => 2,
                'name' => 'Championship Willow Bat',
                'brand' => 'Elite Pro',
                'price' => 449.99,
                'category_id' => 1,
                'category' => 'Cricket Bats',
                'rating' => 4.9,
                'review_count' => 89,
                'image' => 'cricket-bat-pro.svg',
                'stock' => 8,
                'featured' => false,
                'features' => ['Grade A+ Willow', 'Hand Selected', 'Tournament Ready']
            ],
            [
                'id' => 3,
                'name' => 'Junior Cricket Bat',
                'brand' => 'Elite Pro',
                'price' => 129.99,
                'sale_price' => 99.99,
                'category_id' => 1,
                'category' => 'Cricket Bats',
                'rating' => 4.6,
                'review_count' => 156,
                'image' => 'cricket-bat-pro.svg',
                'stock' => 25,
                'featured' => false,
                'features' => ['Youth Size', 'Lightweight', 'Durable']
            ],
            [
                'id' => 4,
                'name' => 'Training Cricket Bat',
                'brand' => 'Elite Sport',
                'price' => 79.99,
                'category_id' => 1,
                'category' => 'Cricket Bats',
                'rating' => 4.3,
                'review_count' => 203,
                'image' => 'cricket-bat-pro.svg',
                'stock' => 40,
                'featured' => false,
                'features' => ['Practice Grade', 'Affordable', 'Beginner Friendly']
            ],

            // Cricket Balls
            [
                'id' => 5,
                'name' => 'Premium Leather Cricket Ball',
                'brand' => 'Elite Match',
                'price' => 45.99,
                'category_id' => 2,
                'category' => 'Cricket Balls',
                'rating' => 4.9,
                'review_count' => 312,
                'image' => 'cricket-ball-leather.svg',
                'stock' => 50,
                'featured' => true,
                'features' => ['Hand Stitched', 'Leather', 'ICC Approved']
            ],
            [
                'id' => 6,
                'name' => 'Training Cricket Ball',
                'brand' => 'Elite Sport',
                'price' => 24.99,
                'category_id' => 2,
                'category' => 'Cricket Balls',
                'rating' => 4.4,
                'review_count' => 187,
                'image' => 'cricket-ball-leather.svg',
                'stock' => 75,
                'featured' => false,
                'features' => ['Synthetic', 'Durable', 'Practice Use']
            ],
            [
                'id' => 7,
                'name' => 'Match Quality Ball Set (6 Pack)',
                'brand' => 'Elite Match',
                'price' => 239.99,
                'sale_price' => 199.99,
                'category_id' => 2,
                'category' => 'Cricket Balls',
                'rating' => 4.8,
                'review_count' => 67,
                'image' => 'cricket-ball-leather.svg',
                'stock' => 12,
                'featured' => false,
                'features' => ['Professional Grade', 'Set of 6', 'Tournament Quality']
            ],

            // Batting Gloves
            [
                'id' => 8,
                'name' => 'Professional Batting Gloves',
                'brand' => 'Elite Guard',
                'price' => 89.99,
                'sale_price' => 74.99,
                'category_id' => 3,
                'category' => 'Batting Gloves',
                'rating' => 4.7,
                'review_count' => 145,
                'image' => 'batting-gloves.svg',
                'stock' => 25,
                'featured' => true,
                'features' => ['Superior Grip', 'Finger Protection', 'Professional Grade']
            ],
            [
                'id' => 9,
                'name' => 'Youth Batting Gloves',
                'brand' => 'Elite Guard',
                'price' => 49.99,
                'category_id' => 3,
                'category' => 'Batting Gloves',
                'rating' => 4.5,
                'review_count' => 98,
                'image' => 'batting-gloves.svg',
                'stock' => 35,
                'featured' => false,
                'features' => ['Youth Size', 'Comfortable', 'Durable']
            ],
            [
                'id' => 10,
                'name' => 'Wicket Keeping Gloves',
                'brand' => 'Elite Guard',
                'price' => 119.99,
                'category_id' => 3,
                'category' => 'Batting Gloves',
                'rating' => 4.8,
                'review_count' => 76,
                'image' => 'batting-gloves.svg',
                'stock' => 18,
                'featured' => false,
                'features' => ['Wicket Keeper', 'Extra Padding', 'Enhanced Grip']
            ],

            // Cricket Helmets
            [
                'id' => 11,
                'name' => 'Elite Cricket Helmet',
                'brand' => 'Elite Safety',
                'price' => 159.99,
                'category_id' => 4,
                'category' => 'Cricket Helmets',
                'rating' => 4.8,
                'review_count' => 134,
                'image' => 'cricket-helmet.svg',
                'stock' => 12,
                'featured' => true,
                'features' => ['Advanced Protection', 'Ventilation', 'Lightweight']
            ],
            [
                'id' => 12,
                'name' => 'Junior Safety Helmet',
                'brand' => 'Elite Safety',
                'price' => 99.99,
                'category_id' => 4,
                'category' => 'Cricket Helmets',
                'rating' => 4.6,
                'review_count' => 89,
                'image' => 'cricket-helmet.svg',
                'stock' => 20,
                'featured' => false,
                'features' => ['Youth Size', 'Safety Certified', 'Adjustable']
            ],

            // Cricket Pads
            [
                'id' => 13,
                'name' => 'Cricket Leg Pads',
                'brand' => 'Elite Guard',
                'price' => 129.99,
                'sale_price' => 109.99,
                'category_id' => 5,
                'category' => 'Cricket Pads',
                'rating' => 4.6,
                'review_count' => 112,
                'image' => 'cricket-leg-pads.svg',
                'stock' => 18,
                'featured' => true,
                'features' => ['Impact Protection', 'Comfortable Fit', 'Professional Grade']
            ],
            [
                'id' => 14,
                'name' => 'Wicket Keeper Pads',
                'brand' => 'Elite Guard',
                'price' => 149.99,
                'category_id' => 5,
                'category' => 'Cricket Pads',
                'rating' => 4.7,
                'review_count' => 67,
                'image' => 'cricket-leg-pads.svg',
                'stock' => 15,
                'featured' => false,
                'features' => ['Keeper Specific', 'Enhanced Mobility', 'Lightweight']
            ],

            // Cricket Shoes
            [
                'id' => 15,
                'name' => 'Professional Cricket Spikes',
                'brand' => 'Elite Performance',
                'price' => 179.99,
                'category_id' => 6,
                'category' => 'Cricket Shoes',
                'rating' => 4.8,
                'review_count' => 156,
                'image' => 'placeholder.jpg',
                'stock' => 22,
                'featured' => false,
                'features' => ['Metal Spikes', 'Professional', 'Superior Grip']
            ],
            [
                'id' => 16,
                'name' => 'Training Cricket Shoes',
                'brand' => 'Elite Performance',
                'price' => 99.99,
                'sale_price' => 79.99,
                'category_id' => 6,
                'category' => 'Cricket Shoes',
                'rating' => 4.5,
                'review_count' => 234,
                'image' => 'placeholder.jpg',
                'stock' => 30,
                'featured' => false,
                'features' => ['Rubber Sole', 'Training', 'Comfortable']
            ],

            // Cricket Clothing
            [
                'id' => 17,
                'name' => 'Elite Performance Jersey',
                'brand' => 'Elite Apparel',
                'price' => 59.99,
                'category_id' => 7,
                'category' => 'Cricket Clothing',
                'rating' => 4.4,
                'review_count' => 178,
                'image' => 'training-jersey.svg',
                'stock' => 45,
                'featured' => true,
                'features' => ['Moisture Wicking', 'UV Protection', 'Lightweight']
            ],
            [
                'id' => 18,
                'name' => 'Cricket Trousers',
                'brand' => 'Elite Apparel',
                'price' => 79.99,
                'category_id' => 7,
                'category' => 'Cricket Clothing',
                'rating' => 4.3,
                'review_count' => 123,
                'image' => 'placeholder.jpg',
                'stock' => 38,
                'featured' => false,
                'features' => ['Breathable', 'Professional', 'Durable']
            ],

            // Bags & Accessories
            [
                'id' => 19,
                'name' => 'Elite Cricket Bag',
                'brand' => 'Elite Gear',
                'price' => 149.99,
                'sale_price' => 119.99,
                'category_id' => 8,
                'category' => 'Bags & Accessories',
                'rating' => 4.7,
                'review_count' => 89,
                'image' => 'placeholder.jpg',
                'stock' => 25,
                'featured' => false,
                'features' => ['Multiple Compartments', 'Wheeled', 'Durable']
            ],
            [
                'id' => 20,
                'name' => 'Cricket Thigh Guard',
                'brand' => 'Elite Guard',
                'price' => 34.99,
                'category_id' => 8,
                'category' => 'Bags & Accessories',
                'rating' => 4.5,
                'review_count' => 156,
                'image' => 'placeholder.jpg',
                'stock' => 42,
                'featured' => false,
                'features' => ['Impact Protection', 'Comfortable', 'Adjustable']
            ],
            
            // Cricket Balls
            [
                'id' => 2,
                'name' => 'Premium Leather Cricket Ball',
                'brand' => 'Elite Match',
                'price' => 45.99,
                'original_price' => null,
                'category_id' => 2,
                'category' => 'Cricket Balls',
                'rating' => 4.9,
                'image' => 'cricket-ball-leather.svg',
                'stock' => 50,
                'featured' => true
            ],
            [
                'id' => 9,
                'name' => 'Practice Cricket Ball',
                'brand' => 'Elite Match',
                'price' => 25.99,
                'original_price' => null,
                'category_id' => 2,
                'category' => 'Cricket Balls',
                'rating' => 4.4,
                'image' => 'practice-cricket-ball.svg',
                'stock' => 100,
                'featured' => false
            ],
            [
                'id' => 10,
                'name' => 'Match Quality Ball Set (6)',
                'brand' => 'Elite Match',
                'price' => 249.99,
                'original_price' => 299.99,
                'category_id' => 2,
                'category' => 'Cricket Balls',
                'rating' => 4.8,
                'image' => 'cricket-ball-leather.svg',
                'stock' => 15,
                'featured' => false
            ],
            
            // Gloves & Pads
            [
                'id' => 3,
                'name' => 'Professional Batting Gloves',
                'brand' => 'Elite Guard',
                'price' => 89.99,
                'original_price' => 109.99,
                'category_id' => 3,
                'category' => 'Gloves & Pads',
                'rating' => 4.7,
                'image' => 'batting-gloves.svg',
                'stock' => 25,
                'featured' => true
            ],
            [
                'id' => 5,
                'name' => 'Cricket Leg Pads',
                'brand' => 'Elite Guard',
                'price' => 129.99,
                'original_price' => 149.99,
                'category_id' => 3,
                'category' => 'Gloves & Pads',
                'rating' => 4.6,
                'image' => 'cricket-leg-pads.svg',
                'stock' => 18,
                'featured' => true
            ],
            [
                'id' => 11,
                'name' => 'Wicket Keeping Gloves',
                'brand' => 'Elite Guard',
                'price' => 119.99,
                'original_price' => null,
                'category_id' => 3,
                'category' => 'Gloves & Pads',
                'rating' => 4.8,
                'image' => 'wicket-keeping-gloves.svg',
                'stock' => 20,
                'featured' => false
            ],
            
            // Helmets
            [
                'id' => 4,
                'name' => 'Elite Cricket Helmet',
                'brand' => 'Elite Safety',
                'price' => 159.99,
                'original_price' => null,
                'category_id' => 4,
                'category' => 'Helmets',
                'rating' => 4.8,
                'image' => 'placeholder.jpg',
                'stock' => 12,
                'featured' => true
            ],
            [
                'id' => 12,
                'name' => 'Junior Safety Helmet',
                'brand' => 'Elite Safety',
                'price' => 89.99,
                'original_price' => 109.99,
                'category_id' => 4,
                'category' => 'Helmets',
                'rating' => 4.5,
                'image' => 'placeholder.jpg',
                'stock' => 22,
                'featured' => false
            ],
            
            // Apparel
            [
                'id' => 6,
                'name' => 'Cricket Training Jersey',
                'brand' => 'Elite Wear',
                'price' => 59.99,
                'original_price' => 79.99,
                'category_id' => 5,
                'category' => 'Apparel',
                'rating' => 4.5,
                'image' => 'placeholder.jpg',
                'stock' => 35,
                'featured' => true
            ],
            [
                'id' => 13,
                'name' => 'Cricket Playing Trousers',
                'brand' => 'Elite Wear',
                'price' => 69.99,
                'original_price' => null,
                'category_id' => 5,
                'category' => 'Apparel',
                'rating' => 4.4,
                'image' => 'placeholder.jpg',
                'stock' => 30,
                'featured' => false
            ],
            [
                'id' => 14,
                'name' => 'Team Cricket Kit',
                'brand' => 'Elite Wear',
                'price' => 199.99,
                'original_price' => 249.99,
                'category_id' => 5,
                'category' => 'Apparel',
                'rating' => 4.7,
                'image' => 'placeholder.jpg',
                'stock' => 12,
                'featured' => false
            ],
            
            // Footwear
            [
                'id' => 15,
                'name' => 'Professional Cricket Spikes',
                'brand' => 'Elite Sports',
                'price' => 139.99,
                'original_price' => null,
                'category_id' => 6,
                'category' => 'Footwear',
                'rating' => 4.6,
                'image' => 'placeholder.jpg',
                'stock' => 18,
                'featured' => false
            ],
            [
                'id' => 16,
                'name' => 'Cricket Training Shoes',
                'brand' => 'Elite Sports',
                'price' => 89.99,
                'original_price' => 119.99,
                'category_id' => 6,
                'category' => 'Footwear',
                'rating' => 4.5,
                'image' => 'placeholder.jpg',
                'stock' => 24,
                'featured' => false
            ],
            
            // Training Equipment
            [
                'id' => 17,
                'name' => 'Batting Practice Net',
                'brand' => 'Elite Training',
                'price' => 299.99,
                'original_price' => 349.99,
                'category_id' => 7,
                'category' => 'Training Equipment',
                'rating' => 4.7,
                'image' => 'placeholder.jpg',
                'stock' => 5,
                'featured' => false
            ],
            [
                'id' => 18,
                'name' => 'Cricket Coaching Cones Set',
                'brand' => 'Elite Training',
                'price' => 39.99,
                'original_price' => null,
                'category_id' => 7,
                'category' => 'Training Equipment',
                'rating' => 4.3,
                'image' => 'placeholder.jpg',
                'stock' => 40,
                'featured' => false
            ],
            
            // Accessories
            [
                'id' => 19,
                'name' => 'Cricket Kit Bag',
                'brand' => 'Elite Gear',
                'price' => 79.99,
                'original_price' => 99.99,
                'category_id' => 8,
                'category' => 'Accessories',
                'rating' => 4.6,
                'image' => 'placeholder.jpg',
                'stock' => 28,
                'featured' => false
            ],
            [
                'id' => 20,
                'name' => 'Insulated Sports Water Bottle',
                'brand' => 'Elite Hydro',
                'price' => 24.99,
                'original_price' => null,
                'category_id' => 8,
                'category' => 'Accessories',
                'rating' => 4.4,
                'image' => 'placeholder.jpg',
                'stock' => 50,
                'featured' => false
            ]
        ];
    }

    private function getCurrentDeals() {
        return [
            [
                'title' => 'Up to 30% Off Premium Bats',
                'description' => 'Professional grade cricket bats at unbeatable prices',
                'discount' => '30%',
                'image' => 'deal-bats.svg',
                'cta' => 'Shop Bats Now'
            ],
            [
                'title' => 'Gloves + Pads Combo Deal',
                'description' => 'Buy any gloves and pads together and save 20%',
                'discount' => '20%',
                'image' => 'deal-combo.svg',
                'cta' => 'View Combos'
            ]
        ];
    }

    private function getTestimonials() {
        return [
            [
                'name' => 'Marcus Johnson',
                'role' => 'Professional Cricketer',
                'image' => 'testimonial-1.jpg',
                'rating' => 5,
                'text' => 'The quality of equipment from Elite Cricket Gear is exceptional. Their bats have improved my game significantly!'
            ],
            [
                'name' => 'Sarah Williams',
                'role' => 'Youth Coach',
                'image' => 'testimonial-2.jpg',
                'rating' => 5,
                'text' => 'I recommend Elite Cricket Gear to all my students. Great quality, fair prices, and excellent customer service.'
            ],
            [
                'name' => 'David Chen',
                'role' => 'Amateur Player',
                'image' => 'testimonial-3.jpg',
                'rating' => 5,
                'text' => 'Fast shipping and authentic products. My go-to store for all cricket equipment needs!'
            ]
        ];
    }

    private function getCartItems() {
        // This would normally come from session or database
        return isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
    }

    private function getAccessories() {
        return [
            [
                'id' => 21,
                'name' => 'Cricket Helmet',
                'brand' => 'GM',
                'image' => 'helmet-gm.jpg',
                'price' => 89.99,
                'original_price' => 119.99,
                'rating' => 4.8,
                'reviews' => 45,
                'category' => 'Safety',
                'stock' => 15,
                'features' => ['Titanium Grille', 'Ventilation System', 'Adjustable Fit']
            ],
            [
                'id' => 22,
                'name' => 'Cricket Pads',
                'brand' => 'Kookaburra',
                'image' => 'pads-kookaburra.jpg',
                'price' => 149.99,
                'original_price' => 189.99,
                'rating' => 4.9,
                'reviews' => 67,
                'category' => 'Protection',
                'stock' => 8,
                'features' => ['Lightweight Design', 'High Density Foam', 'Secure Straps']
            ],
            [
                'id' => 23,
                'name' => 'Socks',
                'brand' => 'Gray-Nicolls',
                'image' => 'socks-gray-nicolls.jpg',
                'price' => 24.99,
                'original_price' => 34.99,
                'rating' => 4.6,
                'reviews' => 89,
                'category' => 'Clothing',
                'stock' => 25,
                'features' => ['Moisture Wicking', 'Cushioned Sole', 'Reinforced Heel']
            ],
            [
                'id' => 24,
                'name' => 'Cricket Gloves',
                'brand' => 'SG',
                'image' => 'gloves-sg.jpg',
                'price' => 79.99,
                'original_price' => 99.99,
                'rating' => 4.7,
                'reviews' => 156,
                'category' => 'Batting',
                'stock' => 12,
                'features' => ['Premium Leather', 'Finger Protection', 'Superior Grip']
            ],
            [
                'id' => 25,
                'name' => 'Water Bottle',
                'brand' => 'Elite Sports',
                'image' => 'bottle-elite.jpg',
                'price' => 19.99,
                'original_price' => 29.99,
                'rating' => 4.4,
                'reviews' => 234,
                'category' => 'Accessories',
                'stock' => 50,
                'features' => ['BPA Free', '750ml Capacity', 'Easy Grip Design']
            ],
            [
                'id' => 26,
                'name' => 'Cricket Bag',
                'brand' => 'Adidas',
                'image' => 'bag-adidas.jpg',
                'price' => 129.99,
                'original_price' => 159.99,
                'rating' => 4.8,
                'reviews' => 78,
                'category' => 'Storage',
                'stock' => 6,
                'features' => ['Multiple Compartments', 'Wheeled Design', 'Durable Material']
            ]
        ];
    }

    private function getAccessoryCategories() {
        return [
            ['id' => 1, 'name' => 'Safety', 'count' => 2],
            ['id' => 2, 'name' => 'Protection', 'count' => 1],
            ['id' => 3, 'name' => 'Clothing', 'count' => 1],
            ['id' => 4, 'name' => 'Batting', 'count' => 1],
            ['id' => 5, 'name' => 'Accessories', 'count' => 1],
            ['id' => 6, 'name' => 'Storage', 'count' => 1]
        ];
    }

    // Profile Management
    public function profile() {
        // Get comprehensive user profile data
        $userModel = $this->model('M_Users');
        $userId = $_SESSION['user_id'];
        $userProfile = $userModel->getUserWithProfile($userId);
        
        $data = [
            'title' => 'My Profile',
            'user' => $userProfile,
            'user_name' => $_SESSION['user_name'] ?? 'Shop Manager'
        ];
        
        $this->view('shop/profile', $data);
    }

    // Update Profile
    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            $userModel = $this->model('M_Users');
            $userId = $_SESSION['user_id'];
            
            // Update basic user info
            $userData = [
                'user_id' => $userId,
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email']),
                'phone_number' => trim($_POST['phone_number']),
                'address' => trim($_POST['address']),
                'school' => trim($_POST['school']),
                'role' => $_SESSION['user_role'], // Keep current role
                'status' => 'active' // Keep active
            ];
            
            // Validate data
            $errors = [];
            if (empty($userData['name'])) {
                $errors[] = 'Name is required';
            }
            if (empty($userData['email'])) {
                $errors[] = 'Email is required';
            }
            if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Invalid email format';
            }
            
            if (empty($errors)) {
                if ($userModel->updateUser($userData)) {
                    // Update session data
                    $_SESSION['user_name'] = $userData['name'];
                    $_SESSION['user_email'] = $userData['email'];
                    
                    flash('profile_message', 'Profile updated successfully');
                } else {
                    flash('profile_message', 'Failed to update profile', 'alert alert-danger');
                }
            } else {
                flash('profile_message', implode('<br>', $errors), 'alert alert-danger');
            }
        }
        
        redirect('shop/profile');
    }

    // Deactivate Account
    public function deactivateAccount() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userModel = $this->model('M_Users');
            $userId = $_SESSION['user_id'];
            
            if ($userModel->suspendUser($userId, 9999)) { // Long suspension = deactivation
                // Clear session and redirect to login
                session_destroy();
                flash('login_message', 'Your account has been deactivated successfully');
                redirect('login');
            } else {
                flash('profile_message', 'Failed to deactivate account', 'alert alert-danger');
                redirect('shop/profile');
            }
        } else {
            redirect('shop/profile');
        }
    }

    // Helper method for dashboard statistics
    private function getDashboardStats() {
        return [
            'total_orders' => 125, // Mock data - would come from database
            'pending_orders' => 8, // Mock data - would come from database
            'monthly_revenue' => 45000, // Mock data - would come from database
            'total_products' => count($this->getAllProducts()),
            'total_categories' => count($this->getCategories()),
            'featured_products' => count($this->getFeaturedProducts()),
            'active_deals' => count($this->getCurrentDeals()),
            'low_stock_products' => 3, // Mock data - would come from database
            'pending_reviews' => 5, // Mock data - would come from database
            'active_rentals' => 12 // Mock data - would come from database
        ];
    }

    // Helper methods for new shop management pages
    private function getOrdersData() {
        // Mock data - would come from database
        return [
            [
                'id' => 1,
                'customer_name' => 'John Doe',
                'order_date' => '2024-10-20',
                'total' => 299.99,
                'status' => 'pending',
                'items' => 3
            ],
            [
                'id' => 2,
                'customer_name' => 'Jane Smith',
                'order_date' => '2024-10-19',
                'total' => 159.99,
                'status' => 'completed',
                'items' => 2
            ]
        ];
    }

    private function getInventoryData() {
        // Mock data - would come from database
        return [
            [
                'id' => 1,
                'name' => 'Pro Series Cricket Bat',
                'stock' => 15,
                'min_stock' => 5,
                'price' => 299.99,
                'status' => 'in_stock'
            ],
            [
                'id' => 2,
                'name' => 'Cricket Helmet',
                'stock' => 3,
                'min_stock' => 5,
                'price' => 159.99,
                'status' => 'low_stock'
            ]
        ];
    }

    private function getRentalsData() {
        // Mock data - would come from database
        return [
            [
                'id' => 1,
                'equipment' => 'Bowling Machine A',
                'renter' => 'Youth Academy',
                'start_date' => '2024-10-20',
                'end_date' => '2024-10-22',
                'status' => 'active'
            ],
            [
                'id' => 2,
                'equipment' => 'Practice Nets',
                'renter' => 'Local Club',
                'start_date' => '2024-10-21',
                'end_date' => '2024-10-21',
                'status' => 'active'
            ]
        ];
    }

    private function getReviewsData() {
        // Mock data - would come from database
        return [
            [
                'id' => 1,
                'product' => 'Pro Series Cricket Bat',
                'customer' => 'Mark Wilson',
                'rating' => 5,
                'comment' => 'Excellent bat, great quality!',
                'date' => '2024-10-19',
                'status' => 'pending'
            ],
            [
                'id' => 2,
                'product' => 'Cricket Helmet',
                'customer' => 'Sarah Johnson',
                'rating' => 4,
                'comment' => 'Good protection, comfortable fit.',
                'date' => '2024-10-18',
                'status' => 'approved'
            ]
        ];
    }

    private function getPrescriptionsData() {
        // Mock data - would come from database
        return [
            [
                'id' => 1,
                'patient' => 'Alex Chen',
                'prescribed_by' => 'Dr. Smith',
                'supplements' => 'Whey Protein, Creatine',
                'date' => '2024-10-19',
                'status' => 'active'
            ],
            [
                'id' => 2,
                'patient' => 'Emma Brown',
                'prescribed_by' => 'Dr. Johnson',
                'supplements' => 'Vitamin D, Calcium',
                'date' => '2024-10-18',
                'status' => 'completed'
            ]
        ];
    }

    private function getFacilitiesData() {
        // Mock data - would come from database
        return [
            [
                'id' => 1,
                'name' => 'Main Cricket Ground',
                'capacity' => 500,
                'status' => 'available',
                'booking_rate' => 2000.00,
                'amenities' => 'Floodlights, Pavilion, Parking'
            ],
            [
                'id' => 2,
                'name' => 'Indoor Training Center',
                'capacity' => 50,
                'status' => 'maintenance',
                'booking_rate' => 1000.00,
                'amenities' => 'AC, Nets, Equipment Storage'
            ]
        ];
    }

    // Upload/Update Profile Image
    public function uploadProfileImage() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_FILES['profile_image']) || $_FILES['profile_image']['error'] === UPLOAD_ERR_NO_FILE) {
                echo json_encode(['success' => false, 'message' => 'No file was uploaded']);
                return;
            }
            
            $file = $_FILES['profile_image'];
            
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $errorMessages = [
                    UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize in php.ini',
                    UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE in HTML form',
                    UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
                    UPLOAD_ERR_NO_FILE => 'No file was uploaded',
                    UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                    UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                    UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload'
                ];
                $message = $errorMessages[$file['error']] ?? 'Unknown upload error occurred';
                echo json_encode(['success' => false, 'message' => $message]);
                return;
            }
            
            $maxFileSize = 2 * 1024 * 1024;
            if ($file['size'] > $maxFileSize) {
                echo json_encode(['success' => false, 'message' => 'File size must be less than 2MB']);
                return;
            }
            
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            $fileType = mime_content_type($file['tmp_name']);
            
            if (!in_array($fileType, $allowedTypes)) {
                echo json_encode(['success' => false, 'message' => 'Only JPG, JPEG, and PNG files are allowed']);
                return;
            }
            
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $userId = $_SESSION['user_id'] ?? 1;
            $newFileName = 'profile_' . $userId . '_' . time() . '.' . $fileExtension;
            
            $projectRoot = dirname(APPROOT);
            $uploadDir = $projectRoot . '/public/uploads/profile_images/';
            
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    echo json_encode(['success' => false, 'message' => 'Failed to create upload directory']);
                    return;
                }
            }
            
            $uploadPath = $uploadDir . $newFileName;
            
            try {
                $userModel = $this->model('M_Users');
                $oldImage = $userModel->getProfileImage($userId);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
                return;
            }
            
            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                $relativePath = 'uploads/profile_images/' . $newFileName;
                
                if ($userModel->updateProfileImage($userId, $relativePath)) {
                    if ($oldImage) {
                        $oldImagePath = $projectRoot . '/public/' . $oldImage;
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }
                    
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Profile image updated successfully',
                        'image_url' => URLROOT . '/' . $relativePath
                    ]);
                } else {
                    unlink($uploadPath);
                    echo json_encode(['success' => false, 'message' => 'Failed to update profile image in database']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        }
    }

    // Delete Profile Image
    public function deleteProfileImage() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userId = $_SESSION['user_id'] ?? 1;
            
            try {
                $userModel = $this->model('M_Users');
                $imagePath = $userModel->getProfileImage($userId);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
                return;
            }
            
            if ($imagePath) {
                $projectRoot = dirname(APPROOT);
                $fullPath = $projectRoot . '/public/' . $imagePath;
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
                
                if ($userModel->deleteProfileImage($userId)) {
                    echo json_encode(['success' => true, 'message' => 'Profile image deleted successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to delete image from database']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'No profile image found']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        }
    }

    // Upload Product Image
    public function uploadProductImage() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['productImage'])) {
            $productId = $_POST['productId'] ?? null;
            
            if (!$productId) {
                echo json_encode(['success' => false, 'message' => 'Product ID is required']);
                return;
            }
            
            $file = $_FILES['productImage'];
            
            // Validate file type
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!in_array($file['type'], $allowedTypes)) {
                echo json_encode(['success' => false, 'message' => 'Only JPG, JPEG, and PNG images are allowed']);
                return;
            }
            
            // Validate file size (5MB for product images)
            $maxSize = 5 * 1024 * 1024; // 5MB in bytes
            if ($file['size'] > $maxSize) {
                echo json_encode(['success' => false, 'message' => 'Image size must be less than 5MB']);
                return;
            }
            
            // Create unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'product_' . $productId . '_' . time() . '.' . $extension;
            
            // Get project root and set upload path
            $projectRoot = dirname(APPROOT);
            $uploadDir = $projectRoot . '/public/uploads/shop_product/';
            $uploadPath = $uploadDir . $filename;
            $relativePath = 'uploads/shop_product/' . $filename;
            
            // Ensure directory exists
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }
            
            // Delete old image if exists
            $productModel = $this->model('M_Product');
            $oldImage = $productModel->getProductImage($productId);
            if ($oldImage) {
                $oldImagePath = $projectRoot . '/public/' . $oldImage;
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            
            // Move uploaded file
            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                // Update database
                if ($productModel->updateProductImage($productId, $relativePath)) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Product image uploaded successfully',
                        'image_url' => URLROOT . '/' . $relativePath
                    ]);
                } else {
                    unlink($uploadPath);
                    echo json_encode(['success' => false, 'message' => 'Failed to update product image in database']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request or no file uploaded']);
        }
    }

    // Delete Product Image
    public function deleteProductImage() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $productId = $_POST['productId'] ?? null;
            
            if (!$productId) {
                echo json_encode(['success' => false, 'message' => 'Product ID is required']);
                return;
            }
            
            try {
                $productModel = $this->model('M_Product');
                $imagePath = $productModel->getProductImage($productId);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
                return;
            }
            
            if ($imagePath) {
                $projectRoot = dirname(APPROOT);
                $fullPath = $projectRoot . '/public/' . $imagePath;
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
                
                if ($productModel->deleteProductImage($productId)) {
                    echo json_encode(['success' => true, 'message' => 'Product image deleted successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to delete image from database']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'No product image found']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        }
    }

    // Add CRUD operations for products
    public function addProduct() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize and collect data
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'category' => trim($_POST['category'] ?? ''),
                'brand' => trim($_POST['brand'] ?? ''),
                'price' => floatval($_POST['price'] ?? 0),
                'stock' => intval($_POST['stock'] ?? 0),
                'status' => $_POST['status'] ?? 'active',
                'weight' => !empty($_POST['weight']) ? floatval($_POST['weight']) : null,
                'dimensions' => !empty(trim($_POST['dimensions'] ?? '')) ? trim($_POST['dimensions']) : null,
                'image' => null
            ];
            
            // Comprehensive validation
            $errors = [];
            
            // Validate product name
            if (empty($data['name'])) {
                $errors[] = 'Product name is required';
            } elseif (strlen($data['name']) < 3) {
                $errors[] = 'Product name must be at least 3 characters';
            } elseif (strlen($data['name']) > 255) {
                $errors[] = 'Product name is too long (max 255 characters)';
            }
            
            // Validate description
            if (!empty($data['description']) && strlen($data['description']) > 1000) {
                $errors[] = 'Description is too long (max 1000 characters)';
            }
            
            // Validate category
            if (empty($data['category'])) {
                $errors[] = 'Category is required';
            }
            
            // Validate brand
            if (!empty($data['brand']) && strlen($data['brand']) > 100) {
                $errors[] = 'Brand name is too long (max 100 characters)';
            }
            
            // Validate price
            if ($data['price'] <= 0) {
                $errors[] = 'Price must be greater than 0';
            } elseif ($data['price'] > 1000000) {
                $errors[] = 'Price is too high (max ₨1,000,000)';
            }
            
            // Validate stock
            if ($data['stock'] < 0) {
                $errors[] = 'Stock quantity cannot be negative';
            } elseif ($data['stock'] > 10000) {
                $errors[] = 'Stock quantity is too high (max 10,000)';
            }
            
            // Validate status
            $validStatuses = ['active', 'discontinued', 'out_of_stock'];
            if (!in_array($data['status'], $validStatuses)) {
                $data['status'] = 'active';
            }
            
            // Check for validation errors
            if (!empty($errors)) {
                echo json_encode([
                    'success' => false, 
                    'message' => implode(', ', $errors)
                ]);
                return;
            }
            
            try {
                $productModel = $this->model('M_Product');
                $productId = $productModel->createProduct($data);
                
                if ($productId) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Product added successfully',
                        'productId' => $productId
                    ]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to add product to database']);
                }
            } catch (Exception $e) {
                error_log('Error adding product: ' . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        }
    }

    public function updateProductData() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $productId = intval($_POST['productId'] ?? 0);
            
            if ($productId <= 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
                return;
            }
            
            // Sanitize and collect data
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'category' => trim($_POST['category'] ?? ''),
                'brand' => trim($_POST['brand'] ?? ''),
                'price' => floatval($_POST['price'] ?? 0),
                'stock' => intval($_POST['stock'] ?? 0),
                'status' => $_POST['status'] ?? 'active',
                'weight' => !empty($_POST['weight']) ? floatval($_POST['weight']) : null,
                'dimensions' => !empty(trim($_POST['dimensions'] ?? '')) ? trim($_POST['dimensions']) : null
            ];
            
            // Comprehensive validation
            $errors = [];
            
            // Validate product name
            if (empty($data['name'])) {
                $errors[] = 'Product name is required';
            } elseif (strlen($data['name']) < 3) {
                $errors[] = 'Product name must be at least 3 characters';
            } elseif (strlen($data['name']) > 255) {
                $errors[] = 'Product name is too long (max 255 characters)';
            }
            
            // Validate description
            if (!empty($data['description']) && strlen($data['description']) > 1000) {
                $errors[] = 'Description is too long (max 1000 characters)';
            }
            
            // Validate category
            if (empty($data['category'])) {
                $errors[] = 'Category is required';
            }
            
            // Validate brand
            if (!empty($data['brand']) && strlen($data['brand']) > 100) {
                $errors[] = 'Brand name is too long (max 100 characters)';
            }
            
            // Validate price
            if ($data['price'] <= 0) {
                $errors[] = 'Price must be greater than 0';
            } elseif ($data['price'] > 1000000) {
                $errors[] = 'Price is too high (max ₨1,000,000)';
            }
            
            // Validate stock
            if ($data['stock'] < 0) {
                $errors[] = 'Stock quantity cannot be negative';
            } elseif ($data['stock'] > 10000) {
                $errors[] = 'Stock quantity is too high (max 10,000)';
            }
            
            // Validate status
            $validStatuses = ['active', 'discontinued', 'out_of_stock'];
            if (!in_array($data['status'], $validStatuses)) {
                $data['status'] = 'active';
            }
            
            // Check for validation errors
            if (!empty($errors)) {
                echo json_encode([
                    'success' => false,
                    'message' => implode(', ', $errors)
                ]);
                return;
            }
            
            $productModel = $this->model('M_Product');
            
            if ($productModel->updateProduct($productId, $data)) {
                echo json_encode(['success' => true, 'message' => 'Product updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update product']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        }
    }

    public function deleteProductData() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $productId = intval($_POST['productId'] ?? 0);
            
            if ($productId <= 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
                return;
            }
            
            $productModel = $this->model('M_Product');
            
            // Delete product image first
            $imagePath = $productModel->getProductImage($productId);
            if ($imagePath) {
                $projectRoot = dirname(APPROOT);
                $fullPath = $projectRoot . '/public/' . $imagePath;
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }
            
            // Delete product from database
            if ($productModel->deleteProduct($productId)) {
                echo json_encode(['success' => true, 'message' => 'Product deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete product']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        }
    }

    public function getProduct() {
        header('Content-Type: application/json');
        
        $productId = intval($_GET['id'] ?? 0);
        
        if ($productId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
            return;
        }
        
        $productModel = $this->model('M_Product');
        $product = $productModel->getProductById($productId);
        
        if ($product) {
            echo json_encode(['success' => true, 'product' => $product]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
        }
    }

    public function getProducts() {
        header('Content-Type: application/json');
        
        $productModel = $this->model('M_Product');
        $products = $productModel->getAllProducts();
        
        echo json_encode(['success' => true, 'products' => $products]);
    }
}
?>