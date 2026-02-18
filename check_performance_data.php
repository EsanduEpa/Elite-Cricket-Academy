<?php
// Check if there's performance data in the database
try {
    $pdo = new PDO('mysql:host=localhost;dbname=cricket_academy', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== Checking Performance Data ===\n\n";
    
    // Check playermatchperformance table
    $stmt = $pdo->query('SELECT PlayerID, COUNT(*) as matches FROM playermatchperformance GROUP BY PlayerID LIMIT 10');
    $players = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($players)) {
        echo "⚠️ No performance data found in playermatchperformance table\n\n";
    } else {
        echo "✓ Players with performance data:\n";
        foreach ($players as $player) {
            echo "  Player ID {$player['PlayerID']}: {$player['matches']} matches\n";
        }
        echo "\n";
    }
    
    // Check a specific player's stats (ID 1)
    $stmt = $pdo->prepare('SELECT 
        COUNT(*) as MatchesPlayed,
        SUM(RunsScored) as TotalRuns,
        SUM(BallsFaced) as TotalBalls,
        MAX(RunsScored) as HighestScore,
        SUM(WicketsTaken) as Wickets,
        SUM(OversBowled) as TotalOvers,
        SUM(RunsConceded) as TotalRunsConceded
        FROM playermatchperformance
        WHERE PlayerID = 1');
    $stmt->execute();
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "=== Player ID 1 Statistics ===\n";
    if ($stats['MatchesPlayed'] > 0) {
        $battingAvg = $stats['TotalRuns'] / $stats['MatchesPlayed'];
        $strikeRate = $stats['TotalBalls'] > 0 ? ($stats['TotalRuns'] / $stats['TotalBalls']) * 100 : 0;
        $economyRate = $stats['TotalOvers'] > 0 ? $stats['TotalRunsConceded'] / $stats['TotalOvers'] : 0;
        
        echo "Matches: {$stats['MatchesPlayed']}\n";
        echo "Total Runs: {$stats['TotalRuns']}\n";
        echo "Batting Average: " . round($battingAvg, 2) . "\n";
        echo "Strike Rate: " . round($strikeRate, 2) . "\n";
        echo "Highest Score: {$stats['HighestScore']}\n";
        echo "Wickets: {$stats['Wickets']}\n";
        echo "Economy Rate: " . round($economyRate, 2) . "\n";
    } else {
        echo "⚠️ No data for Player ID 1\n";
    }
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
