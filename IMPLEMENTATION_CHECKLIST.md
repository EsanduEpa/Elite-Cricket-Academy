# 🎯 Coach Tournament Recommendations - Implementation Checklist

**Project Start Date:** April 7, 2026  
**Target Completion:** April 14-18, 2026 (3-4 days, 26 hours)  
**Database:** `cricket_academy (9)`

---

## 📚 DOCUMENTATION COMPLETE ✅

Documentation files created:
- [x] `COACH_RECOMMENDATIONS_SUMMARY.md` - 1-page overview
- [x] `COACH_TOURNAMENT_RECOMMENDATIONS_PLAN.md` - Full specification (25 pages)
- [x] `COACH_RECOMMENDATIONS_QUICK_START.md` - Implementation guide (15 pages)
- [x] `COACH_RECOMMENDATIONS_PLANNING_COMPLETE.md` - Plan validation (3 pages)
- [x] `COACH_RECOMMENDATIONS_ARCHITECTURE.md` - Design diagrams (10 pages)
- [x] `add_coach_tournament_recommendations.sql` - Database migration
- [x] `COACH_RECOMMENDATIONS_INDEX.md` - Documentation index

---

## 🗄️ DATABASE PHASE (Phase 1 - 15 minutes)

### Setup
- [ ] Execute SQL migration:
  ```bash
  mysql -u root -p cricket_academy < add_coach_tournament_recommendations.sql
  ```

### Verification
- [ ] Verify table created:
  ```sql
  USE cricket_academy;
  DESCRIBE coach_tournament_recommendations;
  ```
- [ ] Verify foreign keys:
  ```sql
  SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME 
  FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
  WHERE TABLE_NAME = 'coach_tournament_recommendations';
  ```
- [ ] Verify indexes:
  ```sql
  SHOW INDEXES FROM coach_tournament_recommendations;
  ```
- [ ] Test UNIQUE constraint (should fail second time):
  ```sql
  INSERT INTO coach_tournament_recommendations 
  (CoachID, TournamentID, PlayerID, Status, RecommendedRole, Reason)
  VALUES (1, 1, 1, 'pending', 'batsman', 'Test');
  
  INSERT INTO coach_tournament_recommendations 
  (CoachID, TournamentID, PlayerID, Status, RecommendedRole, Reason)
  VALUES (1, 1, 1, 'pending', 'bowler', 'Test2');  -- Should error
  ```

---

## 🏗️ MODEL PHASE (Phase 2 - 1 hour)

### File: `app/models/M_CoachTournamentRecommendation.php`

- [ ] Create new file
- [ ] Extend base Controller class (if applicable)
- [ ] Implement method: `addRecommendation()`
  - [ ] Parameters: $coachId, $tournamentId, $playerId, $data
  - [ ] Validate all required fields
  - [ ] Check coach owns player (via playercoachassignment)
  - [ ] Check tournament is upcoming (status check)
  - [ ] Check no duplicate
  - [ ] INSERT and return RecommendationID
  - [ ] Test with: `$model->addRecommendation(1, 1, 1, ['role'=>'batsman', 'reason'=>'Test'])`

- [ ] Implement method: `getRecommendationsByCoach()`
  - [ ] Parameters: $coachId
  - [ ] SELECT all recommendations where CoachID = $coachId
  - [ ] Return array with all details
  - [ ] Test with: `$model->getRecommendationsByCoach(1)`

- [ ] Implement method: `getRecommendationsByTournament()`
  - [ ] Parameters: $tournamentId
  - [ ] SELECT all recommendations where TournamentID = $tournamentId
  - [ ] Include coach and player names
  - [ ] Return array
  - [ ] Test with: `$model->getRecommendationsByTournament(1)`

- [ ] Implement method: `updateRecommendation()`
  - [ ] Parameters: $recommendationId, $data
  - [ ] Verify recommendation exists
  - [ ] Verify status is 'pending'
  - [ ] UPDATE fields (role, reason, comments)
  - [ ] Return success/failure
  - [ ] Test update

- [ ] Implement method: `deleteRecommendation()`
  - [ ] Parameters: $recommendationId
  - [ ] Verify status is 'pending'
  - [ ] DELETE from table
  - [ ] Return success/failure
  - [ ] Test delete

