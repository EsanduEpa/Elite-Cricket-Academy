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

## What to optimize (high-impact)

### 1) `membershipplan` (plan catalog)
- Add `CreatedAt` / `UpdatedAt` for auditing.
- Add an index on `(Status, MonthlyFee)` because the app frequently loads active plans ordered by fee.
- (Optional) add `Currency` / `BillingIntervalMonths` if you plan to support non-monthly billing later.

### 2) `playersubscription` (player ↔ plan + price snapshot)
- Add timestamps + cancellation fields so you can answer: “when did this subscription start/end/cancel?”.
- Add an index on `(PlayerID, Status, StartDate)` because the app loads the latest active subscription per player.
- (Recommended) enforce **at most one active subscription per player** (without breaking historical records).

### 3) `subscriptionpayment` (monthly charges & settlement)
- Make the billing period explicit (so you can enforce “one payment per month”).
- Allow `PaymentDate` to be `NULL` for pending payments.
- Add gateway references (PayHere transaction IDs) + audit fields.
- Add indexes aligned to common queries (status/due date and status/payment date).

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
- If you have **no partial payments**, store the **final monthly charge** in `Amount`.
- Since your policy is **no late fees**, do not store any late fee column.

---

## 🔧 Recommended Column Changes

## Fixed monthly due date rule (business policy)

To enforce a **fixed due date** every month:
- **Billing month starts:** 1st day of the month
- **Payments allowed from:** 1st day of the month
- **Due date:** end of the 2nd week (simplest = **14th**)
- **No late fees:** no `LateFee` column
- **Enforcement:** suspend from the **15th** if still unpaid

Practical DB approach:
- Use `BillingMonth` to represent the month (`YYYY-MM-01`).
- Always insert `DueDate` as the **14th** of `BillingMonth`.

Note for new sign-ups:
- If a subscription starts **after** the due date (after the 14th), the simplest rule is: **start billing from next month** (set first `BillingMonth` to next month) so the player gets a full payment window.

```sql
-- If BillingMonth is like '2026-03-01'
SET @DueDate = DATE_ADD(@BillingMonth, INTERVAL 13 DAY); -- 14th
```

### Recommended approach used in this repo

See the migration script: `db_changes_subscription_payments_optimization.sql`.

Key implementation choices:
- `BillingMonth` is a **generated (computed) DATE column** derived from `DueDate` (first day of the month).
  - This avoids bugs where code forgets to populate `BillingMonth`.
  - It also enables a clean uniqueness rule.
- Add `UNIQUE (SubscriptionID, BillingMonth)` to prevent double-billing the same month.
- Add `PaymentReference`, `Gateway*` fields for PayHere reconciliation.
- Add `CreatedAt` / `UpdatedAt` and allow `PaymentDate` to be `NULL`.
- Add indexes to speed up “pending payments” and finance reporting.

---

## 📋 Improved Schema (After Changes)

```sql
CREATE TABLE subscriptionpayment (
  PaymentID INT AUTO_INCREMENT PRIMARY KEY,
  SubscriptionID INT NOT NULL,
  BillingMonth DATE GENERATED ALWAYS AS (first day of month from DueDate) STORED,
  
  DueDate DATE NOT NULL,
  PaymentDate DATE DEFAULT NULL COMMENT 'When payment was actually received',
  
  Amount DECIMAL(10,2) NOT NULL COMMENT 'Base subscription fee (snapshot)',
  
  PaymentMethod ENUM('cash','card','bank_transfer','online') NOT NULL,
  Status ENUM('pending','completed','failed','refunded') DEFAULT 'pending',
  PaymentReference VARCHAR(100) DEFAULT NULL COMMENT 'Transaction/receipt reference',
  Gateway ENUM('manual','payhere') NOT NULL DEFAULT 'manual',
  GatewayOrderId VARCHAR(50) DEFAULT NULL,
  GatewayPaymentId VARCHAR(50) DEFAULT NULL,
  
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
  (SubscriptionID, DueDate, PaymentDate, Amount, PaymentMethod, Status)
SELECT 
  SubscriptionID,
  '2026-02-14',           -- Due date (fixed: 14th)
  NULL,                   -- pending → no payment date yet
  MonthlyFee,             -- Base amount snapshot
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
-- BillingMonth is generated from DueDate, so any second row with a DueDate in the
-- same month will conflict for the same SubscriptionID.
INSERT INTO subscriptionpayment (SubscriptionID, DueDate, Amount, PaymentMethod, Status)
VALUES (123, '2026-02-14', 4500.00, 'card', 'pending');
```

---

## 📊 Summary of Benefits

| Current Issue | Improvement | Benefit |
|--------------|-------------|---------|
| Can create duplicate payments | `UNIQUE (SubscriptionID, BillingMonth)` | Prevents double-charging |
| Unclear billing period | `BillingMonth DATE` column | Clear month tracking |
| No audit trail | `CreatedAt`, `UpdatedAt` | Track record changes |
| No transaction tracking | `PaymentReference` | Refund/reconciliation support |
| Amount confusion | Store final monthly charge in `Amount` | Clear accounting |
| No failure notes | `Notes TEXT` | Document why payments fail |

---

## 🚀 Migration Path

