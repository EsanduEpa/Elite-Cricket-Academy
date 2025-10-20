<?php
class Shop extends Controller {
    
    public function __construct() {
        $this->shopModel = $this->model('M_Shop');
    }

    public function index() {
        // Get featured products
        $data = [
                     'category' => 'Cricket Bats',
                'rating' => 4.5,
                'image' => 'cricket-bat-pro.svg',
                'stock' => 30,   'title' => 'Elite Cricket Accessories - Premium Cricket Equipment',
            'featured_products' => $this->getFeaturedProducts(),
            'categories' => $this->getCategories(),
            'all_products' => $this->getAllProducts(),
            'deals' => $this->getCurrentDeals(),
            'testimonials' => $this->getTestimonials()
        ];
        
        $this->view('shop/v_shop_home', $data);
    }

    public function category($categoryId = null) {
        if (!$categoryId) {
            redirect('shop');
        }
        
        $data = [
            'title' => 'Shop by Category - Elite Cricket Gear',
            'products' => $this->shopModel->getProductsByCategory($categoryId),
            'category' => $this->shopModel->getCategoryById($categoryId)
        ];
        
        $this->view('shop/v_category', $data);
    }

    public function product($productId = null) {
        if (!$productId) {
            redirect('shop');
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
}
?>