- [ ] Implement method: `approveRecommendation()`
  - [ ] Parameters: $recommendationId, $adminId, $feedback (optional)
  - [ ] UPDATE Status = 'approved'
  - [ ] UPDATE ReviewedBy = $adminId
  - [ ] UPDATE DateReviewed = NOW()
  - [ ] Return success
  - [ ] Test approval

- [ ] Implement method: `rejectRecommendation()`
  - [ ] Parameters: $recommendationId, $adminId, $feedback
  - [ ] UPDATE Status = 'rejected'
  - [ ] UPDATE AdminFeedback = $feedback
  - [ ] UPDATE ReviewedBy = $adminId
  - [ ] UPDATE DateReviewed = NOW()
  - [ ] Return success
  - [ ] Test rejection

- [ ] Implement method: `checkDuplicateRecommendation()`
  - [ ] Parameters: $tournamentId, $playerId, $coachId
  - [ ] SELECT WHERE all three match
  - [ ] Return true if exists, false otherwise
  - [ ] Test duplicate check

- [ ] Implement method: `getCoachAssignedPlayers()`
  - [ ] Parameters: $coachId
  - [ ] JOIN playercoachassignment with playerprofile and user
  - [ ] Return players assigned to coach with details
  - [ ] Include: PlayerID, Name, BattingStyle, BowlingStyle
  - [ ] Test retrieval

- [ ] Add error logging
- [ ] Test all methods with sample data

---

## 🎮 CONTROLLER PHASE (Phase 3 - 1.5 hours)

### File: `app/controllers/Coach.php`

- [ ] Implement method: `tournament_recommendations()`
  - [ ] Route: GET `/coach/tournament-recommendations`
  - [ ] Load M_CoachTournamentRecommendation model
  - [ ] Get current user ID
  - [ ] Get all recommendations for coach
  - [ ] Separate by status (pending, approved, rejected)
  - [ ] Pass to view with: $data['recommendations'], $data['status_counts']
  - [ ] Test page loads

- [ ] Implement method: `recommend_players()`
  - [ ] Route: GET `/coach/recommend-players/{tournamentId}`
  - [ ] Get tournament details
  - [ ] Get coach's assigned players
  - [ ] Get existing recommendations for tournament
  - [ ] Return JSON with: $data['tournament'], $data['players'], $data['existing']
  - [ ] Test AJAX call

- [ ] Implement method: `save_recommendation()`
  - [ ] Route: POST `/coach/save-recommendation`
  - [ ] Validate CSRF token
  - [ ] Get POST data: tournamentId, playerId, role, reason, comments
  - [ ] Validate all fields
  - [ ] Call Model::addRecommendation()
  - [ ] Return JSON: {success: true/false, message: "...", id: 123}
  - [ ] Test POST submission

- [ ] Implement method: `update_recommendation()`
  - [ ] Route: PUT `/coach/update-recommendation/{id}`
  - [ ] Validate CSRF token
  - [ ] Verify recommendation belongs to coach
  - [ ] Verify status is 'pending'
  - [ ] Get PUT data: role, reason, comments
  - [ ] Call Model::updateRecommendation()
  - [ ] Return JSON success/failure
  - [ ] Test PUT request

- [ ] Implement method: `delete_recommendation()`
  - [ ] Route: DELETE `/coach/delete-recommendation/{id}`
  - [ ] Validate CSRF token
  - [ ] Verify recommendation belongs to coach
  - [ ] Verify status is 'pending'
  - [ ] Call Model::deleteRecommendation()
  - [ ] Return JSON success/failure
  - [ ] Test DELETE request

- [ ] Implement method: `get_tournament_details()`
  - [ ] Route: GET `/coach/tournament/{id}/details`
  - [ ] Get tournament info
  - [ ] Get existing recommendations for tournament
  - [ ] Get registration count
  - [ ] Return JSON with full details
  - [ ] Test retrieval

- [ ] Implement method: `get_assigned_players()`
  - [ ] Route: GET `/coach/assigned-players`
  - [ ] Get current user ID
  - [ ] Call Model::getCoachAssignedPlayers()
  - [ ] Return JSON with player array
  - [ ] Test retrieval

- [ ] Add authorization checks to all methods
  - [ ] Verify user is logged in
  - [ ] Verify user is coach
  - [ ] Verify coach owns player/recommendation (where applicable)
- [ ] Add error handling for all endpoints
- [ ] Add logging for all operations
- [ ] Test all endpoints with curl/Postman

---

