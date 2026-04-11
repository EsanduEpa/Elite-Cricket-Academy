<?php
/**
 * Performance Table Schema Checker
 * Run this file in browser: http://localhost/Elite/check_performance_schema.php
 * This will tell you if the database migration is needed
 */

require_once 'app/bootloader.php';

// Database connection
try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Performance Table Schema Check</h2>";
    echo "<style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .warning { color: orange; font-weight: bold; }
        table { border-collapse: collapse; margin: 20px 0; background: white; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #4A90E2; color: white; }
        .box { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        pre { background: #f9f9f9; padding: 15px; border-radius: 5px; overflow-x: auto; }
    </style>";
    
    // Check if table exists
    $stmt = $db->query("SHOW TABLES LIKE 'playermatchperformance'");
    if ($stmt->rowCount() == 0) {
        echo "<div class='box'><p class='error'>❌ Table 'playermatchperformance' does not exist!</p></div>";
        exit;
    }
    
    echo "<div class='box'><p class='success'>✅ Table 'playermatchperformance' exists</p></div>";
    
    // Get table structure
    $stmt = $db->query("DESCRIBE playermatchperformance");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<div class='box'><h3>Current Table Structure:</h3>";
    echo "<table>";
    echo "<tr><th>Column Name</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach ($columns as $col) {
        echo "<tr>";
        echo "<td>{$col['Field']}</td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "<td>{$col['Key']}</td>";
        echo "<td>" . ($col['Default'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table></div>";
    
    // Check for required columns
    $requiredColumns = [
        'VerifiedStatus' => false,
        'AddedBy' => false,
        'VerifiedBy' => false,
        'VerifiedAt' => false,
        'CreatedAt' => false,
        'UpdatedAt' => false
    ];
    
    foreach ($columns as $col) {
        if (isset($requiredColumns[$col['Field']])) {
            $requiredColumns[$col['Field']] = true;
        }
    }
    
    $allColumnsExist = !in_array(false, $requiredColumns, true);
    
    echo "<div class='box'><h3>Verification Columns Status:</h3><ul>";
    foreach ($requiredColumns as $colName => $exists) {
        if ($exists) {
            echo "<li class='success'>✅ $colName - EXISTS</li>";
        } else {
            echo "<li class='error'>❌ $colName - MISSING</li>";
        }
    }
    echo "</ul>";
    
    if ($allColumnsExist) {
        echo "<p class='success'><strong>✅ All required columns exist! Your database is ready.</strong></p>";
    } else {
        echo "<p class='error'><strong>❌ Migration Required!</strong></p>";
        echo "<p>You need to run the migration SQL to add verification columns.</p>";
        echo "<h4>Run this SQL in phpMyAdmin:</h4>";
        echo "<pre>-- Step 1: Add new columns
ALTER TABLE playermatchperformance
  ADD COLUMN VerifiedStatus ENUM('pending', 'verified', 'rejected') DEFAULT 'pending' 
    COMMENT 'Verification status of performance statistics' AFTER Rating,
  ADD COLUMN AddedBy INT NULL 
    COMMENT 'User who added this performance record' AFTER VerifiedStatus,
  ADD COLUMN VerifiedBy INT NULL 
    COMMENT 'Coach/Admin who verified this record' AFTER AddedBy,
  ADD COLUMN VerifiedAt DATETIME NULL 
    COMMENT 'When the record was verified' AFTER VerifiedBy,
  ADD COLUMN CreatedAt DATETIME DEFAULT CURRENT_TIMESTAMP 
    COMMENT 'When the record was created' AFTER VerifiedAt,
  ADD COLUMN UpdatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP 
    COMMENT 'When the record was last updated' AFTER CreatedAt;

-- Step 2: Add indexes
ALTER TABLE playermatchperformance
  ADD INDEX idx_verified_status (VerifiedStatus),
  ADD INDEX idx_added_by (AddedBy),
  ADD INDEX idx_verified_by (VerifiedBy);

-- Step 3: Add foreign keys (if 'user' table exists)
-- Check your user table name first!
ALTER TABLE playermatchperformance
  ADD CONSTRAINT fk_performance_added_by 
    FOREIGN KEY (AddedBy) REFERENCES user(UserID) 
    ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT fk_performance_verified_by 
    FOREIGN KEY (VerifiedBy) REFERENCES user(UserID) 
    ON DELETE SET NULL ON UPDATE CASCADE;</pre>";
    }
    echo "</div>";
    
    // Check for matches
    $stmt = $db->query("SELECT COUNT(*) as count FROM crimatch");
    $matchCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    echo "<div class='box'>";
    if ($matchCount > 0) {
        echo "<p class='success'>✅ Found $matchCount matches in crimatch table</p>";
    } else {
        echo "<p class='warning'>⚠️ No matches found in crimatch table. Run insert_tournament_match_simple.sql to add sample data.</p>";
    }
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div class='box'><p class='error'>Database Error: " . $e->getMessage() . "</p></div>";
}
?>
