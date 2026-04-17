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

    private function buildProductIdFilter(array $productIds, string $placeholderPrefix = ':product_id_'): array {
        $placeholders = [];
        $bindings = [];

        foreach (array_values(array_filter(array_map('intval', $productIds), static function ($value) {
            return $value > 0;
        })) as $index => $productId) {
            $placeholder = $placeholderPrefix . $index;
            $placeholders[] = $placeholder;
            $bindings[$placeholder] = $productId;
        }

        return [$placeholders, $bindings];
    }

    public function getCartItems($playerId, array $productIds = []) {
        $playerId = (int)$playerId;
        if ($playerId <= 0) {
            return [];
        }

        $sql = '
            SELECT
                pc.CartID,
                pc.PlayerID,
                pc.ProductID,
                pc.Quantity,
                pc.AddedDate,
                p.Name,
                p.Description,
                p.Category,
                p.Brand,
                p.Price,
                p.StockQuantity,
                p.Status,
                p.SKU,
                p.ProductImage
            FROM productcart pc
            INNER JOIN product p ON pc.ProductID = p.ProductID
            WHERE pc.PlayerID = :player_id
        ';
        $bindings = [':player_id' => $playerId];

        if (!empty($productIds)) {
            [$placeholders, $extraBindings] = $this->buildProductIdFilter($productIds, ':cart_product_id_');
            if ($placeholders) {
                $sql .= ' AND pc.ProductID IN (' . implode(',', $placeholders) . ')';
                $bindings = array_merge($bindings, $extraBindings);
            }
        }

        $sql .= ' ORDER BY pc.AddedDate DESC, pc.CartID DESC';

        $this->db->query($sql);
        foreach ($bindings as $param => $value) {
            $this->db->bind($param, $value);
        }

        return $this->db->resultSet();
    }

    public function getCartItemCount($playerId) {
        $this->db->query('SELECT COALESCE(SUM(Quantity), 0) as total FROM productcart WHERE PlayerID = :player_id');
        $this->db->bind(':player_id', (int)$playerId);
        $result = $this->db->single();
        return (int)($result->total ?? 0);
    }

    public function getCartTotal($playerId, array $productIds = []) {
        $items = $this->getCartItems($playerId, $productIds);
        $total = 0.0;

        foreach ($items as $item) {
            $total += ((float)($item->Price ?? 0)) * ((int)($item->Quantity ?? 0));
        }

        return $total;
    }

    public function addToCart($playerId, $productId, $quantity = 1) {
        $playerId = (int)$playerId;
        $productId = (int)$productId;
        $quantity = max(1, (int)$quantity);

        if ($playerId <= 0 || $productId <= 0) {
            return ['success' => false, 'message' => 'Invalid cart item data.'];
        }

        $this->db->query('SELECT ProductID, Name, Price, StockQuantity FROM product WHERE ProductID = :product_id AND Status = "active"');
        $this->db->bind(':product_id', $productId);
        $product = $this->db->single();

        if (!$product) {
            return ['success' => false, 'message' => 'Product not found.'];
        }

        if ((int)($product->StockQuantity ?? 0) <= 0) {
            return ['success' => false, 'message' => 'This product is out of stock.'];
        }

        $this->db->query('SELECT CartID, Quantity FROM productcart WHERE PlayerID = :player_id AND ProductID = :product_id LIMIT 1');
        $this->db->bind(':player_id', $playerId);
        $this->db->bind(':product_id', $productId);
        $existing = $this->db->single();

        $currentQuantity = $existing ? (int)($existing->Quantity ?? 0) : 0;
        $newQuantity = $currentQuantity + $quantity;
        $maxQuantity = (int)($product->StockQuantity ?? 0);
        if ($newQuantity > $maxQuantity) {
            $newQuantity = $maxQuantity;
        }

        if ($existing) {
            $this->db->query('UPDATE productcart SET Quantity = :quantity WHERE CartID = :cart_id');
            $this->db->bind(':quantity', $newQuantity);
            $this->db->bind(':cart_id', (int)$existing->CartID);
            $success = $this->db->execute();
        } else {
            $this->db->query('INSERT INTO productcart (PlayerID, ProductID, Quantity) VALUES (:player_id, :product_id, :quantity)');
            $this->db->bind(':player_id', $playerId);
            $this->db->bind(':product_id', $productId);
            $this->db->bind(':quantity', $newQuantity);
            $success = $this->db->execute();
        }

        if (!$success) {
            return ['success' => false, 'message' => 'Failed to update cart.'];
        }

        return [
            'success' => true,
            'message' => 'Product added to cart.',
            'cart_count' => $this->getCartItemCount($playerId),
        ];
    }

    public function updateCartQuantity($playerId, $productId, $quantity) {
        $playerId = (int)$playerId;
        $productId = (int)$productId;
        $quantity = (int)$quantity;

        if ($playerId <= 0 || $productId <= 0) {
            return ['success' => false, 'message' => 'Invalid cart item data.'];
        }

        if ($quantity <= 0) {
            return $this->removeFromCart($playerId, $productId);
        }

        $this->db->query('SELECT StockQuantity FROM product WHERE ProductID = :product_id AND Status = "active"');
        $this->db->bind(':product_id', $productId);
        $product = $this->db->single();

        if (!$product) {
            return ['success' => false, 'message' => 'Product not found.'];
        }

        $maxQuantity = (int)($product->StockQuantity ?? 0);
        if ($maxQuantity > 0 && $quantity > $maxQuantity) {
            $quantity = $maxQuantity;
        }

        $this->db->query('UPDATE productcart SET Quantity = :quantity WHERE PlayerID = :player_id AND ProductID = :product_id');
        $this->db->bind(':quantity', $quantity);
        $this->db->bind(':player_id', $playerId);
        $this->db->bind(':product_id', $productId);

        if (!$this->db->execute()) {
            return ['success' => false, 'message' => 'Failed to update cart quantity.'];
        }

        return [
            'success' => true,
            'message' => 'Cart quantity updated.',
            'cart_count' => $this->getCartItemCount($playerId),
        ];
    }

    public function removeFromCart($playerId, $productId) {
        $this->db->query('DELETE FROM productcart WHERE PlayerID = :player_id AND ProductID = :product_id');
        $this->db->bind(':player_id', (int)$playerId);
        $this->db->bind(':product_id', (int)$productId);

        if (!$this->db->execute()) {
            return ['success' => false, 'message' => 'Failed to remove cart item.'];
        }

        return [
            'success' => true,
            'message' => 'Item removed from cart.',
            'cart_count' => $this->getCartItemCount($playerId),
        ];
    }

    public function clearCart($playerId) {
        $this->db->query('DELETE FROM productcart WHERE PlayerID = :player_id');
        $this->db->bind(':player_id', (int)$playerId);
        $success = $this->db->execute();

        return [
            'success' => $success,
            'message' => $success ? 'Cart cleared.' : 'Failed to clear cart.',
            'cart_count' => 0,
        ];
    }

    // Equipment Rental Cart (DB-backed)
    public function getRentalCartItems(int $playerId): array {
        $playerId = (int)$playerId;
        if ($playerId <= 0) {
            return [];
        }

        if (!$this->tableExists('equipmentrentalcart')) {
            return [];
        }

        $this->db->query('SELECT
                rc.CartID,
                rc.PlayerID,
                rc.EquipmentID,
                rc.AddedDate,
                e.Name AS EquipmentName,
                e.Category,
                e.RentalPrice,
                e.Stock,
                e.AvailabilityStatus,
                e.EqCondition,
                e.Description,
                e.equipmentImage
            FROM equipmentrentalcart rc
            INNER JOIN equipment e ON rc.EquipmentID = e.EquipmentID
            WHERE rc.PlayerID = :player_id
            ORDER BY rc.AddedDate DESC, rc.CartID DESC');
        $this->db->bind(':player_id', $playerId, PDO::PARAM_INT);

        return (array)$this->db->resultSet();
    }

    public function getRentalCartItemCount(int $playerId): int {
        $playerId = (int)$playerId;
        if ($playerId <= 0 || !$this->tableExists('equipmentrentalcart')) {
            return 0;
        }

        $this->db->query('SELECT COUNT(*) AS total FROM equipmentrentalcart WHERE PlayerID = :player_id');
        $this->db->bind(':player_id', $playerId, PDO::PARAM_INT);
        $row = $this->db->single();
        return (int)($row->total ?? 0);
    }

    public function addToRentalCart(array $data): array {
        if (!$this->tableExists('equipmentrentalcart')) {
            return ['success' => false, 'message' => 'Rental cart table is missing. Please run the latest DB changes SQL (2026-04-17).'];
        }

        $playerId = (int)($data['player_id'] ?? 0);
        $equipmentId = (int)($data['equipment_id'] ?? 0);

        if ($playerId <= 0 || $equipmentId <= 0) {
            return ['success' => false, 'message' => 'Invalid cart item details.'];
        }

        $this->db->query('SELECT * FROM equipment WHERE EquipmentID = :equipment_id LIMIT 1');
        $this->db->bind(':equipment_id', $equipmentId, PDO::PARAM_INT);
        $equipment = $this->db->single();

        if (!$equipment) {
            return ['success' => false, 'message' => 'Selected equipment was not found.'];
        }

        $availableStock = (int)($equipment->Stock ?? 0);
        $availability = strtolower((string)($equipment->AvailabilityStatus ?? ''));
        if ($availability !== 'available' || $availableStock <= 0) {
            return ['success' => false, 'message' => 'This equipment is currently unavailable.'];
        }

        $this->db->query('INSERT INTO equipmentrentalcart (PlayerID, EquipmentID)
            VALUES (:player_id, :equipment_id)
            ON DUPLICATE KEY UPDATE AddedDate = AddedDate');
        $this->db->bind(':player_id', $playerId, PDO::PARAM_INT);
        $this->db->bind(':equipment_id', $equipmentId, PDO::PARAM_INT);
        $success = (bool)$this->db->execute();

        if (!$success) {
            return ['success' => false, 'message' => 'Failed to add item to rental cart.'];
        }

        $added = $this->db->rowCount() > 0;
        return [
            'success' => true,
            'message' => $added ? 'Added to rental cart.' : 'This item is already in your rental cart.',
            'cart_count' => $this->getRentalCartItemCount($playerId),
        ];
    }

    public function removeFromRentalCart(int $playerId, int $cartId): array {
        if (!$this->tableExists('equipmentrentalcart')) {
            return ['success' => false, 'message' => 'Rental cart table is missing.'];
        }

        $this->db->query('DELETE FROM equipmentrentalcart WHERE CartID = :cart_id AND PlayerID = :player_id');
        $this->db->bind(':cart_id', (int)$cartId, PDO::PARAM_INT);
        $this->db->bind(':player_id', (int)$playerId, PDO::PARAM_INT);
        $success = (bool)$this->db->execute();

        return [
            'success' => $success,
            'message' => $success ? 'Rental cart item removed.' : 'Failed to remove rental cart item.',
            'cart_count' => $this->getRentalCartItemCount($playerId),
        ];
    }

    public function clearRentalCart(int $playerId): array {
        if (!$this->tableExists('equipmentrentalcart')) {
            return ['success' => false, 'message' => 'Rental cart table is missing.'];
        }

        $this->db->query('DELETE FROM equipmentrentalcart WHERE PlayerID = :player_id');
        $this->db->bind(':player_id', (int)$playerId, PDO::PARAM_INT);
        $success = (bool)$this->db->execute();

        return [
            'success' => $success,
            'message' => $success ? 'Rental cart cleared.' : 'Failed to clear rental cart.',
            'cart_count' => $this->getRentalCartItemCount($playerId),
        ];
    }

    public function getRentalCartItemsByIds(int $playerId, array $cartIds): array {
        $playerId = (int)$playerId;
        if ($playerId <= 0 || !$this->tableExists('equipmentrentalcart')) {
            return [];
        }

        $ids = array_values(array_filter(array_map('intval', $cartIds), static function ($id) {
            return $id > 0;
        }));
        if (!$ids) {
            return [];
        }

        $placeholders = [];
        foreach ($ids as $index => $id) {
            $placeholders[] = ':cart_id_' . $index;
        }

        $sql = 'SELECT
            rc.CartID,
            rc.PlayerID,
            rc.EquipmentID,
            rc.AddedDate,
            e.Name AS EquipmentName,
            e.RentalPrice,
            e.Stock,
            e.AvailabilityStatus
            FROM equipmentrentalcart rc
            INNER JOIN equipment e ON rc.EquipmentID = e.EquipmentID
            WHERE rc.PlayerID = :player_id
              AND rc.CartID IN (' . implode(',', $placeholders) . ')';

        $this->db->query($sql);
        $this->db->bind(':player_id', $playerId, PDO::PARAM_INT);
        foreach ($ids as $index => $id) {
            $this->db->bind(':cart_id_' . $index, $id, PDO::PARAM_INT);
        }

        return (array)$this->db->resultSet();
    }

    public function removeRentalCartItemsByIds(int $playerId, array $cartIds): bool {
        $playerId = (int)$playerId;
        if ($playerId <= 0 || !$this->tableExists('equipmentrentalcart')) {
            return false;
        }

        $ids = array_values(array_filter(array_map('intval', $cartIds), static function ($id) {
            return $id > 0;
        }));
        if (!$ids) {
            return false;
        }

        $placeholders = [];
        foreach ($ids as $index => $id) {
            $placeholders[] = ':cart_id_' . $index;
        }

        $sql = 'DELETE FROM equipmentrentalcart
            WHERE PlayerID = :player_id
              AND CartID IN (' . implode(',', $placeholders) . ')';

        $this->db->query($sql);
        $this->db->bind(':player_id', $playerId, PDO::PARAM_INT);
        foreach ($ids as $index => $id) {
            $this->db->bind(':cart_id_' . $index, $id, PDO::PARAM_INT);
        }

        return (bool)$this->db->execute();
    }

    public function createAutoConfirmedRentalsFromCartItems(int $playerId, array $cartItems, array $rentalMeta, int $processedBy = 5): array {
        $playerId = (int)$playerId;
        $processedBy = (int)$processedBy;

        if ($playerId <= 0 || $processedBy <= 0) {
            return ['success' => false, 'message' => 'Invalid player details.'];
        }

        if (!$cartItems) {
            return ['success' => false, 'message' => 'No rental cart items found.'];
        }

        $startDate = trim((string)($rentalMeta['start_date'] ?? ''));
        $durationDays = (int)($rentalMeta['duration_days'] ?? 0);
        $pickupMethod = strtolower(trim((string)($rentalMeta['pickup_method'] ?? 'pickup')));
        $agreeToTerms = (int)($rentalMeta['agree_to_terms'] ?? 0);

        if ($durationDays < 1 || $durationDays > 7) {
            return ['success' => false, 'message' => 'Invalid rental duration selected. Please choose 1 to 7 days.'];
        }

        if ($pickupMethod !== 'pickup' && $pickupMethod !== 'delivery') {
            return ['success' => false, 'message' => 'Invalid pickup method selected.'];
        }

        if ($agreeToTerms !== 1) {
            return ['success' => false, 'message' => 'You must agree to the rental terms before checkout.'];
        }

        try {
            $start = new DateTime($startDate);
            $today = new DateTime('today');
            if ($start < $today) {
                return ['success' => false, 'message' => 'Please choose today or a future date for your rental.'];
            }
            $end = (clone $start)->modify('+' . $durationDays . ' days');
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Invalid start date. Please choose a valid date.'];
        }

        try {
            $this->db->beginTransaction();

            $createdRentalIds = [];
            $grandTotal = 0.0;

            foreach ($cartItems as $item) {
                $equipmentId = (int)($item->EquipmentID ?? 0);
                if ($equipmentId <= 0) {
                    throw new RuntimeException('Invalid rental cart item detected.');
                }

                $this->db->query('SELECT * FROM equipment WHERE EquipmentID = :equipment_id FOR UPDATE');
                $this->db->bind(':equipment_id', $equipmentId, PDO::PARAM_INT);
                $equipment = $this->db->single();

                if (!$equipment) {
                    throw new RuntimeException('Selected equipment was not found.');
                }

                $availableStock = (int)($equipment->Stock ?? 0);
                $availability = strtolower((string)($equipment->AvailabilityStatus ?? ''));
                if ($availability !== 'available' || $availableStock < 1) {
                    throw new RuntimeException('This equipment is currently unavailable: ' . ($equipment->Name ?? 'equipment') . '.');
                }

                $itemTotal = $this->calculateRentalTotal(
                    (float)($equipment->RentalPrice ?? 0),
                    $durationDays,
                    1,
                    $pickupMethod
                );

                $this->db->query('INSERT INTO equipmentrental
                    (EquipmentID, PlayerID, RentalDate, StartTime, EndTime, Status, TotalCost, ProcessedBy, AgreeToTerms, LateFee, ReturnNotes)
                    VALUES
                    (:equipment_id, :player_id, :rental_date, :start_time, :end_time, "active", :total_cost, :processed_by, 1, 0.00, :return_notes)');
                $this->db->bind(':equipment_id', $equipmentId, PDO::PARAM_INT);
                $this->db->bind(':player_id', $playerId, PDO::PARAM_INT);
                $this->db->bind(':rental_date', $start->format('Y-m-d'));
                $this->db->bind(':start_time', $start->format('Y-m-d 00:00:00'));
                $this->db->bind(':end_time', $end->format('Y-m-d 23:59:59'));
                $this->db->bind(':total_cost', round($itemTotal, 2));
                $this->db->bind(':processed_by', $processedBy, PDO::PARAM_INT);
                $this->db->bind(':return_notes', 'Pickup method: ' . ($pickupMethod === 'delivery' ? 'Home delivery' : 'Academy pickup'));

                if (!$this->db->execute()) {
                    throw new RuntimeException('Could not create the rental.');
                }

                $createdRentalIds[] = (int)$this->db->lastInsertId();

                $remainingStock = $availableStock - 1;
                $newStatus = $remainingStock > 0 ? 'available' : 'rented';
                $this->db->query('UPDATE equipment
                    SET Stock = :stock, AvailabilityStatus = :status
                    WHERE EquipmentID = :equipment_id');
                $this->db->bind(':stock', $remainingStock, PDO::PARAM_INT);
                $this->db->bind(':status', $newStatus);
                $this->db->bind(':equipment_id', $equipmentId, PDO::PARAM_INT);

                if (!$this->db->execute()) {
                    throw new RuntimeException('Could not update equipment stock.');
                }

                $grandTotal += $itemTotal;
            }

            $this->db->commit();

            return [
                'success' => true,
                'message' => 'Rentals confirmed successfully.',
                'rental_ids' => $createdRentalIds,
                'total_cost' => round($grandTotal, 2),
            ];
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log('Auto-confirm cart rentals failed: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage() ?: 'Something went wrong while confirming rentals.'];
        }
    }

    public function createOrderFromCart($playerId, array $productIds = [], $paymentMethod = 'online', $processedBy = null, $shippingAddress = null, $orderNotes = null, $status = 'completed') {
        $playerId = (int)$playerId;
        $productIds = array_values(array_filter(array_map('intval', $productIds), static function ($value) {
            return $value > 0;
        }));

        $items = $this->getCartItems($playerId, $productIds);
        if (!$items) {
            return ['success' => false, 'message' => 'No cart items found.'];
        }

        $startedTransaction = false;
        if (method_exists($this->db, 'inTransaction') && method_exists($this->db, 'beginTransaction') && !$this->db->inTransaction()) {
            $this->db->beginTransaction();
            $startedTransaction = true;
        }

        try {
            $totalAmount = 0.0;
            foreach ($items as $item) {
                $stockQuantity = (int)($item->StockQuantity ?? 0);
                $requestedQuantity = (int)($item->Quantity ?? 0);

                if ($requestedQuantity <= 0) {
                    throw new RuntimeException('Invalid cart quantity detected.');
                }

                if ($stockQuantity < $requestedQuantity) {
                    throw new RuntimeException('Insufficient stock for ' . ($item->Name ?? 'selected product') . '.');
                }

                $totalAmount += ((float)($item->Price ?? 0)) * $requestedQuantity;
            }

            $this->db->query('
                INSERT INTO productorder (
                    PlayerID,
                    TotalAmount,
                    PaymentMethod,
                    Status,
                    ProcessedBy,
                    ShippingAddress,
                    OrderNotes
                ) VALUES (
                    :player_id,
                    :total_amount,
                    :payment_method,
                    :status,
                    :processed_by,
                    :shipping_address,
                    :order_notes
                )
            ');
            $this->db->bind(':player_id', $playerId);
            $this->db->bind(':total_amount', $totalAmount);
            $this->db->bind(':payment_method', $paymentMethod);
            $this->db->bind(':status', $status);
            $this->db->bind(':processed_by', $processedBy);
            $this->db->bind(':shipping_address', $shippingAddress);
            $this->db->bind(':order_notes', $orderNotes);

            if (!$this->db->execute()) {
                throw new RuntimeException('Failed to create product order.');
            }

            $orderId = (int)$this->db->lastInsertId();

            foreach ($items as $item) {
                $productId = (int)$item->ProductID;
                $quantity = (int)$item->Quantity;
                $unitPrice = (float)$item->Price;
                $subTotal = $unitPrice * $quantity;

                $this->db->query('
                    INSERT INTO productorderitem (
                        OrderID,
                        ProductID,
                        Quantity,
                        UnitPrice,
                        SubTotal
                    ) VALUES (
                        :order_id,
                        :product_id,
                        :quantity,
                        :unit_price,
                        :sub_total
                    )
                ');
                $this->db->bind(':order_id', $orderId);
                $this->db->bind(':product_id', $productId);
                $this->db->bind(':quantity', $quantity);
                $this->db->bind(':unit_price', $unitPrice);
                $this->db->bind(':sub_total', $subTotal);

                if (!$this->db->execute()) {
                    throw new RuntimeException('Failed to create order line item.');
                }

                $this->db->query('
                    UPDATE product
                    SET StockQuantity = StockQuantity - :quantity,
                        Status = CASE WHEN (StockQuantity - :quantity) <= 0 THEN "out_of_stock" ELSE Status END
                    WHERE ProductID = :product_id
                      AND StockQuantity >= :quantity
                ');
                $this->db->bind(':quantity', $quantity);
                $this->db->bind(':product_id', $productId);

                if (!$this->db->execute() || $this->db->rowCount() === 0) {
                    throw new RuntimeException('Failed to update stock for ' . ($item->Name ?? 'selected product') . '.');
                }
            }

            $selectedProductIds = array_map(static function ($item) {
                return (int)$item->ProductID;
            }, $items);

            if ($selectedProductIds) {
                [$placeholders, $bindings] = $this->buildProductIdFilter($selectedProductIds, ':delete_product_id_');
                $deleteSql = 'DELETE FROM productcart WHERE PlayerID = :player_id AND ProductID IN (' . implode(',', $placeholders) . ')';
                $this->db->query($deleteSql);
                $this->db->bind(':player_id', $playerId);
                foreach ($bindings as $param => $value) {
                    $this->db->bind($param, $value);
                }
                if (!$this->db->execute()) {
                    throw new RuntimeException('Failed to clear purchased cart items.');
                }
            }

            if ($startedTransaction && method_exists($this->db, 'commit')) {
                $this->db->commit();
            }

            return [
                'success' => true,
                'order_id' => $orderId,
                'total_amount' => $totalAmount,
                'item_count' => count($items),
                'message' => 'Order created successfully.',
            ];
        } catch (Throwable $e) {
            if ($startedTransaction && method_exists($this->db, 'rollBack')) {
                $this->db->rollBack();
            }

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    // Legacy session cart helpers retained for backward compatibility
    public function addToCartSession($productId, $quantity = 1) {
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
    public function removeFromCartSession($productId) {
        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
            return true;
        }
        return false;
    }

    // Update cart quantity
    public function updateCartQuantitySession($productId, $quantity) {
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
    public function getCartTotalSession() {
        $total = 0;
        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $item) {
                $total += $item['product']->price * $item['quantity'];
            }
        }
        return $total;
    }

    // Get cart item count
    public function getCartItemCountSession() {
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
                CONCAT(u.FirstName, \' \', u.LastName) as CustomerName,
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
                CONCAT(u.FirstName, \' \', u.LastName) as CustomerName,
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

    // Get all equipment rentals with details
    public function getAllRentals() {
        $valuePriceExpr = '0';
        if ($this->columnExists('equipment', 'ValuePrice')) {
            $valuePriceExpr = 'e.ValuePrice';
        } elseif ($this->columnExists('equipment', 'PurchasePrice')) {
            $valuePriceExpr = 'e.PurchasePrice';
        }

        $this->db->query('SELECT er.*,
            e.Name AS equipment_name,
            e.Category,
            e.EqCondition,
            ' . $valuePriceExpr . ' AS equipment_value_price,
            CONCAT(COALESCE(u.FirstName, \'\'), \' \', COALESCE(u.LastName, \'\')) AS renter_name,
            u.Email AS renter_email
            FROM equipmentrental er
            LEFT JOIN equipment e ON er.EquipmentID = e.EquipmentID
            LEFT JOIN user u ON er.PlayerID = u.UserID
            ORDER BY er.RentalDate DESC');
        return $this->db->resultSet();
    }

    public function getRentalManagementStats(): array {
        $stats = [
            'active_rentals' => 0,
            'overdue_rentals' => 0,
            'available_equipment' => 0,
            'todays_revenue' => 0.0,
        ];

        $this->db->query('SELECT COUNT(*) AS cnt FROM equipmentrental WHERE Status = "active"');
        $row = $this->db->single();
        $stats['active_rentals'] = (int)($row->cnt ?? 0);

        $this->db->query('SELECT COUNT(*) AS cnt FROM equipmentrental WHERE Status = "overdue"');
        $row = $this->db->single();
        $stats['overdue_rentals'] = (int)($row->cnt ?? 0);

        $this->db->query('SELECT COUNT(*) AS cnt FROM equipment WHERE AvailabilityStatus = "available" AND Stock > 0');
        $row = $this->db->single();
        $stats['available_equipment'] = (int)($row->cnt ?? 0);

        $this->db->query('SELECT COALESCE(SUM(TotalCost), 0) AS total
            FROM equipmentrental
            WHERE RentalDate = CURDATE() AND Status IN ("active","returned")');
        $row = $this->db->single();
        $stats['todays_revenue'] = (float)($row->total ?? 0);

        return $stats;
    }

    public function getAllEquipmentReturns() {
        $this->db->query('SELECT r.*, er.EquipmentID, er.PlayerID, er.EndTime, er.RentalDate,
            e.Name AS equipment_name, e.Category,
            CONCAT(COALESCE(u.FirstName, \'\'), \' \', COALESCE(u.LastName, \'\')) AS renter_name,
            u.Email AS renter_email
            FROM equipmentreturn r
            LEFT JOIN equipmentrental er ON r.RentalID = er.RentalID
            LEFT JOIN equipment e ON er.EquipmentID = e.EquipmentID
            LEFT JOIN user u ON er.PlayerID = u.UserID
            ORDER BY r.ReturnedAt DESC');
        return $this->db->resultSet();
    }

    public function createEquipmentReturn(array $data): array {
        $rentalId = (int)($data['rental_id'] ?? 0);
        $returnedAtRaw = trim((string)($data['returned_at'] ?? ''));
        $returnStatus = (string)($data['return_status'] ?? 'received');
        $damageStatus = (string)($data['damage_status'] ?? 'not_damaged');
        $paymentStatusInput = trim((string)($data['payment_status'] ?? ''));
        $notes = trim((string)($data['notes'] ?? ''));
        $inspectedBy = (int)($data['inspected_by'] ?? 0);

        $allowedReturnStatuses = ['received', 'inspected', 'completed'];
        $allowedDamageStatuses = ['not_damaged', 'slight', 'moderate', 'high'];
        $allowedPaymentStatuses = ['not_required', 'pending', 'paid', 'refunded'];

        if ($rentalId <= 0) {
            return ['success' => false, 'message' => 'Please select a valid rental.'];
        }

        if ($returnedAtRaw === '') {
            return ['success' => false, 'message' => 'Please provide a valid return date/time.'];
        }

        if (!in_array($returnStatus, $allowedReturnStatuses, true)) {
            return ['success' => false, 'message' => 'Invalid return status selected.'];
        }

        if (!in_array($damageStatus, $allowedDamageStatuses, true)) {
            return ['success' => false, 'message' => 'Invalid damage status selected.'];
        }

        if ($paymentStatusInput !== '' && !in_array($paymentStatusInput, $allowedPaymentStatuses, true)) {
            return ['success' => false, 'message' => 'Invalid payment status selected.'];
        }

        try {
            $returnedAt = new DateTime($returnedAtRaw);
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Please provide a valid return date/time.'];
        }

        try {
            $this->db->beginTransaction();

            $rentalLockValuePriceExpr = '0 AS ValuePrice';
            if ($this->columnExists('equipment', 'ValuePrice')) {
                $rentalLockValuePriceExpr = 'e.ValuePrice AS ValuePrice';
            } elseif ($this->columnExists('equipment', 'PurchasePrice')) {
                $rentalLockValuePriceExpr = 'e.PurchasePrice AS ValuePrice';
            }

            // Lock rental row
            $this->db->query('SELECT er.*, e.EquipmentID, ' . $rentalLockValuePriceExpr . '
                FROM equipmentrental er
                JOIN equipment e ON er.EquipmentID = e.EquipmentID
                WHERE er.RentalID = :rental_id
                FOR UPDATE');
            $this->db->bind(':rental_id', $rentalId, PDO::PARAM_INT);
            $rental = $this->db->single();

            if (!$rental) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Rental record not found.'];
            }

            // Prevent duplicates
            $this->db->query('SELECT ReturnID FROM equipmentreturn WHERE RentalID = :rental_id LIMIT 1');
            $this->db->bind(':rental_id', $rentalId, PDO::PARAM_INT);
            $existing = $this->db->single();
            if ($existing) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'A return record already exists for this rental.'];
            }

            $endTimeRaw = (string)($rental->EndTime ?? '');
            $daysLate = 0;
            if ($endTimeRaw !== '') {
                try {
                    $endTime = new DateTime($endTimeRaw);
                    if ($returnedAt > $endTime) {
                        $secondsLate = $returnedAt->getTimestamp() - $endTime->getTimestamp();
                        $daysLate = (int)ceil($secondsLate / 86400);
                    }
                } catch (Exception $e) {
                    $daysLate = 0;
                }
            }

            $lateFee = $this->calculateLateFeeAmount($daysLate);

            $valuePrice = (float)($rental->ValuePrice ?? 0);
            $damageFee = 0.0;
            if ($damageStatus === 'moderate') {
                $damageFee = $valuePrice * 0.5;
            } elseif ($damageStatus === 'high') {
                $damageFee = $valuePrice * 0.8;
            } else {
                // not_damaged and slight => no fee
                $damageFee = 0.0;
            }
            $damageFee = round(max(0.0, (float)$damageFee), 2);

            $totalReturnPay = round($damageFee + $lateFee, 2);
            $paymentStatusDefault = $totalReturnPay > 0 ? 'pending' : 'not_required';
            $paymentStatus = $paymentStatusInput !== '' ? $paymentStatusInput : $paymentStatusDefault;

            // Insert return record
            $this->db->query('INSERT INTO equipmentreturn
                (RentalID, ReturnedAt, ReturnStatus, DamageStatus, DamageFee, DaysLate, LateFee, TotalReturnPay, PaymentStatus, Notes, InspectedBy)
                VALUES
                (:rental_id, :returned_at, :return_status, :damage_status, :damage_fee, :days_late, :late_fee, :total_return_pay, :payment_status, :notes, :inspected_by)');
            $this->db->bind(':rental_id', $rentalId, PDO::PARAM_INT);
            $this->db->bind(':returned_at', $returnedAt->format('Y-m-d H:i:s'));
            $this->db->bind(':return_status', $returnStatus);
            $this->db->bind(':damage_status', $damageStatus);
            $this->db->bind(':damage_fee', number_format($damageFee, 2, '.', ''));
            $this->db->bind(':days_late', $daysLate, PDO::PARAM_INT);
            $this->db->bind(':late_fee', number_format($lateFee, 2, '.', ''));
            $this->db->bind(':total_return_pay', number_format($totalReturnPay, 2, '.', ''));
            $this->db->bind(':payment_status', $paymentStatus);
            $this->db->bind(':notes', $notes);
            $this->db->bind(':inspected_by', $inspectedBy > 0 ? $inspectedBy : null);

            if (!$this->db->execute()) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Could not save the return record.'];
            }

            // Update rental summary fields
            $this->db->query('UPDATE equipmentrental
                SET Status = "returned",
                    LateFee = :late_fee,
                    ReturnNotes = :return_notes,
                    ReturnInspectedBy = :inspected_by
                WHERE RentalID = :rental_id');
            $this->db->bind(':late_fee', number_format($lateFee, 2, '.', ''));
            $this->db->bind(':return_notes', $notes);
            $this->db->bind(':inspected_by', $inspectedBy > 0 ? $inspectedBy : null);
            $this->db->bind(':rental_id', $rentalId, PDO::PARAM_INT);

            if (!$this->db->execute()) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Could not update the rental status.'];
            }

            // Restore one unit back to stock; only flip status back if it was "rented"
            $this->db->query('UPDATE equipment
                SET Stock = Stock + 1,
                    AvailabilityStatus = CASE WHEN AvailabilityStatus = "rented" THEN "available" ELSE AvailabilityStatus END
                WHERE EquipmentID = :equipment_id');
            $this->db->bind(':equipment_id', (int)($rental->EquipmentID ?? 0), PDO::PARAM_INT);
            $this->db->execute();

            $this->db->commit();

            return [
                'success' => true,
                'message' => 'Return recorded successfully.',
                'days_late' => $daysLate,
                'late_fee' => $lateFee,
                'damage_fee' => $damageFee,
                'total_return_pay' => $totalReturnPay,
                'payment_status' => $paymentStatus,
            ];
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log('Create equipment return failed: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Something went wrong while recording the return.'];
        }
    }

    // Get all product reviews with details
    public function getAllProductReviews() {
        $this->db->query('SELECT pr.*, p.Name AS product_name, CONCAT(u.FirstName, \' \', u.LastName) AS customer_name
            FROM productreview pr 
            JOIN product p ON pr.ProductID = p.ProductID 
            JOIN user u ON pr.UserID = u.UserID 
            ORDER BY pr.ReviewDate DESC');
        return $this->db->resultSet();
    }

    // Get all facilities
    public function getAllFacilities() {
        $this->db->query('SELECT * FROM facility ORDER BY Name ASC');
        return $this->db->resultSet();
    }

    public function getFacilityById(int $id) {
        $this->db->query('SELECT * FROM facility WHERE FacilityID = :id');
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }

    // Get supplement prescriptions (supplement plans assigned to players)
    public function getSupplementPrescriptions() {
        $this->db->query('SELECT sp.PlanID, sp.SupplementPlanName AS supplements, 
            sp.Dosage, sp.Duration, sp.CreatedDate AS date,
            CONCAT(u_player.FirstName, \' \', u_player.LastName) AS patient, CONCAT(u_trainer.FirstName, \' \', u_trainer.LastName) AS prescribed_by,
            CASE WHEN DATEDIFF(CURDATE(), sp.CreatedDate) < sp.Duration THEN "active" ELSE "completed" END AS status
            FROM supplementplan sp
            JOIN supplement_player spp ON sp.PlanID = spp.PlanID
            JOIN user u_player ON spp.PlayerID = u_player.UserID
            JOIN user u_trainer ON sp.TrainerID = u_trainer.UserID
            ORDER BY sp.CreatedDate DESC');
        return $this->db->resultSet();
    }

    // Get all product categories from product table
    public function getProductCategories() {
        $this->db->query('SELECT DISTINCT Category AS name, 
            COUNT(*) AS product_count
            FROM product WHERE Status = "active" 
            GROUP BY Category ORDER BY Category ASC');
        return $this->db->resultSet();
    }

    // Get all products from product table (real data)
    public function getAllProductsReal() {
        $this->db->query('SELECT ProductID AS id, Name AS name, Brand AS brand, 
            Price AS price, StockQuantity AS stock, Category AS category,
            Description AS description, Status, SKU, ImagePath AS image,
            CASE WHEN StockQuantity > 0 THEN 1 ELSE 0 END AS in_stock
            FROM product WHERE Status = "active" ORDER BY Name ASC');
        return $this->db->resultSet();
    }

    // Get featured products from product table (real data)
    public function getFeaturedProductsReal($limit = 8) {
        $this->db->query('SELECT ProductID AS id, Name AS name, Brand AS brand, 
            Price AS price, StockQuantity AS stock, Category AS category,
            Description AS description, ImagePath AS image
            FROM product WHERE Status = "active" 
            ORDER BY ProductID ASC LIMIT :limit');
        $this->db->bind(':limit', (int)$limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    // Get available equipment for rent
    public function getAvailableEquipmentForRent() {
        $this->db->query('SELECT * FROM equipment ORDER BY Name ASC');
        return $this->db->resultSet();
    }

    public function getEquipmentById($equipmentId) {
        $this->db->query('SELECT * FROM equipment WHERE EquipmentID = :equipment_id LIMIT 1');
        $this->db->bind(':equipment_id', (int)$equipmentId, PDO::PARAM_INT);
        return $this->db->single();
    }

    public function calculateRentalTotal($dailyRate, $durationDays, $quantity, $pickupMethod) {
        $total = max(0, (float)$dailyRate) * max(1, (int)$durationDays) * max(1, (int)$quantity);

        if ($pickupMethod === 'delivery') {
            $total += 250.00;
        }

        if ((int)$durationDays >= 14) {
            $total *= 0.90;
        } elseif ((int)$durationDays >= 7) {
            $total *= 0.95;
        }

        return round($total, 2);
    }

    public function calculateLateFeeAmount(int $daysLate): float {
        if ($daysLate <= 0) {
            return 0.0;
        }

        // Pattern:
        // day 1 = 750
        // day 2 = 1000 (+250)
        // day 3 = 1500 (+500)
        // day 4 = 2250 (+750)
        // Increase added per extra day by +250 each day.
        // Closed form: 750 + 250 * (1 + 2 + ... + (daysLate - 1))
        //            = 750 + 125 * daysLate * (daysLate - 1)
        $fee = 750 + (125 * $daysLate * ($daysLate - 1));
        return round((float)$fee, 2);
    }

    public function createAutoConfirmedRental(array $data) {
        $equipmentId = (int)($data['equipment_id'] ?? 0);
        $playerId = (int)($data['player_id'] ?? 0);
        $durationDays = (int)($data['duration_days'] ?? 0);
        $quantity = (int)($data['quantity'] ?? 0);
        $processedBy = (int)($data['processed_by'] ?? 5);
        $pickupMethod = (string)($data['pickup_method'] ?? 'pickup');
        $startDate = (string)($data['start_date'] ?? '');
        $agreeToTerms = (int)($data['agree_to_terms'] ?? 0);

        if ($equipmentId <= 0 || $playerId <= 0 || $durationDays <= 0 || $quantity <= 0 || $processedBy <= 0) {
            return ['success' => false, 'message' => 'Invalid rental details. Please try again.'];
        }

        if ($durationDays < 1 || $durationDays > 7) {
            return ['success' => false, 'message' => 'Invalid rental duration selected. Please choose 1 to 7 days.'];
        }

        if ($agreeToTerms !== 1) {
            return ['success' => false, 'message' => 'You must agree to the rental terms before submitting.'];
        }

        try {
            $start = new DateTime($startDate);
            $end = (clone $start)->modify('+' . $durationDays . ' days');
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Invalid start date. Please choose a valid date.'];
        }

        try {
            $this->db->beginTransaction();

            $this->db->query('SELECT * FROM equipment WHERE EquipmentID = :equipment_id FOR UPDATE');
            $this->db->bind(':equipment_id', $equipmentId, PDO::PARAM_INT);
            $equipment = $this->db->single();

            if (!$equipment) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Selected equipment was not found.'];
            }

            $availableStock = (int)($equipment->Stock ?? 0);
            $availability = strtolower((string)($equipment->AvailabilityStatus ?? ''));
            if ($availability !== 'available' || $availableStock < $quantity) {
                $this->db->rollBack();
                return [
                    'success' => false,
                    'message' => 'Only ' . max(0, $availableStock) . ' item(s) are available now.'
                ];
            }

            $totalCost = $this->calculateRentalTotal(
                (float)($equipment->RentalPrice ?? 0),
                $durationDays,
                $quantity,
                $pickupMethod
            );
            $perItemCost = round($totalCost / $quantity, 2);
            $rentalIds = [];

            for ($i = 0; $i < $quantity; $i++) {
                $this->db->query('INSERT INTO equipmentrental
                    (EquipmentID, PlayerID, RentalDate, StartTime, EndTime, Status, TotalCost, ProcessedBy, AgreeToTerms, LateFee, ReturnNotes)
                    VALUES
                    (:equipment_id, :player_id, :rental_date, :start_time, :end_time, "active", :total_cost, :processed_by, :agree_to_terms, 0.00, :return_notes)');
                $this->db->bind(':equipment_id', $equipmentId, PDO::PARAM_INT);
                $this->db->bind(':player_id', $playerId, PDO::PARAM_INT);
                $this->db->bind(':rental_date', $start->format('Y-m-d'));
                $this->db->bind(':start_time', $start->format('Y-m-d 00:00:00'));
                $this->db->bind(':end_time', $end->format('Y-m-d 23:59:59'));
                $this->db->bind(':total_cost', $perItemCost);
                $this->db->bind(':processed_by', $processedBy, PDO::PARAM_INT);
                $this->db->bind(':agree_to_terms', 1, PDO::PARAM_INT);
                $this->db->bind(':return_notes', 'Pickup method: ' . ($pickupMethod === 'delivery' ? 'Home delivery' : 'Academy pickup'));

                if (!$this->db->execute()) {
                    $this->db->rollBack();
                    return ['success' => false, 'message' => 'Could not create the rental. Please try again.'];
                }

                $rentalIds[] = (int)$this->db->lastInsertId();
            }

            $remainingStock = $availableStock - $quantity;
            $newStatus = $remainingStock > 0 ? 'available' : 'rented';
            $this->db->query('UPDATE equipment
                SET Stock = :stock, AvailabilityStatus = :status
                WHERE EquipmentID = :equipment_id');
            $this->db->bind(':stock', $remainingStock, PDO::PARAM_INT);
            $this->db->bind(':status', $newStatus);
            $this->db->bind(':equipment_id', $equipmentId, PDO::PARAM_INT);

            if (!$this->db->execute()) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Could not update equipment stock. Please try again.'];
            }

            $this->db->commit();

            return [
                'success' => true,
                'message' => 'Rental confirmed successfully.',
                'equipment' => $equipment,
                'rental_ids' => $rentalIds,
                'total_cost' => $totalCost,
                'start_time' => $start->format('Y-m-d 00:00:00'),
                'end_time' => $end->format('Y-m-d 23:59:59'),
                'quantity' => $quantity,
                'pickup_method' => $pickupMethod,
            ];
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log('Auto-confirm rental failed: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Something went wrong while confirming the rental. Please try again.'];
        }
    }

    // Get player's current and past rentals
    public function getPlayerRentals($playerId) {
        $this->db->query('SELECT
                er.*,
                e.Name as EquipmentName,
                e.Category,
                e.RentalPrice,
                e.EqCondition,
                r.ReturnID AS ReturnID,
                r.ReturnedAt AS ReturnReturnedAt,
                r.ReturnStatus AS ReturnStatus,
                r.DamageStatus AS ReturnDamageStatus,
                r.DamageFee AS ReturnDamageFee,
                r.DaysLate AS ReturnDaysLate,
                r.LateFee AS ReturnLateFee,
                r.TotalReturnPay AS ReturnTotalPay,
                r.PaymentStatus AS ReturnPaymentStatus
            FROM equipmentrental er
            JOIN equipment e ON er.EquipmentID = e.EquipmentID
            LEFT JOIN equipmentreturn r ON r.RentalID = er.RentalID
            WHERE er.PlayerID = :pid
            ORDER BY er.StartTime DESC');
        $this->db->bind(':pid', $playerId);
        return $this->db->resultSet();
    }

    public function getPendingReturnFeeForPlayer(int $returnId, int $playerId) {
                $hasTotal = $this->columnExists('equipmentreturn', 'TotalReturnPay');
                $hasPay = $this->columnExists('equipmentreturn', 'PaymentStatus');
                $totalExpr = $hasTotal ? 'r.TotalReturnPay' : '(COALESCE(r.DamageFee,0) + COALESCE(r.LateFee,0))';
                $payExpr = $hasPay ? 'r.PaymentStatus' : '"pending"';

                $this->db->query('SELECT r.ReturnID, r.RentalID, ' . $totalExpr . ' AS TotalReturnPay, ' . $payExpr . ' AS PaymentStatus
            FROM equipmentreturn r
            JOIN equipmentrental er ON r.RentalID = er.RentalID
            WHERE r.ReturnID = :return_id
              AND er.PlayerID = :player_id
                            AND ' . $payExpr . ' = "pending"
            LIMIT 1');
        $this->db->bind(':return_id', $returnId, PDO::PARAM_INT);
        $this->db->bind(':player_id', $playerId, PDO::PARAM_INT);
        return $this->db->single();
    }

    public function markReturnFeePaidForPlayer(int $returnId, int $playerId): bool {
        if (!$this->columnExists('equipmentreturn', 'PaymentStatus')) {
            return false;
        }
        $this->db->query('UPDATE equipmentreturn r
            JOIN equipmentrental er ON r.RentalID = er.RentalID
            SET r.PaymentStatus = "paid"
            WHERE r.ReturnID = :return_id
              AND er.PlayerID = :player_id
              AND r.PaymentStatus = "pending"');
        $this->db->bind(':return_id', $returnId, PDO::PARAM_INT);
        $this->db->bind(':player_id', $playerId, PDO::PARAM_INT);
        return (bool)$this->db->execute();
    }

    private function columnExists(string $table, string $column): bool {
        try {
            $this->db->query('SELECT 1
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = :table
                  AND COLUMN_NAME = :column
                LIMIT 1');
            $this->db->bind(':table', $table);
            $this->db->bind(':column', $column);
            $row = $this->db->single();
            return !empty($row);
        } catch (Throwable $e) {
            return false;
        }
    }

    private function tableExists(string $table): bool {
        try {
            $this->db->query('SELECT 1
                FROM INFORMATION_SCHEMA.TABLES
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = :table
                LIMIT 1');
            $this->db->bind(':table', $table);
            $row = $this->db->single();
            return !empty($row);
        } catch (Throwable $e) {
            return false;
        }
    }

    // Get rental stats for a player
    public function getPlayerRentalStats($playerId) {
        $this->db->query('SELECT 
            (SELECT COUNT(*) FROM equipment WHERE AvailabilityStatus = "available") as total_equipment,
            (SELECT COUNT(*) FROM equipmentrental WHERE PlayerID = :pid1 AND Status = "active") as active_rentals,
            (SELECT COALESCE(SUM(TotalCost), 0) FROM equipmentrental WHERE PlayerID = :pid2) as total_spent
        ');
        $this->db->bind(':pid1', $playerId);
        $this->db->bind(':pid2', $playerId);
        return $this->db->single();
    }

    // Get facility stats
    public function getFacilityStats() {
        $this->db->query('SELECT
            (SELECT COUNT(*) FROM facility) as total_facilities,
            (SELECT COUNT(*) FROM facility WHERE AvailabilityStatus = "available") as available_facilities
        ');
        return $this->db->single();
    }

    // Get today's facility bookings count
    public function getTodaysFacilityBookings() {
        $today = date('Y-m-d');
        $this->db->query('SELECT COUNT(*) as count FROM facilitybooking WHERE DATE(BookingDate) = :date');
        $this->db->bind(':date', $today);
        $result = $this->db->single();
        return $result->count ?? 0;
    }

    // Get today's facility revenue
    public function getTodaysFacilityRevenue() {
        $today = date('Y-m-d');
        $this->db->query('SELECT SUM(CAST(TotalCost AS DECIMAL(10,2))) as revenue FROM facilitybooking WHERE DATE(BookingDate) = :date');
        $this->db->bind(':date', $today);
        $result = $this->db->single();
        return (float)($result->revenue ?? 0);
    }

    // Get facilities in maintenance
    public function getFacilitiesInMaintenance() {
        $this->db->query('SELECT COUNT(*) as count FROM facility WHERE AvailabilityStatus = "maintenance"');
        $result = $this->db->single();
        return $result->count ?? 0;
    }
}
?>
