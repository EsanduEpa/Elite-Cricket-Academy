# Elite Cricket Academy Codebase Viva Guide

This guide is a study map for the PHP MVC project. It explains how the main files connect and records the review/refactor progress.

## Project Structure

- `public/index.php`: front controller. Every web request enters the app here.
- `app/bootloader.php`: loads config, core router, database wrapper, controller base class, and session helper.
- `app/config/`: application configuration such as database name, base URL, session timeout, and mail settings.
- `app/libraries/`: reusable infrastructure classes such as router, database wrapper, PayHere, Mailer, and service classes.
- `app/helpers/`: shared procedural helper functions, mainly session/auth helpers.
- `app/controllers/`: receives requests, validates input, calls models/services, and chooses views.
- `app/models/`: database query layer. Models use the `Database` PDO wrapper.
- `app/views/`: PHP/HTML templates. Views should render data and avoid business logic where possible.
- `public/css/`: external CSS files loaded by views.
- `public/js/`: external JavaScript files loaded by views.
- `scripts/`: command-line or test helper scripts, such as reminder sending.
- `*.sql`: schema dumps, seed data, and database migration/change scripts.

## Overall Execution Flow

1. Browser requests a URL such as `http://localhost/Elite/player/dashboard`.
2. `.htaccess` rewrites that URL to `public/index.php?url=player/dashboard`.
3. `public/index.php` loads `app/bootloader.php`.
4. `bootloader.php` loads config, core router, database wrapper, controller base class, and session helper.
5. `Core.php` splits the URL into controller, method, and parameters.
6. `Core.php` calls `enforceRouteAccess()` from `session_helper.php`.
7. If the route is protected, the helper checks login, session timeout, and allowed role.
8. The controller is created and the requested method is called.
9. The controller reads request data, calls models/services, sets flash/session messages, and loads a view.
10. The view renders HTML and links external CSS/JS from `public/`.

## Reviewed Files

### `public/index.php`

Purpose: single entry point for the MVC application.

Viva answer: "The front controller loads the bootloader, then creates `Core`, which routes the request to the correct controller method."

### `app/bootloader.php`

Purpose: loads the minimum required files for the app to run.

Viva answer: "The bootloader connects the configuration, router, database wrapper, base controller, and session helper before any controller logic starts."

### `app/config/config.php`

Purpose: stores database constants, app root paths, base URL, site name, and session timeout.

Viva answer: "Models use the database constants through `Database.php`, and views/controllers use `URLROOT` to build links and redirects."

### `app/libraries/Core.php`

Purpose: parses the URL, resolves the controller/method, checks route access, and calls the method.

Viva answer: "This is the router. It turns `/player/dashboard` into `Player::dashboard()` and blocks unauthorized routes before creating the controller."

### `app/libraries/Controller.php`

Purpose: base class for controllers. Provides `model()` and `view()` helpers.

Viva answer: "Controllers extend this class so they can load models and views using a consistent pattern."

### `app/libraries/Database.php`

Purpose: PDO wrapper used by models for prepared statements, bound values, result fetching, and transactions.

Viva answer: "It reduces repeated database code and helps prevent SQL injection because values are bound to prepared statements."

### `app/helpers/session_helper.php`

Purpose: starts sessions and controls flash messages, login checks, role checks, redirects, JSON auth errors, and inactivity timeout.

Viva answer: "It is the central security helper. It decides whether a route is public, whether the user is logged in, and whether the user's role is allowed."

### `app/controllers/Login.php`

Purpose: handles login form display, credential validation, session creation, role-based dashboard redirect, logout, and password reset.

Review note: debug HTML was removed before redirects because output before `header()` can cause "headers already sent" errors.

### `app/controllers/Home.php`

Purpose: builds the public home page by collecting programs, coaches, facilities, events, testimonials, contact data, and stats.

Viva answer: "The home controller gathers public summary data from several models and sends it to `v_home.php`."

### `app/controllers/Notifications.php`

Purpose: JSON API for the shared notification dropdown.

Viva answer: "The navbar JavaScript calls this controller to list, mark read, delete, and clear notifications without reloading the page."

### `app/controllers/ReminderTasks.php`

Purpose: browser heartbeat endpoint that runs due session and tournament reminders while a user is logged in.

Viva answer: "Because this demo cannot rely on cron on every machine, the frontend heartbeat calls this endpoint hourly."

### `app/controllers/Playerslots.php`

Purpose: player session/facility booking controller.

Viva answer: "It protects routes for players only, loads available slots, creates bookings through `M_SlotPlayer`, sends in-app notifications, and allows eligible cancellations."

### `app/models/M_Notification.php`

Purpose: database layer for in-app notifications.

Viva answer: "It creates notifications, prevents duplicate order/session notifications, finds users by role, and supports dropdown actions like read/delete/clear."

### `app/models/M_Email.php`

Purpose: logs email attempts and prevents duplicate payment/session emails.

Viva answer: "Email sending is non-blocking; this model records whether emails were sent or failed so the app can continue safely."

### `app/libraries/PayHere.php`

Purpose: central PayHere sandbox configuration and hash/signature helpers.

Viva answer: "Checkout uses `buildHash()`, and PayHere server callbacks are verified with `verifyNotify()` before marking payments successful."

### `app/libraries/Mailer.php`

Purpose: raw SMTP mailer using PHP sockets with STARTTLS and AUTH LOGIN.

Viva answer: "It sends email without external libraries and returns false instead of crashing the app when SMTP fails."

### `app/libraries/PaymentEmailService.php`

Purpose: builds and sends successful payment receipt emails.

Viva answer: "It validates the recipient, prevents duplicate receipts, sends the email, and logs success/failure."

### `app/views/v_login.php` and `public/js/login.js`

Purpose: login form and forgot-password modal.

Refactor note: inline JavaScript was moved into `public/js/login.js`.

### `app/views/v_register.php` and `public/js/register.js`

Purpose: registration form, membership plan selection, and client-side validation.

Refactor note: membership-plan modal JavaScript was moved out of the PHP view into `public/js/register.js`.

## Current Risks / Cleanup Notes

- Several views still contain inline `<style>`, inline `<script>`, and many `style=""` attributes. These should be reviewed gradually to avoid breaking dynamic UI.
- Some test files under `public/` contain inline CSS/HTML output. They are development/test utilities, not normal application pages.
- Email template HTML intentionally keeps inline styles because email clients often require inline CSS.
- SQL files are numerous. For viva, focus on the currently imported dump and the latest change scripts unless asked about history.

## Viva Questions and Short Answers

- What architecture does this project use?
  It uses a simple PHP MVC architecture: controllers handle requests, models handle database queries, and views render HTML.

- How does routing work?
  `.htaccess` sends URLs to `public/index.php`, then `Core.php` maps the URL to a controller and method.

- How is login protected?
  `session_helper.php` checks if `user_id` and `user_role` exist in the session, then checks whether the role can access the requested controller.

- How does auto logout work?
  Each request updates `last_activity`. If the user is inactive longer than `SESSION_TIMEOUT_SECONDS`, the session is destroyed and the user is sent home.

- Why use prepared statements?
  Prepared statements bind user input separately from SQL, which helps prevent SQL injection.

- Why are email failures non-blocking?
  Payment or booking success should not be reversed just because SMTP fails. The system logs the failure and continues.

- How does PayHere verification work?
  The app compares PayHere's callback signature with its own calculated signature and only accepts status code `2` as successful.

- Why separate CSS/JS from PHP views?
  It makes the project easier to maintain, easier to explain, and keeps views focused on rendering data instead of holding behavior/style logic.
