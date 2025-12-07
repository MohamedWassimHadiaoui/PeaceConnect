-- Add avatar column to user table
-- Run this SQL in phpMyAdmin or your database tool

ALTER TABLE `user` 
ADD COLUMN `avatar` VARCHAR(255) NULL DEFAULT NULL AFTER `role`;

-- This adds:
-- - avatar: Stores the filename/path of the user's uploaded avatar image
-- - NULL means no custom avatar (will use default)

