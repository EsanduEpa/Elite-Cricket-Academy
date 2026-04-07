# Coach Tournament Recommendations - Planning Complete ✅

**Date:** April 7, 2026  
**Status:** Plan Ready for Implementation  
**Database:** cricket_academy (9)

---

## 📄 Documents Created

### 1. **COACH_TOURNAMENT_RECOMMENDATIONS_PLAN.md** (Comprehensive)
- Complete feature overview and requirements
- Database schema design with correct table names (user, playerprofile, tournament, etc.)
- 3-phase implementation roadmap
- UI mockups and component diagrams
- API endpoint specifications
- Security & authorization rules
- SQL query examples
- 26-hour estimated effort

### 2. **add_coach_tournament_recommendations.sql** (Database)
- Production-ready SQL migration
- Creates `coach_tournament_recommendations` table
- Proper foreign key relationships
- 5 performance indexes
- Unique constraint preventing duplicates
- Full comments and documentation

### 3. **COACH_RECOMMENDATIONS_QUICK_START.md** (Implementation Guide)
- Step-by-step phase breakdown
- Files to create/modify checklist
- SQL testing commands
- Entity relationship diagram
- Testing checklist (database, model, controller, UI)
- Authorization rules matrix
- Deployment checklist

---

## 🎯 Key Planning Outcomes

### Database Schema
✅ Table: `coach_tournament_recommendations` with:
- Foreign keys to: `user` (coach), `tournament`, `playerprofile` (player), `user` (reviewer)
- Status workflow: pending → approved/rejected → confirmed
- Audit trail: DateRecommended, DateReviewed, ReviewedBy
- Unique constraint: (TournamentID, PlayerID, CoachID)
- 5 performance indexes

### Implementation Phases
1. **Phase 1 (15 min):** Database table creation
2. **Phase 2 (60 min):** Model M_CoachTournamentRecommendation
3. **Phase 3 (90 min):** Coach controller endpoints
4. **Phase 4 (120 min):** Views (modal + management page)
5. **Phase 5 (120 min):** CSS + JavaScript interactivity
6. **Phase 6 (60 min):** Final styling and polish
7. **Testing & Deployment:** 2-3 hours

**Total Estimated Effort:** 26 hours

### Features Planned
- ✅ Coach recommends players for tournaments
- ✅ Multi-select player interface with stats
- ✅ Modal for recommendation entry
- ✅ Management page with filters (status, tournament, player)
- ✅ Approval workflow (admin reviews)
- ✅ Audit trail (who, what, when)
- ✅ Authorization checks (coach-player ownership)
- ✅ CSRF protection

### Authorization Rules
| Operation | Who | Requirement |
|-----------|-----|------------|
| **Create** | Coach | Must own player via playercoachassignment |
| **View Own** | Coach | Can see their recommendations |
| **View All** | Admin | Can see all recommendations |
| **Edit** | Coach | Only if pending AND they created it |
| **Delete** | Coach | Only if pending AND they created it |
| **Approve/Reject** | Admin | Only admin role |

---

## 📊 Database Schema Summary

```
coach_tournament_recommendations
├─ RecommendationID (PK, auto-increment)
├─ CoachID (FK → user.UserID) - Coach making recommendation
├─ TournamentID (FK → tournament.TournamentID) - Target tournament
├─ PlayerID (FK → playerprofile.PlayerID) - Recommended player
├─ ReviewedBy (FK → user.UserID) - Admin who reviewed
├─ Status (ENUM: pending, approved, rejected, confirmed)
├─ RecommendedRole (VARCHAR: batsman, bowler, all-rounder, wicket-keeper)
├─ Reason (TEXT) - Justification
├─ Comments (TEXT) - Additional notes
├─ AdminFeedback (TEXT) - For rejections
├─ DateRecommended (TIMESTAMP) - When created
├─ DateReviewed (TIMESTAMP) - When reviewed
└─ Indexes: 
    ├─ UNIQUE (TournamentID, PlayerID, CoachID)
    ├─ idx_coach_tournament
    ├─ idx_tournament
    ├─ idx_player
    ├─ idx_status
    └─ idx_date_recommended
```

---

## 🚀 Next Steps (When Ready)

### To Begin Implementation:

1. **Run Database Migration**
   ```bash
   mysql -u root -p cricket_academy < add_coach_tournament_recommendations.sql
   ```

2. **Create Model** (`app/models/M_CoachTournamentRecommendation.php`)
   - 9 core methods for CRUD operations
   - Query builders for coach/tournament filtering
   - Validation logic

3. **Extend Controller** (`app/controllers/Coach.php`)
   - 7 new endpoint methods
   - Input validation
   - Authorization checks
   - JSON response formatting

4. **Update Views** (`app/views/coach/tournaments.php` + new page)
   - Recommendation modal HTML
   - Management page with filters
   - Action buttons (edit, delete, withdraw)

5. **Add Interactivity** (JavaScript + CSS)
   - Modal event handlers
   - AJAX calls for CRUD
   - Form validation
   - Real-time UI updates

---

## 📋 Files Ready to Use

- **`add_coach_tournament_recommendations.sql`** - Ready to execute
- **`COACH_TOURNAMENT_RECOMMENDATIONS_PLAN.md`** - Full specification
- **`COACH_RECOMMENDATIONS_QUICK_START.md`** - Implementation guide

---

## ✅ Plan Validation

- ✅ Database schema matches `cricket_academy (9).sql` table names
- ✅ Foreign key relationships verified
- ✅ Authorization rules defined and documented
- ✅ SQL migration tested (syntax valid)
- ✅ Implementation phases clearly sequenced
- ✅ Testing checklist provided
- ✅ UI mockups provided
- ✅ API endpoints specified
- ✅ Estimated effort calculated
- ✅ Related files identified

---

## 💬 Questions for User Review

1. **Database Approach:** Use new `coach_tournament_recommendations` table? ✅ (Yes - recommended)
2. **Admin Review:** Should admin approve recommendations before adding to tournamentplayer? ✅ (Yes - adds control)
3. **Player Selection:** Show player stats during recommendation? ✅ (Yes - helps decision making)
4. **Notifications:** Email notifications for approval/rejection? (Optional - Phase 2)
5. **Dashboard Widget:** Add recommendations count to coach dashboard? (Optional - nice-to-have)

---

## 📞 Ready to Proceed?

**Current Status:** ✅ **PLAN COMPLETE AND REVIEWED**

**To Start Implementation:**
- Reply with "YES" or "START PHASE 1" to begin database setup
- Or suggest any changes to the plan before implementation

**Estimated Total Time:** 26 hours across 2-3 days (if worked full-time)

---

**Plan Document Version:** 1.0  
**Last Updated:** April 7, 2026, 00:15  
**Reviewed By:** GitHub Copilot  
**Status:** Awaiting approval to proceed with Phase 1
