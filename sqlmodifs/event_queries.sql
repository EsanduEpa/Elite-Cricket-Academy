-- Quick SQL Queries for Event Management

-- 1. Check Event Table Structure
DESCRIBE Event;

-- 2. Count Total Events
SELECT COUNT(*) as total_events FROM Event;

-- 3. View All Events (Basic Info)
SELECT 
    EventID,
    Name,
    Type,
    Category,
    StartDate,
    EndDate,
    Location,
    Status,
    MaxParticipants,
    RegistrationFee
FROM Event
ORDER BY EventID DESC;

-- 4. View Latest Event (All Fields)
SELECT * FROM Event 
ORDER BY EventID DESC 
LIMIT 1;

-- 5. View Upcoming Events
SELECT 
    EventID,
    Name,
    Type,
    Category,
    StartDate,
    Location,
    Status
FROM Event
WHERE Status IN ('upcoming', 'registration_open', 'registration_closed')
AND StartDate >= NOW()
ORDER BY StartDate ASC;

-- 6. View Events by Type
SELECT 
    Type,
    COUNT(*) as count
FROM Event
GROUP BY Type
ORDER BY count DESC;

-- 7. View Events by Category
SELECT 
    Category,
    COUNT(*) as count
FROM Event
GROUP BY Category
ORDER BY count DESC;

-- 8. View Events with Contact Info
SELECT 
    EventID,
    Name,
    PrimaryContact,
    ContactEmail,
    ContactPhone,
    StartDate
FROM Event
WHERE PrimaryContact IS NOT NULL
ORDER BY StartDate DESC;

-- 9. View Events by Organizer (User)
SELECT 
    e.EventID,
    e.Name,
    e.Type,
    e.StartDate,
    u.FirstName,
    u.LastName,
    u.Role
FROM Event e
JOIN User u ON e.OrganizedBy = u.UserID
ORDER BY e.StartDate DESC;

-- 10. View Events with Coordinator
SELECT 
    e.EventID,
    e.Name,
    e.StartDate,
    CONCAT(u.FirstName, ' ', u.LastName) as Coordinator,
    u.Role as CoordinatorRole
FROM Event e
LEFT JOIN User u ON e.EventCoordinator = u.UserID
WHERE e.EventCoordinator IS NOT NULL
ORDER BY e.StartDate DESC;

-- 11. View Events with Registration Info
SELECT 
    EventID,
    Name,
    RegistrationStart,
    RegistrationEnd,
    RegistrationFee,
    MaxParticipants,
    Status
FROM Event
WHERE RegistrationStart IS NOT NULL
ORDER BY RegistrationStart DESC;

-- 12. View This Month's Events
SELECT 
    EventID,
    Name,
    Type,
    StartDate,
    EndDate,
    Location,
    Status
FROM Event
WHERE MONTH(StartDate) = MONTH(CURRENT_DATE())
AND YEAR(StartDate) = YEAR(CURRENT_DATE())
ORDER BY StartDate ASC;

-- 13. View Events with Special Requirements
SELECT 
    EventID,
    Name,
    Type,
    SpecialRequirements,
    StartDate
FROM Event
WHERE SpecialRequirements IS NOT NULL 
AND SpecialRequirements != ''
ORDER BY StartDate DESC;

-- 14. Delete Test Events (Use with caution!)
-- DELETE FROM Event WHERE Name LIKE '%Test%';

-- 15. View Complete Event Details (Sample)
SELECT 
    e.EventID,
    e.Name as EventName,
    e.Type,
    e.Category,
    e.Description,
    e.StartDate,
    e.EndDate,
    e.Location,
    e.Status,
    e.MaxParticipants,
    e.RegistrationFee,
    e.RegistrationStart,
    e.RegistrationEnd,
    e.PrimaryContact,
    e.ContactEmail,
    e.ContactPhone,
    CONCAT(organizer.FirstName, ' ', organizer.LastName) as OrganizedBy,
    CONCAT(coordinator.FirstName, ' ', coordinator.LastName) as Coordinator,
    e.SpecialRequirements
FROM Event e
LEFT JOIN User organizer ON e.OrganizedBy = organizer.UserID
LEFT JOIN User coordinator ON e.EventCoordinator = coordinator.UserID
ORDER BY e.EventID DESC
LIMIT 1;
