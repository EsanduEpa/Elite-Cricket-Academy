-- =============================================================================
-- TOURNAMENT MODULE — ALL REQUIRED TABLES
-- Elite Cricket Academy
-- Generated: 10 April 2026
-- =============================================================================
-- Run this file on a fresh database (tables are created only if they don't
-- exist, so it is safe to run on an existing DB).
-- =============================================================================

-- -----------------------------------------------------------------------------
-- 1. tournament
--    Core table: one row per tournament.
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS tournament (
    TournamentID         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    Name                 VARCHAR(200)          NOT NULL,
    AgeGroup             VARCHAR(50)           DEFAULT NULL,
    Format               ENUM('T20','ODI','Test','Other') DEFAULT 'T20',
    Description          TEXT                  DEFAULT NULL,
    tdate                DATE                  NOT NULL COMMENT 'Tournament date',
    RegistrationDeadline DATE                  DEFAULT NULL,
    MaxPlayers           TINYINT UNSIGNED      DEFAULT NULL,
    Location             VARCHAR(200)          DEFAULT NULL,
    PrizePool            DECIMAL(10,2)         DEFAULT 0.00,
    CreatedBy            INT UNSIGNED          NOT NULL COMMENT 'FK → user.UserID (admin)',
    Status               ENUM(
                           'created',
                           'registration_open',
                           'registration_closed',
                           'team_announced',
                           'ongoing',
                           'completed',
                           'cancelled'
                         ) NOT NULL DEFAULT 'created',
    IsTeamAnnounced      TINYINT(1)            NOT NULL DEFAULT 0,
    CancelReason         TEXT                  DEFAULT NULL,
    CreatedAt            TIMESTAMP             NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UpdatedAt            TIMESTAMP             NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_tournament_created_by FOREIGN KEY (CreatedBy) REFERENCES user (UserID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------------------------------
-- 2. tournament_join_request
--    Players apply to join a tournament.
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS tournament_join_request (
    RequestID    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    TournamentID INT UNSIGNED NOT NULL,
    PlayerID     INT UNSIGNED NOT NULL COMMENT 'FK → user.UserID',
    Message      TEXT         DEFAULT NULL,
    Status       ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    ReviewedBy   INT UNSIGNED DEFAULT NULL COMMENT 'FK → user.UserID (admin/coach)',
    ReviewNotes  TEXT         DEFAULT NULL,
    RequestedAt  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ReviewedAt   TIMESTAMP    DEFAULT NULL,

    UNIQUE KEY uq_join_request (TournamentID, PlayerID),
    CONSTRAINT fk_tjr_tournament FOREIGN KEY (TournamentID) REFERENCES tournament (TournamentID) ON DELETE CASCADE,
    CONSTRAINT fk_tjr_player     FOREIGN KEY (PlayerID)     REFERENCES user (UserID),
    CONSTRAINT fk_tjr_reviewer   FOREIGN KEY (ReviewedBy)   REFERENCES user (UserID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------------------------------
-- 3. coach_tournament_recommendations
--    Coaches recommend players for a tournament.
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS coach_tournament_recommendations (
    RecommendationID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    CoachID          INT UNSIGNED NOT NULL COMMENT 'FK → user.UserID',
    TournamentID     INT UNSIGNED NOT NULL,
    PlayerID         INT UNSIGNED NOT NULL COMMENT 'FK → user.UserID',
    RecommendedRole  VARCHAR(100) DEFAULT NULL,
    Reason           TEXT         DEFAULT NULL,
    Comments         TEXT         DEFAULT NULL,
    Status           ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    ReviewedBy       INT UNSIGNED DEFAULT NULL COMMENT 'FK → user.UserID (admin)',
    ReviewFeedback   TEXT         DEFAULT NULL,
    DateRecommended  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    DateReviewed     TIMESTAMP    DEFAULT NULL,

    UNIQUE KEY uq_coach_rec (CoachID, TournamentID, PlayerID),
    CONSTRAINT fk_ctr_coach      FOREIGN KEY (CoachID)      REFERENCES user (UserID),
    CONSTRAINT fk_ctr_tournament FOREIGN KEY (TournamentID) REFERENCES tournament (TournamentID) ON DELETE CASCADE,
    CONSTRAINT fk_ctr_player     FOREIGN KEY (PlayerID)     REFERENCES user (UserID),
    CONSTRAINT fk_ctr_reviewer   FOREIGN KEY (ReviewedBy)   REFERENCES user (UserID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------------------------------
-- 4. trainer_tournament_recommendations
--    Trainers (physical trainers) recommend players for a tournament.
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS trainer_tournament_recommendations (
    RecommendationID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    TrainerID        INT UNSIGNED NOT NULL COMMENT 'FK → user.UserID',
    TournamentID     INT UNSIGNED NOT NULL,
    PlayerID         INT UNSIGNED NOT NULL COMMENT 'FK → user.UserID',
    FitnessRecommended ENUM('Yes', 'No') NOT NULL DEFAULT 'No',
    Comments         TEXT         DEFAULT NULL,
    Status           ENUM('pending','reviewed','confirmed','rejected') NOT NULL DEFAULT 'pending',
    ReviewedBy       INT UNSIGNED DEFAULT NULL COMMENT 'FK → user.UserID (head coach)',
    DateRecommended  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    DateReviewed     TIMESTAMP    DEFAULT NULL,

    UNIQUE KEY uq_trainer_rec (TrainerID, TournamentID, PlayerID),
    CONSTRAINT fk_ttr_trainer    FOREIGN KEY (TrainerID)    REFERENCES user (UserID),
    CONSTRAINT fk_ttr_tournament FOREIGN KEY (TournamentID) REFERENCES tournament (TournamentID) ON DELETE CASCADE,
    CONSTRAINT fk_ttr_player     FOREIGN KEY (PlayerID)     REFERENCES user (UserID),
    CONSTRAINT fk_ttr_reviewer   FOREIGN KEY (ReviewedBy)   REFERENCES user (UserID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------------------------------
-- 5. tournamentplayer
--    The selected squad for a tournament (draft → confirmed).
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS tournamentplayer (
    TournamentPlayerID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    TournamentID       INT UNSIGNED NOT NULL,
    PlayerID           INT UNSIGNED NOT NULL COMMENT 'FK → user.UserID',
    Team               VARCHAR(100) DEFAULT 'Academy Team',
    RoleInTeam         VARCHAR(100) DEFAULT NULL,
    SelectedBy         INT UNSIGNED DEFAULT NULL COMMENT 'FK → user.UserID (head coach)',
    SelectionStatus    ENUM('draft','confirmed') NOT NULL DEFAULT 'draft',
    SelectedAt         TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY uq_tournament_player (TournamentID, PlayerID),
    CONSTRAINT fk_tp_tournament FOREIGN KEY (TournamentID) REFERENCES tournament (TournamentID) ON DELETE CASCADE,
    CONSTRAINT fk_tp_player     FOREIGN KEY (PlayerID)     REFERENCES user (UserID),
    CONSTRAINT fk_tp_selected   FOREIGN KEY (SelectedBy)   REFERENCES user (UserID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------------------------------
-- 6. tournament_result
--    Final result entered by admin after the tournament ends.
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS tournament_result (
    ResultID            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    TournamentID        INT(11) NOT NULL,
    
    -- Overall Achievement
    Position            ENUM('champions', '1st runners up', '2nd runners up', '3rd runners up', 'super 8', 'super 16', 'group stage') NOT NULL,
    
    -- Performance Summary (Aggregates)
    TotalMatchesPlayed  TINYINT UNSIGNED DEFAULT 0,
    TotalWins           TINYINT UNSIGNED DEFAULT 0,
    TotalLosses          TINYINT UNSIGNED DEFAULT 0,
    
    -- Awards (Linking to your User table)
    ManOfTournament     INT(11) DEFAULT NULL COMMENT 'FK → user.UserID',
    BestBatsman         INT(11) DEFAULT NULL COMMENT 'FK → user.UserID',
    BestBowler          INT(11) DEFAULT NULL COMMENT 'FK → user.UserID',
    
    -- The "Final Chapter"
    SummaryNotes        TEXT DEFAULT NULL COMMENT 'E.g. "Historical run to the final, lost in a thriller."',
    
    EnteredBy           INT(11) NOT NULL,
    CreatedAt           TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY uq_result_tournament (TournamentID),
    CONSTRAINT fk_tr_tournament FOREIGN KEY (TournamentID) REFERENCES tournament (TournamentID) ON DELETE CASCADE,
    CONSTRAINT fk_tr_man        FOREIGN KEY (ManOfTournament) REFERENCES user (UserID),
    CONSTRAINT fk_tr_batsman    FOREIGN KEY (BestBatsman)     REFERENCES user (UserID),
    CONSTRAINT fk_tr_bowler     FOREIGN KEY (BestBowler)      REFERENCES user (UserID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- -----------------------------------------------------------------------------
-- 7. playertournamentstats
--    Individual player statistics per tournament.
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS playertournamentstats (
    StatID          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    TournamentID    INT UNSIGNED     NOT NULL,
    PlayerID        INT UNSIGNED     NOT NULL COMMENT 'FK → user.UserID',
    MatchesPlayed   TINYINT UNSIGNED NOT NULL DEFAULT 0,
    TotalRuns       SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    TotalWickets    TINYINT UNSIGNED NOT NULL DEFAULT 0,
    BattingAverage  DECIMAL(6,2)     DEFAULT NULL,
    BowlingAverage  DECIMAL(6,2)     DEFAULT NULL,
    StrikeRate      DECIMAL(6,2)     DEFAULT NULL,
    EconomyRate     DECIMAL(5,2)     DEFAULT NULL,
    UpdatedAt       TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uq_player_stats (TournamentID, PlayerID),
    CONSTRAINT fk_pts_tournament FOREIGN KEY (TournamentID) REFERENCES tournament (TournamentID) ON DELETE CASCADE,
    CONSTRAINT fk_pts_player     FOREIGN KEY (PlayerID)     REFERENCES user (UserID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


ALTER TABLE crimatch
  CHANGE COLUMN `name` `Name` VARCHAR(100) NOT NULL COMMENT 'e.g. Semi-Final 1 or League Match 5',
  MODIFY COLUMN `Result` ENUM('win', 'loss', 'tie', 'draw', 'no-result', 'abandoned', 'pending') DEFAULT 'pending',
  ADD COLUMN `MarginValue` INT UNSIGNED DEFAULT NULL COMMENT 'The number part: e.g. 5' AFTER `Result`,
  ADD COLUMN `MarginType` ENUM('runs', 'wickets', 'super over', 'DLS', 'boundaries', 'forfeit') DEFAULT NULL AFTER `MarginValue`,
  ADD COLUMN `OurRuns` SMALLINT UNSIGNED DEFAULT NULL AFTER `MarginType`,
  ADD COLUMN `OurWickets` TINYINT UNSIGNED DEFAULT NULL AFTER `OurRuns`,
  ADD COLUMN `OpponentRuns` SMALLINT UNSIGNED DEFAULT NULL AFTER `OurWickets`,
  ADD COLUMN `OpponentWickets` TINYINT UNSIGNED DEFAULT NULL AFTER `OpponentRuns`,
  ADD COLUMN `IsDLS` TINYINT(1) DEFAULT 0 COMMENT '1 if Duckworth-Lewis was applied' AFTER `OpponentWickets`,
  ADD COLUMN `SummaryNotes` TEXT DEFAULT NULL AFTER `IsDLS`,
  ADD COLUMN `CreatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER `SummaryNotes`,
  ADD COLUMN `UpdatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `CreatedAt`;

  ALTER TABLE crimatch
  DROP COLUMN `OurScore`,
  DROP COLUMN `OpponentScore`;

  do the system changes according to the db change, forms , controllers, views and models that refer to the tournament result and cri match