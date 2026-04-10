-- ============================================================
--  learning_crud_setup.sql
--  Run this file ONCE to set up everything needed for
--  learning_crud.php
--
--  HOW TO RUN:
--  Option A — phpMyAdmin
--    1. Open http://localhost/phpmyadmin
--    2. Click "Import" tab at the top
--    3. Choose this file → click Go
--
--  Option B — MySQL command line (XAMPP terminal)
--    mysql -u root -p < learning_crud_setup.sql
-- ============================================================


-- ── STEP 1: CREATE DATABASE (if it does not exist yet) ──────
-- A database is a container that holds many tables.
-- IF NOT EXISTS means: skip this line if the database already exists.
CREATE DATABASE IF NOT EXISTS cricket_academy;

-- ── STEP 2: SELECT THE DATABASE ─────────────────────────────
-- All SQL commands after this line will run inside cricket_academy.
USE cricket_academy;

-- ── STEP 3: DROP OLD TABLE (so we start fresh each time) ────
-- DROP TABLE IF EXISTS removes the table only if it already exists.
-- Useful when you want to reset the demo data.
-- Remove this line if you want to KEEP existing rows.
DROP TABLE IF EXISTS learning_names;

-- ── STEP 4: CREATE THE TABLE ─────────────────────────────────
--
--  Column breakdown:
--  ┌────────────┬──────────────────────────────────────────────┐
--  │ id         │ Auto-increasing unique number. MySQL fills   │
--  │            │ this in — you never insert it manually.      │
--  ├────────────┼──────────────────────────────────────────────┤
--  │ name       │ The person's name. Up to 100 characters.     │
--  │            │ NOT NULL = the field cannot be left empty.   │
--  ├────────────┼──────────────────────────────────────────────┤
--  │ created_at │ Date + time the row was inserted.            │
--  │            │ MySQL fills this in automatically.           │
--  └────────────┴──────────────────────────────────────────────┘
CREATE TABLE learning_names (
    id         INT          AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
);

-- ── STEP 5: INSERT SAMPLE ROWS ───────────────────────────────
-- INSERT INTO table (column1, column2) VALUES (value1, value2);
-- We only supply 'name'. MySQL handles id and created_at automatically.

-- Single row insert (basic form)
INSERT INTO learning_names (name) VALUES ('Kasun Perera');

-- You can also insert multiple rows in one statement:
INSERT INTO learning_names (name) VALUES
    ('Nuwan Silva'),
    ('Ashan Fernando'),
    ('Chamara Jayasinghe'),
    ('Dinesh Bandara');

-- ── STEP 6: VERIFY — SELECT everything back ─────────────────
-- After running the inserts, this SELECT shows all inserted rows.
-- You should see 10 rows with ids 1–10.
SELECT * FROM learning_names ORDER BY id ASC;
