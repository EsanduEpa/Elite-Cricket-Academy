# Session Management - Layout Visual Guide

## 🎨 Current Layout Structure

```
┌─────────────────────────────────────────────────────────────────┐
│  HEADER (Fixed Top - 80px)                                      │
│  Elite Cricket Academy Logo | Navigation Menu | User Profile    │
└─────────────────────────────────────────────────────────────────┘
┌──────────────┬──────────────────────────────────────────────────┐
│              │                                                  │
│   SIDEBAR    │              MAIN CONTENT                        │
│   (280px)    │              (Flexible Width)                    │
│              │                                                  │
│ ┌──────────┐ │ ┌──────────────────────────────────────────────┐│
│ │Coach Panel│ │ │  Dashboard Header (Blue Gradient)          │││
│ │    🎓    │ │ │  Session & Schedule Management              │││
│ └──────────┘ │ │  Manage your coaching sessions...           │││
│              │ │  [+ Add Session] [🔄 Refresh]               │││
│ ┌──────────┐ │ └──────────────────────────────────────────────┘│
│ │Dashboard │ │                                                  │
│ │  📊      │ │ ┌─────┬─────┬─────┬─────┐ Statistics Grid      │
│ └──────────┘ │ │Today│Week │Part.│Atten│                      │
│              │ │  0  │  5  │ 46  │ 74% │                      │
│ ┌──────────┐ │ └─────┴─────┴─────┴─────┘                      │
│ │Sessions  │ │                                                  │
│ │  📅 ✓   │ │ ┌──────────────────────────────────────────────┐│
│ └──────────┘ │ │  Filters: [Type▼] [Mode▼] [Status▼] [🔍]  │││
│              │ └──────────────────────────────────────────────┘│
│ ┌──────────┐ │                                                  │
│ │Schedules │ │ ┌──────────────────────────────────────────────┐│
│ │  📋      │ │ │ [Calendar] [List]                           │││
│ └──────────┘ │ ├──────────────────────────────────────────────┤│
│              │ │ [◀] October 2025 [▶] [Today]                │││
│ ┌──────────┐ │ │ [Month] [Week] [Day]                        │││
│ │Players   │ │ ├──────────────────────────────────────────────┤│
│ │  👥      │ │ │ Sun │ Mon │ Tue │ Wed │ Thu │ Fri │ Sat     │││
│ └──────────┘ │ ├─────┼─────┼─────┼─────┼─────┼─────┼─────┤  ││
│              │ │     │     │  22 │  23 │  24 │  25 │  26 │  ││
│ ┌──────────┐ │ │     │     │ 🟦 │ 🟦 │     │ 🟦 │     │  ││
│ │Tournaments│ │ │     │     │ 🟩 │     │     │     │     │  ││
│ │  🏆      │ │ │  27 │  28 │  29 │  30 │  31 │     │     │  ││
│ └──────────┘ │ │     │ 🟩 │     │     │     │     │     │  ││
│              │ └──────────────────────────────────────────────┘│
│ ┌──────────┐ │                                                  │
│ │Events    │ │  🟦 = Coaching Session                          │
│ │  📅      │ │  🟩 = Physical Training                         │
│ └──────────┘ │                                                  │
│              │                                                  │
│    [◀]      │                                                  │
│  Collapse    │                                                  │
│              │                                                  │
└──────────────┴──────────────────────────────────────────────────┘
```

---

## 📱 Collapsed Sidebar (80px)

```
┌─────────────────────────────────────────────────────────────────┐
│  HEADER (Fixed Top)                                             │
└─────────────────────────────────────────────────────────────────┘
┌───┬─────────────────────────────────────────────────────────────┐
│   │                                                             │
│ S │              MAIN CONTENT                                   │
│ I │              (Wider - More Space)                           │
│ D │                                                             │
│ E │ ┌─────────────────────────────────────────────────────────┐│
│ B │ │  Dashboard Header                                       ││
│ A │ └─────────────────────────────────────────────────────────┘│
│ R │                                                             │
│   │ ┌───┬───┬───┬───┐ Statistics                              │
│ 🎓│ │ 0 │ 5 │46 │74%│                                          │
│   │ └───┴───┴───┴───┘                                          │
│ 📊│                                                             │
│   │ ┌─────────────────────────────────────────────────────────┐│
│ 📅│ │  Filters                                                ││
│   │ └─────────────────────────────────────────────────────────┘│
│ 📋│                                                             │
│   │ ┌─────────────────────────────────────────────────────────┐│
│ 👥│ │  Calendar (Larger View)                                 ││
│   │ │  More visible days and events                           ││
│ 🏆│ │                                                         ││
│   │ └─────────────────────────────────────────────────────────┘│
│ 📅│                                                             │
│   │                                                             │
│ ▶ │                                                             │
│Exp│                                                             │
│and│                                                             │
│   │                                                             │
└───┴─────────────────────────────────────────────────────────────┘
```

