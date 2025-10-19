# AUTHENTICATION SYSTEM - TESTING GUIDE
**Elite Cricket Academy Management System**

## 📋 AUTHENTICATION IMPLEMENTATION SUMMARY

### ✅ Completed Tasks

1. **Sample User Data Created**
   - 5 test users added to database with proper roles
   - All users have password: `password123`
   - Profile tables populated (CoachProfile, TrainerProfile, ShopEmployeeProfile, PlayerProfile)

2. **Logout Functionality Verified**
   - All dashboard logout buttons pointing to `/login/logout`
   - Session destruction working properly
   - Redirects to login page after logout

3. **Login System Enhanced**
   - Credential validation with email/username support
   - Password verification using bcrypt
   - Role-based dashboard redirecting
   - Session management implemented

4. **Access Control Added**
   - Session validation in all controllers
   - Role-based access control (RBAC)
   - Unauthorized access prevention
   - Auto-redirect to appropriate dashboard

---

## 👥 TEST USER CREDENTIALS

### 1️⃣ Admin User
- **Username:** `admin`
- **Email:** `admin@cricketacademy.com`
- **Password:** `password123`
- **Dashboard:** http://localhost/Elite/admin/dashboard
- **Access:** Full admin panel (Dashboard, Staff, Events, Feedback, Finance)

### 2️⃣ Trainer User
- **Username:** `trainer`
- **Email:** `trainer@test.com`
- **Password:** `password123`
- **Dashboard:** http://localhost/Elite/trainer/dashboard
- **Access:** Trainer management panel (Bookings, Schedules, Events)

### 3️⃣ Coach User
- **Username:** `coach`
- **Email:** `coach@test.com`
- **Password:** `password123`
- **Dashboard:** http://localhost/Elite/coach/dashboard
- **Access:** Coach management panel (Bookings, Events, Player Management)

### 4️⃣ Shop Employee
- **Username:** `shopkeeper`
- **Email:** `shop@test.com`
- **Password:** `password123`
- **Dashboard:** http://localhost/Elite/shop/dashboard
- **Access:** Shop management (Orders, Products, Inventory, Rentals, Facilities)

### 5️⃣ Player User
- **Username:** `player`
- **Email:** `player@test.com`
- **Password:** `password123`
- **Dashboard:** http://localhost/Elite/player/dashboard
- **Access:** Player portal (Training, Tournaments, Performance, Payments, Shopping)

---

## 🧪 TESTING CHECKLIST

### Test 1: Login Functionality
- [ ] Navigate to: http://localhost/Elite/login
- [ ] Try logging in with **Admin** credentials
- [ ] Verify redirect to admin dashboard
- [ ] Check that user name appears in header
- [ ] Test invalid password (should show error)
- [ ] Test invalid username (should show error)

### Test 2: Role-Based Access Control
- [ ] Login as **Admin**
- [ ] Try to access trainer dashboard directly: http://localhost/Elite/trainer/dashboard
- [ ] Should redirect back to admin dashboard with error message
- [ ] Repeat for all roles accessing other role dashboards

### Test 3: Logout Functionality
For each user role:
- [ ] Login successfully
- [ ] Click logout button in dashboard
- [ ] Verify redirect to login page
- [ ] Verify session is destroyed
- [ ] Try to access dashboard URL directly (should redirect to login)

### Test 4: Session Persistence
- [ ] Login as any user
- [ ] Navigate between different pages of their dashboard
- [ ] Refresh the page (Cmd+R)
- [ ] Verify user stays logged in
- [ ] Verify user info persists across pages

### Test 5: Direct URL Access (Unauthorized)
Without logging in:
- [ ] Try accessing: http://localhost/Elite/admin/dashboard
- [ ] Should redirect to login with message
- [ ] Try accessing: http://localhost/Elite/coach/dashboard
- [ ] Should redirect to login with message
- [ ] Repeat for all protected routes

### Test 6: Cross-Role Testing
Login sequence:
1. [ ] Login as **Player** → Verify player dashboard
2. [ ] Logout → Verify redirect to login
3. [ ] Login as **Admin** → Verify admin dashboard
4. [ ] Logout → Verify redirect to login
5. [ ] Login as **Coach** → Verify coach dashboard
6. [ ] Verify no session bleed between logins

---

## 🔧 TECHNICAL DETAILS

### Authentication Flow

```
1. User visits /login
2. Enters credentials (username/email + password)
3. Login controller validates:
   - Email/Username exists in database
   - Password matches (bcrypt verification)
4. If valid:
   - Create session variables:
     * $_SESSION['user_id']
     * $_SESSION['user_email']
     * $_SESSION['user_name']
     * $_SESSION['user_role']
   - Redirect to role-based dashboard
5. If invalid:
   - Display error message
   - Stay on login page
```

### Authorization Flow

```
1. User requests dashboard page
2. Controller constructor runs
3. requireAuth(['Role']) checks:
   - Is user logged in? (session exists)
   - Does user have required role?
4. If authorized:
   - Load dashboard page
5. If not authorized:
   - Flash error message
   - Redirect to login (or their own dashboard)
```