## 🎨 VIEW PHASE (Phase 4 - 2 hours)

### File: `app/views/coach/tournaments.php` (Modify)

- [ ] Add recommendation modal HTML
  - [ ] Hidden by default
  - [ ] Tournament info display
  - [ ] Player multi-select form
  - [ ] Role selector
  - [ ] Reason textarea
  - [ ] Comments textarea
  - [ ] Submit/Cancel buttons
  - [ ] Test modal displays

- [ ] Add "Recommend Players" button to each tournament card
  - [ ] Button onclick triggers modal
  - [ ] Pass tournamentId to modal
  - [ ] Display tournament name in modal
  - [ ] Test button functionality

- [ ] Verify existing tournament grid still works
- [ ] Verify existing tournament table still works
- [ ] Test page loads without errors

### File: `app/views/coach/tournament-recommendations.php` (Create)

- [ ] Create new view file
- [ ] Add page header: "My Player Recommendations"
- [ ] Add filter section:
  - [ ] Status buttons (All, Pending, Approved, Rejected, Confirmed)
  - [ ] Tournament filter dropdown
  - [ ] Player search box
  - [ ] Sort dropdown (Newest, Oldest, Status)
  - [ ] Test filters load

- [ ] Add recommendations list section:
  - [ ] Loop through $data['recommendations']
  - [ ] Display each recommendation:
    - [ ] Tournament name
    - [ ] Player name
    - [ ] Role
    - [ ] Status badge (color-coded)
    - [ ] Date recommended
    - [ ] Action buttons (Edit, Delete, View Details)
  - [ ] Test list displays

- [ ] Add details modal:
  - [ ] Shows full recommendation details
  - [ ] Shows admin feedback (if rejected)
  - [ ] Shows edit form (if pending)
  - [ ] Test modal functionality

- [ ] Add no-results message
- [ ] Test pagination (if needed)
- [ ] Test responsive design

---

## 🎨 STYLING PHASE (Phase 5a - 1 hour)

### File: `public/css/coach/tournament-recommendations.css` (Create)

- [ ] Create new stylesheet
- [ ] Style recommendation modal:
  - [ ] `.recommendation-modal` - modal container
  - [ ] `.modal-header` - header styling
  - [ ] `.modal-body` - body styling
  - [ ] `.modal-footer` - footer with buttons
  - [ ] `.modal-overlay` - semi-transparent background

- [ ] Style player selection:
  - [ ] `.player-select-group` - wrapper
  - [ ] `.player-checkbox` - checkbox styling
  - [ ] `.player-card` - player info card
  - [ ] `.player-stats` - stats display
  - [ ] Hover effects

- [ ] Style form elements:
  - [ ] `.form-group` - form group wrapper
  - [ ] `.form-label` - label styling
  - [ ] `.form-control` - input/textarea styling
  - [ ] `.form-error` - error message styling
  - [ ] Focus/invalid states

- [ ] Style status badges:
  - [ ] `.status-badge` - base style
  - [ ] `.status-pending` - pending color (yellow)
  - [ ] `.status-approved` - approved color (green)
  - [ ] `.status-rejected` - rejected color (red)
  - [ ] `.status-confirmed` - confirmed color (blue)

- [ ] Style recommendation cards:
  - [ ] `.recommendation-card` - card container
  - [ ] `.card-header` - header with title
  - [ ] `.card-body` - main content
  - [ ] `.card-footer` - actions footer
  - [ ] Hover effects

- [ ] Style list and table:
  - [ ] `.recommendations-list` - list wrapper
  - [ ] `.list-item` - individual item
  - [ ] `.action-buttons` - button group
  - [ ] `.action-button` - individual button

- [ ] Style filters:
  - [ ] `.filter-section` - filter wrapper
  - [ ] `.filter-group` - filter group
  - [ ] `.filter-button` - button styles
  - [ ] `.filter-active` - active state
  - [ ] `.search-input` - search box

- [ ] Test responsive design (mobile, tablet, desktop)
- [ ] Test dark mode (if applicable)
- [ ] Test accessibility (color contrast, etc.)

---

## ⚙️ JAVASCRIPT PHASE (Phase 5b - 1.5 hours)

### File: `public/js/coach/tournament-recommendations.js` (Create)

