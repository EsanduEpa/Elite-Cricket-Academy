<?php
    // Bootloader: central place that loads the minimum files required
    // before any controller/model/view can run.
    //
    // Loading order matters:
    // 1. config.php defines constants such as APPROOT, URLROOT, DB_NAME.
    // 2. Core.php routes the request to the correct controller method.
    // 3. Database.php gives models a safe PDO wrapper.
    // 4. Controller.php gives controllers the model() and view() helpers.
    // 5. session_helper.php starts the session and enforces login/role rules.
    require_once 'config/config.php';
    require_once 'libraries/Core.php';
    require_once 'libraries/Database.php';
    require_once 'libraries/Controller.php';
    require_once 'helpers/session_helper.php';
