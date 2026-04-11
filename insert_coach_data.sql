USE cricket_academy;

-- Assign players to Coach Sarath (UserID 3) 
INSERT INTO playercoachassignment (PlayerID, CoachID, AssignmentType, Status, Notes) VALUES 
(6, 3, 'regular', 'active', 'Regular training group'),
(7, 3, 'regular', 'active', 'Regular training group'),
(15, 3, 'private', 'active', 'Private batting sessions'),
(16, 3, 'regular', 'active', 'Regular training group'),
(18, 3, 'both', 'active', 'Private and group sessions'),
(20, 3, 'regular', 'active', 'Regular training group');

-- Add notifications for Coach Sarath (UserID 3)
INSERT INTO notification (UserID, Type, Title, Message, IsRead, CreatedAt) VALUES
(3, 'session', 'New Session Booking', 'Player Esandu has enrolled in your batting session', 0, NOW() - INTERVAL 1 HOUR),
(3, 'injury', 'Injury Report Filed', 'Swairi reported a minor knee strain during practice', 0, NOW() - INTERVAL 3 HOUR),
(3, 'event', 'Tournament Update', 'Junior Championship registration deadline extended to March 1', 0, NOW() - INTERVAL 1 DAY),
(3, 'player', 'Player Achievement', 'Vijini achieved Man of the Match in elite league', 1, NOW() - INTERVAL 2 DAY),
(3, 'session', 'Session Feedback', 'Please provide feedback for your completed sessions this week', 1, NOW() - INTERVAL 3 DAY),
(3, 'system', 'Profile Update Reminder', 'Please update your coaching certifications for the new term', 1, NOW() - INTERVAL 5 DAY);

-- Add more feedback entries (from players to Coach Sarath)
INSERT INTO feedback (FromUserID, ToUserID, Content, Rating, Category, Status) VALUES
(6, 3, 'Coach Sarath explains techniques very clearly. Batting improved significantly.', 5, 'coach', 'pending'),
(7, 3, 'Great group sessions, could use more individual feedback during practice.', 4, 'coach', 'pending'),
(15, 3, 'The bowling drills are very effective. Would like more advanced sessions.', 4, 'coach', 'reviewed'),
(18, 3, 'Excellent coaching style. Very motivating and patient with beginners.', 5, 'coach', 'resolved');

-- Add sessions for today and upcoming for Coach Sarath (UserID 3)
INSERT INTO session (SessionType, SessionMode, CoachOrTrainerID, Name, Date, StartTime, EndTime, Location, Status, MaxParticipants, PricePerSession, IsRecurring) VALUES
('Coaching', 'Group', 3, 'Morning Batting Practice', CURDATE(), '09:00:00', '11:00:00', 'Main Ground', 'active', 15, 500.00, 1),
('Coaching', 'Private', 3, 'Advanced Bowling Technique', CURDATE(), '14:00:00', '15:30:00', 'Indoor Net 1', 'active', 3, 1500.00, 0),
('Coaching', 'Group', 3, 'Fielding Drills', CURDATE() + INTERVAL 1 DAY, '08:00:00', '10:00:00', 'Practice Ground', 'active', 20, 500.00, 1),
('Coaching', 'Private', 3, 'Spin Bowling Masterclass', CURDATE() + INTERVAL 2 DAY, '10:00:00', '11:30:00', 'Indoor Net 2', 'active', 5, 2000.00, 0),
('Coaching', 'Group', 3, 'Match Simulation', CURDATE() + INTERVAL 3 DAY, '07:00:00', '12:00:00', 'Main Ground', 'active', 22, 750.00, 0),
('Coaching', 'Group', 3, 'Weekend Fitness and Cricket', CURDATE() + INTERVAL 5 DAY, '06:00:00', '08:00:00', 'Fitness Center', 'active', 25, 500.00, 1);
