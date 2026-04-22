# PHASE 3 - CONTROLLER ENDPOINTS QUICK REFERENCE

## 7 New Endpoints Added to `Coach.php`

### 1️⃣ View Recommendations Page
```
GET /coach/tournament-recommendations
```
**Returns:** View with all coach's recommendations, stats, pending count  
**Auth:** Coach  
**Response:** HTML view (coach/tournament-recommendations.php)

---

### 2️⃣ Get Form Data
```
GET /coach/recommend-players/{tournamentId}
```
**Returns:** JSON with tournament details, player list, and role options  
**Auth:** Coach  
**Response Type:** JSON  
**Example Response:**
```json
{
    "success": true,
    "tournament": { /* tournament data */ },
    "players": [ /* coach's assigned players */ ],
    "roles": ["batsman", "bowler", "all-rounder", "wicket-keeper"],
    "roleDescriptions": { /* descriptions */ }
}
```

---

### 3️⃣ Create Recommendation
```
POST /coach/save-recommendation
```
**Accepts:** JSON or form data  
**Required Fields:** tournamentId, playerId, recommendedRole  
**Optional Fields:** reason, comments  
**Auth:** Coach  
**Response Type:** JSON  

**Request Example:**
```json
{
    "tournamentId": 6,
    "playerId": 7,
    "recommendedRole": "batsman",
    "reason": "Excellent batting performance",
    "comments": "Ready for tournament"
}
```

**Response on Success:**
```json
{
    "success": true,
    "message": "Recommendation saved successfully",
    "recommendationId": 2
}
```

**Validations:**
- ✅ Prevents duplicate recommendations
- ✅ Validates tournament exists
- ✅ Validates player exists and is assigned to coach
- ✅ Validates role format

---

### 4️⃣ Update Recommendation
```
POST /PUT /coach/update-recommendation/{recommendationId}
```
**Accepts:** JSON or form data  
**Required Fields:** recommendedRole  
**Optional Fields:** reason, comments  
**Auth:** Coach (must own recommendation)  
**Response Type:** JSON  

**Request Example:**
```json
{
    "recommendedRole": "bowler",
    "reason": "Updated bowling assessment",
    "comments": "New insights from practice"
}
```

**Response on Success:**
```json
{
    "success": true,
    "message": "Recommendation updated successfully"
}
```

**Restrictions:**
- ❌ Can only edit pending (status='pending') recommendations
- ❌ Cannot edit if you don't own it (CoachID != user_id)

---

### 5️⃣ Delete Recommendation
```
DELETE /POST /coach/delete-recommendation/{recommendationId}
```
**Auth:** Coach (must own recommendation)  
**Response Type:** JSON  

**Response on Success:**
```json
{
    "success": true,
    "message": "Recommendation deleted successfully"
}
```

**Restrictions:**
- ❌ Can only delete pending recommendations
- ❌ Cannot delete if you don't own it
- ❌ Once approved/rejected by admin, cannot delete

---

### 6️⃣ Get Tournament Details
```
GET /coach/tournament/{tournamentId}/details
```
**Returns:** JSON tournament data with recommendation status  
**Auth:** Coach  
**Response Type:** JSON  

**Response Example:**
```json
{
    "success": true,
    "tournament": { /* tournament details */ },
    "status": "accepting"
}
```

**Status Values:**
- `"accepting"` - Tournament accepting recommendations
- `"in-progress"` - Tournament has started
- `"closed"` - Tournament completed or cancelled

---

### 7️⃣ Get Assigned Players
```
GET /coach/assigned-players
```
**Returns:** JSON list of all players assigned to coach  
**Auth:** Coach  
**Response Type:** JSON  

**Response Example:**
```json
{
    "success": true,
    "players": [
        {
            "PlayerID": 7,
            "Name": "Player Name",
            "Role": "Batsman",
            "Status": "active"
        }
    ],
    "count": 1
}
```

---

## 🔐 Security Features

All endpoints include:
- ✅ **Authentication:** Only coaches can access
- ✅ **Authorization:** Ownership checks for edit/delete
- ✅ **Input Validation:** All parameters validated
- ✅ **Error Handling:** Try-catch blocks, error logging
- ✅ **Status Checks:** Can't edit/delete approved/rejected

---

## 📊 Integration Points

**Models Used:**
- `M_CoachTournamentRecommendation` - Core model with 12 methods
- `Event` - Tournament details
- `M_Users` - (via Model) Player assignment

**Views Used:**
- `coach/tournament-recommendations` - Display all recommendations

**Database Table:**
- `coach_tournament_recommendations` - 13 columns, proper indexes & constraints

---

## ✨ What's Next (Phase 4)

Views needed:
1. `app/views/coach/tournament-recommendations.php` - Full page with recommendations
2. Update `app/views/coach/tournaments.php` - Add "Recommend" button + modal

Then Phase 5: CSS + JavaScript for styling and interactivity
