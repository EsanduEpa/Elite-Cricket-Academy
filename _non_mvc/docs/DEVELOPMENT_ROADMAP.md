# Elite Cricket Academy - Development Roadmap
**Project Deadline:** April 2026  
**Current Progress:** 60%  
**Time Remaining:** ~10 weeks

---

## 📊 CURRENT SYSTEM OVERVIEW

### ✅ What You Have (60% Complete)

#### 1. **Core Architecture** ✓
- **MVC Framework**: Custom-built PHP MVC
- **Routing System**: Core.php handles URL routing
- **Database Layer**: PDO-based Database.php for MySQL
- **Session Management**: User authentication and role-based access

#### 2. **Frontend (100% Complete)** ✓
- Login/Register pages
- Admin Dashboard + all admin views
- Coach Dashboard + all coach views
- Player Dashboard + all player views
- Trainer Dashboard + all trainer views
- Shop interface

#### 3. **Controllers Implemented** ✓
- `Login.php` - Authentication
- `Register.php` - User registration
- `Admin.php` - Admin operations
- `Coach.php` - Coach operations
- `Player.php` - Player operations
- `Trainer.php` - Trainer operations
- `Shop.php` - Shop operations

#### 4. **Basic CRUD Operations** ✓
- **Events** (Admin) - Create, Read, Update, Delete
- **Sessions** (Coach) - CRUD for training sessions
- **Medical Records** (Player) - CRUD for health records
- **Workout Plans** (Trainer) - CRUD for fitness plans
- **Products** (Shop) - CRUD for shop items

#### 5. **Database Schema** ✓
- All tables created and relationships defined
- User management tables
- Performance tracking tables
- Shop and inventory tables
- Booking system tables

---

## 🎯 WHAT NEEDS TO BE BUILT (40% Remaining)

### Priority System:
- 🔴 **CRITICAL** - Must have for basic functionality
- 🟡 **HIGH** - Important for user experience
- 🟢 **MEDIUM** - Good to have
- ⚪ **LOW** - Nice to have (if time permits)

---

## 📅 WEEK-BY-WEEK DEVELOPMENT PLAN

### **WEEK 1-2: Booking System** 🔴 CRITICAL
**Goal:** Players can book coach/trainer sessions and facilities

**What to Build:**
1. **Bookings Controller** (`app/controllers/Bookings.php`)
   - `create()` - Create new booking
   - `view()` - View all bookings
   - `cancel()` - Cancel booking (with rules)
   - `reschedule()` - Request reschedule

2. **Bookings Model** (`app/models/M_Bookings.php`)
   - `createBooking($data)` - Insert booking
   - `getPlayerBookings($player_id)` - Get player's bookings
   - `getCoachBookings($coach_id)` - Get coach's bookings
   - `validateBooking($data)` - Check availability, limits
   - `cancelBooking($booking_id)` - Cancel with refund logic

3. **Frontend Updates:**
   - Player: Booking form with calendar
   - Coach/Trainer: View and manage bookings
   - Calendar integration (FullCalendar.js)

**Validation Rules:**
- Max 2 bookings per day per player
- Max 3 cancellations per month
- No cancellation within 24 hours
- Check coach/trainer availability

**Files to Create/Edit:**
```
app/controllers/Bookings.php (NEW)
app/models/M_Bookings.php (NEW)
app/views/player/bookings.php (EDIT)
public/js/player/bookings.js (EDIT)
```

---

### **WEEK 3-4: Performance Tracking System** 🔴 CRITICAL
**Goal:** Coaches can track player performance, players can view stats

**What to Build:**
1. **Performance Controller Methods** (in `Coach.php`)
   - `addPerformance()` - Add performance data
   - `editPerformance()` - Update performance
   - `viewPerformanceReport()` - Generate reports

2. **Performance Model** (`app/models/M_Performance.php`)
   - `addPerformanceRecord($data)`
   - `getPlayerPerformance($player_id, $filters)`
   - `calculateAverages($player_id)`
   - `comparePerformance($player_id, $comparison_type)`
   - `getPerformanceStats($player_id)`

