USE cricket_academy;

-- Add ProductImage column to Product table
ALTER TABLE Product 
ADD COLUMN ProductImage VARCHAR(255) DEFAULT NULL 
COMMENT 'Relative path to product image (e.g., uploads/shop_product/product_1_123456789.jpg)';

-- Add index for faster queries
CREATE INDEX idx_product_image ON Product(ProductImage);

-- Display success message
SELECT '✅ ProductImage column added successfully!' AS Status;

-- Show the updated table structure
DESCRIBE Product;
