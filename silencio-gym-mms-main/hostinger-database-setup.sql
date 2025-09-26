-- Hostinger Database Setup for Silencio Gym Management System
-- Run this SQL script in your Hostinger MySQL database

-- Create database (if not exists)
-- CREATE DATABASE IF NOT EXISTS your_database_name;
-- USE your_database_name;

-- Note: Replace 'your_database_name' with your actual database name from Hostinger

-- The Laravel migrations will create all necessary tables
-- This file is for reference and manual database setup if needed

-- Key tables that will be created by migrations:
-- - users (admin and employee accounts)
-- - members (gym member information)
-- - attendances (check-in/check-out records)
-- - payments (payment transactions)
-- - rfid_logs (RFID activity logs)
-- - active_sessions (current active sessions)
-- - membership_periods (membership history)
-- - membership_plans (available membership plans)
-- - password_reset_tokens (password reset functionality)
-- - sessions (Laravel session storage)
-- - cache (Laravel cache storage)
-- - jobs (Laravel queue jobs)
-- - failed_jobs (failed queue jobs)
-- - migrations (Laravel migration tracking)

-- Important: Make sure your Hostinger MySQL user has the following privileges:
-- - CREATE
-- - DROP
-- - ALTER
-- - INSERT
-- - UPDATE
-- - DELETE
-- - SELECT
-- - INDEX
-- - REFERENCES

-- To check your current user privileges:
-- SHOW GRANTS FOR CURRENT_USER();

-- If you need to grant additional privileges (contact Hostinger support if needed):
-- GRANT ALL PRIVILEGES ON your_database_name.* TO 'your_username'@'localhost';
-- FLUSH PRIVILEGES;
