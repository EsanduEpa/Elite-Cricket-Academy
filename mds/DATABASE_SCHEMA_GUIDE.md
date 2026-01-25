# Cricket Academy Database Schema Documentation

## Overview
This document outlines the comprehensive database schema for the Cricket Academy Management System. The schema is designed for MySQL 8.0+ with enhanced security, role-based access control, and comprehensive functionality.

## Core Tables

### 1. User Table (Main user management)
```sql
CREATE TABLE User (
    UserID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(255) NOT NULL,
    DateOfBirth DATE NOT NULL,
    PhoneNumber VARCHAR(20),
    Email VARCHAR(255) UNIQUE NOT NULL,
    Address TEXT,
    Role ENUM('Admin', 'ShopEmployee', 'Coach', 'Trainer', 'Player') NOT NULL,
    Username VARCHAR(100) UNIQUE NOT NULL,
    PasswordHash VARCHAR(255) NOT NULL,
    DateJoined DATETIME DEFAULT CURRENT_TIMESTAMP,
    Status ENUM('active', 'inactive') DEFAULT 'active',
    ...additional security fields
)
```

**Key Features:**
- Centralized user management for all roles
- Enhanced security with password change deadlines
- Account locking mechanism
- Login attempt tracking
- Admin notes and creation tracking

### 2. Role-Specific Profile Tables

#### PlayerProfile
- Links to User.UserID
- Contains batting/bowling styles, jersey number
- Subscription type management
- Emergency contact information
- Medical conditions tracking

#### CoachProfile
- Specialization areas (Batting, Bowling, All-rounder, Wicket-keeping)
- Head coach designation (only one per academy)
- Experience and certifications

#### TrainerProfile
- Physical training specialization
- Experience and certifications

#### ShopEmployeeProfile
- Department assignment (Equipment, Facility, General)
- Hire date tracking

### 3. Assignment and Relationship Tables

#### PlayerCoachAssignment
- Tracks player-coach relationships
- Assignment types: regular, private, both
- Status tracking and notes

#### PlayerTrainerAssignment
- Tracks player-trainer relationships
- Physical training assignments

### 4. Session Management
- Session types: group, private, training
- Enrollment and attendance tracking
- Performance updates and ratings
- Coaching session logs

### 5. Facility and Equipment Management
- Facility booking system
- Equipment rental tracking
- Product inventory management
- Maintenance schedules

### 6. Financial Management
- Membership plans and subscriptions
- Payment tracking and processing
- Late fees and penalties
- Financial reporting

### 7. Communication System
- Notification system (regular and live)
- Email tracking and delivery status
- Activity logging and audit trails

## Key Relationships

1. **User → Profile Tables**: One-to-one relationships based on role
2. **Player ↔ Coach**: Many-to-many through PlayerCoachAssignment
3. **Player ↔ Trainer**: Many-to-many through PlayerTrainerAssignment
4. **Sessions**: Connected to coaches, trainers, and players
5. **Bookings**: Link facilities, equipment, and users

## Role-Based Access Control

### Admin
- Full system access
- User management and role assignments
- Financial overview and reporting
- System configuration

### ShopEmployee
- Equipment and facility management
- Booking processing
- Payment collection
- Inventory management

### Coach
- Player assignment management
- Session scheduling and logging
- Performance tracking
- Training program development

### Trainer
- Physical training programs
- Player fitness assessments
- Equipment maintenance
- Health and safety monitoring

### Player
- Profile management
- Session booking and attendance
- Performance viewing
- Payment and subscription management

## Database Features

1. **Security**: Password hashing, account locking, login tracking
2. **Audit Trail**: Complete activity logging system
3. **Performance**: Optimized indexes for fast queries
4. **Scalability**: Designed for growth with proper normalization
5. **Data Integrity**: Foreign key constraints and validation
6. **Reporting**: Comprehensive views for analytics

## Integration Points

- Email system integration for notifications
- Payment gateway integration for online payments
- Mobile app API endpoints
- Reporting and analytics dashboards
- Third-party sports management tools

## Migration Notes

When updating existing code to use this schema:

1. **Table Names**: Use exact case-sensitive names (e.g., "User" not "user")
2. **Field Names**: Use camelCase as specified (e.g., "UserID", "DateOfBirth")
3. **Relationships**: Always use proper foreign key references
4. **Validation**: Implement ENUM validations in application code
5. **Security**: Use prepared statements and proper password hashing

This schema provides a robust foundation for the Cricket Academy Management System with room for future enhancements and scalability.