3. **Frontend:**
   - Coach: Performance entry form
   - Player: Performance dashboard with charts
   - Statistics visualization (Chart.js)

**Performance Metrics to Track:**
- Batting: Runs, Average, Strike Rate, Centuries, Fifties
- Bowling: Wickets, Average, Economy, 5-wicket hauls
- Fielding: Catches, Run-outs
- Fitness: Speed, Endurance scores

**Files to Create/Edit:**
```
app/models/M_Performance.php (NEW)
app/controllers/Coach.php (EDIT - add methods)
app/views/coach/performance.php (NEW)
app/views/player/statistics.php (EDIT)
public/js/player/statistics.js (EDIT)
```

---

### **WEEK 5-6: Notification System** 🟡 HIGH
**Goal:** Users receive real-time notifications for important events

**What to Build:**
1. **Notification Model** (`app/models/M_Notification.php`)
   - `createNotification($user_id, $message, $type)`
   - `getUserNotifications($user_id)`
   - `markAsRead($notification_id)`
   - `getUnreadCount($user_id)`

2. **Notification Service** (`app/helpers/notification_helper.php`)
   - Functions to trigger notifications on events
   - Email notification wrapper (optional)

3. **Frontend:**
   - Notification bell icon in navbar
   - Notification dropdown
   - Real-time updates (AJAX polling or WebSockets)

**Notification Triggers:**
- Booking confirmed/cancelled/rescheduled
- Performance report added
- Tournament team selection
- Session schedule changes
- Medical record approval/rejection

**Files to Create/Edit:**
```
app/models/M_Notification.php (NEW)
app/helpers/notification_helper.php (NEW)
app/views/inc/components/notifications.php (NEW)
public/js/common/notifications.js (NEW)
```

---

### **WEEK 7-8: Tournament Management** 🟡 HIGH
**Goal:** Complete tournament workflow from creation to team selection

**What to Build:**
1. **Tournament Features in Admin Controller:**
   - Already have create/edit/delete
   - Add: `publishTournament()` - Make visible to coaches
   - Add: `finalizeTeam()` - Lock team selection
   - Add: `addResults()` - Post tournament results

2. **Tournament Model Updates** (`app/models/M_Tournament.php`)
   - `recommendPlayer($tournament_id, $player_id, $coach_id)`
   - `getRecommendations($tournament_id)`
   - `selectTeam($tournament_id, $player_ids)` - Head coach only
   - `notifySelectedPlayers($tournament_id)`
   - `addTournamentResults($tournament_id, $results)`

3. **Frontend:**
   - Coach: Tournament list with recommend button
   - Head Coach: Team selection interface
   - Player: Tournament invitations and results
   - Admin: Tournament management dashboard

**Workflow:**
1. Admin creates tournament (7+ days advance)
2. Coaches recommend players
3. Head coach reviews and selects final team
4. Selected players get notified
5. Admin posts results after tournament

**Files to Create/Edit:**
```
app/models/M_Tournament.php (NEW)
app/controllers/Admin.php (EDIT)
app/controllers/Coach.php (EDIT)
app/views/coach/tournaments.php (EDIT)
app/views/player/tournaments.php (NEW)
```

---

### **WEEK 9-10: Shop E-Commerce Features** 🟡 HIGH
**Goal:** Complete online shop with cart, checkout, and rentals

**What to Build:**
1. **Shop Controller Enhancements:**
   - `addToCart()` - Add item to cart
   - `checkout()` - Process order
   - `rentEquipment()` - Rent instead of buy
   - `viewOrders()` - Order history
   - `returnRental()` - Return rented item

2. **Shop Model Updates:**
   - `processOrder($cart_items, $user_id, $payment_data)`
   - `createRental($product_id, $user_id, $duration)`
   - `updateStock($product_id, $quantity)`
   - `checkAvailability($product_id, $quantity)`
   - `calculateRentalFee($product_id, $duration)`

3. **Frontend:**
   - Shopping cart page
   - Checkout form
   - Order confirmation
   - Rental management
   - Order tracking

**Rental Rules:**
- Max 2 hours per day for equipment
- Deposit required
- Late return penalties
- Damage assessment

