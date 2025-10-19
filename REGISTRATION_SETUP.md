# 🏏 Elite Cricket Academy - Registration Setup Guide

## 📋 **Prerequisites**
- XAMPP running (Apache + MySQL)
- Database named `cricket_academy` created

---

## 🚀 **Step-by-Step Setup**

### **Step 1: Create Database & Table**
1. Open **phpMyAdmin** (http://localhost/phpmyadmin)
2. Create database `cricket_academy` (if not already created)
3. Run the SQL script from `/Elite/database_setup.sql`:

```sql
USE cricket_academy;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL UNIQUE,
  `email` varchar(100) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `date_of_birth` date NOT NULL,
  `address` text NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `school` varchar(100) NOT NULL,
  `user_type` enum('player','coach','trainer','admin','staff') DEFAULT 'player',
  `membership_level` enum('basic','premium','elite') DEFAULT 'basic',
  `status` enum('active','inactive','pending') DEFAULT 'pending',
  `profile_image` varchar(255) DEFAULT NULL,
  `emergency_contact` varchar(100) DEFAULT NULL,
  `emergency_phone` varchar(20) DEFAULT NULL,
  `medical_conditions` text DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### **Step 2: Test Database Connection**
Visit: `http://localhost/Elite/public/test_db.php`

You should see:
- ✅ Database connection successful!
- ✅ Users table exists!

### **Step 3: Test Registration**
1. Go to: `http://localhost/Elite/register`
2. Fill out the registration form
3. Click "Register"

---

## 🔧 **Files Modified**

### **Database Configuration**
- ✅ `app/config/config.php` - Updated DB_NAME to 'cricket_academy'

### **Registration System**
- ✅ `app/controllers/Register.php` - Added username uniqueness check
- ✅ `app/models/M_Users.php` - Already configured for user registration
- ✅ `app/views/v_register.php` - Added proper error display
- ✅ `public/css/register.css` - Added error message styling

---

## 📊 **Database Schema**

The `users` table includes:
- **Basic Info**: id, full_name, username, email, password
- **Personal**: date_of_birth, address, contact_number, school
- **System**: user_type, membership_level, status
- **Optional**: profile_image, emergency_contact, medical_conditions
- **Timestamps**: created_at, updated_at

---

## 🎯 **Registration Flow**

1. **User visits** `/register`
2. **Fills form** with personal information
3. **Server validates** all fields
4. **Checks uniqueness** of email and username
5. **Hashes password** securely
6. **Inserts user** into database
7. **Redirects to login** with success message

---

## 🔍 **Testing Checklist**

- [ ] Database connection works
- [ ] Users table exists
- [ ] Registration form loads
- [ ] Form validation works (client & server)
- [ ] Email uniqueness check works
- [ ] Username uniqueness check works
- [ ] Password hashing works
- [ ] User successfully inserted into database
- [ ] Success message displays
- [ ] Redirect to login works

---

## 🛠 **Troubleshooting**

### **Database Connection Issues**
- Check XAMPP MySQL is running
- Verify database name is `cricket_academy`
- Check credentials in `config.php`

### **Table Not Found**
- Run the SQL script to create users table
- Refresh test_db.php to verify

### **Registration Fails**
- Check browser console for JavaScript errors
- Check PHP error logs
- Verify all form fields are filled correctly

---

## 🎉 **Next Steps**

After registration works:
1. **Set up login functionality**
2. **Create user dashboards**
3. **Add profile management**
4. **Implement role-based access**

---

**Good luck! 🚀**