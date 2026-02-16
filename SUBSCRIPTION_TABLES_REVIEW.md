# Subscription Tables Review & Recommendations

## Current Connection Between Tables

The tables **are already connected** via a common field:

```
membershipplan                playersubscription              subscriptionpayment
├─ PlanID (PK)      ←────────┤ PlanID (FK)                   
                              ├─ SubscriptionID (PK)  ←───────┤ SubscriptionID (FK)
                              ├─ PlayerID (FK)
```

**Current Foreign Keys:**
- `playersubscription.PlanID` → `membershipplan.PlanID`
- `playersubscription.PlayerID` → `playerprofile.PlayerID`
- `subscriptionpayment.SubscriptionID` → `playersubscription.SubscriptionID`
- `subscriptionpayment.ProcessedBy` → `shopemployeeprofile.ShopEmployeeID`

✅ **The connection exists and works correctly.**

---

## Current subscriptionpayment Schema

```sql
CREATE TABLE subscriptionpayment (
  PaymentID INT AUTO_INCREMENT PRIMARY KEY,
  SubscriptionID INT NOT NULL,
  PaymentDate DATE NOT NULL,
  Amount DECIMAL(10,2) NOT NULL,
  PaymentMethod ENUM('cash','card','bank_transfer','online') NOT NULL,
  Status ENUM('pending','completed','failed','refunded') DEFAULT 'pending',
  DueDate DATE NOT NULL,
  LateFee DECIMAL(10,2) DEFAULT 0.00,
  ProcessedBy INT,
  
  FOREIGN KEY (SubscriptionID) REFERENCES playersubscription(SubscriptionID) ON DELETE CASCADE,
  FOREIGN KEY (ProcessedBy) REFERENCES shopemployeeprofile(ShopEmployeeID) ON DELETE SET NULL
) ENGINE=InnoDB COMMENT='Monthly subscription payments';
```

---

## ⚠️ Problems with Current Design

### 1. **No Prevention of Duplicate Payments for Same Month**
- A player could accidentally be charged twice for January 2026.
- Need: `UNIQUE (SubscriptionID, BillingMonth)` or similar.

### 2. **No Clear "Billing Period" Representation**
- `DueDate` doesn't clearly show *which month* this payment is for.
- Is a Feb 5 payment for January or February?

### 3. **Missing Audit Trail**
- No `CreatedAt` / `UpdatedAt` timestamps.
- Can't track when a payment record was created vs when it was actually processed.

### 4. **No Transaction Reference**
- For online/card payments, you need to store gateway transaction IDs for refunds/reconciliation.

### 5. **Amount Ambiguity**
- If late fees apply, is `Amount` the base fee or base+late?
- Better: separate `AmountDue` and `AmountPaid`.

---

## 🔧 Recommended Column Changes

### Option A: Add BillingMonth (Simple & Clean)

```sql
ALTER TABLE subscriptionpayment
  -- New column to clearly identify which month this payment is for
  ADD COLUMN BillingMonth DATE NOT NULL COMMENT 'First day of billing month (e.g., 2026-02-01)' AFTER SubscriptionID,
  
  -- Prevent duplicate charges for same subscription+month
  ADD UNIQUE KEY uk_subscription_billing (SubscriptionID, BillingMonth),
  
  -- Audit timestamps
  ADD COLUMN CreatedAt DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'When payment record was created',
  ADD COLUMN UpdatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  -- Transaction reference for online/card payments
  ADD COLUMN PaymentReference VARCHAR(100) DEFAULT NULL COMMENT 'Gateway transaction ID or receipt number',
  
  -- Better amount tracking
  ADD COLUMN AmountDue DECIMAL(10,2) NOT NULL COMMENT 'Base subscription fee for this month' AFTER Amount,
  ADD COLUMN AmountPaid DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Actual amount paid (may include late fees)' AFTER AmountDue,
  
  -- Notes for failed/refunded payments
  ADD COLUMN Notes TEXT DEFAULT NULL COMMENT 'Reason for failure, refund details, etc.';

-- Then update existing Amount column to be optional or rename it
ALTER TABLE subscriptionpayment
  MODIFY COLUMN Amount DECIMAL(10,2) DEFAULT NULL COMMENT 'DEPRECATED - use AmountDue + LateFee';
```

