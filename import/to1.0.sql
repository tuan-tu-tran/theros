ALTER TABLE `theros_dev`.`teacher` 
ADD COLUMN `tea_pwd_changed` TINYINT NOT NULL DEFAULT 0 COMMENT 'whether passwd was initd (#12)' AFTER `tea_password`;

