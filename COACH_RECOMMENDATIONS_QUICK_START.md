# Coach Tournament Recommendations - Quick Start Guide

## ✅ Database Setup (Phase 1)

### Step 1: Create the Table
```bash
mysql -u root -p cricket_academy < add_coach_tournament_recommendations.sql
```

### Step 2: Verify Table Created
```sql
-- In MySQL/MariaDB console:
USE cricket_academy;
DESCRIBE coach_tournament_recommendations;
SHOW INDEXES FROM coach_tournament_recommendations;
```

---

## 📦 Files to Create/Modify (By Phase)

### Phase 1: Backend (Models + API)

**NEW FILES:**
- `app/models/M_CoachTournamentRecommendation.php` — Database operations model

**MODIFY:**
- `app/controllers/Coach.php` — Add recommendation endpoints

### Phase 2: Frontend (Views + Interactivity)

**MODIFY:**
- `app/views/coach/tournaments.php` — Add recommendation modal and section

**NEW FILES:**
- `app/views/coach/tournament-recommendations.php` — Recommendations management page
- `public/css/coach/tournament-recommendations.css` — Styling
- `public/js/coach/tournament-recommendations.js` — Client-side logic

### Phase 3: Enhancements

**MODIFY:**
- `app/views/coach/dashboard.php` — Add recommendations widget

---

## 🔄 Implementation Workflow

### Step 1: Database (15 minutes)
```bash
# Apply SQL migration
mysql -u root -p cricket_academy < add_coach_tournament_recommendations.sql

# Verify it worked
mysql -u root -p cricket_academy -e "DESCRIBE coach_tournament_recommendations;"
```

### Step 2: Create Model (60 minutes)
Create `app/models/M_CoachTournamentRecommendation.php` with:
```php
public function addRecommendation($coachId, $tournamentId, $playerId, $data)
public function getRecommendationsByCoach($coachId)
public function getRecommendationsByTournament($tournamentId)
public function updateRecommendation($recommendationId, $data)
public function deleteRecommendation($recommendationId)
public function approveRecommendation($recommendationId, $feedback)
public function rejectRecommendation($recommendationId, $feedback)
public function checkDuplicateRecommendation($tournamentId, $playerId, $coachId)
public function getCoachAssignedPlayers($coachId)
```

### Step 3: Extend Controller (90 minutes)
Add to `app/controllers/Coach.php`:
```php
public function tournament_recommendations()
public function recommend_players($tournamentId)
public function save_recommendation()
public function update_recommendation($id)
public function delete_recommendation($id)
public function get_tournament_details($id)
public function get_assigned_players()
```

### Step 4: Create Views (120 minutes)
- `tournaments.php` enhancement: Add recommendation modal
- `tournament-recommendations.php`: Full management page with filters, list, details

### Step 5: Add Interactivity (120 minutes)
- Modal open/close handlers
- Player multi-select with checkboxes
- Form validation
- AJAX POST/PUT/DELETE calls
- Filter and sort logic

### Step 6: Styling (60 minutes)
- Modal styling
- Card layouts for recommendations
- Status badge colors
- Responsive design

---

