-- Insert sample feedback data for testing
-- First, get some user IDs
SET @player1 = (SELECT UserID FROM User WHERE Role = 'player' LIMIT 1);
SET @coach1 = (SELECT UserID FROM User WHERE Role = 'coach' LIMIT 1);
SET @trainer1 = (SELECT UserID FROM User WHERE Role = 'trainer' LIMIT 1);

-- Insert sample feedback with various categories and statuses
INSERT INTO feedback (FromUserID, ToUserID, Content, Rating, Category, Status, CreatedDate) VALUES
(@player1, @coach1, 'The training sessions have been excellent. Coach really knows how to motivate the team and improve our technique.', 5, 'coach', 'resolved', DATE_SUB(NOW(), INTERVAL 5 DAY)),
(@player1, NULL, 'I would like to request better equipment for batting practice. The current bats need replacement.', 3, 'equipment', 'pending', DATE_SUB(NOW(), INTERVAL 3 DAY)),
(@player1, NULL, 'The facility needs more maintenance, especially the practice nets. Some of them are torn.', 2, 'facility', 'reviewed', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(@player1, @trainer1, 'Very satisfied with the nutritional guidance provided by the trainer. Feeling much more energetic.', 5, 'trainer', 'resolved', DATE_SUB(NOW(), INTERVAL 7 DAY)),
(@player1, NULL, 'The shop prices are quite reasonable and products are of good quality. Happy with my purchase.', 4, 'shop', 'resolved', DATE_SUB(NOW(), INTERVAL 10 DAY)),
(@player1, NULL, 'Overall great experience at the academy. The staff is professional and facilities are mostly good.', 4, 'general', 'pending', NOW()),
(@player1, @coach1, 'Could we have more bowling practice sessions? I feel we need to work on spin bowling.', 3, 'coach', 'reviewed', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(@player1, NULL, 'The changing rooms need better ventilation and cleaning.', 2, 'facility', 'pending', DATE_SUB(NOW(), INTERVAL 4 DAY)),
(@player1, NULL, 'Really appreciate the timely communication and updates from the academy.', 5, 'general', 'resolved', DATE_SUB(NOW(), INTERVAL 15 DAY)),
(@player1, @trainer1, 'The fitness training program is excellent. Seeing good results.', 5, 'trainer', 'resolved', DATE_SUB(NOW(), INTERVAL 12 DAY));