**Files to Create/Edit:**
```
app/controllers/Shop.php (EDIT)
app/models/M_Shop.php (EDIT)
app/views/shop/cart.php (NEW)
app/views/shop/checkout.php (NEW)
app/views/shop/orders.php (NEW)
public/js/shop/cart.js (NEW)
```

---

### **WEEK 11-12: Reports & Analytics** 🟢 MEDIUM
**Goal:** Generate comprehensive reports for admin and users

**What to Build:**
1. **Report Controller** (`app/controllers/Reports.php`)
   - `playerStatistics()` - Individual player report
   - `financialReport()` - Revenue, expenses
   - `tournamentReport()` - Tournament analysis
   - `salesReport()` - Shop sales data
   - `exportReport($type, $format)` - PDF/Excel export

2. **Report Model** (`app/models/M_Reports.php`)
   - Query aggregators for various metrics
   - Data formatters for charts
   - Export functions

3. **Frontend:**
   - Admin: Reports dashboard
   - Coach: Player progress reports
   - Player: Personal performance history
   - Shop: Sales analytics

**Files to Create/Edit:**
```
app/controllers/Reports.php (NEW)
app/models/M_Reports.php (NEW)
app/views/admin/reports.php (EDIT)
public/js/admin/reports.js (EDIT)
```

---

### **WEEK 13-14: Advanced Features** ⚪ LOW (If Time Permits)

**Additional Features:**
1. **Communication System**
   - Coach-to-trainer recommendations
   - Internal messaging
   - Feedback system improvements

2. **User Profile Enhancements**
   - Password change
   - Profile picture upload
   - Activity logs

3. **Medical Records Workflow**
   - Approval system
   - Injury tracking
   - Recovery monitoring

4. **Security Hardening**
   - Input validation improvements
   - CSRF protection
   - XSS prevention
   - SQL injection auditing

---

## 🧪 TESTING CHECKLIST

Test each feature after implementation:

### For Each Module:
- [ ] Create operation works
- [ ] Read/View displays correct data
- [ ] Update saves changes properly
- [ ] Delete removes data correctly
- [ ] Validation prevents invalid data
- [ ] Error messages are user-friendly
- [ ] UI is responsive on mobile
- [ ] Role-based access is enforced

### Integration Tests:
- [ ] Booking affects calendar availability
- [ ] Performance updates reflect in statistics
- [ ] Notifications trigger correctly
- [ ] Shop orders update inventory
- [ ] Tournament selections notify players

---

## 📚 FOR VIVA PREPARATION

### Questions You Should Be Able to Answer:

**1. Architecture Questions:**
- Explain your MVC pattern implementation
- How does routing work in your system?
- Why did you choose this architecture?

**2. Database Questions:**
- Explain key database relationships
- How do you prevent SQL injection?
- Explain your normalization strategy

**3. Feature Questions:**
- How does the booking system handle conflicts?
- Explain performance calculation algorithms
- How do notifications work?

**4. Security Questions:**
- How do you secure user authentication?
- Explain role-based access control
- How do you protect against common vulnerabilities?

**5. Project Management Questions:**
- How did you divide work among team members?
- How did you handle version control?
- What was the biggest challenge?

---

## 🎯 SUCCESS METRICS

By April 2026, you should have:
- [ ] 100% completion of critical features
- [ ] Working demo for all user roles
- [ ] Test data for demonstration
- [ ] Documentation for viva
- [ ] Understanding of every component
- [ ] Ability to explain and defend design decisions

---

## 🆘 WHEN YOU NEED HELP

Ask yourself these questions first:
1. What exactly am I trying to achieve?
2. What have I tried already?
3. What error messages am I seeing?
4. Which file/function is causing issues?

Then reach out with specific details!

---

## 📖 LEARNING RESOURCES

- **PHP PDO**: https://www.php.net/manual/en/book.pdo.php
- **Chart.js**: https://www.chartjs.org/docs/latest/
- **FullCalendar**: https://fullcalendar.io/docs
- **Bootstrap**: https://getbootstrap.com/docs/

---

**Remember:** Understanding is more important than completion. Build each feature slowly, test thoroughly, and make sure you can explain WHY and HOW it works.

Good luck! 🚀