### Option B: Separate Year/Month Columns (More Normalized)

```sql
ALTER TABLE subscriptionpayment
  ADD COLUMN BillingYear SMALLINT NOT NULL COMMENT 'Year of billing period (e.g., 2026)' AFTER SubscriptionID,
  ADD COLUMN BillingMonth TINYINT NOT NULL COMMENT 'Month of billing period (1-12)' AFTER BillingYear,
  
  ADD UNIQUE KEY uk_subscription_billing (SubscriptionID, BillingYear, BillingMonth),
  
  -- Same other improvements as Option A
  ADD COLUMN CreatedAt DATETIME DEFAULT CURRENT_TIMESTAMP,
  ADD COLUMN UpdatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  ADD COLUMN PaymentReference VARCHAR(100) DEFAULT NULL,
  ADD COLUMN AmountDue DECIMAL(10,2) NOT NULL AFTER Amount,
  ADD COLUMN AmountPaid DECIMAL(10,2) DEFAULT 0.00 AFTER AmountDue,
  ADD COLUMN Notes TEXT DEFAULT NULL;
```

**Recommendation**: Use **Option A** (single `BillingMonth DATE`) - it's simpler for queries and date math.

---

## 📋 Improved Schema (After Changes)

```sql
CREATE TABLE subscriptionpayment (
  PaymentID INT AUTO_INCREMENT PRIMARY KEY,
  SubscriptionID INT NOT NULL,
  BillingMonth DATE NOT NULL COMMENT 'First day of billing month',
  
  DueDate DATE NOT NULL,
  PaymentDate DATE DEFAULT NULL COMMENT 'When payment was actually received',
  
  AmountDue DECIMAL(10,2) NOT NULL COMMENT 'Base subscription fee',
  LateFee DECIMAL(10,2) DEFAULT 0.00,
  AmountPaid DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Total paid (may differ if partial)',
  
  PaymentMethod ENUM('cash','card','bank_transfer','online') NOT NULL,
  Status ENUM('pending','completed','failed','refunded') DEFAULT 'pending',
  PaymentReference VARCHAR(100) DEFAULT NULL COMMENT 'Transaction/receipt reference',
  
  ProcessedBy INT,
  Notes TEXT DEFAULT NULL,
  
  CreatedAt DATETIME DEFAULT CURRENT_TIMESTAMP,
  UpdatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  UNIQUE KEY uk_subscription_billing (SubscriptionID, BillingMonth),
  INDEX idx_status (Status),
  INDEX idx_payment_date (PaymentDate),
  
  FOREIGN KEY (SubscriptionID) REFERENCES playersubscription(SubscriptionID) ON DELETE CASCADE,
  FOREIGN KEY (ProcessedBy) REFERENCES shopemployeeprofile(ShopEmployeeID) ON DELETE SET NULL
) ENGINE=InnoDB COMMENT='Monthly subscription payments with billing period tracking';
```

---

## 🎯 Usage Examples (After Improvements)

### Creating a Payment for Feb 2026
```sql
INSERT INTO subscriptionpayment 
  (SubscriptionID, BillingMonth, DueDate, AmountDue, PaymentMethod, Status)
SELECT 
  SubscriptionID,
  '2026-02-01',           -- Billing month
  '2026-02-01',           -- Due date
  MonthlyFee,             -- From playersubscription
  'card',
  'pending'
FROM playersubscription
WHERE PlayerID = 7 AND Status = 'active';
```

