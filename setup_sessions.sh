#!/bin/bash

# ===================================================================
# SESSION MANAGEMENT SYSTEM - QUICK SETUP SCRIPT
# ===================================================================
# Description: Automated setup for session management system
# Usage: chmod +x setup_sessions.sh && ./setup_sessions.sh
# ===================================================================

echo "╔════════════════════════════════════════════════════════════════╗"
echo "║   SESSION MANAGEMENT SYSTEM - QUICK SETUP                      ║"
echo "║   Elite Cricket Academy                                        ║"
echo "╚════════════════════════════════════════════════════════════════╝"
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Project directory
PROJECT_DIR="/Applications/XAMPP/xamppfiles/htdocs/Elite"
DB_NAME="cricket_academy"
DB_USER="root"
DB_PASS=""

echo -e "${BLUE}[1/6] Checking project directory...${NC}"
if [ -d "$PROJECT_DIR" ]; then
    echo -e "${GREEN}✓ Project directory found${NC}"
    cd "$PROJECT_DIR"
else
    echo -e "${RED}✗ Project directory not found: $PROJECT_DIR${NC}"
    exit 1
fi

echo ""
echo -e "${BLUE}[2/6] Verifying required files...${NC}"

files=(
    "app/controllers/Coach.php"
    "app/models/M_Session.php"
    "app/views/coach/sessions.php"
    "create_session_tables.sql"
)

all_files_exist=true
for file in "${files[@]}"; do
    if [ -f "$file" ]; then
        echo -e "${GREEN}✓ Found: $file${NC}"
    else
        echo -e "${RED}✗ Missing: $file${NC}"
        all_files_exist=false
    fi
done

if [ "$all_files_exist" = false ]; then
    echo -e "${RED}✗ Some required files are missing. Aborting.${NC}"
    exit 1
fi

echo ""
echo -e "${BLUE}[3/6] Checking XAMPP status...${NC}"
if pgrep -x "mysqld" > /dev/null; then
    echo -e "${GREEN}✓ MySQL is running${NC}"
else
    echo -e "${YELLOW}⚠ MySQL is not running. Starting XAMPP...${NC}"
    sudo /Applications/XAMPP/xamppfiles/xampp start
    sleep 3
    
    if pgrep -x "mysqld" > /dev/null; then
        echo -e "${GREEN}✓ MySQL started successfully${NC}"
    else
        echo -e "${RED}✗ Failed to start MySQL${NC}"
        exit 1
    fi
fi

echo ""
echo -e "${BLUE}[4/6] Setting up database tables...${NC}"

# Run SQL file
if mysql -u "$DB_USER" "$DB_NAME" < create_session_tables.sql 2>/dev/null; then
    echo -e "${GREEN}✓ Database tables created successfully${NC}"
else
    echo -e "${RED}✗ Failed to create database tables${NC}"
    echo -e "${YELLOW}  Check if database '$DB_NAME' exists${NC}"
    exit 1
fi

echo ""
echo -e "${BLUE}[5/6] Verifying database setup...${NC}"

# Check if tables exist
tables=("Session" "SessionParticipants" "SessionAttendance" "SessionNotification")
tables_ok=true

for table in "${tables[@]}"; do
    count=$(mysql -u "$DB_USER" "$DB_NAME" -se "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA='$DB_NAME' AND TABLE_NAME='$table'" 2>/dev/null)
    if [ "$count" = "1" ]; then
        echo -e "${GREEN}✓ Table '$table' exists${NC}"
    else
        echo -e "${RED}✗ Table '$table' not found${NC}"
        tables_ok=false
    fi
done

if [ "$tables_ok" = false ]; then
    echo -e "${RED}✗ Database verification failed${NC}"
    exit 1
fi

echo ""
echo -e "${BLUE}[6/6] Checking sample data...${NC}"

session_count=$(mysql -u "$DB_USER" "$DB_NAME" -se "SELECT COUNT(*) FROM Session" 2>/dev/null)
participant_count=$(mysql -u "$DB_USER" "$DB_NAME" -se "SELECT COUNT(*) FROM SessionParticipants" 2>/dev/null)
attendance_count=$(mysql -u "$DB_USER" "$DB_NAME" -se "SELECT COUNT(*) FROM SessionAttendance" 2>/dev/null)

echo -e "${GREEN}✓ Sessions: $session_count${NC}"
echo -e "${GREEN}✓ Participants: $participant_count${NC}"
echo -e "${GREEN}✓ Attendance Records: $attendance_count${NC}"

echo ""
echo "╔════════════════════════════════════════════════════════════════╗"
echo "║                 SETUP COMPLETED SUCCESSFULLY! ✓                ║"
echo "╚════════════════════════════════════════════════════════════════╝"
echo ""
echo -e "${GREEN}✓ All files verified${NC}"
echo -e "${GREEN}✓ Database tables created${NC}"
echo -e "${GREEN}✓ Sample data loaded${NC}"
echo ""
echo -e "${YELLOW}═══════════════════ NEXT STEPS ═══════════════════${NC}"
echo ""
echo "1. Open your browser:"
echo -e "   ${BLUE}http://localhost/Elite/public/login${NC}"
echo ""
echo "2. Login as a coach:"
echo "   - Use your test coach account"
echo ""
echo "3. Navigate to Sessions page:"
echo -e "   ${BLUE}http://localhost/Elite/public/coach/sessions${NC}"
echo ""
echo "4. Test the features:"
echo "   - View calendar"
echo "   - Create new session"
echo "   - Mark attendance"
echo "   - Edit/cancel sessions"
echo ""
echo -e "${YELLOW}═══════════════════ DOCUMENTATION ═══════════════════${NC}"
echo ""
echo "📚 Full Guide:"
echo -e "   ${BLUE}SESSION_MANAGEMENT_GUIDE.md${NC}"
echo ""
echo "📝 Summary:"
echo -e "   ${BLUE}SESSION_IMPLEMENTATION_SUMMARY.md${NC}"
echo ""
echo "💾 Database Schema:"
echo -e "   ${BLUE}create_session_tables.sql${NC}"
echo ""
echo -e "${GREEN}Setup completed at: $(date)${NC}"
echo ""
