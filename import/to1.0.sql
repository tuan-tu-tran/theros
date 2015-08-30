ALTER TABLE `teacher` 
ADD COLUMN `tea_pwd_changed` TINYINT NOT NULL DEFAULT 0 COMMENT 'whether passwd was initd (#12)' AFTER `tea_password`;
ALTER TABLE `work` 
ADD COLUMN `w_result` VARCHAR(45) NULL AFTER `w_sy_id`;

