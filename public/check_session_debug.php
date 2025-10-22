<?php
session_start();
header('Content-Type: application/json');
echo json_encode([
    'session_id' => session_id(),
    'user_id' => $_SESSION['user_id'] ?? 'NOT SET',
    'username' => $_SESSION['username'] ?? 'NOT SET',
    'role' => $_SESSION['role'] ?? 'NOT SET',
    'all_session_data' => $_SESSION
], JSON_PRETTY_PRINT);