### Session Variables

| Variable | Type | Description | Example |
|----------|------|-------------|---------|
| `$_SESSION['user_id']` | int | User's database ID | `1` |
| `$_SESSION['user_email']` | string | User's email | `admin@test.com` |
| `$_SESSION['user_name']` | string | User's full name | `Admin User` |
| `$_SESSION['user_role']` | string | User's role | `Admin` |

### Protected Controllers

| Controller | Allowed Roles | Protected Pages |
|------------|--------------|-----------------|
| `Admin.php` | Admin | All admin/* pages |
| `Coach.php` | Coach | All coach/* pages |
| `Trainer.php` | Trainer | All trainer/* pages |
| `Shop.php` | ShopEmployee | All shop/* pages |
| `Player.php` | Player | All player/* pages |

---

## 🛡️ SECURITY FEATURES

### ✅ Implemented
- Password hashing using bcrypt (PASSWORD_DEFAULT)
- Session-based authentication
- Role-based access control (RBAC)
- SQL injection prevention (prepared statements)
- XSS protection (input sanitization)
- Session hijacking prevention (session_regenerate_id on login)
- Logout functionality (session destruction)

### 🔄 Ready for Enhancement
- Account lockout after failed attempts (code exists, can be activated)
- Password change on first login (schema supports it)
- Last login timestamp tracking (schema supports it)
- Activity logging (code exists, can be activated)
- Remember me functionality
- Password reset via email
- Two-factor authentication (2FA)

---

## 📁 KEY FILES MODIFIED

### Controllers
- `/app/controllers/Login.php` - Login/logout logic
- `/app/controllers/Admin.php` - Admin authentication
- `/app/controllers/Coach.php` - Coach authentication
- `/app/controllers/Trainer.php` - Trainer authentication
- `/app/controllers/Shop.php` - Shop authentication
- `/app/controllers/Player.php` - Player authentication

### Models
- `/app/models/M_Users.php` - User database operations

### Helpers
- `/app/helpers/session_helper.php` - Authentication helpers:
  - `isLoggedIn()` - Check if user is logged in
  - `hasRole($role)` - Check if user has specific role
  - `requireAuth($roles)` - Enforce authentication and role
  - `redirectToDashboard()` - Redirect to user's dashboard

### Database
- `sample_users_data.sql` - Sample user creation script
- `update_sample_users.sql` - Password update script

---

## 🚀 QUICK TEST COMMANDS

### Check Database Users
```bash
/Applications/XAMPP/bin/mysql -u root cricket_academy -e "SELECT UserID, Name, Username, Role, Status FROM User;"
```

### Reset User Passwords
```bash
cd /Applications/XAMPP/xamppfiles/htdocs/Elite
/Applications/XAMPP/bin/mysql -u root cricket_academy < update_sample_users.sql
```

### Check Active Sessions (PHP)
```php
<?php
session_start();
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
?>
```

---

## 🐛 TROUBLESHOOTING

### Issue: "Headers already sent" error
**Solution:** Ensure no output before `session_start()` or `header()` calls

### Issue: Login successful but redirects to login again
**Solution:** 
- Check if sessions are enabled in php.ini
- Verify session_start() is called
- Check browser cookies are enabled

### Issue: User can access other role dashboards
**Solution:** 
- Verify `requireAuth()` is in controller constructor
- Check role parameter matches database Role column exactly

### Issue: Password not matching
**Solution:**
- Verify password hash in database is correct bcrypt format
- Re-run: `update_sample_users.sql`
- Check `password_verify()` is used in M_Users.php

### Issue: Session persists after logout
**Solution:**
- Clear browser cookies
- Check `session_destroy()` is in logout method
- Try incognito/private browser window

---

## ✅ SIGN-OFF CHECKLIST

Before considering authentication complete:

- [x] All 5 test users created in database
- [x] Password hashing implemented (bcrypt)
- [x] Login validates credentials correctly
- [x] Sessions created on successful login
- [x] Role-based redirects working
- [x] All controllers have requireAuth()
- [x] Logout destroys session properly
- [x] Direct URL access blocked for unauthorized users
- [ ] All manual tests passed (see Testing Checklist above)
- [ ] Cross-role testing completed
- [ ] Session persistence verified
- [ ] Security review completed

---

## 📝 NEXT STEPS (Optional Enhancements)

1. **Activity Logging** - Track user actions
2. **Password Strength Validator** - Enforce strong passwords
3. **Account Lockout** - Prevent brute force attacks
4. **Password Reset** - Email-based password recovery
5. **Remember Me** - Persistent login cookie
6. **Profile Management** - Allow users to update their info
7. **Admin User Management** - CRUD operations for users
8. **Role Permissions** - Granular permission system

---

**Last Updated:** October 19, 2025
**System Status:** ✅ Authentication System Active and Ready for Testing
