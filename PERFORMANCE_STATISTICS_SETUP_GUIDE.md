# Performance Statistics - Quick Setup Guide

## Step-by-Step Setup

### Step 1: Update the Database Schema

Run the SQL migration to add the verification columns:

```bash
# Option 1: Using command line
mysql -u root -p cricket_academy < add_performance_verified_status.sql

# Option 2: Using phpMyAdmin
# 1. Open phpMyAdmin
# 2. Select 'cricket_academy' database
# 3. Go to SQL tab
# 4. Copy and paste contents of add_performance_verified_status.sql
# 5. Click 'Go'
```

### Step 2: Verify the Changes

Check that the new columns exist:

```sql
DESCRIBE playermatchperformance;
```

You should see these new columns:
- VerifiedStatus
- AddedBy
- VerifiedBy  
- VerifiedAt
- CreatedAt
- UpdatedAt

### Step 3: Test the Feature

1. **Access the Performance Page**:
   - Navigate to: `http://localhost/Elite/player/performance`
   - Or click "Performance" in the player sidebar

2. **Add Performance Statistics**:
   - Scroll down to the "My Performance Statistics" section
   - Click the "Add Performance" button
   - A modal will open with a form

3. **Fill in the Form**:
   - Select a match from the dropdown
   - Enter batting stats (runs scored, balls faced)
   - Enter bowling stats (wickets, overs, runs conceded)
   - Enter fielding stats (catches, stumpings)
   - Rate your performance (0-10)
   - Click "Submit Performance Statistics"

4. **View Your Statistics**:
   - After submission, the page will reload
   - Your performance will appear in the table
   - Status will show as "Pending" (⏳)

## Troubleshooting

### Issue: "No matches available"
**Solution**: Make sure you have matches in the `crimatch` table:
```sql
SELECT * FROM crimatch LIMIT 5;
```

If no matches exist, you need to add some test matches first.

### Issue: Modal doesn't open
**Solution**: Check browser console (F12) for JavaScript errors. Make sure:
- jQuery is loaded
- Font Awesome icons are loaded
- No conflicts with other scripts

### Issue: Form submission fails
**Solution**: 
1. Check browser console for errors
2. Verify the URL path in the AJAX request
3. Check PHP error logs: `c:\xampp\apache\logs\error.log`

### Issue: Dropdown doesn't load matches
**Solution**: 
1. Open browser console
2. Check the AJAX request to `/player/getAvailableMatches`
3. Verify the controller method exists and is accessible

## Testing with Sample Data

### Add a test match (if needed):
```sql
INSERT INTO crimatch (TournamentID, Date, Venue, OpponentTeam, OurScore, OpponentScore, Result)
VALUES (1, '2026-02-20', 'Home Ground', 'Thunder Warriors', '185/8', '178/10', 'Won by 7 runs');
```

### View all performance records:
```sql
SELECT 
    pmp.PerformanceID,
    u.Name AS PlayerName,
    cm.Date,
    cm.OpponentTeam,
    pmp.RunsScored,
    pmp.WicketsTaken,
    pmp.VerifiedStatus
FROM playermatchperformance pmp
JOIN user u ON pmp.PlayerID = u.UserID
LEFT JOIN crimatch cm ON pmp.MatchID = cm.MatchID
ORDER BY pmp.CreatedAt DESC;
```

### View pending performance records:
```sql
SELECT 
    pmp.*,
    u.Name AS PlayerName,
    cm.OpponentTeam
FROM playermatchperformance pmp
JOIN user u ON pmp.PlayerID = u.UserID
LEFT JOIN crimatch cm ON pmp.MatchID = cm.MatchID
WHERE pmp.VerifiedStatus = 'pending';
```

## Next Steps (Coach Verification)

To implement coach verification functionality:

1. **Create Coach View** (`app/views/coach/verify_performance.php`)
2. **Add Coach Controller Methods**:
   ```php
   public function verifyPerformance() {
       // Display pending performance records
   }
   
   public function updatePerformanceStatus() {
       // Verify or reject performance record
   }
   ```

3. **Update Navigation**: Add link to coach dashboard

## Screenshots Locations

After testing, you should see:
- ✅ Performance Statistics table in the Performance page
- ✅ "Add Performance" button
- ✅ Modal with form fields
- ✅ Match dropdown populated with matches
- ✅ Status badges (Pending/Verified/Rejected)
- ✅ Success notification after submission

## Database Schema Diagram

```
playerprofile
    └── PlayerID (PK)
            ↓ (FK)
playermatchperformance
    ├── PerformanceID (PK)
    ├── PlayerID (FK) → playerprofile.PlayerID
    ├── MatchID (FK) → crimatch.MatchID
    ├── AddedBy (FK) → user.UserID
    ├── VerifiedBy (FK) → user.UserID
    ├── VerifiedStatus ENUM('pending', 'verified', 'rejected')
    └── Statistics (runs, wickets, catches, etc.)
            ↑
        crimatch
        └── MatchID (PK)
```

## Feature Checklist

- [x] Database schema updated with VerifiedStatus column
- [x] Model methods created for CRUD operations
- [x] Controller methods for AJAX endpoints
- [x] View updated with performance table and modal
- [x] JavaScript handlers for form submission
- [x] Verification status badges implemented
- [ ] Coach verification interface (Future)
- [ ] Email notifications (Future)
- [ ] Mobile responsive design (Future)

## Support & Documentation

- Full documentation: `PERFORMANCE_STATISTICS_IMPLEMENTATION.md`
- Database schema: `cricket_academy_schema.sql`
- Sample data: `insert_all_sample_data.sql`

---

**Date Created**: February 17, 2026  
**Version**: 1.0.0  
**Status**: Initial Implementation Complete