- [ ] Modal Management
  - [ ] `openRecommendationModal(tournamentId, tournamentName)` function
    - [ ] Show modal overlay
    - [ ] Load tournament details via AJAX
    - [ ] Load assigned players via AJAX
    - [ ] Populate dropdowns
    - [ ] Test modal opens

  - [ ] `closeRecommendationModal()` function
    - [ ] Hide modal
    - [ ] Clear form data
    - [ ] Test modal closes

- [ ] Player Selection
  - [ ] Multi-select functionality
    - [ ] Check/uncheck players
    - [ ] Update selected count
    - [ ] Show/hide role fields as needed
  - [ ] Test checkboxes work

- [ ] Form Validation
  - [ ] `validateRecommendationForm()` function
    - [ ] Validate tournament selected
    - [ ] Validate at least one player selected
    - [ ] Validate role selected for each player
    - [ ] Validate reason not empty
    - [ ] Show error messages
  - [ ] Test validation works

- [ ] AJAX Calls
  - [ ] `submitRecommendation(formData)` - POST
    - [ ] Include CSRF token
    - [ ] POST to `/coach/save-recommendation`
    - [ ] Handle success: show message, close modal, refresh list
    - [ ] Handle error: show error message
  - [ ] `updateRecommendation(recommendationId, data)` - PUT
    - [ ] Include CSRF token
    - [ ] PUT to `/coach/update-recommendation/{id}`
    - [ ] Update UI on success
    - [ ] Handle error
  - [ ] `deleteRecommendation(recommendationId)` - DELETE
    - [ ] Include CSRF token
    - [ ] Confirm before delete
    - [ ] DELETE to `/coach/delete-recommendation/{id}`
    - [ ] Remove from list on success
    - [ ] Handle error
  - [ ] `loadRecommendations()` - GET
    - [ ] GET from `/coach/tournament-recommendations`
    - [ ] Populate list with results
    - [ ] Update status counts
  - [ ] Test all AJAX calls

- [ ] Filter & Sort
  - [ ] `filterByStatus(status)` function
    - [ ] Filter recommendations array
    - [ ] Update display
  - [ ] `filterByTournament(tournamentId)` function
    - [ ] Filter recommendations array
    - [ ] Update display
  - [ ] `searchPlayers(query)` function
    - [ ] Filter by player name
    - [ ] Real-time as user types
  - [ ] `sortRecommendations(sortBy)` function
    - [ ] Sort by date, status, etc.
  - [ ] Test all filters

- [ ] UI Updates
  - [ ] `updateRecommendationStatus(recommendationId, newStatus)` function
    - [ ] Update status badge
    - [ ] Update buttons
  - [ ] `showApprovalNotes(recommendationId)` function
    - [ ] Fetch notes from server
    - [ ] Display in modal
  - [ ] `refresh RecommendationsList()` function
    - [ ] Reload from server
    - [ ] Update display

- [ ] Event Handlers
  - [ ] Button click handlers
  - [ ] Form submit handler
  - [ ] Modal close handler
  - [ ] Filter change handlers
  - [ ] Search input handlers

- [ ] Error Handling
  - [ ] Try/catch blocks
  - [ ] User-friendly error messages
  - [ ] Console logging for debugging

- [ ] Test all JavaScript functions
- [ ] Test AJAX error handling
- [ ] Test form validation

---

## 🧪 TESTING PHASE (Testing & Refinement - 2 hours)

### Database Tests
- [ ] Insert test recommendation
  ```sql
  INSERT INTO coach_tournament_recommendations 
  (CoachID, TournamentID, PlayerID, Status, RecommendedRole, Reason)
  VALUES (1, 1, 1, 'pending', 'batsman', 'Test recommendation');
  ```
- [ ] Query test data
- [ ] Verify UNIQUE constraint works
- [ ] Verify foreign keys work
- [ ] Verify indexes are being used

### Model Tests
- [ ] Test `addRecommendation()` inserts correctly
- [ ] Test `getRecommendationsByCoach()` returns correct data
- [ ] Test `getRecommendationsByTournament()` returns correct data
- [ ] Test `updateRecommendation()` updates fields
- [ ] Test `deleteRecommendation()` removes records
- [ ] Test `approveRecommendation()` changes status
- [ ] Test `rejectRecommendation()` saves feedback
- [ ] Test `checkDuplicateRecommendation()` detects duplicates

