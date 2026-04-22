<?php
class Performance extends Controller {
    
    private $userModel;
    private $achievementModel;
    private $productModel;
    
    public function __construct() {
        // Check authentication for all performance pages
        requireAuth(['Player']);
        // Enable database for achievement functionality
        $this->userModel = $this->model('M_Users');
        $this->achievementModel = $this->model('M_Achievement');
    }
    
    // Main performance dashboard
    public function index() {
        $perfModel = $this->model('M_Performance');
        $playerId = $_SESSION['user_id'] ?? 6;
        
        $data = [
            'title' => 'Performance History',
            'player' => $this->getPlayerData(),
            'practiceMatches' => $this->getPracticeMatches(),
            'tournaments' => $this->getTournaments(),
            'performanceSummary' => $this->getPerformanceStats(),
            'performanceStats' => $this->getDetailedPerformanceStats(),
            'battingStats' => $this->getBattingStats(),
            'bowlingStats' => $this->getBowlingStats(),
            'achievements' => $this->getPlayerAchievements(),
            'availableMatches' => $perfModel->getAvailableMatches(50),
            'playerPerformanceRecords' => $perfModel->getPerformanceStatistics($playerId, true),
            'pendingPerformanceRecords' => $perfModel->getPendingPerformanceStatistics($playerId),
        ];
        $this->view('player/performance', $data);
    }

    public function match_history() {
        // Match history is now part of the main Performance page.
        redirect('performance#matchHistorySection');
    }
    
