# Elite Cricket Academy

Elite Cricket Academy is a PHP and MySQL web application for managing a cricket academy. It covers user authentication, role-based access, player and staff workflows, coaching sessions, performance tracking, tournament features, shop operations, notifications, and payment-related flows.

The application is structured as a lightweight MVC-style system. The public entry point is in `public/`, shared bootstrap code lives in `app/`, and database/schema helper scripts are stored at the repository root and under `_non_mvc/`.

## Overview

The system supports these user roles:

- Admin
- Coach
- Trainer
- Player
- ShopEmployee

Common areas covered by the application include:

- login, registration, sessions, and role-based redirects
- player and coach dashboards
- training sessions, attendance, and scheduling
- performance statistics and match-related records
- nutrition, medical, feedback, and notifications
- tournament management and recommendations
- shop/product and payment flows

## Technology Stack

- PHP
- MySQL
- Apache/XAMPP
- Bootstrap-based front-end pages
- JavaScript for interactive dashboard and form behavior

## Project Structure

- `public/` - web entry point, assets, and public-facing routes
- `app/` - bootstrap, config, controllers, models, views, helpers, and libraries
- `app/bootloader.php` - central bootstrap that loads config, routing, database, controller, and session helpers
- `app/config/config.php` - application constants such as database settings and base URL
- `app/controllers/` - request handlers for each feature area
- `app/models/` - database-backed business logic and domain models
- `app/views/` - role-based views for admin, coach, player, staff, trainer, and shared pages
- `scripts/` - maintenance and utility scripts
- `payhere_test/` - payment gateway test pages
- `_non_mvc/` - legacy/support scripts and standalone checks

## Main Features

- Authentication and role-based access control
- Coach, player, trainer, admin, and shop workflows
- Session scheduling, attendance, and reminders
- Player performance and statistics tracking
- Nutrition, medical, and feedback modules
- Tournament management and recommendations
- Shop and finance-related features
- Notification and email support

## Local Setup

1. Install and start XAMPP or an equivalent Apache + MySQL stack.
2. Create a MySQL database named `cricket_academy`.
3. Import the SQL schema and seed files used by the module you want to run.
4. Update `app/config/config.php` if your local host, database credentials, or base URL differ from the default values.
5. Open `http://localhost/Elite/public` in your browser.

The default configuration expects:

- database host: `localhost`
- database user: `root`
- database password: empty
- database name: `cricket_academy`
- base URL: `http://localhost/Elite`

## Development Notes

- Database access goes through the custom PDO wrapper in `app/libraries/Database.php`.
- Authentication helpers are loaded from `app/helpers/session_helper.php`.
- `DEV_MODE` is disabled by default and should stay off for normal use.
- The codebase uses SQL scripts rather than a migration framework.
- Several modules have standalone verification or test pages in `public/` and `_non_mvc/`.

## Useful Paths

- `public/index.php` - public entry point
- `app/controllers/Login.php` - authentication flow
- `app/controllers/Home.php` - default landing/dashboard logic
- `app/controllers/Coach.php` - coach features
- `app/controllers/Player.php` - player features
- `app/controllers/Admin.php` - admin features

## Setup Helper

The repository includes `setup_sessions.sh` for automating session-module setup in Unix-like environments. If you are on Windows, run the equivalent database import and verification steps manually through XAMPP and MySQL.

## Troubleshooting

- If the application shows a blank page, check the Apache error log first.
- Confirm that MySQL is running and the `cricket_academy` database exists.
- Verify `URLROOT` in `app/config/config.php` matches the folder name you are using locally.
- If authentication behaves unexpectedly, check session state and the role stored in the login flow.

## Notes for Contributors

- Keep changes consistent with the existing MVC structure.
- Prefer updating the relevant controller, model, or view rather than adding new ad hoc entry points.
- Add or update SQL scripts when database structures change.

