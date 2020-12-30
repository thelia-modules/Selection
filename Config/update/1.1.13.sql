SET FOREIGN_KEY_CHECKS = 0;

-- Add the 'code' column to the selection tables.
ALTER TABLE `selection` ADD `code` varchar(255) NOT NULL AFTER `visible`;
ALTER TABLE `selection_container` ADD `code` varchar(255) NOT NULL AFTER `visible`;

-- Generate a pseudo-code for each existing selection
UPDATE `selection` set `code` = CONCAT('selection_', id);
UPDATE `selection_container` set `code` = CONCAT('container_', id);

SET FOREIGN_KEY_CHECKS = 1;