### Finding Unpaid Months
```sql
SELECT 
  ps.SubscriptionID,
  u.Name,
  ps.MonthlyFee,
  sp.BillingMonth,
  sp.Status
FROM playersubscription ps
JOIN user u ON ps.PlayerID = u.UserID
LEFT JOIN subscriptionpayment sp ON ps.SubscriptionID = sp.SubscriptionID
WHERE ps.Status = 'active'
  AND (sp.Status IS NULL OR sp.Status IN ('pending','failed'))
ORDER BY sp.BillingMonth;
```

### Prevent Duplicate Billing (Automatic)
```sql
-- This will fail if a payment for same SubscriptionID + BillingMonth already exists
INSERT INTO subscriptionpayment (SubscriptionID, BillingMonth, ...)
VALUES (123, '2026-02-01', ...);
-- Error: Duplicate entry for unique key 'uk_subscription_billing'
```

---

## 📊 Summary of Benefits

| Current Issue | Improvement | Benefit |
|--------------|-------------|---------|
| Can create duplicate payments | `UNIQUE (SubscriptionID, BillingMonth)` | Prevents double-charging |
| Unclear billing period | `BillingMonth DATE` column | Clear month tracking |
| No audit trail | `CreatedAt`, `UpdatedAt` | Track record changes |
| No transaction tracking | `PaymentReference` | Refund/reconciliation support |
| Amount confusion | Separate `AmountDue`, `AmountPaid`, `LateFee` | Clear accounting |
| No failure notes | `Notes TEXT` | Document why payments fail |

---

## 🚀 Migration Path

1. **Backup your database first**
2. Run the ALTER TABLE statements from Option A
3. Update existing rows to populate BillingMonth from DueDate:
   ```sql
   UPDATE subscriptionpayment
   SET BillingMonth = DATE_FORMAT(DueDate, '%Y-%m-01'),
       AmountDue = Amount,
       AmountPaid = CASE WHEN Status = 'completed' THEN Amount + LateFee ELSE 0 END;
   ```
4. Test queries with the new schema
5. Update your PHP insert/update code to use new columns

---

## 🌍 Real-Life Scenarios

### Scenario 1: New Player Signs Up for "General" Plan

**Steps:**
1. **User Registration** (Front-end form)
   - Parent fills form: child's name, DOB, contact info
   - Selects "General Plan" (group sessions only, Rs. 4500/month)
   - Pays first month fee via card

2. **Database Operations:**
   ```sql
   -- Step 1: Create user account (auto-inserts into playerprofile via trigger)
   INSERT INTO user (Name, Email, Role, ...) VALUES ('Sahan Perera', 'sahan@email.com', 'Player', ...);
   -- Trigger creates: PlayerProfile with PlayerID = 25
   
   -- Step 2: Create subscription
   INSERT INTO playersubscription 
     (PlayerID, PlanID, StartDate, Status, MonthlyFee, PaymentDay, AutoRenewal)
   VALUES 
     (25, 1, '2026-02-15', 'active', 4500.00, 15, 1);
   -- Returns SubscriptionID = 101
   
   -- Step 3: Record first payment
   INSERT INTO subscriptionpayment 
     (SubscriptionID, BillingMonth, DueDate, PaymentDate, AmountDue, AmountPaid, 
      PaymentMethod, Status, PaymentReference, ProcessedBy)
   VALUES 
     (101, '2026-02-01', '2026-02-15', '2026-02-15', 4500.00, 4500.00, 
      'card', 'completed', 'TXN_20260215_4521', 5);
   ```

3. **Result:**
   - Player can now attend group coaching sessions
   - Next payment automatically due March 15, 2026
   - Parent receives confirmation email with receipt

---

### Scenario 2: Monthly Billing Cycle (Automated)

**Every night at midnight, a cron job runs:**

