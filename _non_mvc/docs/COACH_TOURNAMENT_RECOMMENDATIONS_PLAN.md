# Coach Tournament Player Recommendations - Implementation Plan

## 📋 Executive Summary
Enable coaches to recommend and manage player selections for upcoming tournaments. This feature allows coaches to view available tournaments, select players from their assigned roster, add player recommendations with reasoning, and track recommendation status.

---

## 🗂️ Current System Analysis

### Database
- **Database Name:** `cricket_academy`
- **Source Schema:** `cricket_academy (9).sql` (April 1, 2026)

### Existing Database Tables
```
✓ User (UserID, Name, Email, Role, DateOfBirth, Status, etc.)
✓ PlayerProfile (PlayerID, user_id/UserID, BattingStyle, BowlingStyle, CoachID, etc.)
✓ CoachProfile (CoachID, UserID, Specialization, Experience, etc.)
✓ Tournament (TournamentID, Name, StartDate/TournamentDate, Location, CreatedBy, Status, PrizePool)
✓ TournamentPlayer (TournamentID, PlayerID, Team, RoleInTeam, SelectedBy)
✓ PlayerCoachAssignment (PlayerID, CoachID, AssignmentType, Status, etc.)
```

### Verified Active Tables in `cricket_academy (9)`
- All tables confirmed present in current schema
- Foreign key relationships: User → PlayerProfile, CoachProfile, Tournament relationships
- Ready for new recommendation table integration

### Existing Models
- `Event.php` — Gets upcoming/past events
- `M_Performance.php` — Tournament-related queries (getAllTournaments, getPlayerTournaments, etc.)
- `Coach.php` — Coach controller with tournaments() method

### Current Tournaments Page
- Shows upcoming tournaments (grid view with cards)
- Shows past tournaments (table view)
- No player recommendation functionality

---

## 🎯 Feature Requirements

### 1. **Coach Dashboard Integration**
   - Add "Player Recommendations" widget/card on coach dashboard
   - Show:
     - Pending recommendations count
     - Upcoming tournaments needing recommendations
     - Quick actions to recommend players

### 2. **Enhanced Tournaments Page**
   - Add "Recommend Players" button on each tournament card
   - Show tournament details (date, location, participants needed)
   - Modal/drawer to select players and add recommendations
   - Display existing recommendations for each tournament

### 3. **Recommendation Management Section**
   - New page: `/coach/tournament-recommendations`
   - View all recommendations (pending, approved, rejected)
   - Filter by tournament, status, player
   - Edit/delete pending recommendations
   - See admin feedback on rejected recommendations

### 4. **Player Selection Interface**
   - Multi-select from assigned players
   - Player cards showing:
     - Name, photo, batting/bowling style
     - Recent performance metrics
     - Health status (from medical records)
     - Tournament participation history
   - Bulk select / select all options
   - Player statistics for informed decisions

### 5. **Recommendation Details**
   - Reason/comments for recommendation
   - Player role (batsman, bowler, all-rounder, wicket-keeper)
   - Notes field for coach's comments
   - Selected date
   - Status tracking (pending → approved/rejected → confirmed)

---

## 💾 Database Schema Changes Required

### New Table: `coach_tournament_recommendations`
```sql
CREATE TABLE `coach_tournament_recommendations` (
    `RecommendationID` INT PRIMARY KEY AUTO_INCREMENT,
    `CoachID` INT NOT NULL,
    `TournamentID` INT NOT NULL,
    `PlayerID` INT NOT NULL,
    `RecommendedRole` VARCHAR(50), -- batsman, bowler, all-rounder, wicket-keeper
    `Reason` TEXT,
    `Comments` TEXT,
    `Status` ENUM('pending', 'approved', 'rejected', 'confirmed') DEFAULT 'pending',
    `AdminFeedback` TEXT,
    `ReviewedBy` INT,
    `DateRecommended` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `DateReviewed` TIMESTAMP NULL,
    
    FOREIGN KEY (`CoachID`) REFERENCES `user`(`UserID`) ON DELETE RESTRICT,
    FOREIGN KEY (`TournamentID`) REFERENCES `tournament`(`TournamentID`) ON DELETE CASCADE,
    FOREIGN KEY (`PlayerID`) REFERENCES `playerprofile`(`PlayerID`) ON DELETE CASCADE,
    FOREIGN KEY (`ReviewedBy`) REFERENCES `user`(`UserID`) ON DELETE SET NULL,
    
    UNIQUE KEY `unique_recommendation` (`TournamentID`, `PlayerID`, `CoachID`),
    INDEX `idx_coach_tournament` (`CoachID`, `TournamentID`),
    INDEX `idx_status` (`Status`),
    INDEX `idx_tournament` (`TournamentID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Alternative (No New Table - Use tournaments table)
