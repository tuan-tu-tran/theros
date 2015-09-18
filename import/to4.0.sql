ALTER TABLE `student` 
ADD COLUMN `st_tutor` VARCHAR(255) NULL AFTER `st_name`,
ADD COLUMN `st_address` VARCHAR(512) NULL AFTER `st_tutor`,
ADD COLUMN `st_zip` VARCHAR(45) NULL AFTER `st_address`,
ADD COLUMN `st_city` VARCHAR(128) NULL AFTER `st_zip`;
