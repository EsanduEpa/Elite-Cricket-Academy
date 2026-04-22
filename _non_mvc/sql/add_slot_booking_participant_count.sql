USE cricket_academy;

ALTER TABLE slot_booking
MODIFY COLUMN BookingSource ENUM('self','admin','shop_employee','system') NOT NULL DEFAULT 'self'
COMMENT 'self=player portal | admin=management console | shop_employee=counter | system=automatic program allocation';

ALTER TABLE slot_booking
ADD COLUMN ParticipantCount INT(11) NOT NULL DEFAULT 1
COMMENT 'How many people will use the reserved slot under this one booking, including the player.'
AFTER Status;

UPDATE slot_booking
SET ParticipantCount = 1
WHERE ParticipantCount IS NULL OR ParticipantCount < 1;