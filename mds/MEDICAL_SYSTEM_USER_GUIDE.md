# Medical Record System - User Guide

## Quick Reference Guide for the Updated Medical Record System

---

## For Players

### Adding a Medical Record

1. **Navigate** to Medical page from sidebar
2. **Click** "Add Record" button
3. **Fill in the form:**
   
   **Required Fields:**
   - **Injury Date**: When did the injury happen? (not when you're reporting it)
   - **Reported Date**: Today's date (when you're filing this report)
   - **Happened at Academy**: Select Yes or No
   - **Injury Details**: Describe what happened and symptoms
   - **Diagnosis**: Medical diagnosis or your assessment
   - **Recovery Status**: Choose from:
     - Ongoing (still have symptoms)
     - Recovering (getting better)
     - Recovered (fully healed)
     - Chronic (long-term condition)
   
   **Optional Fields:**
   - **Treatment Given**: Medications, therapy, etc.
   - **Rest Days Needed**: Estimated days to recover (e.g., 7)
   - **Diagnosis Receipt**: Upload medical documents (JPG, PNG, PDF, DOC)

4. **Submit** the form

### Viewing Your Medical Records

Your medical records table shows:
- 📅 **Injury Date**: When the injury occurred
- 📝 **Injury Details**: What happened
- 🏥 **Diagnosis**: Medical assessment
- 💊 **Treatment**: What was done
- 🏫 **At Academy**: Badge showing where injury happened
- ⏰ **Rest Days**: Recommended recovery time
- 📄 **Receipt**: View uploaded documents
- 🔄 **Recovery Status**: Current healing status (color-coded)
- ✅ **Verify Status**: Trainer/Coach verification (pending/verified/rejected)

### Status Badges Explained:

**Recovery Status:**
- 🟡 **Ongoing**: Yellow - Still experiencing symptoms
- 🔵 **Recovering**: Blue - Getting better
- 🟢 **Recovered**: Green - Fully healed
- 🟠 **Chronic**: Orange - Long-term condition

**Verify Status:**
- 🟡 **Pending**: Yellow - Waiting for trainer/coach review
- 🟢 **Verified**: Green - Approved by trainer/coach
- 🔴 **Rejected**: Red - Not approved (can be deleted)

**At Academy:**
- 🟡 **Yes**: Yellow with school icon - Happened at academy
- ⚫ **No**: Gray with home icon - Happened outside academy

### Updating Recovery Status

1. **Click** "Update Status" button on a record
2. **Select** new recovery status
3. **Submit** the update

### Deleting Records

- Only records with **Rejected** verify status can be deleted
- Click the "Delete" button
- Confirm deletion in the popup

---

## For Trainers

### Viewing Injury Reports

Access via: `Sidebar → Injury Reports`

### Medical Reports Table

Displays all player injuries with new information:
- **Injury Date** (when it happened)
- **Reported Date** (when it was filed)
- **Player Name**
- **Injury Details**
- **Diagnosis**
- **At Academy** (yes/no badge)
- **Rest Days Needed**
- **Receipt** (view medical documents)
- **Recovery Status** (color-coded)
- **Verify Status** (pending/verified/rejected)

### Verifying Reports

1. **Click** "Verify" button on a record
2. **Review** the injury details
3. **Select** verification status:
   - Verified (approve the report)
   - Rejected (decline the report)
   - Pending (needs more info)
4. **Add** optional comments
5. **Submit** verification

### Using Medical Receipts

- Click "View" button in Receipt column
- Opens document in new tab
- Verify authenticity of medical claims
- Check dates match reported information

### Identifying Academy Injuries

- Look for yellow badges in "At Academy" column
- Helps track safety issues at the facility
- Important for incident reports and insurance

---

## For Coaches

### Viewing Health & Injury Reports

Access via: `Sidebar → Health & Injury`

### Same Features as Trainers:
- View all player medical records
- Verify injury reports
- Access medical receipts
- Track academy vs outside injuries
- Monitor rest day recommendations

### Verification Process (Same as Trainers)

1. Click "Verify" button
2. Review details
3. Select status (Verified/Rejected/Pending)
4. Add comments (optional)
5. Submit

---

## Common Use Cases

### Scenario 1: Player Injured at Training
```
1. Player goes to Medical page
2. Clicks "Add Record"
3. Fills in:
   - Injury Date: [Date of training]
   - Reported Date: [Today]
   - Happened at Academy: YES
   - Details: "Twisted ankle during fielding drill"
   - Diagnosis: "Minor ankle sprain"
   - Rest Days: 7
4. Submits form
5. Status shows as "Pending" verification
```

### Scenario 2: Player Has Medical Appointment
```
1. Player visits doctor
2. Gets diagnosis and prescription
3. Goes to Medical page → Add Record
4. Fills in details
5. Uploads prescription/receipt photo
6. Submits
7. Trainer/Coach can view receipt to verify
```

### Scenario 3: Trainer Verifies Report
```
1. Trainer checks Injury Reports
2. Sees new report from player
3. Clicks "View" on receipt to check documentation
4. Clicks "Verify" button
5. Selects "Verified"
6. Adds comment: "Rest for 7 days, reassess next week"
7. Submits verification
8. Player sees status change to "Verified"
```

### Scenario 4: Tracking Academy Injuries
```
1. Coach/Trainer views Health reports
2. Filters for "At Academy = Yes" badges
3. Identifies pattern of similar injuries
4. Takes preventive action (e.g., better warm-up routine)
```

---

## Tips & Best Practices

### For Players:
- ✅ Report injuries as soon as possible
- ✅ Be honest about where injury occurred
- ✅ Upload medical receipts when available
- ✅ Update recovery status regularly
- ✅ Estimate rest days realistically
- ❌ Don't hide academy injuries
- ❌ Don't exaggerate severity

### For Trainers/Coaches:
- ✅ Review reports promptly
- ✅ Check uploaded receipts for authenticity
- ✅ Track patterns in academy injuries
- ✅ Use data to improve safety measures
- ✅ Provide helpful comments when verifying
- ❌ Don't reject without explanation
- ❌ Don't ignore repeated injuries

---

## File Upload Guidelines

### Accepted File Types:
- ✅ JPG, JPEG (photos)
- ✅ PNG (images)
- ✅ PDF (documents)
- ✅ DOC, DOCX (Word documents)

### Maximum File Size:
- Recommended: 5 MB or less
- Larger files may fail to upload

### What to Upload:
- Medical prescriptions
- Doctor's notes
- X-ray reports
- Lab results
- Pharmacy receipts
- Insurance forms
- Treatment plans

### Privacy Notice:
- Uploaded files are stored securely
- Only accessible to player, trainers, and coaches
- Used for verification purposes only

---

## Troubleshooting

### File Upload Not Working:
- Check file size (should be under 5 MB)
- Verify file type is accepted
- Ensure stable internet connection
- Try renaming file (remove special characters)

### Can't See Medical Records:
- Ensure you're logged in as correct user
- Refresh the page
- Check if any records exist
- Contact admin if issue persists

### Verify Button Not Working:
- Check browser console for errors
- Ensure JavaScript is enabled
- Try different browser
- Contact developer

### Receipt Link Shows 404:
- File may not have uploaded successfully
- Check if file exists in uploads folder
- Contact admin to restore file

---

## Support

For technical issues or questions:
- Contact Academy Administrator
- Check documentation: MEDICAL_SYSTEM_UPDATE.md
- Report bugs to development team

---

**Last Updated:** October 22, 2025  
**Version:** 2.0  
**System:** Elite Cricket Academy Management System