```sql
-- Find all active subscriptions where payment is due tomorrow
SELECT 
  ps.SubscriptionID,
  ps.PlayerID,
  u.Name,
  u.Email,
  ps.MonthlyFee,
  ps.PaymentDay,
  DATE_FORMAT(CURDATE(), '%Y-%m-01') as BillingMonth
FROM playersubscription ps
JOIN user u ON ps.PlayerID = u.UserID
WHERE ps.Status = 'active'
  AND ps.PaymentDay = DAY(CURDATE() + INTERVAL 1 DAY)
  AND NOT EXISTS (
    SELECT 1 FROM subscriptionpayment sp
    WHERE sp.SubscriptionID = ps.SubscriptionID
      AND sp.BillingMonth = DATE_FORMAT(CURDATE(), '%Y-%m-01')
  );
```

**For each subscription found:**
1. Create pending payment record:
   ```sql
   INSERT INTO subscriptionpayment 
     (SubscriptionID, BillingMonth, DueDate, AmountDue, PaymentMethod, Status)
   VALUES (101, '2026-03-01', '2026-03-15', 4500.00, 'card', 'pending');
   ```

2. Send payment reminder email
3. If AutoRenewal = 1, charge saved card
4. Update payment status based on gateway response

---

### Scenario 3: Player Upgrades from "General" to "Pro" Plan

**Real-life:** Parent notices child improving, wants private coaching too.

**Process:**
1. **Admin/Parent clicks "Upgrade Plan"**

2. **System checks:**
   ```sql
   -- Calculate prorated amount for current month
   SELECT 
     DATEDIFF(LAST_DAY(CURDATE()), CURDATE()) as RemainingDays,
     DAY(LAST_DAY(CURDATE())) as TotalDaysInMonth,
     4500.00 as PaidForGeneral,
     10000.00 as NewProFee;
   -- If 15 days left in month: credit = (4500/28) * 15 = Rs. 2410
   -- Additional charge: (10000/28) * 15 - 2410 = Rs. 2947
   ```

3. **Database updates:**
   ```sql
   -- End current subscription
   UPDATE playersubscription 
   SET Status = 'cancelled', EndDate = CURDATE()
   WHERE SubscriptionID = 101;
   
   -- Create new Pro subscription
   INSERT INTO playersubscription 
     (PlayerID, PlanID, StartDate, Status, MonthlyFee, PaymentDay, AutoRenewal)
   VALUES (25, 3, '2026-02-16', 'active', 10000.00, 15, 1);
   -- New SubscriptionID = 102
   
   -- Record upgrade payment (prorated)
   INSERT INTO subscriptionpayment 
     (SubscriptionID, BillingMonth, DueDate, PaymentDate, AmountDue, AmountPaid, 
      PaymentMethod, Status, Notes, ProcessedBy)
   VALUES 
     (102, '2026-02-01', '2026-02-16', '2026-02-16', 2947.00, 2947.00,
      'card', 'completed', 'Prorated upgrade from General to Pro (15 days)', 5);
   ```

4. **Result:**
   - Player now has access to both group AND private sessions
   - Next full Pro payment due March 15 (Rs. 10,000)

---

### Scenario 4: Failed Payment & Suspension

**Real-life:** Credit card expired, payment fails.

**Month of March 15, 2026:**

1. **Auto-billing attempts payment:**
   ```sql
   -- Payment already created as 'pending'
   SELECT PaymentID FROM subscriptionpayment 
   WHERE SubscriptionID = 101 
     AND BillingMonth = '2026-03-01' 
     AND Status = 'pending';
   -- PaymentID = 205
   ```

2. **Gateway returns: "Card Declined"**
   ```sql
   UPDATE subscriptionpayment 
   SET Status = 'failed',
       PaymentDate = CURDATE(),
       Notes = 'Card declined - expired card'
   WHERE PaymentID = 205;
   ```

3. **System sends notification:**
   - Email: "Payment failed, please update card"
   - SMS alert to parent
   - In-app notification