If you prefer to extend existing `tournamentplayer` table with a `RecommendedBy` and `recommendation_status` fields (simpler but less flexible).

---

## 🏗️ Architecture & Data Flow

### Component Hierarchy
```
Coach Tournament Page
├── Upcoming Tournaments Grid
│   ├── Tournament Card
│   │   ├── "Recommend Players" Button
│   │   └── Existing Recommendations Count
│   └── Recommendation Modal
│       ├── Player Selection (Multi-select)
│       ├── Player Stats Panel
│       └── Recommendation Form
├── Recommendation Management Section
│   ├── Filter/Sort Controls
│   ├── Recommendations List
│   │   └── Recommendation Item
│   │       ├── Status Badge
│   │       ├── Player Info
│   │       └── Actions (Edit/Delete/View)
│   └── Details Modal
└── Dashboard Widget (Quick View)
```

### Data Flow Diagram
```
Coach Action
    ↓
Select Players from Assigned Roster
    ↓
Add Recommendation (Role + Reason)
    ↓
Save to coach_player_recommendations (Status: pending)
    ↓
Admin Reviews (Approves/Rejects)
    ↓
Approved → Add to tournamentplayer table
Rejected → Store feedback, awaits coach revision
```

---

## 📝 Implementation Roadmap

### Phase 1: Database & Backend (Models + API)
**Files to Create/Modify:**

1. **Database Migration**
   - Create `coach_player_recommendations` table
   - Add indexes
   - Seed initial data if needed

2. **New Model: `M_TournamentRecommendation.php`**
   ```php
   Methods:
   - getRecommendationsByCoach($coachId)
   - getRecommendationsByTournament($tournamentId)
   - addRecommendation($data)
   - updateRecommendation($recommendationId, $data)
   - deleteRecommendation($recommendationId)
   - approveRecommendation($recommendationId, $feedback)
   - rejectRecommendation($recommendationId, $feedback)
   - getPlayerStats($playerId)
   - getPlayerHealthStatus($playerId)
   - getCoachAssignedPlayers($coachId)
   ```

3. **Extend `Coach.php` Controller**
   ```php
   New Methods:
   - tournament_recommendations() — View all recommendations
   - recommend_players($tournamentId) — Get modal form + data
   - save_recommendation() — POST endpoint (JSON)
   - update_recommendation($id) — PUT endpoint
   - delete_recommendation($id) — DELETE endpoint
   - get_tournament_details($id) — GET tournament + existing recommendations
   - get_assigned_players() — GET coach's players with stats
   ```

### Phase 2: Frontend (Views + Styling)
**Files to Create/Modify:**

1. **Update `app/views/coach/tournaments.php`**
   - Add "Recommend Players" button to each tournament card
   - Add recommendation count badge
   - Integrate recommendation modal

2. **New View: `app/views/coach/tournament-recommendations.php`**
   - Recommendations management page
   - Filter tabs (pending, approved, rejected)
   - Recommendation list with actions
   - Details modal

3. **New CSS: `public/css/coach/tournament-recommendations.css`**
   - Styling for recommendations page
   - Modal styles
   - Player selection card styles
   - Status badge styles

4. **New JS: `public/js/coach/tournament-recommendations.js`**
   - Modal open/close handlers
   - Player multi-select functionality
   - Form validation
   - AJAX POST/PUT/DELETE for recommendations
   - Filter/sort logic
   - Real-time UI updates

### Phase 3: UX Enhancements
**Optional Additions:**

1. **Dashboard Widget**
   - Add to `app/views/coach/dashboard.php`
   - Show pending recommendations count
   - Quick link to recommendations page

2. **Notifications**
   - Add notification when recommendation is approved/rejected
   - Notify admin when new recommendation submitted

3. **Player Stats Panel**
   - Show player's recent performance
   - Health status indicator
   - Tournament history

---

## 🎨 UI Mockup Structure

### Tournament Card (Enhanced)
```
┌─────────────────────────────────────────┐
│ Tournament Name                 [Status] │
│ 📅 Date  📍 Location  🏷️ Type          │
│ Description...                          │
│                                         │
│ Players: 15/20 registered              │
│ [Recommend Players Button]              │
│ Existing: 3 recommendations             │
└─────────────────────────────────────────┘
```