1. **Backup your database first**
2. Run `db_changes_subscription_payments_optimization.sql`
3. Validate there are no duplicate rows per `(SubscriptionID, month(DueDate))` before the UNIQUE index is created.
4. (Optional) update application logic to:
  - set `PaymentDate` only when `Status='completed'`
  - populate `PaymentReference/Gateway*` for online payments

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
     (25, 1, '2026-02-01', 'active', 4500.00, 14, 1);
   -- Returns SubscriptionID = 101
   
   -- Step 3: Record first payment
   INSERT INTO subscriptionpayment 
    (SubscriptionID, DueDate, PaymentDate, Amount,
    PaymentMethod, Status, PaymentReference, Gateway, GatewayOrderId, GatewayPaymentId, ProcessedBy)
   VALUES 
    (101, '2026-02-14', '2026-02-01', 4500.00,
    'online', 'completed', 'PH_TXN_20260201_4521', 'payhere', 'SUBPAY-101-2026-02', 'PH_PAY_123456', 5);
   ```

3. **Result:**
   - Player can now attend group coaching sessions
  - Next payment automatically due March 14, 2026
   - Parent receives confirmation email with receipt

---

### Scenario 2: Monthly Billing Cycle (Automated)

**On the 1st of every month, a cron job runs:**

```sql
-- Find all active subscriptions that do NOT have a payment row for this month yet
SELECT 
  ps.SubscriptionID,
  ps.PlayerID,
  u.Name,
  u.Email,
  ps.MonthlyFee,
  DATE_FORMAT(CURDATE(), '%Y-%m-01') as BillingMonth,
  DATE_ADD(DATE_FORMAT(CURDATE(), '%Y-%m-01'), INTERVAL 13 DAY) as DueDate
FROM playersubscription ps
JOIN user u ON ps.PlayerID = u.UserID
WHERE ps.Status = 'active'
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
     (SubscriptionID, DueDate, PaymentDate, Amount, PaymentMethod, Status)
   VALUES (101, '2026-03-14', NULL, 4500.00, 'online', 'pending');
   ```

2. Send payment reminder email
3. If AutoRenewal = 1, charge saved card
4. Update payment status based on gateway response

---

### Scenario 3: Player Upgrades from "General" to "Pro" Plan

**Real-life:** Parent notices child improving, wants private coaching too.

**Process:**
1. **Admin/Parent clicks "Upgrade Plan"**

2. **Database updates (simple, no history):**
   ```sql
  -- Update subscription plan in-place (payments remain monthly)
  UPDATE playersubscription
  SET PlanID = 3,
     MonthlyFee = 10000.00,
     FeeOverrideReason = 'Upgrade to Pro',
     FeeOverrideAppliedAt = NOW(),
     FeeOverrideAppliedBy = 5
  WHERE SubscriptionID = 101;
   ```

4. **Result:**
   - Player now has access to both group AND private sessions
  - Next full Pro payment due next billing cycle (Rs. 10,000)

---

### Scenario 4: Failed Payment & Suspension

**Real-life:** Credit card expired, payment fails.

**Month of March 2026 (due by March 14):**

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
     PaymentDate = NULL,
     FailedAt = NOW(),
       Notes = 'Card declined - expired card'
   WHERE PaymentID = 205;
   ```

3. **System sends notification:**
   - Email: "Payment failed, please update card"
   - SMS alert to parent
   - In-app notification

4. **On/after the 15th (still unpaid):**
   ```sql
   -- Suspend subscription
   UPDATE playersubscription 
  SET Status = 'suspended',
     StatusChangedAt = NOW()
   WHERE SubscriptionID = 101;
   
   -- Player can no longer book sessions
   ```

5. **Parent updates card and retries:**
   ```sql
   -- Update the existing row (or upsert) for the same billing month.
   -- BillingMonth is generated, and a UNIQUE (SubscriptionID, BillingMonth)
   -- prevents creating a second row for the same month.
   INSERT INTO subscriptionpayment
     (SubscriptionID, DueDate, PaymentDate, Amount, PaymentMethod, Status, PaymentReference)
   VALUES
     (101, '2026-03-14', '2026-03-18', 4500.00, 'card', 'completed', 'TXN_20260318_8821')
   ON DUPLICATE KEY UPDATE
     PaymentDate = VALUES(PaymentDate),
     Status = VALUES(Status),
     PaymentMethod = VALUES(PaymentMethod),
     PaymentReference = VALUES(PaymentReference),
     UpdatedAt = CURRENT_TIMESTAMP,
     PaidAt = CASE
       WHEN VALUES(Status) = 'completed' THEN CURRENT_TIMESTAMP
       ELSE PaidAt
     END;
   
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
  SUM(sp.Amount) as TotalRevenue,
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
| PlanName | CurrentPrice | TotalSubscriptions | TotalPayments | TotalRevenue | AvgDaysLate |
|----------|--------------|-------------------|---------------|--------------|------------|
| pro      | 10000.00     | 45                | 520           | 5,200,000    | 2.3        |
| general  | 4500.00      | 120               | 1380          | 6,210,000    | 1.8        |
| private  | 7000.00      | 30                | 350           | 2,450,000    | 1.5        |

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

### Scenario 7: Bulk Suspension & Reminders

**Daily (or on the 15th), suspend overdue subscriptions and send reminders:**

```sql
-- No late fees policy: suspend if unpaid after DueDate
UPDATE playersubscription ps
SET ps.Status = 'suspended',
    ps.StatusChangedAt = NOW()
WHERE ps.Status = 'active'
  AND EXISTS (
    SELECT 1
    FROM subscriptionpayment sp
    WHERE sp.SubscriptionID = ps.SubscriptionID
      AND sp.BillingMonth = DATE_FORMAT(CURDATE(), '%Y-%m-01')
      AND sp.Status IN ('pending','failed')
      AND sp.DueDate < CURDATE()
  );

-- Send reminder emails
SELECT u.Email, u.Name, sp.Amount, sp.DueDate
FROM subscriptionpayment sp
JOIN playersubscription ps ON sp.SubscriptionID = ps.SubscriptionID
JOIN user u ON ps.PlayerID = u.UserID
WHERE sp.Status = 'pending' 
  AND sp.DueDate >= CURDATE();
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
SELECT SUM(Amount) as TotalRefunded
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