4. **After 3 days (no payment):**
   ```sql
   -- Suspend subscription
   UPDATE playersubscription 
   SET Status = 'suspended'
   WHERE SubscriptionID = 101;
   
   -- Player can no longer book sessions
   ```

5. **Parent updates card and retries:**
   ```sql
   -- Create new payment attempt for same billing month
   INSERT INTO subscriptionpayment 
     (SubscriptionID, BillingMonth, DueDate, PaymentDate, AmountDue, LateFee, 
      AmountPaid, PaymentMethod, Status, PaymentReference)
   VALUES 
     (101, '2026-03-01', '2026-03-15', '2026-03-18', 4500.00, 100.00, 
      4600.00, 'card', 'completed', 'TXN_20260318_8821');
   
   -- Reactivate subscription
   UPDATE playersubscription SET Status = 'active' WHERE SubscriptionID = 101;
   ```

---

### Scenario 5: Annual Report - Revenue by Plan

**Admin needs to see: "How much revenue did each plan generate in 2025?"**

```sql
SELECT 
  mp.PlanName,
  mp.MonthlyFee as CurrentPrice,
  COUNT(DISTINCT ps.SubscriptionID) as TotalSubscriptions,
  COUNT(sp.PaymentID) as TotalPayments,
  SUM(sp.AmountPaid) as TotalRevenue,
  SUM(sp.LateFee) as LateFeeRevenue,
  AVG(DATEDIFF(sp.PaymentDate, sp.DueDate)) as AvgDaysLate
FROM membershipplan mp
LEFT JOIN playersubscription ps ON mp.PlanID = ps.PlanID
LEFT JOIN subscriptionpayment sp ON ps.SubscriptionID = sp.SubscriptionID
WHERE sp.Status = 'completed'
  AND YEAR(sp.PaymentDate) = 2025
GROUP BY mp.PlanID, mp.PlanName, mp.MonthlyFee
ORDER BY TotalRevenue DESC;
```

**Sample Output:**
| PlanName | CurrentPrice | TotalSubscriptions | TotalPayments | TotalRevenue | LateFeeRevenue | AvgDaysLate |
|----------|--------------|-------------------|---------------|--------------|----------------|-------------|
| pro      | 10000.00     | 45                | 520           | 5,200,000    | 12,500         | 2.3         |
| general  | 4500.00      | 120               | 1380          | 6,210,000    | 8,200          | 1.8         |
| private  | 7000.00      | 30                | 350           | 2,450,000    | 3,100          | 1.5         |

---

### Scenario 6: Player Cancels Subscription (Mid-Month)

**Real-life:** Player is moving to another city on Feb 20.

**Process:**
1. **Admin marks subscription as cancelled:**
   ```sql
   UPDATE playersubscription 
   SET Status = 'cancelled', 
       EndDate = '2026-02-20',
       AutoRenewal = 0
   WHERE SubscriptionID = 101;
   ```

2. **No refund for current month** (policy: paid month is non-refundable)
   - Feb payment already marked 'completed'
   - Player can attend sessions until Feb 20

3. **System prevents new billing:**
   ```sql
   -- When March billing runs, this subscription is skipped
   -- because Status != 'active'
   ```

4. **Historical data preserved:**
   - All past payments remain in `subscriptionpayment`
   - Finance reports still accurate
   - Player profile shows "Former member since 2026-02-20"

---

### Scenario 7: Bulk Late Fee Calculation

**1st of every month, apply late fees to overdue payments:**

