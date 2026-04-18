<?php

require_once APPROOT . '/models/M_Tournament.php';
require_once APPROOT . '/models/M_Notification.php';

class TournamentNotificationService
{
    private const REMINDER_DAYS = [7, 1];

    public static function sendDueReminders(bool $dryRun = false): array
    {
        $tournamentModel = new M_Tournament();
        $notificationModel = new M_Notification();

        $summary = [
            'found' => 0,
            'sent' => 0,
            'skipped' => 0,
            'failed' => 0,
            'dry_run' => $dryRun,
            'items' => [],
        ];

        foreach (self::REMINDER_DAYS as $daysBefore) {
            $targetDate = (new DateTime('today'))->modify('+' . $daysBefore . ' days')->format('Y-m-d');
            $tournaments = $tournamentModel->getUpcomingReminderTournaments($targetDate);
            $summary['found'] += count($tournaments);

            foreach ($tournaments as $tournament) {
                $tournamentId = (int)($tournament->TournamentID ?? 0);
                if ($tournamentId <= 0) {
                    $summary['skipped']++;
                    $summary['items'][] = ['tournament_id' => $tournamentId, 'status' => 'skipped', 'reason' => 'invalid_tournament'];
                    continue;
                }

                if ($dryRun) {
                    $summary['skipped']++;
                    $summary['items'][] = ['tournament_id' => $tournamentId, 'status' => 'dry_run', 'days_before' => $daysBefore];
                    continue;
                }

                try {
                    $created = self::createReminderNotifications($notificationModel, $tournamentModel, $tournament, $daysBefore);
                    $summary['sent'] += $created;
                    $summary['items'][] = [
                        'tournament_id' => $tournamentId,
                        'status' => $created > 0 ? 'sent' : 'skipped',
                        'days_before' => $daysBefore,
                        'created' => $created,
                    ];
                } catch (Throwable $e) {
                    $summary['failed']++;
                    $summary['items'][] = [
                        'tournament_id' => $tournamentId,
                        'status' => 'failed',
                        'days_before' => $daysBefore,
                        'reason' => 'notification_error',
                    ];
                    error_log('Tournament reminder notification failed for tournament #' . $tournamentId . ': ' . $e->getMessage());
                }
            }
        }

        return $summary;
    }

    public static function notifyTeamSelection(int $tournamentId): int
    {
        if ($tournamentId <= 0) {
            return 0;
        }

        try {
            $tournamentModel = new M_Tournament();
            $notificationModel = new M_Notification();
            $tournament = $tournamentModel->getTournamentById($tournamentId);
            $team = $tournamentModel->getTeam($tournamentId);

            if (!$tournament || empty($team)) {
                return 0;
            }

            $created = 0;
            $tournamentName = self::tournamentName($tournament);

            foreach ($team as $player) {
                $playerId = (int)($player->PlayerID ?? 0);
                if ($playerId <= 0) {
                    continue;
                }

                $role = trim((string)($player->RoleInTeam ?? ''));
                $roleText = $role !== '' ? ' as ' . $role : '';
                $created += $notificationModel->createOnceForUsers(
                    [$playerId],
                    'tournament-selected-' . $tournamentId . '-' . $playerId,
                    'tournament',
                    'Selected for tournament',
                    'You have been selected for ' . $tournamentName . $roleText . '.',
                    URLROOT . '/player/tournament_detail/' . $tournamentId
                );
            }

            return $created;
        } catch (Throwable $e) {
            error_log('Tournament team selection notifications failed for tournament #' . $tournamentId . ': ' . $e->getMessage());
            return 0;
        }
    }

    private static function createReminderNotifications(
        M_Notification $notificationModel,
        M_Tournament $tournamentModel,
        object $tournament,
        int $daysBefore
    ): int {
        $tournamentId = (int)($tournament->TournamentID ?? 0);
        $tournamentName = self::tournamentName($tournament);
        $dateText = self::dateText($tournament);
        $location = trim((string)($tournament->Location ?? ''));
        $locationText = $location !== '' ? ' at ' . $location : '';
        $whenText = $daysBefore === 1 ? 'tomorrow' : 'in ' . $daysBefore . ' days';
        $message = $tournamentName . ' is scheduled ' . $whenText . ' on ' . $dateText . $locationText . '.';

        $created = 0;
        $created += $notificationModel->createOnceForRoles(
            ['Admin'],
            'tournament-reminder-' . $tournamentId . '-' . $daysBefore . '-admin',
            'tournament',
            'Tournament reminder',
            $message,
            URLROOT . '/admin/tournament_detail/' . $tournamentId
        );
        $created += $notificationModel->createOnceForRoles(
            ['Coach'],
            'tournament-reminder-' . $tournamentId . '-' . $daysBefore . '-coach',
            'tournament',
            'Tournament reminder',
            $message,
            URLROOT . '/coach/tournament_detail/' . $tournamentId
        );
        $created += $notificationModel->createOnceForRoles(
            ['Trainer'],
            'tournament-reminder-' . $tournamentId . '-' . $daysBefore . '-trainer',
            'tournament',
            'Tournament reminder',
            $message,
            URLROOT . '/trainer/tournament_detail/' . $tournamentId
        );

        foreach ($tournamentModel->getTeam($tournamentId) as $player) {
            $playerId = (int)($player->PlayerID ?? 0);
            if ($playerId <= 0) {
                continue;
            }

            $created += $notificationModel->createOnceForUsers(
                [$playerId],
                'tournament-reminder-' . $tournamentId . '-' . $daysBefore . '-player-' . $playerId,
                'tournament',
                'Tournament reminder',
                $message,
                URLROOT . '/player/tournament_detail/' . $tournamentId
            );
        }

        return $created;
    }

    private static function tournamentName(object $tournament): string
    {
        $name = trim((string)($tournament->Name ?? 'Tournament'));
        return $name !== '' ? $name : 'Tournament';
    }

    private static function dateText(object $tournament): string
    {
        $date = trim((string)($tournament->tdate ?? ''));
        if ($date === '') {
            return 'the scheduled date';
        }

        $timestamp = strtotime($date);
        return $timestamp ? date('D, d M Y', $timestamp) : $date;
    }
}