    // Add Achievement
    public function addAchievement() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('performance');
            return;
        }

        $data = [
            'player_id' => $_SESSION['user_id'] ?? 6,
            'date' => trim($_POST['date'] ?? ''),
            'match_name' => trim($_POST['match_name'] ?? ''),
            'tournament' => trim($_POST['tournament'] ?? ''),
            'achievement' => trim($_POST['achievement'] ?? ''),
            'verified_status' => 'pending'
        ];

        $errors = [];
        if (empty($data['date'])) {
            $errors[] = 'Date is required';
        }
        if (empty($data['match_name'])) {
            $errors[] = 'Match name is required';
        }
        if (empty($data['tournament'])) {
            $errors[] = 'Tournament is required';
        }
        if (empty($data['achievement'])) {
            $errors[] = 'Achievement description is required';
        }

        if (!empty($errors)) {
            flash('performance_message', implode('<br>', $errors), 'alert alert-danger');
            redirect('performance');
            return;
        }

        try {
            $achievementId = $this->achievementModel->addAchievement($data);

            if ($achievementId) {
                flash('performance_message', 'Achievement added successfully. It will be reviewed by the coaching staff.');
            } else {
                flash('performance_message', 'Failed to add achievement. Please try again.', 'alert alert-danger');
            }
        } catch (Exception $e) {
            error_log("Achievement creation error: " . $e->getMessage());
            flash('performance_message', 'Database error occurred. Please try again later.', 'alert alert-danger');
        }

        redirect('performance');
    }

    // Edit Achievement
    public function editAchievement() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('performance');
            return;
        }

        $playerId = $_SESSION['user_id'] ?? 6;
        $data = [
            'achievement_id' => filter_input(INPUT_POST, 'achievement_id', FILTER_VALIDATE_INT),
            'player_id' => $playerId,
            'date' => trim($_POST['date'] ?? ''),
            'match_name' => trim($_POST['match_name'] ?? ''),
            'tournament' => trim($_POST['tournament'] ?? ''),
            'achievement' => trim($_POST['achievement'] ?? ''),
            'verified_status' => 'pending'
        ];

        $errors = [];
        if (empty($data['achievement_id'])) {
            $errors[] = 'Achievement ID is required';
        }
        if (empty($data['date'])) {
            $errors[] = 'Date is required';
        }
        if (empty($data['match_name'])) {
            $errors[] = 'Match name is required';
        }
        if (empty($data['tournament'])) {
            $errors[] = 'Tournament is required';
        }
        if (empty($data['achievement'])) {
            $errors[] = 'Achievement description is required';
        }

        $existingAchievement = null;
        if (empty($errors)) {
            $existingAchievement = $this->achievementModel->getAchievementById((int) $data['achievement_id']);
            if (!$existingAchievement || (int) ($existingAchievement->PlayerID ?? 0) !== (int) $playerId) {
                $errors[] = 'Achievement not found or access denied';
            } elseif (($existingAchievement->VerifiedStatus ?? 'pending') !== 'pending') {
                $errors[] = 'Only pending achievements can be edited';
            }
        }

        if (!empty($errors)) {
            flash('performance_message', implode('<br>', $errors), 'alert alert-danger');
            redirect('performance');
            return;
        }

        try {
            $success = $this->achievementModel->updateAchievement($data);

            if ($success) {
                flash('performance_message', 'Achievement updated successfully.');
            } else {
                flash('performance_message', 'Failed to update achievement. Please try again.', 'alert alert-danger');
            }
        } catch (Exception $e) {
            error_log("Achievement update error: " . $e->getMessage());
            flash('performance_message', 'Database error occurred. Please try again later.', 'alert alert-danger');
        }

        redirect('performance');
    }

    // Get Achievement (AJAX method)
    public function getAchievement() {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $achievementId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            $playerId = $_SESSION['user_id'] ?? 6; // Default to 6 for testing

            header('Content-Type: application/json');

            if (!$achievementId) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid achievement ID'
                ]);
                return;
            }

            try {
                $achievement = $this->achievementModel->getAchievementById($achievementId);
                
                if ($achievement && $achievement->PlayerID == $playerId) {
                    echo json_encode([
                        'success' => true,
                        'achievement' => $achievement
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Achievement not found or access denied'
                    ]);
                }
            } catch (Exception $e) {
                error_log("Achievement retrieval error: " . $e->getMessage());
                echo json_encode([
                    'success' => false,
                    'message' => 'Database error occurred'
                ]);
            }
        } else {
            redirect('performance');
        }
    }

    // Delete Achievement
    public function deleteAchievement() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('performance');
            return;
        }

        $achievementId = filter_input(INPUT_POST, 'achievement_id', FILTER_VALIDATE_INT);
        $playerId = $_SESSION['user_id'] ?? 6;

        if (!$achievementId) {
            flash('performance_message', 'Invalid achievement ID', 'alert alert-danger');
            redirect('performance');
            return;
        }

        try {
            $achievement = $this->achievementModel->getAchievementById($achievementId);

            if (!$achievement || (int) $achievement->PlayerID !== (int) $playerId) {
                flash('performance_message', 'Achievement not found or access denied', 'alert alert-danger');
                redirect('performance');
                return;
            }

            if (($achievement->VerifiedStatus ?? 'pending') !== 'rejected') {
                flash('performance_message', 'Only rejected achievements can be deleted', 'alert alert-danger');
                redirect('performance');
                return;
            }

            $success = $this->achievementModel->deleteAchievement($achievementId, $playerId);

            if ($success) {
                flash('performance_message', 'Rejected achievement deleted successfully.');
            } else {
                flash('performance_message', 'Failed to delete achievement. Please try again.', 'alert alert-danger');
            }
        } catch (Exception $e) {
            error_log("Achievement deletion error: " . $e->getMessage());
            flash('performance_message', 'Database error occurred. Please try again later.', 'alert alert-danger');
        }

        redirect('performance');
    }

    // Add Performance Statistics
    public function addPerformanceStats() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('performance');
            return;
        }

        $data = [
            'player_id' => $_SESSION['user_id'] ?? 6,
            'match_id' => filter_input(INPUT_POST, 'match_id', FILTER_VALIDATE_INT),
            'runs_scored' => filter_input(INPUT_POST, 'runs_scored', FILTER_VALIDATE_INT) ?? 0,
            'balls_faced' => filter_input(INPUT_POST, 'balls_faced', FILTER_VALIDATE_INT) ?? 0,
            'wickets_taken' => filter_input(INPUT_POST, 'wickets_taken', FILTER_VALIDATE_INT) ?? 0,
            'overs_bowled' => floatval($_POST['overs_bowled'] ?? 0),
            'runs_conceded' => filter_input(INPUT_POST, 'runs_conceded', FILTER_VALIDATE_INT) ?? 0,
            'catches' => filter_input(INPUT_POST, 'catches', FILTER_VALIDATE_INT) ?? 0,
            'stumpings' => filter_input(INPUT_POST, 'stumpings', FILTER_VALIDATE_INT) ?? 0,
            'rating' => floatval($_POST['rating'] ?? 0),
            'added_by' => $_SESSION['user_id'] ?? 6
        ];

        $errors = $this->validatePerformanceStats($data);

        if (!empty($errors)) {
            flash('performance_message', implode('<br>', $errors), 'alert alert-danger');
            redirect('performance');
            return;
        }

        try {
            $perfModel = $this->model('M_Performance');
            $performanceId = $perfModel->addPerformanceStatistics($data);

            if ($performanceId) {
                flash('performance_message', 'Performance statistics added successfully. They will be reviewed by the coaching staff.');
            } else {
                flash('performance_message', 'Failed to add performance statistics. Please try again.', 'alert alert-danger');
            }
        } catch (Exception $e) {
            error_log("Performance statistics creation error: " . $e->getMessage());
            flash('performance_message', 'Database error occurred while saving performance statistics.', 'alert alert-danger');
        }

        redirect('performance');
    }

    // Get available matches for dropdown (AJAX method)
    public function getAvailableMatches() {
        header('Content-Type: application/json');
        
        try {
            $perfModel = $this->model('M_Performance');
            $matches = $perfModel->getAvailableMatches(50);
            
            echo json_encode([
                'success' => true,
                'matches' => $matches
            ]);
        } catch (Exception $e) {
            error_log("Error fetching matches: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Failed to load matches'
            ]);
        }
    }

    // Get single performance record (AJAX method)
    public function getPerformanceRecord() {
        header('Content-Type: application/json');
        
        $performanceId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        
        if (!$performanceId) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid performance ID'
            ]);
            return;
        }
        
        try {
            $perfModel = $this->model('M_Performance');
            $performance = $perfModel->getPerformanceById($performanceId);
            
            if ($performance) {
                echo json_encode([
                    'success' => true,
                    'performance' => $performance
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Performance record not found'
                ]);
            }
        } catch (Exception $e) {
            error_log("Error fetching performance: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Failed to load performance record'
            ]);
        }
    }

    // Edit/Update Performance Statistics
    public function editPerformanceStats() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('performance');
            return;
        }

        $performanceId = filter_input(INPUT_POST, 'performance_id', FILTER_VALIDATE_INT);
        $playerId = $_SESSION['user_id'] ?? 6;
        
        $data = [
            'match_id' => filter_input(INPUT_POST, 'match_id', FILTER_VALIDATE_INT),
            'runs_scored' => filter_input(INPUT_POST, 'runs_scored', FILTER_VALIDATE_INT) ?? 0,
            'balls_faced' => filter_input(INPUT_POST, 'balls_faced', FILTER_VALIDATE_INT) ?? 0,
            'wickets_taken' => filter_input(INPUT_POST, 'wickets_taken', FILTER_VALIDATE_INT) ?? 0,
            'overs_bowled' => floatval($_POST['overs_bowled'] ?? 0),
            'runs_conceded' => filter_input(INPUT_POST, 'runs_conceded', FILTER_VALIDATE_INT) ?? 0,
            'catches' => filter_input(INPUT_POST, 'catches', FILTER_VALIDATE_INT) ?? 0,
            'stumpings' => filter_input(INPUT_POST, 'stumpings', FILTER_VALIDATE_INT) ?? 0,
            'rating' => floatval($_POST['rating'] ?? 0)
        ];

        $errors = $this->validatePerformanceStats($data);
        if (!$performanceId) {
            $errors[] = 'Invalid performance ID';
        }

        try {
            $perfModel = $this->model('M_Performance');
            $existing = $performanceId ? $perfModel->getPerformanceById($performanceId) : null;

            if (!$existing || (int) ($existing->PlayerID ?? 0) !== (int) $playerId) {
                $errors[] = 'Performance record not found or access denied';
            } elseif (($existing->VerifiedStatus ?? 'pending') !== 'pending') {
                $errors[] = 'Cannot edit verified or rejected performance records';
            }

            if (!empty($errors)) {
                flash('performance_message', implode('<br>', array_unique($errors)), 'alert alert-danger');
                redirect('performance');
                return;
            }

            $success = $perfModel->updatePerformanceStatistics($performanceId, $data);

            if ($success) {
                flash('performance_message', 'Performance statistics updated successfully.');
            } else {
                flash('performance_message', 'Failed to update performance statistics. Please try again.', 'alert alert-danger');
            }
        } catch (Exception $e) {
            error_log("Performance update error: " . $e->getMessage());
            flash('performance_message', 'Database error occurred. Please try again later.', 'alert alert-danger');
        }

        redirect('performance');
    }

    // Delete Performance Statistics
    public function deletePerformanceStats() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('performance');
            return;
        }

        $performanceId = filter_input(INPUT_POST, 'performance_id', FILTER_VALIDATE_INT);
        $playerId = $_SESSION['user_id'] ?? 6;

        if (!$performanceId) {
            flash('performance_message', 'Invalid performance ID', 'alert alert-danger');
            redirect('performance');
            return;
        }

        try {
            $perfModel = $this->model('M_Performance');
            $existing = $perfModel->getPerformanceById($performanceId);

            if (!$existing || (int) ($existing->PlayerID ?? 0) !== (int) $playerId) {
                flash('performance_message', 'Performance record not found or access denied', 'alert alert-danger');
                redirect('performance');
                return;
            }

            if (($existing->VerifiedStatus ?? 'pending') !== 'pending') {
                flash('performance_message', 'Only pending performance records can be deleted', 'alert alert-danger');
                redirect('performance');
                return;
            }

            $success = $perfModel->deletePerformanceStatistics($performanceId, $playerId);

            if ($success) {
                flash('performance_message', 'Performance record deleted successfully.');
            } else {
                flash('performance_message', 'Failed to delete performance record. Please try again.', 'alert alert-danger');
            }
        } catch (Exception $e) {
            error_log("Performance deletion error: " . $e->getMessage());
            flash('performance_message', 'Database error occurred. Please try again later.', 'alert alert-danger');
        }

        redirect('performance');
    }

    // Get player achievements (for display)
    private function getPlayerAchievements() {
        $playerId = $_SESSION['user_id'] ?? 6; // Default to 6 for testing
        
        // First, try to get data from database
        try {
            $achievements = $this->achievementModel->getAchievementsByPlayer($playerId);
            
            // If we get successful results from database, return them
            if ($achievements !== false) {
                error_log("Successfully fetched " . count($achievements) . " achievements from database");
                return $achievements;
            }
            
        } catch (Exception $e) {
            error_log("Database error fetching achievements: " . $e->getMessage());
            // Continue to fallback data
        }
        
        // If database fails or returns false, provide fallback data for testing
        error_log("Using fallback achievement data");
        return [
            (object)[
                'AchievementID' => 1,
                'Date' => '2024-10-15',
                'MatchName' => 'vs Team Alpha',
                'Tournament' => 'Elite League',
                'Achievement' => 'Century Maker - Scored 100+ runs in single match',
                'VerifiedStatus' => 'verified',
                'CreatedAt' => '2024-10-15 15:30:00'
            ],
            
        ];
    }

    private function validatePerformanceStats(array $data): array
    {
        $errors = [];

        if (empty($data['match_id'])) {
            $errors[] = 'Please select a match';
        }

        $wholeNumberFields = [
            'runs_scored' => 'Runs scored',
            'balls_faced' => 'Balls faced',
            'wickets_taken' => 'Wickets taken',
            'runs_conceded' => 'Runs conceded',
            'catches' => 'Catches',
            'stumpings' => 'Stumpings',
        ];

        foreach ($wholeNumberFields as $field => $label) {
            if (($data[$field] ?? 0) < 0) {
                $errors[] = $label . ' cannot be negative';
            }
        }

        if (($data['overs_bowled'] ?? 0) < 0) {
            $errors[] = 'Overs bowled cannot be negative';
        }

        $rating = (float) ($data['rating'] ?? 0);
        if ($rating < 0 || $rating > 10) {
            $errors[] = 'Overall rating must be between 0 and 10';
        }

        return $errors;
    }
    
    // Achievements
    public function achievements() {
        $data = [
            'title' => 'Achievements',
            'player' => $this->getPlayerData(),
            'awards' => $this->getAwards(),
            'records' => $this->getRecords(),
            'certificates' => $this->getCertificates()
        ];
        $this->view('player/achievements', $data);
    }

    // =========================================================================
    // PRIVATE HELPER METHODS - ALL USE REAL DATABASE QUERIES
    // =========================================================================

    private function getPlayerData() {
        if (!isset($_SESSION['user_id'])) {
            redirect('login');
        }
        $userId = $_SESSION['user_id'];
        $userModel = $this->model('M_Users');
        $user = $userModel->getUserWithProfile($userId);
        
        if (!$user) {
            $user = $userModel->getUserById($userId);
        }
        
        if ($user) {
            // Get subscription info
            $paymentModel = $this->model('M_Payment');
            $subscription = $paymentModel->getPlayerSubscription($userId);
            
            return [
                'id' => $user->UserID,
                'name' => $user->Name,
                'email' => $user->Email,
                'phone' => $user->PhoneNumber ?? '',
                'roles' => ($user->BattingStyle ?? 'N/A') . ' | ' . ($user->BowlingStyle ?? 'N/A'),
                'profile_picture' => $user->ProfileImage ?? 'default-profile.jpg',
                'date_of_birth' => $user->DateOfBirth ?? '',
                'address' => $user->Address ?? '',
                'membership_level' => $subscription->PlanName ?? 'Standard',
                'joined_date' => $user->DateJoined ?? date('Y-m-d')
            ];
        }
        return [
            'id' => $userId, 'name' => 'Player', 'email' => '', 'phone' => '',
            'roles' => 'Cricket Player', 'profile_picture' => 'default-profile.jpg',
            'date_of_birth' => '', 'address' => '', 'membership_level' => 'Standard',
            'joined_date' => date('Y-m-d')
        ];
    }
    
    private function getPerformanceStats() {
    $playerId = $_SESSION['user_id'] ?? 6;
    $perfModel = $this->model('M_Performance');
    $stats = $perfModel->getOverallStats($playerId);
    if ($stats) {
        return [
            'batting_avg' => $stats->BattingAverage ?? 0,  // Changed from BattingAvg
            'strike_rate' => $stats->StrikeRate ?? 0,
            'total_runs' => $stats->TotalRuns ?? 0,
            'total_wickets' => $stats->TotalWickets ?? 0,  // Changed from Wickets
            'bowling_avg' => $stats->BowlingAverage ?? 0,
            'economy_rate' => $stats->EconomyRate ?? 0,
            'matches_played' => $stats->MatchesPlayed ?? 0,
            'wins' => $stats->Wins ?? 0
        ];
    }
    return ['batting_avg'=>0,'strike_rate'=>0,'total_runs'=>0,'total_wickets'=>0,
            'bowling_avg'=>0,'economy_rate'=>0,'matches_played'=>0,'wins'=>0];
}

    private function getBattingStats() {
        $playerId = $_SESSION['user_id'] ?? 6;
        $perfModel = $this->model('M_Performance');
        return $perfModel->getBattingStatsForPlayer($playerId, 10);
    }

    private function getBowlingStats() {
        $playerId = $_SESSION['user_id'] ?? 6;
        $perfModel = $this->model('M_Performance');
        return $perfModel->getBowlingStatsForPlayer($playerId, 10);
    }
    
    private function getPracticeMatches() {
        $playerId = $_SESSION['user_id'] ?? 6;
        $perfModel = $this->model('M_Performance');
        return $perfModel->getMatchHistory($playerId, 10);
    }
    
    private function getTournaments() {
        $playerId = $_SESSION['user_id'] ?? 6;
        $perfModel = $this->model('M_Performance');
        return $perfModel->getTournamentStats($playerId);
    }
    
    private function getDetailedPerformanceStats() {
        $playerId = $_SESSION['user_id'] ?? 6;
        $perfModel = $this->model('M_Performance');
        $stats = $perfModel->getOverallStats($playerId);
        if ($stats) {
            return [
                'batting' => [
                    'total_runs' => $stats->TotalRuns ?? 0,
                    'average' => $stats->BattingAverage ?? 0,
                    'strike_rate' => $stats->StrikeRate ?? 0,
                    'centuries' => $stats->Centuries ?? 0,
                    'half_centuries' => $stats->HalfCenturies ?? 0,
                    'highest_score' => $stats->HighestScore ?? 0
                ],
                'bowling' => [
                    'total_wickets' => $stats->TotalWickets ?? 0,
                    'average' => $stats->BowlingAverage ?? 0,
                    'economy_rate' => $stats->EconomyRate ?? 0,
                    'best_figures' => $stats->BestBowling ?? 'N/A',
                    'five_wickets' => $stats->FiveWickets ?? 0,
                    'four_wickets' => $stats->FourWickets ?? 0
                ]
            ];
        }
        return ['batting'=>['total_runs'=>0,'average'=>0,'strike_rate'=>0,'centuries'=>0,'half_centuries'=>0,'highest_score'=>0],
                'bowling'=>['total_wickets'=>0,'average'=>0,'economy_rate'=>0,'best_figures'=>'N/A','five_wickets'=>0,'four_wickets'=>0]];
    }
    
    private function getAwards() {
        $playerId = $_SESSION['user_id'] ?? 6;
        $achievements = $this->achievementModel->getAchievementsByPlayer($playerId);
        $awards = [];
        if ($achievements) {
            foreach ($achievements as $a) {
                if ($a->VerifiedStatus === 'verified') {
                    $awards[] = [
                        'title' => $a->Achievement,
                        'event' => $a->Tournament,
                        'date' => $a->Date
                    ];
                }
            }
        }
        return $awards;
    }
    
    private function getRecords() {
        $playerId = $_SESSION['user_id'] ?? 6;
        $perfModel = $this->model('M_Performance');
        $stats = $perfModel->getOverallStats($playerId);
        $records = [];
        if ($stats) {
            if (!empty($stats->HighestScore)) {
                $records[] = ['record' => 'Highest Individual Score', 'value' => $stats->HighestScore . ' runs', 'match' => '', 'date' => ''];
            }
            if (!empty($stats->BestBowling)) {
                $records[] = ['record' => 'Best Bowling Figures', 'value' => $stats->BestBowling, 'match' => '', 'date' => ''];
            }
            if (!empty($stats->TotalRuns)) {
                $records[] = ['record' => 'Total Career Runs', 'value' => $stats->TotalRuns . ' runs', 'match' => '', 'date' => ''];
            }
        }
        return $records;
    }
    
    private function getCertificates() {
        // Certificates could overlap with verified achievements
        $playerId = $_SESSION['user_id'] ?? 6;
        $achievements = $this->achievementModel->getAchievementsByPlayer($playerId);
        $certs = [];
        if ($achievements) {
            foreach ($achievements as $a) {
                $certs[] = [
                    'title' => $a->Achievement,
                    'issued_by' => $a->Tournament ?? 'Elite Cricket Academy',
                    'date' => $a->Date
                ];
            }
        }
        return $certs;
    }
}
?>
