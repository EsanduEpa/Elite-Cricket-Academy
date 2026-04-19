<?php
    // Database connection settings used by app/libraries/Database.php.
    // In viva terms: every model reaches MySQL through these constants.
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'cricket_academy');

    // Absolute filesystem path to the /app folder.
    // Controllers use APPROOT when loading views, libraries, and services.
    define('APPROOT', dirname(dirname(__FILE__)));

    // Base URL used when building links, form actions, redirects, CSS, and JS paths.
    define('URLROOT', 'http://localhost/Elite');

    // Website name displayed in views where needed.
    define('SITENAME', 'Elite-Cricket-Academy');
    
    // Authentication/session settings.
    // DEV_MODE must stay false for viva/demo so users cannot bypass login.
    define('DEV_MODE', false);
    define('SESSION_TIMEOUT_SECONDS', 1800); // 30 minutes of inactivity
