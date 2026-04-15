<?php
class Finance {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * GET TOTAL REVENUE - All completed transactions
     * Sources: Product orders, Subscription payments
     */
    public function getTotalRevenue() {
        // Product orders revenue (completed only)
        $this->db->query("SELECT COALESCE(SUM(TotalAmount), 0) as revenue 
                         FROM productorder 
                         WHERE Status = 'completed'");
        $productRevenue = $this->db->single()->revenue;

        // Subscription payments revenue (completed only)
        $this->db->query("SELECT COALESCE(SUM(Amount), 0) as revenue 
                         FROM subscriptionpayment 
                         WHERE Status = 'completed'");
        $subscriptionRevenue = $this->db->single()->revenue;

        return floatval($productRevenue) + floatval($subscriptionRevenue);
    }

    /**
     * GET MONTHLY REVENUE - Current month
     */
    public function getMonthlyRevenue() {
        $currentMonth = date('Y-m');

        // Product orders this month
        $this->db->query("SELECT COALESCE(SUM(TotalAmount), 0) as revenue 
                         FROM productorder 
                         WHERE Status = 'completed' 
                         AND DATE_FORMAT(OrderDate, '%Y-%m') = :month");
        $this->db->bind(':month', $currentMonth);
        $productRevenue = $this->db->single()->revenue;

        // Subscription payments this month
        $this->db->query("SELECT COALESCE(SUM(Amount), 0) as revenue 
                         FROM subscriptionpayment 
                         WHERE Status = 'completed' 
                         AND DATE_FORMAT(PaymentDate, '%Y-%m') = :month");
        $this->db->bind(':month', $currentMonth);
        $subscriptionRevenue = $this->db->single()->revenue;

        return floatval($productRevenue) + floatval($subscriptionRevenue);
    }

    /**
     * GET REVENUE BY CATEGORY
     */
    public function getRevenueByCategory() {
        $revenue = [
            'shop_sales' => 0,
            'membership_fees' => 0
        ];

        // Shop sales (product orders)
        $this->db->query("SELECT COALESCE(SUM(TotalAmount), 0) as revenue 
                         FROM productorder 
                         WHERE Status = 'completed'");
        $revenue['shop_sales'] = floatval($this->db->single()->revenue);

        // Membership fees (subscription payments)
        $this->db->query("SELECT COALESCE(SUM(Amount), 0) as revenue 
                         FROM subscriptionpayment 
                         WHERE Status = 'completed'");
        $revenue['membership_fees'] = floatval($this->db->single()->revenue);

        // Calculate percentages
        $total = $revenue['shop_sales'] + $revenue['membership_fees'];
        
        $categories = [
            'shop_sales' => [
                'amount' => $revenue['shop_sales'],
                'percentage' => $total > 0 ? round(($revenue['shop_sales'] / $total) * 100, 1) : 0
            ],
            'membership_fees' => [
                'amount' => $revenue['membership_fees'],
                'percentage' => $total > 0 ? round(($revenue['membership_fees'] / $total) * 100, 1) : 0
            ]
        ];

        return $categories;
    }

    /**
     * GET RECENT TRANSACTIONS - Combined orders and payments
     */
    public function getRecentTransactions($limit = 20) {
        $transactions = [];

        // Get product orders
        $this->db->query("SELECT 
            po.OrderID as id,
            'Product Order' as type,
            CONCAT(u.FirstName, ' ', u.LastName) as customer,
            po.TotalAmount as amount,
            po.OrderDate as date,
            po.Status as status,
            po.PaymentMethod as method
        FROM productorder po
        LEFT JOIN User u ON po.PlayerID = u.UserID
        ORDER BY po.OrderDate DESC
        LIMIT :limit");
        $this->db->bind(':limit', $limit);
        $orders = $this->db->resultSet();

        foreach($orders as $order) {
            $transactions[] = [
                'id' => 'ORD-' . $order->id,
                'type' => $order->type,
                'customer' => $order->customer ?? 'Unknown',
                'amount' => floatval($order->amount),
                'date' => $order->date,
                'status' => $order->status,
                'method' => ucfirst($order->method)
            ];
        }

        // Get subscription payments
        $this->db->query("SELECT 
            sp.PaymentID as id,
            'Subscription Payment' as type,
            CONCAT(u.FirstName, ' ', u.LastName) as customer,
            sp.Amount as amount,
            sp.PaymentDate as date,
            sp.Status as status,
            sp.PaymentMethod as method
        FROM subscriptionpayment sp
        LEFT JOIN playersubscription ps ON sp.SubscriptionID = ps.SubscriptionID
        LEFT JOIN User u ON ps.PlayerID = u.UserID
        ORDER BY sp.PaymentDate DESC
        LIMIT :limit");
        $this->db->bind(':limit', $limit);
        $payments = $this->db->resultSet();

        foreach($payments as $payment) {
            $transactions[] = [
                'id' => 'PAY-' . $payment->id,
                'type' => $payment->type,
                'customer' => $payment->customer ?? 'Unknown',
                'amount' => floatval($payment->amount),
                'date' => $payment->date,
                'status' => $payment->status,
                'method' => ucfirst($payment->method)
            ];
        }

        // Sort by date descending
        usort($transactions, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return array_slice($transactions, 0, $limit);
    }

    /**
     * GET PENDING PAYMENTS COUNT
     */
    public function getPendingPaymentsCount() {
        // Count pending product orders
        $this->db->query("SELECT COUNT(*) as count 
                         FROM productorder 
                         WHERE Status = 'pending'");
        $pendingOrders = $this->db->single()->count;

        // Count pending subscription payments
        $this->db->query("SELECT COUNT(*) as count 
                         FROM subscriptionpayment 
                         WHERE Status = 'pending'");
        $pendingPayments = $this->db->single()->count;

        return intval($pendingOrders) + intval($pendingPayments);
    }

    /**
     * GET MONTHLY DATA - Last 12 months revenue
     */
    public function getMonthlyData($months = 12) {
        $data = [];
        
        for ($i = $months - 1; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $monthName = date('F', strtotime("-$i months"));

            // Product orders for this month
            $this->db->query("SELECT COALESCE(SUM(TotalAmount), 0) as revenue 
                             FROM productorder 
                             WHERE Status = 'completed' 
                             AND DATE_FORMAT(OrderDate, '%Y-%m') = :month");
            $this->db->bind(':month', $month);
            $productRevenue = $this->db->single()->revenue;

            // Subscription payments for this month
            $this->db->query("SELECT COALESCE(SUM(Amount), 0) as revenue 
                             FROM subscriptionpayment 
                             WHERE Status = 'completed' 
                             AND DATE_FORMAT(PaymentDate, '%Y-%m') = :month");
            $this->db->bind(':month', $month);
            $subscriptionRevenue = $this->db->single()->revenue;

            $data[$monthName] = floatval($productRevenue) + floatval($subscriptionRevenue);
        }

        return $data;
    }

    /**
     * GET TOP REVENUE SOURCES
     */
    public function getTopRevenueSources($limit = 5) {
        $sources = [];

        // Top products by revenue
        $this->db->query("SELECT 
            p.Name as item,
            COUNT(poi.OrderItemID) as quantity,
            SUM(poi.SubTotal) as revenue,
            'shop_sales' as category
        FROM productorderitem poi
        INNER JOIN product p ON poi.ProductID = p.ProductID
        INNER JOIN productorder po ON poi.OrderID = po.OrderID
        WHERE po.Status = 'completed'
        GROUP BY p.ProductID
        ORDER BY revenue DESC
        LIMIT :limit");
        $this->db->bind(':limit', $limit);
        $products = $this->db->resultSet();

        foreach($products as $product) {
            $sources[] = [
                'item' => $product->item,
                'quantity' => intval($product->quantity),
                'revenue' => floatval($product->revenue),
                'category' => $product->category
            ];
        }

        // Add subscription as a source
        $this->db->query("SELECT 
            COUNT(*) as quantity,
            SUM(Amount) as revenue
        FROM subscriptionpayment
        WHERE Status = 'completed'");
        $subData = $this->db->single();
        
        if ($subData && $subData->revenue > 0) {
            $sources[] = [
                'item' => 'Membership Subscriptions',
                'quantity' => intval($subData->quantity),
                'revenue' => floatval($subData->revenue),
                'category' => 'membership_fees'
            ];
        }

        // Sort by revenue
        usort($sources, function($a, $b) {
            return $b['revenue'] - $a['revenue'];
        });

        return array_slice($sources, 0, $limit);
    }

    /**
     * GET REVENUE STATS - Summary statistics
     */
    public function getRevenueStats() {
        $totalRevenue = $this->getTotalRevenue();
        $monthlyRevenue = $this->getMonthlyRevenue();
        
        // Previous month for comparison
        $prevMonth = date('Y-m', strtotime('-1 month'));
        $this->db->query("SELECT COALESCE(SUM(TotalAmount), 0) as revenue 
                         FROM productorder 
                         WHERE Status = 'completed' 
                         AND DATE_FORMAT(OrderDate, '%Y-%m') = :month");
        $this->db->bind(':month', $prevMonth);
        $prevProductRev = $this->db->single()->revenue;

        $this->db->query("SELECT COALESCE(SUM(Amount), 0) as revenue 
                         FROM subscriptionpayment 
                         WHERE Status = 'completed' 
                         AND DATE_FORMAT(PaymentDate, '%Y-%m') = :month");
        $this->db->bind(':month', $prevMonth);
        $prevSubRev = $this->db->single()->revenue;
        
        $prevMonthRevenue = floatval($prevProductRev) + floatval($prevSubRev);

        // Calculate growth
        $growthRate = 0;
        if ($prevMonthRevenue > 0) {
            $growthRate = (($monthlyRevenue - $prevMonthRevenue) / $prevMonthRevenue) * 100;
        }

        return [
            'total' => $totalRevenue,
            'monthly' => $monthlyRevenue,
            'daily_average' => $monthlyRevenue / date('j'), // Days in current month
            'growth_rate' => round($growthRate, 1),
            'pending_count' => $this->getPendingPaymentsCount()
        ];
    }
}
