<?php
    // Front controller: every browser request enters the MVC app from here.
    // .htaccess rewrites URLs like /player/dashboard into index.php?url=player/dashboard.
    require_once '../app/bootloader.php';

    // Core reads the URL, checks access rules, creates the correct controller,
    // and calls the requested method with any URL parameters.
    $ini = new Core();
