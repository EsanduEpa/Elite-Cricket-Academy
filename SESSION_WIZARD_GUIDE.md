# Session Wizard Implementation Guide

## 📋 Overview

A beautiful, multi-step wizard interface for creating new coaching and physical training sessions. Built with vanilla JavaScript, matching the coach dashboard's glassmorphism theme.

---

## 🎯 Features

### ✨ User Experience
- **4-Step Progressive Flow**: Type → Schedule → Details → Review
- **Visual Progress Indicator**: Animated progress bar with step circles
- **Real-time Validation**: Field validation before proceeding to next step
- **Responsive Design**: Works seamlessly on desktop, tablet, and mobile
- **Glassmorphism Theme**: Consistent with coach dashboard design
- **Smooth Animations**: Fade transitions between steps
- **Summary Review**: Complete overview before submission

### 🔒 Form Validation
- Required field checking
- Time range validation (end > start)
- Minimum participant validation
- Date validation (no past dates)
- Real-time error feedback

### 💾 Data Structure
Matches `cricket_academy_schema.sql` Session table exactly:
```sql
SessionType ENUM('Coaching', 'Physical Training')
SessionMode ENUM('Group', 'Private')
Name VARCHAR(255)
Date DATE
StartTime TIME
EndTime TIME
Location VARCHAR(255)
MaxParticipants INT
PricePerSession DECIMAL(10,2)
IsRecurring BOOLEAN
Status ENUM('active', 'cancelled', 'completed') -- Auto-set to 'active'
```

---

## 📁 File Structure

```
Elite/
├── app/views/coach/
│   └── sessions.php               # Main view with wizard modal (1,438 lines)
└── public/css/coach/
    ├── sessions.css               # Calendar styles (733 lines)
    └── session-wizard.css         # Wizard-specific styles (NEW - 732 lines)
```

---

## 🎨 Wizard Steps Breakdown

### Step 1: Session Type & Mode

**Purpose**: Select coaching/physical training and group/private mode

**Fields**:
- `sessionType` (radio) - Coaching | Physical Training
- `sessionMode` (radio) - Group | Private