```sql
-- Find payments pending more than 5 days past due date
UPDATE subscriptionpayment
SET LateFee = CASE 
    WHEN DATEDIFF(CURDATE(), DueDate) BETWEEN 6 AND 10 THEN 100.00
    WHEN DATEDIFF(CURDATE(), DueDate) BETWEEN 11 AND 20 THEN 250.00
    WHEN DATEDIFF(CURDATE(), DueDate) > 20 THEN 500.00
    ELSE 0.00
  END,
  AmountDue = AmountDue + LateFee,
  Notes = CONCAT(
    COALESCE(Notes, ''), 
    ' | Late fee applied: ', DATEDIFF(CURDATE(), DueDate), ' days overdue'
  )
WHERE Status = 'pending'
  AND DATEDIFF(CURDATE(), DueDate) > 5;

-- Send reminder emails
SELECT u.Email, u.Name, sp.AmountDue, sp.LateFee, sp.DueDate
FROM subscriptionpayment sp
JOIN playersubscription ps ON sp.SubscriptionID = ps.SubscriptionID
JOIN user u ON ps.PlayerID = u.UserID
WHERE sp.Status = 'pending' 
  AND sp.LateFee > 0;
```

---

### Scenario 8: Player on "Private" Plan Books Extra Sessions

**Real-life:** Private plan includes 2 sessions/week, player wants 3rd session.

**How the tables work together:**
1. **Check plan limits:**
   ```sql
   SELECT 
     mp.PrivateSessionsIncluded,
     COUNT(se.SessionID) as SessionsThisWeek
   FROM playersubscription ps
   JOIN membershipplan mp ON ps.PlanID = mp.PlanID
   LEFT JOIN sessionenrollment se ON ps.PlayerID = se.PlayerID
     AND YEARWEEK(se.EnrollmentDate) = YEARWEEK(CURDATE())
   WHERE ps.PlayerID = 25;
   -- Result: 2 included, 2 already booked
   ```

2. **Charge for extra session:**
   ```sql
   -- Extra sessions charged separately (not in subscriptionpayment)
   -- Use a different table like 'sessionpayment' or 'extraservicepayment'
   INSERT INTO sessionpayment 
     (PlayerID, SessionID, Amount, PaymentMethod, Status)
   VALUES (25, 456, 1500.00, 'card', 'completed');
   ```

**Key insight:** `subscriptionpayment` is ONLY for recurring monthly fees, not one-time extras.

---

### Scenario 9: Refund Processing

**Real-life:** Payment charged twice by mistake.

```sql
-- Original payment
PaymentID = 301, Amount = 4500.00, Status = 'completed'

-- Duplicate payment (same SubscriptionID + BillingMonth)
PaymentID = 302, Amount = 4500.00, Status = 'completed'

-- Admin processes refund:
UPDATE subscriptionpayment
SET Status = 'refunded',
    Notes = 'Duplicate payment - refunded to card ending 1234',
    PaymentReference = 'REFUND_TXN_20260219_9912'
WHERE PaymentID = 302;

-- Finance team can track:
SELECT SUM(AmountPaid) as TotalRefunded
FROM subscriptionpayment
WHERE Status = 'refunded' 
  AND YEAR(PaymentDate) = 2026;
```

---

## 💡 Key Takeaways from Scenarios

1. **membershipplan** = Template (what's offered)
2. **playersubscription** = Active agreement (who subscribed to what)
3. **subscriptionpayment** = Financial transactions (payment history)

**Data Flow:**
```
Player selects Plan → Creates Subscription → Generates monthly Payments
     (template)            (agreement)              (transactions)
```

**Why separate tables?**
- **Change prices** without affecting existing subscriptions (MonthlyFee stored on subscription)
- **Track payment history** even after subscription ends
- **Support multiple subscriptions** per player (upgrade/downgrade scenarios)
- **Financial reporting** independent of current plan offerings

---

## ✅ Current Seed File Status

The file `insert_membership_subscriptions_payments.sql` is **ready to use** and correctly:
- Inserts only `playersubscription` rows (no payments yet)
- Uses your exact PlanIDs (1,2,3) and fees (4500, 7000, 10000)
- Has safe re-run guards (`WHERE NOT EXISTS`)
- Creates realistic test scenarios (active, suspended, expired subscriptions)

**Next step**: After you apply the schema improvements above, I can create a matching seed file for `subscriptionpayment` using the new `BillingMonth` column pattern.