---

## 📱 Mobile View (< 768px)

```
┌─────────────────────────────┐
│  HEADER                     │
│  [☰] Elite Cricket Academy │
└─────────────────────────────┘
┌─────────────────────────────┐
│  Dashboard Header           │
│  Session Management         │
│  [+ Add] [Refresh]          │
└─────────────────────────────┘
┌─────────────────────────────┐
│  Today's Sessions: 0        │
├─────────────────────────────┤
│  This Week: 5               │
├─────────────────────────────┤
│  Participants: 46           │
├─────────────────────────────┤
│  Attendance: 74%            │
└─────────────────────────────┘
┌─────────────────────────────┐
│  Filters                    │
│  [Type ▼]                   │
│  [Mode ▼]                   │
│  [Status ▼]                 │
│  [Search...]                │
└─────────────────────────────┘
┌─────────────────────────────┐
│  [Calendar] [List]          │
├─────────────────────────────┤
│  [◀] Oct 2025 [▶]          │
│  [Month][Week][Day]         │
├─────────────────────────────┤
│ S│M│T│W│T│F│S              │
├─┼─┼─┼─┼─┼─┼─┤              │
│ │ │22│23│24│25│26          │
│ │ │🟦│🟦│  │🟦│             │
└─────────────────────────────┘

Sidebar: Hidden (swipe/tap to open)
```

---

## 🎨 Color Coding

### Session Types
- **🟦 Blue** - Coaching Sessions (#4A90E2)
- **🟩 Green** - Physical Training (#10b981)

### UI Elements
- **White Text** - Headers on gradient backgrounds
- **Dark Gray** - Primary text (#333333)
- **Medium Gray** - Secondary text (#666666)
- **Light Gray** - Muted text (#999999)

### Interactive States
- **Hover** - Lighter background, lift effect
- **Active** - Blue background, white text
- **Focus** - Blue border, shadow glow
- **Disabled** - Reduced opacity, no cursor

---

## 📐 Dimensions

### Desktop Layout
- **Header Height**: 80px (fixed)
- **Sidebar Width**: 280px (collapsible to 80px)
- **Main Content Margin**: 280px left (auto right)
- **Max Content Width**: 1600px
- **Content Padding**: 30px

### Card Sizes
- **Stat Card**: min-width 240px, height auto
- **Calendar Day**: min-height 120px
- **List Card**: full width, height auto

### Border Radius
- **Small (sm)**: 12px - buttons, inputs
- **Medium (md)**: 16px - cards
- **Large (lg)**: 20px - headers

### Spacing
- **Gap Small**: 8px - inline elements
- **Gap Medium**: 16-20px - card grid
- **Gap Large**: 24-32px - sections

---

## 🎯 Component Hierarchy

```
coach-layout
├── coach-sidebar
│   ├── sidebar-header
│   │   ├── coach-logo
│   │   └── sidebar-toggle
│   └── sidebar-nav
│       └── nav-menu (6 items)
│
└── main-content
    ├── dashboard-header
    │   ├── header-left (title + subtitle)
    │   └── header-actions (2 buttons)
    │
    ├── stats-grid (4 cards)
    │   ├── stat-card (Today)
    │   ├── stat-card (Week)
    │   ├── stat-card (Participants)
    │   └── stat-card (Attendance)
    │
    ├── filters-section
    │   └── filters-grid (4 fields)
    │
    ├── view-tabs
    │   ├── tab-btn (Calendar) ✓
    │   └── tab-btn (List)
    │
    ├── calendar-container
    │   ├── calendar-header
    │   │   ├── calendar-nav
    │   │   ├── calendar-title
    │   │   └── calendar-view-switcher
    │   │
    │   └── calendar-month
    │       ├── day-headers (7)
    │       └── day-cells (42)
    │           └── calendar-events
    │
    └── list-container
        └── list-session-cards
```

---

## ✨ Glassmorphism Effect

```css
/* Applied to all major containers */
background: rgba(255, 255, 255, 0.25);
backdrop-filter: blur(10px);
-webkit-backdrop-filter: blur(10px);
border: 1px solid rgba(255, 255, 255, 0.18);
box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
```

**Result**: Frosted glass appearance with depth and modern aesthetic

---

## 🔄 Animations

### Hover Effects
- **Cards**: Lift up 4px, increase shadow
- **Buttons**: Lift up 2px, increase shadow
- **Calendar Days**: Scale 1.02, lighten background
- **Events**: Scale 1.05, increase shadow

### Transitions
- **Fast**: 150ms - hover states
- **Base**: 300ms - most interactions
- **Smooth**: cubic-bezier(0.25, 0.8, 0.25, 1)

### Background Pulse
- **Dashboard Header**: Radial gradient pulse (15s loop)
- **Icons**: Scale pulse (2s loop)

---

*Layout guide last updated: October 21, 2025*
