-- =====================================================
-- Fix Medical Receipt URLs - Remove 'public/' Prefix
-- Date: October 22, 2025
-- Description: Update existing DiagnosisReceiptURL to remove 'public/' prefix
-- =====================================================

-- Update all records that have 'public/' in the path
UPDATE PlayerMedicalRecord 
SET DiagnosisReceiptURL = REPLACE(DiagnosisReceiptURL, 'public/', '')
WHERE DiagnosisReceiptURL LIKE 'public/%';

-- Verify the changes
SELECT RecordID, PlayerID, DiagnosisReceiptURL 
FROM PlayerMedicalRecord 
WHERE DiagnosisReceiptURL IS NOT NULL 
ORDER BY RecordID DESC 
LIMIT 10;

-- =====================================================
-- Expected Result:
-- Old: public/uploads/medical_receipts/receipt_123_1729603200.jpg
-- New: uploads/medical_receipts/receipt_123_1729603200.jpg
-- =====================================================
