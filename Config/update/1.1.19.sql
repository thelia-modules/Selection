SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE `selection` CHANGE `visible` `visible` tinyint(4) NOT NULL DEFAULT '1' AFTER `id`;
ALTER TABLE `selection_container` CHANGE `visible` `visible` tinyint(4) NOT NULL DEFAULT '1' AFTER `id`;

SET FOREIGN_KEY_CHECKS = 1;
