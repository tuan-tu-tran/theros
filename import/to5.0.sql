ALTER TABLE `raw_data` 
ADD COLUMN `rd_sy_id` INT NOT NULL DEFAULT 1 AFTER `rd_treated`,
ADD INDEX `fk_raw_data_3_idx` (`rd_sy_id` ASC),
ADD CONSTRAINT `fk_raw_data_3`
  FOREIGN KEY (`rd_sy_id`)
  REFERENCES `schoolyear` (`sy_id`)
  ON DELETE RESTRICT
  ON UPDATE RESTRICT,
DROP INDEX `rd_st_id_UNIQUE` ,
ADD UNIQUE INDEX `rd_st_id_UNIQUE` (`rd_st_id` ASC, `rd_sy_id` ASC)
;