### Recommendation Modal
```
┌──────────────────────────────────────────┐
│ Recommend Players - Tournament Name   [X] │
├──────────────────────────────────────────┤
│ Select Players:                          │
│ ☐ Player 1 (Right-hand bat)  [Stats]   │
│ ☐ Player 2 (Fast bowler)     [Stats]   │
│ ☐ Player 3 (All-rounder)     [Stats]   │
│ [Select All] [Clear]                    │
│                                          │
│ Selected: 2 players                      │
│                                          │
│ For each selected player:                │
│ Role: [Batsman/Bowler/All-rounder]     │
│ Reason: [Text field]                    │
│ Comments: [Text area]                   │
│                                          │
│ [Cancel] [Save Recommendations]         │
└──────────────────────────────────────────┘
```

### Recommendations Management Page
```
┌──────────────────────────────────────────┐
│ Tournament Recommendations               │
├──────────────────────────────────────────┤
│ Filters: [All] [Pending] [Approved]     │
│ [Rejected] [Confirmed]                   │
│ Sort: [Newest] | Search: [________]     │
├──────────────────────────────────────────┤
│ Tournament: Elite League                 │
│ Player: Arjun Silva                      │
│ Role: All-rounder                        │
│ Status: [Pending]                        │
│ [Edit] [Delete] [View Details]          │
│                                          │
│ Tournament: Junior Championship          │
│ Player: Nithya Patel                     │
│ Role: Wicket-keeper                      │
│ Status: [Approved] ✓                     │
│ [View Details]                           │
└──────────────────────────────────────────┘
```

---

## 🔌 API Endpoints

### Tournament Recommendations Endpoints
```
GET    /coach/tournament-recommendations
       → List all recommendations for coach

POST   /coach/recommend-players
       { tournamentId, players: [{playerId, role, reason}] }
       → Save multiple recommendations

PUT    /coach/recommendation/{id}
       { role, reason, comments }
       → Update pending recommendation

DELETE /coach/recommendation/{id}
       → Delete pending recommendation (cascade)

GET    /coach/tournament/{id}/recommendations
       → Get recommendations for specific tournament

GET    /coach/players?coachId=X
       → List coach's assigned players with stats

GET    /coach/tournament/{id}/details
       → Tournament details + existing recommendations
```

---

## ✅ Acceptance Criteria

### Must-Have
- [ ] Coach can see upcoming tournaments on tournaments page
- [ ] Coach can click "Recommend Players" button
- [ ] Modal shows multi-select of coach's assigned players
- [ ] Coach can add role and reason for each player
- [ ] Recommendations save to database with "pending" status
- [ ] Coach can view all their recommendations on dedicated page
- [ ] Coach can edit/delete pending recommendations
- [ ] Filter recommendations by status, tournament, player
- [ ] Admin/Manager can approve/reject with feedback
- [ ] Player stats and health status visible during selection

### Nice-to-Have
- [ ] Dashboard widget showing pending count
- [ ] Notification when recommendation approved/rejected
- [ ] Tournament participation history for players
- [ ] Bulk actions (approve all, reject all)
- [ ] Export recommendations to CSV
- [ ] Recommendation templates/suggestions
- [ ] Team composition analysis (suggest balanced team)

---

## 📊 Database Query Examples

### Get Coach's Assigned Players with Stats
```sql
SELECT 
    pp.PlayerID,
    u.Name,
    pp.BattingStyle,
    pp.BowlingStyle,
    pca.AssignmentType,
    (SELECT COUNT(*) FROM `playertournamentstats` WHERE `PlayerID` = pp.`PlayerID`) as TournamentCount
FROM `playerprofile` pp
JOIN `user` u ON pp.`UserID` = u.`UserID`
LEFT JOIN `playercoachassignment` pca ON pp.`PlayerID` = pca.`PlayerID`
WHERE pca.`CoachID` = ? 
  AND u.`Status` = 'active'
ORDER BY u.`Name`;
```

### Get Tournament with Recommendation Count
```sql
SELECT 
    t.`TournamentID`,
    t.`Name`,
    t.`StartDate`,
    t.`Location`,
    t.`Status`,
    COUNT(ctr.`RecommendationID`) as RecommendationCount,
    (SELECT COUNT(*) FROM `tournamentplayer` WHERE `TournamentID` = t.`TournamentID`) as RegisteredPlayers
FROM `tournament` t
LEFT JOIN `coach_tournament_recommendations` ctr ON t.`TournamentID` = ctr.`TournamentID`
WHERE t.`Status` IN ('upcoming', 'registration_open')
GROUP BY t.`TournamentID`
ORDER BY t.`StartDate` ASC;
```

