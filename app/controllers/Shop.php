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
        if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'ShopEmployee') {
            redirect('shop/dashboard');
        }
        
        // For public users, show products page with real data
        $products = $this->shopModel->getAllProductsReal();
        $categories = $this->shopModel->getProductCategories();
        $featured = $this->shopModel->getFeaturedProductsReal(6);
        
        $data = [
            'title' => 'Elite Cricket Accessories - Premium Cricket Equipment',
            'featured_products' => $featured,
            'categories' => $categories,
            'all_products' => $products,
            'deals' => $this->getCurrentDeals(),
            'testimonials' => $this->getTestimonials()
        ];
        
        $this->view('shop/products', $data);
    }

    public function dashboard() {
        // Check authentication for shop employees
        requireAuth(['ShopEmployee', 'Shop']);
        
        $data = [
            'title' => 'Shop Dashboard - Elite Cricket Gear',
            'user_name' => $_SESSION['user_name'] ?? 'ShopEmployee',
            'stats' => $this->getDashboardStats()
        ];
        
        $this->view('shop/dashboard', $data);
    }

    public function orders($status = 'all') {
        // Check authentication for shop employees
        requireAuth(['ShopEmployee', 'Shop']);
        
        // Get order statistics
        $stats = $this->shopModel->getOrderStats();
        
        // Get orders based on status filter
        if ($status === 'all') {
            $orders = $this->shopModel->getAllOrders();
        } else {
            $orders = $this->shopModel->getOrdersByStatus($status);
        }
        
        $data = [
            'title' => 'Order Management - Elite Cricket Gear',
            'user_name' => $_SESSION['user_name'] ?? 'Shop Manager',
            'stats' => $stats,
            'orders' => $orders,
            'current_status' => $status
        ];
        
        $this->view('shop/orders', $data);
    }
    
    public function updateOrderStatus() {
        // Check authentication for shop employees
        requireAuth(['ShopEmployee', 'Shop']);
        
        // Check if POST request
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get JSON data
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (isset($input['orderId']) && isset($input['status'])) {
                $orderId = $input['orderId'];
                $status = $input['status'];
                
                // Update order status
                if ($this->shopModel->updateOrderStatus($orderId, $status)) {
                    echo json_encode([
                        'success' => true,
                        'message' => "Order #$orderId status updated to $status"
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to update order status'
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid request data'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
        }
        exit;
    }

    public function inventory() {
        // Check authentication for shop employees
        requireAuth(['ShopEmployee', 'Shop']);
        
        // Get inventory statistics
        $stats = $this->shopModel->getInventoryStats();
        
        // Get all inventory items
        $inventory = $this->shopModel->getAllInventoryItems();
        
        $data = [
            'title' => 'Inventory Management - Elite Cricket Gear',
            'user_name' => $_SESSION['user_name'] ?? 'Shop Manager',
            'stats' => $stats,
            'inventory' => $inventory
        ];
        
        $this->view('shop/inventory', $data);
    }

    public function rentals() {
        // Check authentication for shop employees
        requireAuth(['ShopEmployee', 'Shop']);
        
        $data = [
            'title' => 'Equipment Rentals - Elite Cricket Gear',
            'user_name' => $_SESSION['user_name'] ?? 'Shop Manager',
            'rentals' => $this->shopModel->getAllRentals(),
            'returns' => $this->shopModel->getAllEquipmentReturns(),
            'rental_stats' => $this->shopModel->getRentalManagementStats(),
        ];
        
        $this->view('shop/rentals', $data);
    }

    public function add_equipment_return() {
        requireAuth(['ShopEmployee', 'Shop']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('shop/rentals');
        }

        $rentalId = (int)($_POST['rental_id'] ?? 0);
        $returnedAt = trim((string)($_POST['returned_at'] ?? ''));
        $returnStatus = trim((string)($_POST['return_status'] ?? 'received'));
        $damageStatus = trim((string)($_POST['damage_status'] ?? 'not_damaged'));
        $paymentStatus = trim((string)($_POST['payment_status'] ?? ''));
        $notes = trim((string)($_POST['notes'] ?? ''));
        $inspectedBy = (int)($_SESSION['user_id'] ?? 0);

        $result = $this->shopModel->createEquipmentReturn([
            'rental_id' => $rentalId,
            'returned_at' => $returnedAt,
            'return_status' => $returnStatus,
            'damage_status' => $damageStatus,
            'payment_status' => $paymentStatus,
            'notes' => $notes,
            'inspected_by' => $inspectedBy,
        ]);

        if (!empty($result['success'])) {
            flash('shop_rental_message', $result['message'] ?? 'Return recorded successfully.');
        } else {
            flash('shop_rental_message', $result['message'] ?? 'Could not record the return.', 'alert alert-danger');
        }

        redirect('shop/rentals');
    }

    public function reviews() {
        // Check authentication for shop employees
        requireAuth(['ShopEmployee', 'Shop']);
        
        $data = [
            'title' => 'Reviews & Feedback - Elite Cricket Gear',
            'user_name' => $_SESSION['user_name'] ?? 'Shop Manager',
            'reviews' => $this->shopModel->getAllProductReviews()
        ];
        
        $this->view('shop/reviews', $data);
    }

    public function prescriptions() {
        // Check authentication for shop employees
        requireAuth(['ShopEmployee', 'Shop']);
        
        $data = [
            'title' => 'Prescriptions - Elite Cricket Gear',
            'user_name' => $_SESSION['user_name'] ?? 'Shop Manager',
            'prescriptions' => $this->shopModel->getSupplementPrescriptions()
        ];
        
        $this->view('shop/prescriptions', $data);
    }

    public function facilities() {
        // Check authentication for shop employees
        requireAuth(['ShopEmployee', 'Shop']);

        $facilities = $this->shopModel->getAllFacilities();
        $facilityStats = $this->shopModel->getFacilityStats();
        $slotModel = $this->model('M_SlotPlayer');
        $slotBookings = $slotModel->getFacilityOnlyBookingsForCounter(0, 0);
        $todaysBookings = array_map(function($booking) {
            $row = new stdClass();
            $row->FacilityBookingID = (int) ($booking->BookingID ?? 0);
            $row->facility_name = (string) ($booking->FacilityName ?? 'Unknown Facility');
            $row->player_name = (string) ($booking->PlayerName ?? 'Unknown Player');
            $row->player_email = (string) ($booking->PlayerEmail ?? 'N/A');
            $row->StartTime = $booking->StartTime ?? null;
            $row->EndTime = $booking->EndTime ?? null;
            $row->TotalCost = (float) ($booking->AmountCharged ?? 0);
            $row->Location = (string) ($booking->FacilityName ?? 'N/A');
            $row->Status = (string) ($booking->Status ?? 'confirmed');
            return $row;
        }, $slotBookings);

        $data = [
            'title' => 'Facility Management - Elite Cricket Gear',
            'user_name' => $_SESSION['user_name'] ?? 'Shop Manager',
            'facilities' => $facilities,
            'totalFacilities' => $facilityStats->total_facilities ?? 0,
            'availableFacilities' => $facilityStats->available_facilities ?? 0,
            'todaysBookingCount' => $this->shopModel->getTodaysFacilityBookings(),
            'todaysBookings' => $todaysBookings,
            'facilitiesInMaintenance' => $this->shopModel->getFacilitiesInMaintenance(),
            'todaysRevenue' => $this->shopModel->getTodaysFacilityRevenue()
        ];

        $this->view('shop/facilities', $data);
    }

    public function products() {
        // Check authentication for shop employees
        requireAuth(['ShopEmployee', 'Shop']);
        
        // Get product statistics
        $stats = $this->productModel->getProductStats();
        
        // Get all products with details
        $products = $this->productModel->getAllProductsWithDetails();
        
        $data = [
            'title' => 'Product Management - Elite Cricket Gear',
            'user_name' => $_SESSION['user_name'] ?? 'Shop Manager',
            'products' => $products,
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
        $products = $this->shopModel->getAllProductsReal();
        $categories = $this->shopModel->getProductCategories();
        $featured = $this->shopModel->getFeaturedProductsReal(6);
        
        $data = [
            'title' => 'Shop All Products - Elite Cricket Gear',
            'all_products' => $products,
            'categories' => $categories,
            'featured_products' => $featured
        ];
        
        $this->view('shop/shopping', $data);
    }

    public function accessories() {
        $products = $this->shopModel->getAllProductsReal();
        $categories = $this->shopModel->getProductCategories();
        
        $data = [
            'title' => 'Accessories - Elite Cricket Gear',
            'accessories' => $products,
            'categories' => $categories
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
                'firstName' => trim($_POST['firstName'] ?? ''),
                'lastName' => trim($_POST['lastName'] ?? ''),
                'email' => trim($_POST['email']),
                'phone_number' => trim($_POST['phone_number'] ?? $_POST['phone'] ?? ''),
                'address' => trim($_POST['address']),
                'school' => trim($_POST['school']),
                'role' => $_SESSION['user_role'], // Keep current role
                'status' => 'active' // Keep active
            ];
            
            // Validate data
            $errors = [];
            if (empty($userData['firstName'])) {
                $errors[] = 'First name is required';
            }
            if (empty($userData['lastName'])) {
                $errors[] = 'Last name is required';
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
                    $_SESSION['user_name'] = trim($userData['firstName'] . ' ' . $userData['lastName']);
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
        // Get real statistics from database
        $stats = [
            'total_orders' => $this->shopModel->getTotalOrders(),
            'pending_orders' => $this->shopModel->getPendingOrders(),
            'monthly_revenue' => $this->shopModel->getMonthlyRevenue(),
            'total_products' => $this->shopModel->getTotalProducts(),
            'low_stock_products' => $this->shopModel->getLowStockCount(),
            'pending_reviews' => $this->shopModel->getPendingReviewsCount(),
            'active_rentals' => $this->shopModel->getActiveRentalsCount(),
            'low_stock_items' => $this->shopModel->getLowStockItems(),
            'pending_review_items' => $this->shopModel->getPendingReviewItems(),
            'active_rental_items' => $this->shopModel->getActiveRentalItems(),
            'top_products' => $this->shopModel->getTopSellingProducts()
        ];
        
        return $stats;
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

    // =========================================================
    // SLOT COUNTER BOOKING (Shop Employee)
    // =========================================================

    /** GET /shop/counter */
    public function counter() {
        requireAuth(['ShopEmployee']);
        require_once APPROOT . '/libraries/SlotBookingService.php';

        $slotModel = $this->model('M_SlotPlayer');
        $slots     = $slotModel->getCounterSlots();
        $facilityBookings = $slotModel->getFacilityOnlyBookingsForCounter();

        $this->view('shop/counter_booking', [
            'title' => 'Counter Slot Booking',
            'user_name' => $_SESSION['user_name'] ?? 'Shop Manager',
            'slots'  => $slots,
            'facilityBookings' => $facilityBookings,
        ]);
    }

    /** POST /shop/searchplayer  (AJAX) */
    public function searchplayer() {
        requireAuth(['ShopEmployee']);
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid method']);
            return;
        }

        $term = trim($_POST['term'] ?? '');
        if (strlen($term) < 2) {
            echo json_encode(['success' => true, 'players' => []]);
            return;
        }

        $slotModel = $this->model('M_SlotPlayer');
        $players   = $slotModel->searchPlayers($term);

        echo json_encode(['success' => true, 'players' => $players]);
    }

    /** POST /shop/slotbook */
    public function slotbook() {
        requireAuth(['ShopEmployee']);
        require_once APPROOT . '/libraries/SlotBookingService.php';

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('shop/counter');
        }

        $occurrenceId = (int)($_POST['occurrence_id'] ?? 0);
        $playerId     = (int)($_POST['player_id']     ?? 0);
        $employeeId   = (int)($_SESSION['user_id']    ?? 5);

        if ($occurrenceId <= 0 || $playerId <= 0) {
            $_SESSION['counter_error'] = 'Please select a session and a player.';
            redirect('shop/counter');
        }

        // Medical flag: block (cannot clear — admin only) + warn
        // Use today as the check date — if any injury covers today they cannot book
        $med = SlotBookingService::checkMedicalFlag($playerId, date('Y-m-d'));
        if (!$med['ok']) {
            $_SESSION['counter_error'] =
                'Booking blocked: this player has an active medical flag. ' .
                'Direct the player to the Admin to have it cleared before booking.';
            redirect('shop/counter');
        }

        $slotModel = $this->model('M_SlotPlayer');
        $participantCount = max(1, (int)($_POST['participant_count'] ?? 1));
        $result    = $slotModel->createBooking(
            $occurrenceId,
            $playerId,
            'shop_employee',     // source
            $employeeId,         // bookedBy
            null,                // subscriptionId — not required for walk-in
            (float)($_POST['amount'] ?? 0),
            'cash',              // payMethod
            'paid',              // payStatus
            $participantCount
        );

        if ($result === true) {
            $_SESSION['counter_success'] = 'Session booked successfully for player.';
            redirect('shop/counter');
        }

        $messages = [
            'duplicate'   => 'This player already has a booking for that session.',
            'full'        => 'Session is fully booked.',
            'not_found'   => 'Session not found.',
            'active_injury' => 'Player has an active medical flag — direct to Admin.',
            'error'       => 'An unexpected error occurred. Please try again.',
        ];
        $_SESSION['counter_error'] = $messages[$result] ?? $messages['error'];
        redirect('shop/counter');
    }

    public function updateFacilityBookingStatus() {
        requireAuth(['ShopEmployee']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('shop/counter');
        }

        $bookingId = (int) ($_POST['booking_id'] ?? 0);
        $status = trim((string) ($_POST['booking_status'] ?? ''));
        $shopEmployeeId = (int) ($_SESSION['user_id'] ?? 0);

        if ($bookingId <= 0 || $status === '') {
            $_SESSION['counter_error'] = 'Please select a valid facility booking status.';
            redirect('shop/counter');
        }

        $slotModel = $this->model('M_SlotPlayer');
        $result = $slotModel->updateFacilityBookingStatus($bookingId, $status, $shopEmployeeId);

        if ($result === true) {
            $_SESSION['counter_success'] = 'Facility booking status updated successfully.';
            redirect('shop/counter');
        }

        $messages = [
            'not_found' => 'The selected facility booking could not be found.',
            'not_allowed' => 'Only facility-only slot bookings can be updated here.',
            'locked' => 'Cancelled bookings cannot be changed here.',
            'invalid_status' => 'That booking status is not allowed.',
            'error' => 'Could not update the facility booking status. Please try again.',
        ];
        $_SESSION['counter_error'] = $messages[$result] ?? $messages['error'];
        redirect('shop/counter');
    }
}
?>