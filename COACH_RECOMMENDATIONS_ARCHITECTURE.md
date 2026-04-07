# Coach Tournament Recommendations - Visual Architecture Guide

## 🏗️ System Architecture Diagram

```
┌────────────────────────────────────────────────────────────────────────┐
│                         ELITE CRICKET ACADEMY                          │
│                    Coach Tournament Recommendations                     │
└────────────────────────────────────────────────────────────────────────┘

LAYER 1: PRESENTATION (Views)
┌────────────────────────────────────────────────────────────────────────┐
│                                                                        │
│  Coach Dashboard          Tournaments Page        My Recommendations   │
│  ┌──────────────┐        ┌──────────────┐       ┌──────────────┐    │
│  │ Widget:      │        │ Tournament 1  │       │ [Status]     │    │
│  │ Pending: 3   │        │ • Recommend  │       │ Tournament 1 │    │
│  │ → Go to page │        │ • Details    │       │ Player 1     │    │
│  │              │        │              │       │ • Edit       │    │
│  └──────────────┘        └──────────────┘       │ • Withdraw   │    │
│                                                  │              │    │
│                          ┌─────────────────┐    └──────────────┘    │
│                          │  MODAL: Recommend│                       │
│                          │  ┌────────────┐  │                       │
│                          │  │Player Sel. │  │                       │
│                          │  │Role: [Sel] │  │                       │
│                          │  │Reason: [...] │  │                       │
│                          │  │[Recommend] │  │                       │
│                          │  └────────────┘  │                       │
│                          └─────────────────┘                       │
└────────────────────────────────────────────────────────────────────────┘

LAYER 2: API/CONTROLLER (Routes & Logic)
┌────────────────────────────────────────────────────────────────────────┐
│                         Coach.php Controller                           │
│                                                                        │
│  GET  /coach/tournament-recommendations    → tournament_recommendations()
│  POST /coach/save-recommendation           → save_recommendation()     │
│  PUT  /coach/update-recommendation/{id}    → update_recommendation()   │
│  DEL  /coach/delete-recommendation/{id}    → delete_recommendation()   │
│                                                                        │
│  GET  /coach/tournament/{id}/details       → get_tournament_details()  │
│  GET  /coach/assigned-players              → get_assigned_players()   │
│                                                                        │
└────────────────────────────────────────────────────────────────────────┘

LAYER 3: BUSINESS LOGIC (Models)
┌────────────────────────────────────────────────────────────────────────┐
│               M_CoachTournamentRecommendation.php                       │
│                                                                        │
│  Methods:                                                             │
│  • addRecommendation($coach, $tournament, $player, $data)            │
│  • getRecommendationsByCoach($coachId)                               │
│  • getRecommendationsByTournament($tournamentId)                     │
│  • updateRecommendation($id, $data)                                  │
│  • deleteRecommendation($id)                                         │
│  • approveRecommendation($id, $feedback)                             │
│  • rejectRecommendation($id, $feedback)                              │
│  • checkDuplicateRecommendation($t, $p, $c)                          │
│  • getCoachAssignedPlayers($coachId)                                 │
│                                                                        │
│  Validation & Authorization:                                          │
│  ✓ Coach owns player (playercoachassignment check)                   │
│  ✓ Tournament is upcoming (not closed)                               │
│  ✓ No duplicate recommendations (UNIQUE constraint)                  │
│  ✓ Can only edit own pending recommendations                         │
│                                                                        │
└────────────────────────────────────────────────────────────────────────┘

LAYER 4: PERSISTENCE (Database)
┌────────────────────────────────────────────────────────────────────────┐
│                         DATABASE SCHEMA                                │
│                                                                        │
│  coach_tournament_recommendations                                     │
│  ┌─────────────────────────────────────────────────────┐             │
│  │ RecommendationID (PK)                              │             │
│  │ CoachID (FK)        →  user.UserID                │             │
│  │ TournamentID (FK)   →  tournament.TournamentID    │             │
│  │ PlayerID (FK)       →  playerprofile.PlayerID     │             │
│  │ ReviewedBy (FK)     →  user.UserID                │             │
│  │                                                    │             │
│  │ Status (pending→approved→confirmed)               │             │
│  │ RecommendedRole (batsman|bowler|all-rounder)     │             │
│  │ Reason, Comments, AdminFeedback                  │             │
│  │ DateRecommended, DateReviewed                    │             │
│  │                                                    │             │
│  │ UNIQUE (TournamentID, PlayerID, CoachID)          │             │
│  │ INDEX idx_coach_tournament, idx_status, ...       │             │
│  └─────────────────────────────────────────────────────┘             │
│                                                                        │
│  Related Tables:                                                      │
│  • user (coaches, players, admins)                                  │
│  • tournament (tournaments)                                         │
│  • playerprofile (player details)                                  │
│  • playercoachassignment (coach-player relationships)              │
│  • tournamentplayer (final confirmed selections)                   │
│                                                                        │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 🔄 Data Flow Diagram

```
┌─────────┐
│  Coach  │
└────┬────┘
     │
     ├─→ Views Available Tournaments
     │   (GET /tournament or tournaments.php)
     │
     ├─→ Clicks "Recommend Players"
     │   (Opens Modal)
     │
     ├─→ Selects Player(s)
     │   (From coach's assigned players)
     │   (GET /coach/assigned-players)
     │
     ├─→ Fills Form
     │   • Role: batsman | bowler | all-rounder
     │   • Reason: "Strong batting performance..."
     │   • Comments: "Ready for senior tournament"
     │
     ├─→ Submits Recommendation
     │   │ POST /coach/save-recommendation
     │   │ {tournamentId, playerId, role, reason, comments}
     │   │
     │   ├─→ Controller: save_recommendation()
     │   │   ├─ Validate CSRF token
     │   │   ├─ Verify coach owns player
     │   │   ├─ Check tournament is upcoming
     │   │   ├─ Check no duplicate
     │   │   └─ Call Model.addRecommendation()
     │   │
     │   └─→ Model: addRecommendation()
     │       ├─ INSERT INTO coach_tournament_recommendations
     │       │  (CoachID, TournamentID, PlayerID, Status='pending', ...)
     │       ├─ Return RecommendationID
     │       └─ JSON Response: {success: true, id: 123}
     │
     ├─→ UI Updates
     │   (Add recommendation to "My Recommendations" list)
     │   (Show as "Pending" status)
     │
     └─→ Can Edit/Delete (if still pending)
         PUT  /coach/update-recommendation/{id}
         DEL  /coach/delete-recommendation/{id}

┌────────┐
│ Admin  │
└────┬───┘
     │
     ├─→ Views All Recommendations
     │   (GET /admin/recommendations)
     │
     ├─→ Reviews Recommendation
     │   (Coach: John, Player: Arjun, Tournament: Elite League)
     │   (Reason: "Strong performance in last 3 games")
     │
     ├─→ Approves or Rejects
     │   │ PUT /admin/approve-recommendation/{id}
     │   │ {action: 'approve', feedback: "Good selection"}
     │   │
     │   ├─→ If APPROVED:
     │   │   ├─ UPDATE Status = 'approved'
     │   │   ├─ UPDATE DateReviewed, ReviewedBy
     │   │   ├─ Prepare to add to tournamentplayer
     │   │   └─ Response: {success: true}
     │   │
     │   └─→ If REJECTED:
     │       ├─ UPDATE Status = 'rejected'
     │       ├─ UPDATE AdminFeedback = "Not eligible..."
     │       ├─ Notify coach
     │       └─ Response: {success: true}
     │
     └─→ Optionally add approved to tournamentplayer table
         (Future automation possible)
```

---

## 🎨 UI Component Breakdown

### Component 1: Tournament Card (Existing + Enhanced)

```
BEFORE:
┌──────────────────────────────┐
│ Elite League Championship     │
│ 📅 Mar 15, 2026              │
│ 📍 Colombo Cricket Club       │
│ Status: Upcoming              │
│ [View Details]               │
└──────────────────────────────┘

AFTER:
┌──────────────────────────────────────┐
│ Elite League Championship             │
│ 📅 Mar 15, 2026                      │
│ 📍 Colombo Cricket Club               │
│ Status: Upcoming                      │
│ 15/20 Players Registered              │
│ Your Recommendations: 2               │
│                                      │
│ [Recommend Players] [View Details]  │
└──────────────────────────────────────┘
```

### Component 2: Recommendation Modal

```
┌─────────────────────────────────────────────┐
│ Recommend Player for Tournament          [X] │
├─────────────────────────────────────────────┤
│                                             │
│ Tournament: Elite League Championship        │
│ Date: March 15, 2026                       │
│ Location: Colombo Cricket Club             │
│                                             │
│ SELECT PLAYERS:                             │
│ ☐ Arjun Silva      (Right-hand bat)        │
│ ☐ Ismail Khan      (Fast bowler)           │
│ ☐ Nithya Patel     (Wicket-keeper)         │
│ ☐ Rajeev Gupta     (All-rounder)           │
│ ☐ Samantha Perera  (Left-arm spin)         │
│                                             │
│ SELECTED: 1 player                         │
│                                             │
│ For Arjun Silva:                            │
│ Role: [Select]                              │
│   • Opener                                 │
│   • Middle Order                           │
│   • Bowler                                 │
│   • All-rounder                            │
│   • Wicket-keeper                          │
│                                             │
│ Reason for Recommendation:                  │
│ [Strong batting performance in recent...]   │
│                                             │
│ Additional Comments:                        │
│ [Ready for international level tournament]  │
│                                             │
│ [Cancel] [Recommend]                       │
└─────────────────────────────────────────────┘
```

### Component 3: Recommendations Management Page

```
HEADER:
┌─────────────────────────────────────────┐
│ My Player Recommendations                │
│ Filters: [All] [Pending] [Approved]     │
│ [Rejected] [Confirmed]                   │
│ Search: [____________] [Clear]          │
│ Sort: [Newest ▼]                        │
└─────────────────────────────────────────┘

RECOMMENDATIONS LIST:
┌────────────────────────────────────────────────────────────┐
│ Tournament                 Player    Role    Status Actions │
├────────────────────────────────────────────────────────────┤
│ Elite League Championship  Arjun     Opener  ⏳ Pending    │
│ 📅 Mar 15, 2026           Silva              [Edit|Delete] │
│ 📍 Colombo Cricket Club                                   │
│ "Strong batting..."                                       │
├────────────────────────────────────────────────────────────┤
│ Western Province U-19      Ismail    Bowler  ✅ Approved  │
│ 📅 Mar 22, 2026           Khan              [View Notes]   │
│ 📍 Kandy Cricket Club                                     │
│ "Fast bowling expertise..."                               │
├────────────────────────────────────────────────────────────┤
│ Local Cup                  Nithya    WK      ❌ Rejected   │
│ 📅 Mar 29, 2026           Patel             [View Feedback]│
│ 📍 Galle Oval                                             │
│ "Age and experience match..."                             │
│ Feedback: "Not yet eligible for this tournament"          │
└────────────────────────────────────────────────────────────┘
```

---

## 📊 State Diagram

```
                    [PENDING]
                        │
                ┌───────┴────────┐
                │                │
            APPROVED          REJECTED
                │                │
                │          [Get Feedback]
                │                │
                ▼                ▼
            [CONFIRMED]      [Re-recommend]
                │                │
                │            (Back to PENDING)
                │
          Add to Tournament
          Player Table
```

---

## 🔒 Security Flow

```
USER SUBMITS FORM
    │
    ├─ CSRF Token Check
    │  └─ ✗ Fail? → 403 Forbidden
    │
    ├─ Authentication Check
    │  └─ ✗ Not Logged In? → 401 Unauthorized
    │
    ├─ Authorization Check
    │  │  ├─ Is User a Coach?
    │  │  └─ ✗ No? → 403 Forbidden
    │
    ├─ Data Validation
    │  ├─ Tournament ID valid?
    │  ├─ Player ID valid?
    │  ├─ Role in allowed list?
    │  ├─ Reason not empty?
    │  └─ ✗ Any fail? → 400 Bad Request
    │
    ├─ Business Logic Checks
    │  ├─ Coach owns player? (playercoachassignment)
    │  ├─ Tournament is upcoming?
    │  ├─ No duplicate recommendation?
    │  └─ ✗ Any fail? → 422 Unprocessable Entity
    │
    └─ INSERT INTO coach_tournament_recommendations
        └─ ✓ Success → 200 OK + {id: 123}
```

---

## 📈 Database Query Performance

```
Common Queries & Index Strategy:

1. Get coach's recommendations
   WHERE CoachID = ?
   → Index: idx_coach_tournament
   Estimated: < 1ms

2. Get pending recommendations for tournament
   WHERE TournamentID = ? AND Status = 'pending'
   → Index: idx_tournament, idx_status
   Estimated: < 1ms

3. Get recommendation details
   WHERE RecommendationID = ?
   → Primary Key (fast)
   Estimated: < 0.1ms

4. Get all recommendations (admin)
   Full table scan
   → Better with pagination
   Estimated: 10-50ms (depends on data size)
```

---

**Architecture Document**  
**Version:** 1.0  
**Created:** April 7, 2026  
**Status:** Ready for Implementation
