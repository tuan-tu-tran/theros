CREATE TABLE `role` (
  `ro_id` int(11) NOT NULL AUTO_INCREMENT,
  `ro_role` varchar(45) NOT NULL,
  PRIMARY KEY (`ro_id`),
  UNIQUE KEY `ro_role_UNIQUE` (`ro_role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `teacher_role` (
  `tr_id` int(11) NOT NULL AUTO_INCREMENT,
  `tr_tea_id` int(11) NOT NULL,
  `tr_ro_id` int(11) NOT NULL,
  PRIMARY KEY (`tr_id`),
  UNIQUE KEY `index2` (`tr_tea_id`,`tr_ro_id`),
  KEY `fk_teacher_role_2_idx` (`tr_ro_id`),
  CONSTRAINT `fk_teacher_role_2` FOREIGN KEY (`tr_ro_id`) REFERENCES `role` (`ro_id`),
  CONSTRAINT `fk_teacher_role_1` FOREIGN KEY (`tr_tea_id`) REFERENCES `teacher` (`tea_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