## 📋 Entity Relationship Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                    DATABASE SCHEMA                         │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  user (UserID) ──────┬──→ coachprofile (CoachID=UserID)   │
│         ↑            │                                      │
│         │            └──→ playerprofile (UserID)           │
│         │                     ↑                            │
│    CoachID                 PlayerID                        │
│         │                     │                            │
│         │              playercoachassignment              │
│         │                (tracks assignments)              │
│         │                                                  │
│  coach_tournament_recommendations                          │
│  ├─ CoachID → user (Coach making recommendation)          │
│  ├─ PlayerID → playerprofile (Player being recommended)   │
│  ├─ TournamentID → tournament (Target tournament)         │
│  ├─ ReviewedBy → user (Admin who approved/rejected)      │
│  └─ Status (pending/approved/rejected/confirmed)         │
│                                                            │
│  tournament (TournamentID)                                │
│  └─ TournamentPlayer (for final confirmed selections)    │
│                                                            │
└─────────────────────────────────────────────────────────────┘
```

---

## 🧪 Testing Checklist

### Database Tests
- [ ] Table created successfully
- [ ] All foreign keys working
- [ ] Unique constraint prevents duplicates
- [ ] Indexes created

### Model Tests
- [ ] `addRecommendation()` inserts with correct data
- [ ] `getRecommendationsByCoach()` returns coach's recommendations
- [ ] `approveRecommendation()` updates status and timestamp
- [ ] `deleteRecommendation()` removes record

### Controller Tests
- [ ] `/coach/tournament-recommendations` loads page
- [ ] `POST /coach/save-recommendation` validates coach ownership
- [ ] `PUT /coach/update-recommendation/{id}` updates correctly
- [ ] `DELETE /coach/delete-recommendation/{id}` removes record
- [ ] CSRF token validation

### UI Tests
- [ ] Modal opens/closes correctly
- [ ] Player selection works (check/uncheck)
- [ ] Form validation shows errors
- [ ] AJAX calls succeed and UI updates
- [ ] Recommendations display with correct status colors
- [ ] Filter/sort works

---

## 🔐 Authorization Rules

| Operation | Who | Requirement |
|-----------|-----|------------|
| Create recommendation | Coach | Must own the player (via playercoachassignment) |
| View own recommendations | Coach | Can see their recommendations |
| View all recommendations | Admin | Can see all tournament recommendations |
| Edit recommendation | Coach | Only if status = 'pending' AND coach is creator |
| Delete recommendation | Coach | Only if status = 'pending' AND coach is creator |
| Approve/Reject | Admin | Only admin role can review |

---

## 📝 SQL Queries for Manual Testing

```sql
-- See all recommendations for a tournament
SELECT * FROM coach_tournament_recommendations 
WHERE TournamentID = 1 
ORDER BY DateRecommended DESC;

-- See pending recommendations
SELECT * FROM coach_tournament_recommendations 
WHERE Status = 'pending' 
ORDER BY DateRecommended DESC;

-- See recommendations by coach
SELECT * FROM coach_tournament_recommendations 
WHERE CoachID = 5 
ORDER BY DateRecommended DESC;

-- Count recommendations by status
SELECT Status, COUNT(*) as Count 
FROM coach_tournament_recommendations 
GROUP BY Status;

-- See duplicate attempts (should be prevented by constraint)
SELECT TournamentID, PlayerID, CoachID, COUNT(*) 
FROM coach_tournament_recommendations 
GROUP BY TournamentID, PlayerID, CoachID 
HAVING COUNT(*) > 1;
```

---

## 🚀 Deployment Checklist

- [ ] SQL migration applied to production database
- [ ] Model M_CoachTournamentRecommendation.php created
- [ ] Coach controller updated with new methods
- [ ] Views updated with recommendation UI
- [ ] JavaScript loaded and tested
- [ ] CSS deployed and responsive
- [ ] All authorization checks in place
- [ ] CSRF tokens implemented
- [ ] Logging/audit trail working
- [ ] Error handling tested
- [ ] Edge cases handled (closed tournaments, deleted players, etc.)
- [ ] Performance verified (indexes working)

---

## 💡 Key Implementation Notes

1. **Duplicate Prevention**: Unique constraint on (TournamentID, PlayerID, CoachID) prevents same coach from recommending same player twice for same tournament

2. **Cascade Delete**: If tournament is deleted, recommendations are deleted. If coach/player deleted, recommendations are deleted.

3. **Status Workflow**: 
   - `pending` → Coach created, awaiting review
   - `approved` → Admin approved, ready to add to tournamentplayer
   - `rejected` → Admin rejected with feedback
   - `confirmed` → Added to tournamentplayer table

4. **Audit Trail**: DateRecommended and DateReviewed track workflow progression

5. **Player Filter**: Use `playercoachassignment` table to get only players assigned to coach (security)

6. **Tournament Filter**: Only show upcoming/registration_open tournaments (status check in controller)

---

## 📞 Support & References

- **Plan Document**: `COACH_TOURNAMENT_RECOMMENDATIONS_PLAN.md`
- **Database Schema**: `cricket_academy (9).sql`
- **Config**: `app/config/config.php` (database name: cricket_academy)
- **Coach Dashboard**: `app/views/coach/dashboard.php`
- **Existing Tournament System**: `app/views/coach/tournaments.php`

---

**Created:** April 7, 2026  
**Status:** Ready for Implementation  
**Next Step:** Proceed with Phase 1 Database Setup
