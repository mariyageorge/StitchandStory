-- Fix Orders Table - Add missing created_at column
-- Run this in phpMyAdmin or MySQL command line if needed

USE stitch_and_story;

-- Check and add created_at column if it doesn't exist
ALTER TABLE orders 
ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER order_status;

-- Verify the table structure
SHOW COLUMNS FROM orders;

-- Success message
SELECT 'Orders table updated successfully!' as Status;

