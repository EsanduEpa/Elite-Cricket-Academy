<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../app/bootloader.php';
require_once '../app/libraries/Database.php';
require_once '../app/models/Event.php';

echo 'Testing Event Model...' . PHP_EOL;
try {
    $eventModel = new Event();
    $total = $eventModel->getTotalEvents();
    echo 'Total Events: ' . $total . PHP_EOL;
} catch (Exception $e) {
    echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
}

