<!DOCTYPE html>
<html>
<head>
    <title>Test Sessions API</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        pre { background: #f4f4f4; padding: 15px; border-radius: 5px; overflow-x: auto; }
        .success { color: green; }
        .error { color: red; }
        h2 { margin-top: 30px; }
    </style>
</head>
<body>
    <h1>🧪 Sessions API Test</h1>
    
    <h2>1. Current Session Info</h2>
    <pre><?php
    session_start();
    echo "Session ID: " . session_id() . "\n";
    echo "User ID: " . ($_SESSION['user_id'] ?? 'NOT SET') . "\n";
    echo "Username: " . ($_SESSION['user_name'] ?? 'NOT SET') . "\n";
    echo "Role: " . ($_SESSION['user_role'] ?? 'NOT SET') . "\n";
    echo "\nFull Session Data:\n";
    print_r($_SESSION);
    ?></pre>
    
    <h2>2. Database Check</h2>
    <pre><?php
    require_once '../app/config/config.php';
    require_once '../app/libraries/Database.php';
    
    $db = new Database();
    $db->query('SELECT SessionID, Name, `Date`, CoachOrTrainerID FROM `Session` ORDER BY SessionID DESC LIMIT 5');
    $sessions = $db->resultSet();
    
    echo "Sessions in database:\n";
    print_r($sessions);
    ?></pre>
    
    <h2>3. API Test (via JavaScript)</h2>
    <button onclick="testAPI()">Test API</button>
    <pre id="apiResult">Click button to test...</pre>
    
    <script>
    async function testAPI() {
        const resultDiv = document.getElementById('apiResult');
        resultDiv.textContent = 'Loading...';
        
        try {
            const response = await fetch('<?php echo URLROOT; ?>/coach/get_sessions_list');
            const data = await response.json();
            
            resultDiv.textContent = JSON.stringify(data, null, 2);
            
            if (data.success && data.sessions.length > 0) {
                resultDiv.style.color = 'green';
            } else if (data.success && data.sessions.length === 0) {
                resultDiv.style.color = 'orange';
                resultDiv.textContent += '\n\n⚠️ API works but returned 0 sessions for coach ID: ' + data.coachId;
            } else {
                resultDiv.style.color = 'red';
            }
        } catch (error) {
            resultDiv.textContent = '❌ Error: ' + error.message;
            resultDiv.style.color = 'red';
        }
    }
    </script>
    
    <h2>4. Direct Model Test</h2>
    <pre><?php
    require_once '../app/models/M_Session.php';
    
    $sessionModel = new M_Session();
    $coachId = $_SESSION['user_id'] ?? 4; // Default to 4 for testing
    
    echo "Testing getSessionsByCoach() with coach_id: $coachId\n\n";
    $result = $sessionModel->getSessionsByCoach($coachId);
    
    echo "Result:\n";
    print_r($result);
    
    if (count($result) > 0) {
        echo "\n✅ SUCCESS! Found " . count($result) . " session(s)";
    } else {
        echo "\n⚠️ No sessions found for coach ID: $coachId";
    }
    ?></pre>
    
    <p><a href="<?php echo URLROOT; ?>/coach/sessions">← Back to Sessions Page</a></p>
</body>
</html>