### Controller Tests
- [ ] Test `GET /coach/tournament-recommendations` loads page
- [ ] Test `POST /coach/save-recommendation` creates recommendation
- [ ] Test `PUT /coach/update-recommendation/{id}` updates recommendation
- [ ] Test `DELETE /coach/delete-recommendation/{id}` deletes recommendation
- [ ] Test CSRF token validation
- [ ] Test authorization (coach can't update others' recommendations)
- [ ] Test error responses

### UI Tests
- [ ] Modal opens on button click
- [ ] Modal closes on cancel
- [ ] Player selection works (check/uncheck)
- [ ] Role dropdown populates
- [ ] Form validation shows errors
- [ ] Submit button is disabled if form invalid
- [ ] Loading indicators show during AJAX
- [ ] Success message displays after submit
- [ ] List updates after submit
- [ ] Filters work correctly
- [ ] Sort works correctly
- [ ] Search filters in real-time

### Integration Tests
- [ ] Coach can recommend player for tournament
- [ ] Coach can see their recommendations
- [ ] Coach can edit pending recommendations
- [ ] Coach can delete pending recommendations
- [ ] Coach cannot edit approved recommendations
- [ ] Admin can approve recommendations
- [ ] Admin can reject recommendations
- [ ] Duplicate recommendations are prevented

### Browser Tests
- [ ] Chrome/Chromium
- [ ] Firefox
- [ ] Safari
- [ ] Edge
- [ ] Mobile Chrome
- [ ] Mobile Safari

### Responsive Tests
- [ ] Desktop (1920x1080)
- [ ] Tablet (768x1024)
- [ ] Mobile (375x812)
- [ ] Portrait and landscape

### Performance Tests
- [ ] Page loads in < 2 seconds
- [ ] AJAX calls complete in < 500ms
- [ ] Filters/search are responsive
- [ ] No N+1 query problems

### Accessibility Tests
- [ ] Keyboard navigation works
- [ ] Screen reader compatible
- [ ] Color contrast sufficient (WCAG AA)
- [ ] Form labels present
- [ ] ARIA labels used

---

## 📋 REFINEMENT & POLISH (0.5 hours)

- [ ] Review all code for bugs
- [ ] Remove console.log debug statements
- [ ] Add production error handling
- [ ] Verify all error messages are user-friendly
- [ ] Test all edge cases:
  - [ ] Closed tournaments
  - [ ] Deleted players
  - [ ] Deleted coaches
  - [ ] Network errors
  - [ ] Form submission errors
  - [ ] Database constraints
- [ ] Optimize database queries
- [ ] Optimize JavaScript bundle size
- [ ] Optimize CSS delivery
- [ ] Add comments to complex code
- [ ] Code review with another developer (if available)

---

## 🚀 DEPLOYMENT & FINAL VERIFICATION (1 hour)

- [ ] Verify all changes committed to `coach` branch
- [ ] Run full test suite one final time
- [ ] Check for any console errors in browser DevTools
- [ ] Verify CSRF tokens are working
- [ ] Verify authorization checks are in place
- [ ] Test production database
- [ ] Clear browser cache
- [ ] Clear application cache
- [ ] Verify no debug code in production
- [ ] Test with production-like data volume
- [ ] Monitor error logs
- [ ] Get sign-off from stakeholders

---

## 📞 SIGN-OFF CHECKLIST

- [ ] Feature complete
- [ ] All tests passing
- [ ] Code reviewed
- [ ] Documentation updated
- [ ] Database optimized
- [ ] Security audit passed
- [ ] Performance verified
- [ ] Accessibility checked
- [ ] Browser compatibility confirmed
- [ ] Responsive design verified
- [ ] Ready for production

---

## 🎉 COMPLETION SUMMARY

**Start Date:** April 7, 2026  
**Estimated Completion:** April 14-18, 2026  
**Total Effort:** 26 hours

**Phases Completed:**
- [x] Phase 1: Database (0.25 hrs)
- [x] Phase 2: Model (1 hr)
- [x] Phase 3: Controller (1.5 hrs)
- [x] Phase 4: Views (2 hrs)
- [x] Phase 5: CSS + JS (2.5 hrs)
- [x] Phase 6: Testing (2 hrs)
- [x] Phase 7: Polish & Deploy (1 hr)

**Total Implementation Time:** ~26 hours

---

**Document Created:** April 7, 2026  
**Version:** 1.0  
**Status:** Ready to Execute

**Next Step:** Print this checklist and start with Phase 1 (Database)!
