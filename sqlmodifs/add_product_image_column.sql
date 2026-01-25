-- ============================================
-- PRODUCT IMAGE COLUMN - DATABASE MIGRATION
-- ============================================
-- Add ProductImage column to Product table
-- This migration adds a column to store the product image path

USE cricket_academy;

-- ============================================
-- OPTION 1: Add New ProductImage Column (Recommended)
-- ============================================
-- Note: Product table already has ImageURL column
-- This adds a new ProductImage column with better naming convention

ALTER TABLE Product 
ADD COLUMN IF NOT EXISTS ProductImage VARCHAR(255) DEFAULT NULL 
COMMENT 'Relative path to product image (e.g., uploads/shop_product/product_1_123456789.jpg)'
AFTER ImageURL;

-- ============================================
-- OPTION 2: Rename Existing ImageURL to ProductImage
-- ============================================
-- Uncomment if you want to rename the existing column instead

-- ALTER TABLE Product 
-- CHANGE COLUMN ImageURL ProductImage VARCHAR(255) DEFAULT NULL
-- COMMENT 'Relative path to product image (e.g., uploads/product_images/product_1_123456789.jpg)';

-- ============================================
-- Add Index for Faster Queries (Optional but Recommended)
-- ============================================

CREATE INDEX IF NOT EXISTS idx_product_image ON Product(ProductImage);

-- ============================================
-- VERIFICATION QUERIES
-- ============================================

-- Check if column was added successfully
SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT, COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'cricket_academy'
  AND TABLE_NAME = 'Product'
  AND COLUMN_NAME IN ('ImageURL', 'ProductImage');

-- Display success message
SELECT '✅ ProductImage column added successfully!' AS Status;

-- Show the updated table structure
DESCRIBE Product;

-- Count existing product images
SELECT 
    COUNT(*) as total_products,
    COUNT(ImageURL) as products_with_old_imageurl,
    COUNT(ProductImage) as products_with_new_image,
    COUNT(*) - COUNT(ProductImage) as products_without_new_image
FROM Product;

-- ============================================
-- MIGRATION: Copy ImageURL to ProductImage (Optional)
-- ============================================
-- Uncomment if you want to copy existing ImageURL values to ProductImage

-- UPDATE Product 
-- SET ProductImage = ImageURL 
-- WHERE ImageURL IS NOT NULL AND ProductImage IS NULL;

-- ============================================
-- ROLLBACK (If you want to remove the column)
-- ============================================

-- USE cricket_academy;
-- ALTER TABLE Product DROP COLUMN ProductImage;
-- DROP INDEX IF EXISTS idx_product_image ON Product;

-- ============================================
-- SAMPLE DATA (For testing purposes)
-- ============================================

-- Update a test product with sample product image
-- UPDATE Product 
-- SET ProductImage = 'uploads/product_images/product_1_1234567890.jpg'
-- WHERE ProductID = 1;

-- ============================================
-- DIRECTORY SETUP INSTRUCTIONS
-- ============================================
-- 1. Create directory: /public/uploads/shop_product/
-- 2. Set permissions: chmod -R 775 /public/uploads/shop_product/
-- 3. Set ownership (macOS): sudo chown -R daemon:admin /public/uploads/shop_product/
-- 4. Set ownership (Linux): sudo chown -R www-data:www-data /public/uploads/shop_product/

-- ============================================
-- NOTES
-- ============================================
-- * Product table already has ImageURL column for backward compatibility
-- * ProductImage is the new standardized column for product images
-- * Both columns can coexist during migration period
-- * Consider deprecating ImageURL after full migration
-- * Image path format: uploads/shop_product/product_{productId}_{timestamp}.{ext}
-- * Accepted formats: JPG, JPEG, PNG
-- * Maximum file size: 5MB (recommended for product images)

-- ============================================
-- SUCCESS!
-- ============================================
-- Column ProductImage has been added to the Product table
-- You can now start uploading product images!
