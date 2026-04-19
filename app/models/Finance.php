<?php
class Finance {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getTotalRevenue(): float {
        return array_sum(array_column($this->getRevenueSourceAmounts(), 'amount'));
    }

    public function getMonthlyRevenue(): float {
        return $this->getRevenueForMonth(date('Y-m'));
    }

    public function getRevenueByCategory(): array {
        $sources = $this->getRevenueSourceAmounts();
        $total = array_sum(array_column($sources, 'amount'));
        $categories = [];

        foreach ($sources as $key => $source) {
            $amount = (float)$source['amount'];
            $categories[$key] = [
                'label' => $source['label'],
                'amount' => $amount,
                'percentage' => $total > 0 ? round(($amount / $total) * 100, 1) : 0,
            ];
        }

        return $categories;
    }

    public function getRecentTransactions($limit = 20): array {
        $limit = max(1, (int)$limit);
        $transactions = [];

        if ($this->tableExists('productorder')) {
            $this->db->query("SELECT
                    po.OrderID AS id,
                    'Shop Sale' AS type,
                    CONCAT(u.FirstName, ' ', u.LastName) AS customer,
                    po.TotalAmount AS amount,
                    po.OrderDate AS date,
                    po.Status AS status,
                    po.PaymentMethod AS method
                FROM productorder po
                LEFT JOIN user u ON po.PlayerID = u.UserID
                ORDER BY po.OrderDate DESC
                LIMIT :limit");
            $this->db->bind(':limit', $limit, PDO::PARAM_INT);
            foreach ($this->db->resultSet() as $row) {
                $transactions[] = $this->mapTransaction('ORD-', $row);
            }
        }

        if ($this->tableExists('subscriptionpayment')) {
            $this->db->query("SELECT
                    sp.PaymentID AS id,
                    'Membership Fee' AS type,
                    CONCAT(u.FirstName, ' ', u.LastName) AS customer,
                    sp.Amount AS amount,
                    COALESCE(sp.PaidAt, sp.PaymentDate, sp.UpdatedAt, sp.CreatedAt, sp.DueDate) AS date,
                    sp.Status AS status,
                    sp.PaymentMethod AS method
                FROM subscriptionpayment sp
                LEFT JOIN playersubscription ps ON sp.SubscriptionID = ps.SubscriptionID
                LEFT JOIN user u ON ps.PlayerID = u.UserID
                ORDER BY COALESCE(sp.PaidAt, sp.PaymentDate, sp.UpdatedAt, sp.CreatedAt, sp.DueDate) DESC
                LIMIT :limit");
            $this->db->bind(':limit', $limit, PDO::PARAM_INT);
            foreach ($this->db->resultSet() as $row) {
                $transactions[] = $this->mapTransaction('SUB-', $row);
            }
        }

        if ($this->tableExists('slot_booking')) {
            $this->db->query("SELECT
                    sb.BookingID AS id,
                    'Facility Booking' AS type,
                    CONCAT(u.FirstName, ' ', u.LastName) AS customer,
                    sb.AmountCharged AS amount,
                    COALESCE(sb.PaidAt, sb.UpdatedAt, sb.CreatedAt) AS date,
                    CASE WHEN sb.PaymentStatus = 'paid' THEN 'completed' ELSE sb.PaymentStatus END AS status,
                    sb.PaymentMethod AS method
                FROM slot_booking sb
                LEFT JOIN user u ON sb.PlayerID = u.UserID
                WHERE COALESCE(sb.AmountCharged, 0) > 0
                ORDER BY COALESCE(sb.PaidAt, sb.UpdatedAt, sb.CreatedAt) DESC
                LIMIT :limit");
            $this->db->bind(':limit', $limit, PDO::PARAM_INT);
            foreach ($this->db->resultSet() as $row) {
                $transactions[] = $this->mapTransaction('FAC-', $row);
            }
        }

        if ($this->tableExists('facilitybooking')) {
            $this->db->query("SELECT
                    fb.FacilityBookingID AS id,
                    'Facility Booking' AS type,
                    CONCAT(u.FirstName, ' ', u.LastName) AS customer,
                    fb.TotalCost AS amount,
                    fb.BookingDate AS date,
                    CASE WHEN fb.Status = 'cancelled' THEN 'cancelled' ELSE 'completed' END AS status,
                    'cash' AS method
                FROM facilitybooking fb
                LEFT JOIN user u ON fb.PlayerID = u.UserID
                WHERE COALESCE(fb.TotalCost, 0) > 0
                ORDER BY fb.BookingDate DESC
                LIMIT :limit");
            $this->db->bind(':limit', $limit, PDO::PARAM_INT);
            foreach ($this->db->resultSet() as $row) {
                $transactions[] = $this->mapTransaction('LEG-FAC-', $row);
            }
        }

        if ($this->tableExists('equipmentrental')) {
            $this->db->query("SELECT
                    er.RentalID AS id,
                    'Equipment Rental' AS type,
                    CONCAT(u.FirstName, ' ', u.LastName) AS customer,
                    er.TotalCost AS amount,
                    er.RentalDate AS date,
                    CASE WHEN er.Status = 'cancelled' THEN 'cancelled' ELSE 'completed' END AS status,
                    'online' AS method
                FROM equipmentrental er
                LEFT JOIN user u ON er.PlayerID = u.UserID
                WHERE COALESCE(er.TotalCost, 0) > 0
                ORDER BY er.RentalDate DESC
                LIMIT :limit");
            $this->db->bind(':limit', $limit, PDO::PARAM_INT);
            foreach ($this->db->resultSet() as $row) {
                $transactions[] = $this->mapTransaction('REN-', $row);
            }
        }

        if ($this->tableExists('equipmentreturnpayment')) {
            $this->db->query("SELECT
                    erp.PaymentID AS id,
                    'Return Fee' AS type,
                    CONCAT(u.FirstName, ' ', u.LastName) AS customer,
                    erp.Amount AS amount,
                    COALESCE(erp.PaidAt, erp.PaymentDate, erp.UpdatedAt, erp.CreatedAt, erp.DueDate) AS date,
                    erp.Status AS status,
                    erp.PaymentMethod AS method
                FROM equipmentreturnpayment erp
                LEFT JOIN user u ON erp.PlayerID = u.UserID
                ORDER BY COALESCE(erp.PaidAt, erp.PaymentDate, erp.UpdatedAt, erp.CreatedAt, erp.DueDate) DESC
                LIMIT :limit");
            $this->db->bind(':limit', $limit, PDO::PARAM_INT);
            foreach ($this->db->resultSet() as $row) {
                $transactions[] = $this->mapTransaction('RET-', $row);
            }
        }

        usort($transactions, static function ($a, $b) {
            return strtotime((string)$b['date']) <=> strtotime((string)$a['date']);
        });

        return array_slice($transactions, 0, $limit);
    }

    public function getPendingPaymentsCount(): int {
        $count = 0;

        if ($this->tableExists('productorder')) {
            $count += $this->countRows("SELECT COUNT(*) AS total FROM productorder WHERE Status = 'pending'");
        }
        if ($this->tableExists('subscriptionpayment')) {
            $count += $this->countRows("SELECT COUNT(*) AS total FROM subscriptionpayment WHERE Status = 'pending'");
        }
        if ($this->tableExists('slot_booking')) {
            $count += $this->countRows("SELECT COUNT(*) AS total FROM slot_booking WHERE PaymentStatus = 'pending'");
        }
        if ($this->tableExists('equipmentreturnpayment')) {
            $count += $this->countRows("SELECT COUNT(*) AS total FROM equipmentreturnpayment WHERE Status = 'pending'");
        }

        return $count;
    }

    public function getMonthlyData($months = 12): array {
        $data = [];

        for ($i = max(1, (int)$months) - 1; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $monthName = date('F', strtotime("-$i months"));
            $data[$monthName] = $this->getRevenueForMonth($month);
        }

        return $data;
    }

    public function getTopRevenueSources($limit = 5): array {
        $limit = max(1, (int)$limit);
        $sources = [];

        if ($this->tableExists('productorderitem') && $this->tableExists('productorder') && $this->tableExists('product')) {
            $this->db->query("SELECT
                    p.Name AS item,
                    COUNT(poi.OrderItemID) AS quantity,
                    SUM(poi.SubTotal) AS revenue,
                    'shop_sales' AS category
                FROM productorderitem poi
                INNER JOIN product p ON poi.ProductID = p.ProductID
                INNER JOIN productorder po ON poi.OrderID = po.OrderID
                WHERE po.Status = 'completed'
                GROUP BY p.ProductID, p.Name
                ORDER BY revenue DESC
                LIMIT :limit");
            $this->db->bind(':limit', $limit, PDO::PARAM_INT);
            foreach ($this->db->resultSet() as $row) {
                $sources[] = $this->mapSource($row);
            }
        }

        $categorySources = [
            'membership_fees' => ['Membership Fees', 'subscriptionpayment', 'Status = "completed"', 'Amount'],
            'equipment_rentals' => ['Equipment Rentals', 'equipmentrental', 'Status <> "cancelled"', 'TotalCost'],
            'return_fees' => ['Equipment Return Fees', 'equipmentreturnpayment', 'Status = "completed"', 'Amount'],
        ];

        foreach ($categorySources as $category => [$label, $table, $where, $amountColumn]) {
            if (!$this->tableExists($table)) {
                continue;
            }

            $this->db->query("SELECT COUNT(*) AS quantity, COALESCE(SUM({$amountColumn}), 0) AS revenue FROM {$table} WHERE {$where}");
            $row = $this->db->single();
            if ($row && (float)($row->revenue ?? 0) > 0) {
                $sources[] = [
                    'item' => $label,
                    'quantity' => (int)($row->quantity ?? 0),
                    'revenue' => (float)($row->revenue ?? 0),
                    'category' => $category,
                ];
            }
        }

        $facilityRevenue = (float)($this->getRevenueSourceAmounts()['facility_bookings']['amount'] ?? 0);
        if ($facilityRevenue > 0) {
            $sources[] = [
                'item' => 'Facility & Session Bookings',
                'quantity' => $this->getFacilityBookingCount(),
                'revenue' => $facilityRevenue,
                'category' => 'facility_bookings',
            ];
        }

        usort($sources, static function ($a, $b) {
            return $b['revenue'] <=> $a['revenue'];
        });

        return array_slice($sources, 0, $limit);
    }

    public function getRevenueStats(): array {
        $monthlyRevenue = $this->getMonthlyRevenue();
        $previousMonthRevenue = $this->getRevenueForMonth(date('Y-m', strtotime('-1 month')));
        $growthRate = $previousMonthRevenue > 0
            ? (($monthlyRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100
            : 0;

        return [
            'total' => $this->getTotalRevenue(),
            'monthly' => $monthlyRevenue,
            'daily_average' => $monthlyRevenue / max(1, (int)date('j')),
            'growth_rate' => round($growthRate, 1),
            'pending_count' => $this->getPendingPaymentsCount(),
        ];
    }

    private function getRevenueSourceAmounts(?string $month = null): array {
        $sources = [
            'membership_fees' => [
                'label' => 'Membership Fees',
                'amount' => $this->sumIfTableExists(
                    'subscriptionpayment',
                    'Amount',
                    'Status = "completed"',
                    'DATE_FORMAT(COALESCE(PaidAt, PaymentDate, UpdatedAt, CreatedAt, DueDate), "%Y-%m")',
                    $month
                ),
            ],
            'shop_sales' => [
                'label' => 'Shop Sales',
                'amount' => $this->sumIfTableExists(
                    'productorder',
                    'TotalAmount',
                    'Status = "completed"',
                    'DATE_FORMAT(OrderDate, "%Y-%m")',
                    $month
                ),
            ],
            'facility_bookings' => [
                'label' => 'Facility & Session Bookings',
                'amount' => $this->sumIfTableExists(
                        'slot_booking',
                        'AmountCharged',
                        'PaymentStatus = "paid"',
                        'DATE_FORMAT(COALESCE(PaidAt, UpdatedAt, CreatedAt), "%Y-%m")',
                        $month
                    ) + $this->sumIfTableExists(
                        'facilitybooking',
                        'TotalCost',
                        'Status <> "cancelled"',
                        'DATE_FORMAT(BookingDate, "%Y-%m")',
                        $month
                    ),
            ],
            'equipment_rentals' => [
                'label' => 'Equipment Rentals',
                'amount' => $this->sumIfTableExists(
                    'equipmentrental',
                    'TotalCost',
                    'Status <> "cancelled"',
                    'DATE_FORMAT(RentalDate, "%Y-%m")',
                    $month
                ),
            ],
            'return_fees' => [
                'label' => 'Return Fees',
                'amount' => $this->sumIfTableExists(
                    'equipmentreturnpayment',
                    'Amount',
                    'Status = "completed"',
                    'DATE_FORMAT(COALESCE(PaidAt, PaymentDate, UpdatedAt, CreatedAt, DueDate), "%Y-%m")',
                    $month
                ),
            ],
        ];

        return $sources;
    }

    private function getRevenueForMonth(string $month): float {
        return array_sum(array_column($this->getRevenueSourceAmounts($month), 'amount'));
    }

    private function sumIfTableExists(string $table, string $amountColumn, string $where, string $monthExpression = '', ?string $month = null): float {
        if (!$this->tableExists($table)) {
            return 0.0;
        }

        $sql = "SELECT COALESCE(SUM({$amountColumn}), 0) AS revenue FROM {$table} WHERE {$where}";
        if ($month !== null && $monthExpression !== '') {
            $sql .= " AND {$monthExpression} = :month";
        }

        $this->db->query($sql);
        if ($month !== null && $monthExpression !== '') {
            $this->db->bind(':month', $month);
        }

        $row = $this->db->single();
        return (float)($row->revenue ?? 0);
    }

    private function countRows(string $sql): int {
        $this->db->query($sql);
        $row = $this->db->single();
        return (int)($row->total ?? 0);
    }

    private function getFacilityBookingCount(): int {
        $count = 0;
        if ($this->tableExists('slot_booking')) {
            $count += $this->countRows("SELECT COUNT(*) AS total FROM slot_booking WHERE PaymentStatus = 'paid' AND COALESCE(AmountCharged, 0) > 0");
        }
        if ($this->tableExists('facilitybooking')) {
            $count += $this->countRows("SELECT COUNT(*) AS total FROM facilitybooking WHERE Status <> 'cancelled' AND COALESCE(TotalCost, 0) > 0");
        }
        return $count;
    }

    private function mapTransaction(string $prefix, object $row): array {
        $method = trim((string)($row->method ?? 'online'));
        $status = strtolower(trim((string)($row->status ?? 'pending')));

        return [
            'id' => $prefix . (string)($row->id ?? ''),
            'type' => (string)($row->type ?? 'Payment'),
            'customer' => trim((string)($row->customer ?? '')) ?: 'Unknown',
            'amount' => (float)($row->amount ?? 0),
            'date' => (string)($row->date ?? date('Y-m-d')),
            'status' => $status !== '' ? $status : 'pending',
            'method' => ucfirst($method !== '' ? $method : 'online'),
        ];
    }

    private function mapSource(object $row): array {
        return [
            'item' => (string)($row->item ?? 'Revenue Source'),
            'quantity' => (int)($row->quantity ?? 0),
            'revenue' => (float)($row->revenue ?? 0),
            'category' => (string)($row->category ?? 'general'),
        ];
    }

    private function tableExists(string $table): bool {
        $this->db->query('SHOW TABLES LIKE :table_name');
        $this->db->bind(':table_name', $table);
        return (bool)$this->db->single();
    }
}