**UI Components**:
- **Type Cards**: Large clickable cards with icons
  - Coaching: Blue (#4A90E2) with chalkboard icon
  - Physical Training: Green (#10b981) with running icon
- **Mode Options**: Radio buttons with labels

**Validation**:
- Both type and mode must be selected

**Visual Design**:
```
┌─────────────────────────────────────────────┐
│  Select Session Type                         │
│  ┌─────────────┐  ┌─────────────┐           │
│  │  🎓         │  │  🏃         │           │
│  │  Coaching   │  │  Physical   │           │
│  │  Session    │  │  Training   │           │
│  └─────────────┘  └─────────────┘           │
│                                              │
│  Session Mode                                │
│  ⦿ Group Session    ○ Private Session       │
└─────────────────────────────────────────────┘
```

---

### Step 2: Schedule

**Purpose**: Set date, time, and session name

**Fields**:
- `sessionName` (text) - Descriptive name
- `sessionDate` (date) - Session date (min: today)
- `startTime` (time) - Start time
- `endTime` (time) - End time

**Validation**:
- All fields required
- Date cannot be in the past
- End time must be after start time

**Visual Design**:
```
┌─────────────────────────────────────────────┐
│  Schedule Session                            │
│                                              │
│  Session Name *                              │
│  ┌─────────────────────────────────────┐    │
│  │ Batting Techniques                  │    │
│  └─────────────────────────────────────┘    │
│                                              │
│  Session Date *    Start Time *  End Time * │
│  ┌─────────┐      ┌─────┐      ┌─────┐     │
│  │10/25/25 │      │09:00│      │11:00│     │
│  └─────────┘      └─────┘      └─────┘     │
└─────────────────────────────────────────────┘
```

---

### Step 3: Details

**Purpose**: Configure capacity, location, and pricing

**Fields**:
- `location` (text) - Where session takes place
- `maxParticipants` (number) - Capacity (1-50)
- `pricePerSession` (decimal) - Cost (0 for group sessions)
- `isRecurring` (checkbox) - Regular vs one-time

**Validation**:
- Location required
- Max participants ≥ 1

**Smart Defaults**:
- Max Participants: 10
- Price: 0.00 (free for group sessions)
- Is Recurring: Checked (regular sessions by default)

**Visual Design**:
```
┌─────────────────────────────────────────────┐
│  Session Details                             │
│                                              │
│  Location *        Max Participants *        │
│  ┌─────────────┐  ┌────────────────┐        │
│  │ Main Ground │  │ 10             │        │
│  └─────────────┘  └────────────────┘        │
│                                              │
│  Price Per Session                           │
│  ┌────────────────┐                          │
│  │ 0.00           │ 0 for group sessions     │
│  └────────────────┘                          │
│                                              │
│  ☑ This is a recurring regular session      │
└─────────────────────────────────────────────┘
```

---

### Step 4: Review & Confirm

**Purpose**: Show complete summary before creating session

**Sections**:
1. **Session Information**
   - Type (badge)
   - Mode (badge)
   - Session Name

2. **Schedule**
   - Full date (e.g., "Monday, October 21, 2025")
   - Time range
   - Location

3. **Additional Details**
   - Max Participants
   - Price (formatted)
   - Recurring status

**Visual Design**:
```
┌─────────────────────────────────────────────┐
│  Review & Confirm                            │
│                                              │
│  ┌─── Session Information ─────────────┐    │
│  │ Type:  [Coaching]                   │    │
│  │ Mode:  [Group]                      │    │
│  │ Name:  Batting Techniques           │    │
│  └─────────────────────────────────────┘    │
│                                              │
│  ┌─── Schedule ─────────────────────────┐   │
│  │ Date:  Monday, October 21, 2025     │    │
│  │ Time:  9:00 AM - 11:00 AM           │    │
│  │ Location: Main Ground               │    │
│  └─────────────────────────────────────┘    │
│                                              │
│  ┌─── Additional Details ──────────────┐    │
│  │ Max Participants: 10                │    │
│  │ Price: Free (Group Session)         │    │
│  │ Recurring: Yes (Regular Session)    │    │
│  └─────────────────────────────────────┘    │
└─────────────────────────────────────────────┘
```

---

## 🎨 Design System

### Color Palette

```css
/* Primary Colors */
--primary-color: #4A90E2          /* Blue - Primary actions */
--primary-dark: #357ABD           /* Darker blue - Gradients */

/* Session Type Colors */
--coaching-color: #4A90E2         /* Blue */
--physical-training-color: #10b981 /* Green */

/* Session Mode Colors */
--group-color: #8b5cf6            /* Purple */
--private-color: #f59e0b          /* Orange */

/* Status Colors */
--success-color: #10b981          /* Green - Success states */
--danger-color: #ef4444           /* Red - Errors */

/* Glassmorphism */
--bg-glass: rgba(255, 255, 255, 0.25)
--bg-glass-strong: rgba(255, 255, 255, 0.95)
--border-glass: rgba(255, 255, 255, 0.3)
--shadow-glass: 0 20px 60px rgba(31, 38, 135, 0.5)
```

### Typography

```css
/* Headers */
h2: 28px / 700 weight
h3: 22px / 700 weight  
h4: 18px / 700 weight

/* Body */
Body text: 14px / 400 weight
Labels: 14px / 600 weight
Help text: 12px / 400 weight
Buttons: 15px / 600 weight
```

### Spacing

```css
/* Modal Padding */
Header: 30px 40px
Progress: 30px 40px
Body: 40px
Footer: 24px 40px

/* Form Gaps */
Form grid: 24px gap
Form groups: 8px gap
Radio groups: 20px gap
```

### Border Radius

```css
Modal: 20px
Cards: 16px / 12px
Inputs: 12px
Buttons: 12px
Badges: 6px
Circles: 50% (step indicators)
```

---

## 🔄 Wizard State Management

### State Object

```javascript
let wizardState = {
    currentStep: 1,           // Active step (1-4)
    totalSteps: 4,            // Total number of steps
    formData: {               // Collected form data
        // Step 1
        sessionType: '',      // 'Coaching' | 'Physical Training'
        sessionMode: '',      // 'Group' | 'Private'
        
        // Step 2
        name: '',             // Session name
        date: '',             // YYYY-MM-DD
        startTime: '',        // HH:MM
        endTime: '',          // HH:MM
        
        // Step 3
        location: '',         // Location string
        maxParticipants: 10,  // Number
        pricePerSession: 0.00,// Decimal
        isRecurring: true     // Boolean
    }
};
```

### Key Functions

#### Navigation

```javascript
function openAddSessionModal()
// Opens wizard modal with animation

function closeWizard()
// Closes modal and resets state after delay

function nextStep()
// Validates current step, saves data, moves forward

function prevStep()
// Moves back one step (no validation)

function showStep(stepNumber)
// Displays specific step, updates UI
```

#### Validation

```javascript
function validateStep(stepNumber)
// Returns true/false, shows alert on errors

// Step 1: Checks both radio groups selected
// Step 2: Name, date, times required + end > start
// Step 3: Location required, participants ≥ 1
// Step 4: No validation (review only)
```

#### Data Management

```javascript
function saveStepData(stepNumber)
// Saves form fields to wizardState.formData

function populateSummary()
// Populates Step 4 summary from formData

function resetWizard()
// Resets state, form, shows step 1
```

#### UI Updates

```javascript
function updateProgress()
// Updates step circles and progress bar

function updateButtons()
// Shows/hides prev/next/submit buttons

function selectTypeCard(type)
// Handles type card selection in Step 1
```

#### Submission

```javascript
function submitSession()
// Creates session object matching DB schema
// Logs to console (TODO: API call)
// Adds to calendar temporarily
// Closes wizard
```

---

## 🎭 Animations & Interactions

### Modal Entrance
```css
/* Overlay */
opacity: 0 → 1
backdrop-blur: 0 → 8px

/* Modal */
scale: 0.9 → 1
translateY: 30px → 0
transition: 0.3s cubic-bezier(0.25, 0.8, 0.25, 1)
```

### Step Transitions
```css
@keyframes fadeInStep {
    from { opacity: 0; transform: translateX(30px); }
    to   { opacity: 1; transform: translateX(0); }
}
duration: 0.4s ease-out
```

### Progress Bar
```css
width: calculated % based on step
transition: 0.5s cubic-bezier(0.25, 0.8, 0.25, 1)
```

### Button Hovers
```css
/* Primary/Success buttons */
transform: translateY(-2px)
box-shadow: enhanced

/* Secondary buttons */
border-color: primary
color: primary
background: white
```

### Close Button
```css
transform: rotate(90deg) on hover
background: enhanced transparency
```

---

## 📱 Responsive Behavior

### Desktop (> 768px)
- Modal: 800px max-width
- Form grid: 2 columns (auto-fit, min 250px)
- Progress steps: Full labels visible
- Step circles: 40px

### Mobile (≤ 768px)
- Modal: 95% width
- Form grid: Single column
- Progress steps: Smaller circles (35px)
- Step labels: Smaller font (11px)
- Reduced padding: 20px
- Stacked footer buttons (full-width)

---

## 🚀 Usage Guide

### 1. Opening the Wizard

Click "Add New Session" button anywhere in the interface:

```html
<button class="btn-primary" onclick="openAddSessionModal()">
    <i class="fas fa-plus"></i>
    Add New Session
</button>
```

### 2. Filling the Form

**Step 1 - Select Type:**
- Click on a type card (Coaching or Physical Training)
- Select session mode (Group or Private)
- Click "Next"

**Step 2 - Schedule:**
- Enter session name
- Pick a date (today or future)
- Set start and end times
- Click "Next"

**Step 3 - Details:**
- Enter location
- Set max participants (default: 10)
- Set price if private session
- Check/uncheck recurring
- Click "Next"

**Step 4 - Review:**
- Review all details
- Click "Create Session" to submit

### 3. Navigation

- **Previous**: Go back to edit (no data loss)
- **Next**: Validate and proceed
- **Close (×)**: Cancel and close wizard
- **Click outside**: Close wizard

### 4. Error Handling

Validation errors show as alerts:
```
⚠️ Validation Errors
• Please select a session type
• Session name is required
• End time must be after start time
```

---

## 🔌 Backend Integration (TODO)

Currently, the wizard displays data in console and adds to calendar temporarily. To connect to database:

### 1. Update Controller Route

`app/controllers/Coach.php`:
```php
public function create_session() {
    if (!isLoggedIn() || $_SESSION['role'] !== 'Coach') {
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        return;
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate data
    if (empty($data['Name']) || empty($data['Date'])) {
        echo json_encode(['success' => false, 'error' => 'Missing required fields']);
        return;
    }
    
    // Add CoachOrTrainerID from session
    $data['CoachOrTrainerID'] = $_SESSION['coach_id'];
    
    // Create session via model
    $sessionId = $this->sessionModel->createSession($data);
    
    if ($sessionId) {
        echo json_encode([
            'success' => true,
            'sessionId' => $sessionId,
            'message' => 'Session created successfully'
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to create session']);
    }
}
```

### 2. Update JavaScript submitSession()

Replace lines in `sessions.php`:
```javascript
function submitSession() {
    saveStepData(wizardState.currentStep);
    
    const sessionData = {
        SessionType: wizardState.formData.sessionType,
        SessionMode: wizardState.formData.sessionMode,
        Name: wizardState.formData.name,
        Date: wizardState.formData.date,
        StartTime: wizardState.formData.startTime,
        EndTime: wizardState.formData.endTime,
        Location: wizardState.formData.location,
        Status: 'active',
        MaxParticipants: parseInt(wizardState.formData.maxParticipants),
        PricePerSession: parseFloat(wizardState.formData.pricePerSession),
        IsRecurring: wizardState.formData.isRecurring
    };
    
    // REPLACE THIS SECTION:
    fetch('<?php echo URLROOT; ?>/coach/create_session', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(sessionData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Success notification
            alert('✅ Session created successfully!');
            
            // Add to calendar
            const newSession = {
                id: data.sessionId,
                name: sessionData.Name,
                sessionType: sessionData.SessionType,
                sessionMode: sessionData.SessionMode,
                date: sessionData.Date,
                startTime: sessionData.StartTime,
                endTime: sessionData.EndTime,
                location: sessionData.Location,
                maxParticipants: sessionData.MaxParticipants,
                currentParticipants: 0,
                status: 'active',
                color: sessionData.SessionType === 'Coaching' ? '#4A90E2' : '#10b981'
            };
            
            calendarState.sessions.push(newSession);
            renderCalendar();
            updateStatistics();
            closeWizard();
        } else {
            alert('❌ Error: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Failed to create session. Please try again.');
    });
}
```

### 3. Model Method

The `M_Session.php` model already has `createSession()` method:

```php
public function createSession($data) {
    $this->db->query('INSERT INTO Session 
        (SessionType, SessionMode, CoachOrTrainerID, Name, Date, StartTime, 
         EndTime, Location, Status, MaxParticipants, PricePerSession, IsRecurring) 
        VALUES 
        (:sessionType, :sessionMode, :coachId, :name, :date, :startTime, 
         :endTime, :location, :status, :maxParticipants, :price, :recurring)');
    
    // Bind values
    $this->db->bind(':sessionType', $data['SessionType']);
    $this->db->bind(':sessionMode', $data['SessionMode']);
    $this->db->bind(':coachId', $data['CoachOrTrainerID']);
    $this->db->bind(':name', $data['Name']);
    $this->db->bind(':date', $data['Date']);
    $this->db->bind(':startTime', $data['StartTime']);
    $this->db->bind(':endTime', $data['EndTime']);
    $this->db->bind(':location', $data['Location']);
    $this->db->bind(':status', 'active');
    $this->db->bind(':maxParticipants', $data['MaxParticipants']);
    $this->db->bind(':price', $data['PricePerSession']);
    $this->db->bind(':recurring', $data['IsRecurring']);
    
    if ($this->db->execute()) {
        return $this->db->lastInsertId();
    }
    return false;
}
```

---

## 🧪 Testing Checklist

### Functional Testing

- [ ] **Modal opens** when clicking "Add New Session"
- [ ] **Modal closes** with X button
- [ ] **Modal closes** when clicking outside
- [ ] **Step 1 validation** - type and mode required
- [ ] **Step 2 validation** - all fields required, end > start
- [ ] **Step 3 validation** - location and participants required
- [ ] **Navigation works** - Next, Previous, step indicators
- [ ] **Progress bar** animates correctly
- [ ] **Summary populates** with correct data
- [ ] **Submit creates** session (check console.log)
- [ ] **Calendar updates** after submission

### UI/UX Testing

- [ ] **Animations smooth** - modal entrance, step transitions
- [ ] **Buttons update** - disabled states, visibility
- [ ] **Type cards** - selection highlights
- [ ] **Form fields** - focus states work
- [ ] **Responsive** - test on mobile/tablet
- [ ] **Scrolling** - wizard body scrolls if content tall
- [ ] **Icons display** - FontAwesome icons load
- [ ] **Colors match** - coach dashboard theme
- [ ] **Glassmorphism** - backdrop blur works

### Data Testing

- [ ] **Coaching type** - saves correctly
- [ ] **Physical Training** - saves correctly
- [ ] **Group mode** - price = 0
- [ ] **Private mode** - custom price
- [ ] **Date validation** - no past dates
- [ ] **Time validation** - end after start
- [ ] **Recurring checkbox** - state saved
- [ ] **Max participants** - number validated

### Browser Testing

- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Edge (latest)
- [ ] Mobile Safari (iOS)
- [ ] Mobile Chrome (Android)

---

## 🎯 Database Schema Mapping

### Form Fields → Database Columns

| Wizard Field         | Type      | DB Column         | DB Type         | Notes                  |
|---------------------|-----------|-------------------|-----------------|------------------------|
| sessionType         | radio     | SessionType       | ENUM            | 'Coaching'/'Physical'  |
| sessionMode         | radio     | SessionMode       | ENUM            | 'Group'/'Private'      |
| sessionName         | text      | Name              | VARCHAR(255)    | Required               |
| sessionDate         | date      | Date              | DATE            | Format: YYYY-MM-DD     |
| startTime           | time      | StartTime         | TIME            | Format: HH:MM          |
| endTime             | time      | EndTime           | TIME            | Format: HH:MM          |
| location            | text      | Location          | VARCHAR(255)    | Required               |
| maxParticipants     | number    | MaxParticipants   | INT             | Default: 10            |
| pricePerSession     | number    | PricePerSession   | DECIMAL(10,2)   | 0 for group sessions   |
| isRecurring         | checkbox  | IsRecurring       | BOOLEAN         | Default: TRUE          |
| (auto)              | -         | CoachOrTrainerID  | INT             | From session           |
| (auto)              | -         | Status            | ENUM            | Always 'active'        |
| (auto)              | -         | SessionID         | INT             | Auto-increment PK      |

### Not Included (Extended Tables)

These fields can be added in future:
- `SessionDetails` table fields (FacilityType, RecurrencePattern, etc.)
- `SessionEnrollment` (handled separately)
- `SessionAttendance` (tracking feature)

---

## 🎨 Customization Guide

### Change Colors

Edit `session-wizard.css`:

```css
:root {
    --primary-color: #YOUR_COLOR;        /* Main brand color */
    --coaching-color: #YOUR_COLOR;       /* Coaching sessions */
    --physical-color: #YOUR_COLOR;       /* Physical training */
}
```

### Add More Steps

1. Update `wizardState.totalSteps`
2. Add step HTML in wizard body
3. Add step circle in progress section
4. Update `validateStep()` and `saveStepData()`
5. Update progress bar calculation

### Change Step Order

Reorder step HTML and update step IDs consistently.

### Custom Validation

Add to `validateStep()`:

```javascript
if (stepNumber === YOUR_STEP) {
    // Your validation logic
    if (!condition) {
        errors.push('Your error message');
        isValid = false;
    }
}
```

---

## 📊 Code Statistics

```
Total Files: 3
Total Lines: 2,903 lines

Breakdown:
- sessions.php:        1,438 lines (view + JavaScript)
- sessions.css:          733 lines (calendar styles)
- session-wizard.css:    732 lines (wizard styles)

JavaScript Functions: 25
CSS Rules: ~200
Form Fields: 8 inputs + 2 radio groups + 1 checkbox
```

---

## 🐛 Common Issues & Solutions

### Issue: Modal doesn't open
**Solution**: Check if `openAddSessionModal()` is defined and jQuery/conflicts

### Issue: Steps don't advance
**Solution**: Check console for validation errors, ensure all required fields filled

### Issue: Glassmorphism not showing
**Solution**: Verify browser supports `backdrop-filter`, check for CSS conflicts

### Issue: Date field allows past dates
**Solution**: Ensure `min="<?php echo date('Y-m-d'); ?>"` in HTML

### Issue: Progress bar doesn't move
**Solution**: Check `updateProgress()` is called, verify `wizardState.currentStep` updates

### Issue: Summary shows "-" or empty
**Solution**: Ensure `populateSummary()` called, check `wizardState.formData` has values

---

## 🚀 Future Enhancements

### Priority 1 (High)
- [ ] Backend API integration
- [ ] Success/error toast notifications
- [ ] Real-time conflict checking (same time/location)
- [ ] Coach availability validation

### Priority 2 (Medium)
- [ ] Recurring session pattern configuration
- [ ] Bulk session creation
- [ ] Template system (save common session configs)
- [ ] Player enrollment selection

### Priority 3 (Nice to Have)
- [ ] Drag-and-drop time selection from calendar
- [ ] Session cloning feature
- [ ] Multi-language support
- [ ] Dark mode variant
- [ ] Export session details to PDF

---

## 📖 Related Documentation

- `SESSION_VIEW_IMPLEMENTATION.md` - Full calendar system
- `SESSION_THEME_UPDATE.md` - Theme consistency guide
- `SESSION_LAYOUT_GUIDE.md` - Visual layout reference
- `DATABASE_SCHEMA_GUIDE.md` - Database structure
- `cricket_academy_schema.sql` - SQL schema file

---

## 👨‍💻 Developer Notes

### Code Organization
- Wizard state in global `wizardState` object
- All wizard functions prefixed with wizard action (open, close, next, etc.)
- CSS organized by component (header, progress, steps, footer)
- Validation separated by step number

### Best Practices
- Always save data before advancing steps
- Validate before saving
- Update all UI elements (buttons, progress, counter)
- Maintain consistent naming conventions
- Comment complex logic

### Performance
- Minimal DOM queries (cache elements when possible)
- CSS transitions instead of JavaScript animations
- Lazy validation (on next/submit, not on every input)

---

## 📝 Changelog

### Version 1.0.0 (Current)
- ✅ Initial wizard implementation
- ✅ 4-step progressive flow
- ✅ Full field validation
- ✅ Glassmorphism theme
- ✅ Responsive design
- ✅ Summary review page
- ✅ Console logging for debugging

### Planned (v1.1.0)
- API integration
- Toast notifications
- Conflict checking
- Template system

---

**Created**: October 2025  
**Author**: Elite Cricket Academy Development Team  
**Status**: ✅ Ready for Database Integration