### Get Recommendations for Specific Tournament by Coach
```sql
SELECT 
    ctr.`RecommendationID`,
    ctr.`CoachID`,
    u_coach.`Name` as CoachName,
    ctr.`PlayerID`,
    u_player.`Name` as PlayerName,
    pp.`BattingStyle`,
    pp.`BowlingStyle`,
    ctr.`RecommendedRole`,
    ctr.`Reason`,
    ctr.`Status`,
    ctr.`DateRecommended`,
    u_reviewer.`Name` as ReviewedByName,
    ctr.`DateReviewed`
FROM `coach_tournament_recommendations` ctr
JOIN `user` u_coach ON ctr.`CoachID` = u_coach.`UserID`
JOIN `user` u_player ON ctr.`PlayerID` = u_player.`UserID` -- through playerprofile
JOIN `playerprofile` pp ON ctr.`PlayerID` = pp.`PlayerID`
LEFT JOIN `user` u_reviewer ON ctr.`ReviewedBy` = u_reviewer.`UserID`
WHERE ctr.`TournamentID` = ? AND ctr.`CoachID` = ?
ORDER BY ctr.`DateRecommended` DESC;
```

---

## 🔒 Security Considerations

1. **Authorization**
   - Verify coach owns/is assigned to the player they're recommending
   - Prevent coaches from recommending players from other coaches
   - Verify tournament exists and is open for recommendations

2. **Validation**
   - Validate role field against allowed values
   - Check player is active and assigned to coach
   - Prevent duplicate recommendations (unique constraint)

3. **CSRF Protection**
   - Include CSRF token in POST/PUT/DELETE requests
   - Validate token on backend

4. **Audit Trail**
   - Log who made recommendations
   - Log who approved/rejected
   - Store timestamps

---

## 📈 Future Enhancements

1. **Analytics Dashboard**
   - Success rate of recommendations (% approved)
   - Player selection patterns
   - Tournament participation trends

2. **AI/ML Integration**
   - Suggest players based on performance metrics
   - Predict team composition

3. **Collaboration**
   - Allow co-coaches to make joint recommendations
   - Comments/discussion on recommendations

4. **Integration with Tournament Management**
   - Auto-populate team rosters from approved recommendations
   - Conflict resolution if multiple coaches recommend same player

5. **Mobile App**
   - Recommend players on-the-go
   - Push notifications for approvals

---

## 📅 Development Timeline (Estimated)

| Phase | Task | Hours | Status |
|-------|------|-------|--------|
| 1 | DB schema & migration | 2 | Not Started |
| 1 | Create M_TournamentRecommendation model | 3 | Not Started |
| 1 | Extend Coach controller with API | 4 | Not Started |
| 2 | Update tournaments.php view | 2 | Not Started |
| 2 | Create tournament-recommendations.php view | 3 | Not Started |
| 2 | Create CSS for recommendations | 2 | Not Started |
| 2 | Create JS for interactivity | 4 | Not Started |
| 3 | Testing & bug fixes | 3 | Not Started |
| 3 | Dashboard widget & notifications | 2 | Not Started |
| **Total** | | **~26 hours** | |

---

## 🚀 Next Steps

1. **Review this plan** with stakeholders
2. **Approve database schema** and finalize table structure
3. **Start Phase 1** with database migration and model creation
4. **Iterate and test** as each component is built
5. **Deploy and gather feedback**

---

## 📚 Related Files & Schema References

### Configuration
- Database: `cricket_academy` (as per `app/config/config.php`)
- Schema Source: `cricket_academy (9).sql` (April 1, 2026)

### Controllers & Models
- Coach Controller: `app/controllers/Coach.php`
- Tournaments View: `app/views/coach/tournaments.php`
- Event Model: `app/models/Event.php`
- Performance Model: `app/models/M_Performance.php`
- Player Profile Model: `app/models/PlayerProfile.php`

### UI Assets
- Dashboard CSS: `public/css/coach-dashboard.css`
- Tournaments JS: `public/js/coach/tournaments.js`
- Theme Assets: `public/css/`, `public/js/`

### Database Schema References
- Main Tables: `user`, `playerprofile`, `coachprofile`, `tournament`, `tournamentplayer`
- Assignment Table: `playercoachassignment` (tracks coach-player relationships)
- Performance Table: `playertournamentstats` (tournament statistics)

---

**Document Created:** April 7, 2026  
**Version:** 1.0  
**Status:** Ready for Review
