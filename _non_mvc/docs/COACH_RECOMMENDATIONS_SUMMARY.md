# Coach Tournament Recommendations - 1-Page Summary

**Feature:** Coaches recommend players for tournaments  
**Database:** `cricket_academy (9)`  
**Status:** ✅ Plan Complete  
**Est. Effort:** 26 hours

---

## 🎯 What This Feature Does

Coaches can:
1. View upcoming tournaments
2. Recommend their assigned players for specific tournaments
3. Provide role and reasoning for each recommendation
4. See all their recommendations with status (pending/approved/rejected)
5. Edit or withdraw pending recommendations

Admins can:
1. Review all recommendations
2. Approve with confirmation
3. Reject with feedback

---

## 📦 Deliverables Created

| File | Purpose | Status |
|------|---------|--------|
| `COACH_TOURNAMENT_RECOMMENDATIONS_PLAN.md` | Full specification (2000+ words) | ✅ Ready |
| `add_coach_tournament_recommendations.sql` | Database migration | ✅ Ready |
| `COACH_RECOMMENDATIONS_QUICK_START.md` | Implementation guide | ✅ Ready |
| `COACH_RECOMMENDATIONS_PLANNING_COMPLETE.md` | This summary | ✅ Ready |

---

## 🗄️ Database Table

**Table:** `coach_tournament_recommendations`

```
Columns:
- RecommendationID (PK)
- CoachID (FK → user)
- TournamentID (FK → tournament)
- PlayerID (FK → playerprofile)
- Status: pending | approved | rejected | confirmed
- RecommendedRole: batsman | bowler | all-rounder | wicket-keeper
- Reason, Comments, AdminFeedback, ReviewedBy, DateRecommended, DateReviewed

Constraints:
- UNIQUE (TournamentID, PlayerID, CoachID) - No duplicates
- Foreign keys: user, tournament, playerprofile

Indexes: 5 performance indexes for fast queries
```

---

## 📋 Implementation Phases (26 hours)

| Phase | Task | Files | Hours |
|-------|------|-------|-------|
| 1 | Database table | `add_coach_tournament_recommendations.sql` | 0.25 |
| 2 | Model | Create `M_CoachTournamentRecommendation.php` | 1 |
| 3 | Controller | Extend `Coach.php` with 7 new methods | 1.5 |
| 4 | Views | Update `tournaments.php` + new page | 2 |
| 5 | CSS + JS | Styling and interactivity | 2.5 |
| Testing | Full E2E testing | Various | 2 |
| **TOTAL** | | | **~10.25 hours** |

---

## 🔐 Authorization Matrix

```
Create Recommendation   → Coach (must own player)
View Own                → Coach
View All                → Admin
Edit (pending only)     → Coach + creator
Delete (pending only)   → Coach + creator
Approve/Reject          → Admin only
```

---

## 📱 UI Components

### Tournament Card (Enhanced)
```
┌─────────────────────────────┐
│ Tournament Name      [Badge] │
│ 📅 Date  📍 Location         │
│ Players: X/Y                 │
│                              │
│ [Recommend Players Button]   │
└─────────────────────────────┘
```

### Recommendation Modal
```
Select Players: [Dropdown/Checkboxes]
Player: [Select]
Role: [batsman|bowler|all-rounder|wicket-keeper]
Reason: [Textarea]
[Cancel] [Recommend]
```

### Management Page
```
Filters: [Status] [Tournament] [Player]
Sort: [Newest|Oldest]

Results:
Tournament | Player | Role | Status | Actions
─────────────────────────────────────────────
```

---

## 🚀 How to Start

### Option 1: Full Implementation (26 hours)
```bash
# 1. Apply database migration
mysql -u root -p cricket_academy < add_coach_tournament_recommendations.sql

# 2. Create model M_CoachTournamentRecommendation.php
# 3. Extend Coach.php controller
# 4. Update tournaments.php view
# 5. Add CSS and JavaScript
# 6. Test and deploy
```

### Option 2: MVP Only (Phase 1-3, ~3 hours)
- Just database + model + basic API
- Manual testing with SQL
- Polish UI later

---

## 📊 Key Queries

```sql
-- Get coach's recommendations
SELECT * FROM coach_tournament_recommendations 
WHERE CoachID = ?;

-- Get pending recommendations for tournament
SELECT * FROM coach_tournament_recommendations 
WHERE TournamentID = ? AND Status = 'pending';

-- Approve recommendation
UPDATE coach_tournament_recommendations 
SET Status = 'approved', ReviewedBy = ?, DateReviewed = NOW() 
WHERE RecommendationID = ?;
```

---

## ✅ Validation Checklist

- ✅ Uses correct database `cricket_academy`
- ✅ Foreign keys reference correct tables (`user`, `tournament`, `playerprofile`)
- ✅ Prevents duplicate recommendations (unique constraint)
- ✅ Tracks recommendation workflow (status enum)
- ✅ Maintains audit trail (created/reviewed timestamps)
- ✅ Proper authorization rules defined
- ✅ Security considerations documented (CSRF, validation)
- ✅ SQL migration ready to execute
- ✅ Implementation sequence clear
- ✅ Testing approach documented

---

## 📞 Next Step

**Ready to implement?** 

Reply with:
- `START PHASE 1` → Begin with database setup
- `START PHASE 2` → Begin with model creation (assuming DB exists)
- `REVIEW PLAN` → Want to discuss anything first?
- `SKIP THIS` → Work on something else

---

**Created:** April 7, 2026  
**Status:** Plan Approved & Ready  
**Contact:** See COACH_TOURNAMENT_RECOMMENDATIONS_PLAN.md for full details
