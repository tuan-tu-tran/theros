/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `class`
--

DROP TABLE IF EXISTS `class`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `class` (
  `cl_id` int(11) NOT NULL AUTO_INCREMENT,
  `cl_desc` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`cl_id`),
  UNIQUE KEY `cl_desc_UNIQUE` (`cl_desc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `raw_data`
--

DROP TABLE IF EXISTS `raw_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `raw_data` (
  `rd_id` int(11) NOT NULL AUTO_INCREMENT,
  `rd_st_id` int(11) NOT NULL,
  `rd_cl_id` int(11) NOT NULL,
  `rd_desc` text NOT NULL,
  `rd_treated` bit(1) DEFAULT b'0',
  PRIMARY KEY (`rd_id`),
  UNIQUE KEY `rd_st_id_UNIQUE` (`rd_st_id`),
  KEY `fk_raw_data_2_idx` (`rd_cl_id`),
  CONSTRAINT `fk_raw_data_1` FOREIGN KEY (`rd_st_id`) REFERENCES `student` (`st_id`),
  CONSTRAINT `fk_raw_data_2` FOREIGN KEY (`rd_cl_id`) REFERENCES `class` (`cl_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `raw_data_work`
--

DROP TABLE IF EXISTS `raw_data_work`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `raw_data_work` (
  `rdw_rd_id` int(11) NOT NULL,
  `rdw_w_id` int(11) NOT NULL COMMENT 'tracks which work were created by which raw_data',
  PRIMARY KEY (`rdw_rd_id`,`rdw_w_id`),
  KEY `fk_raw_data_work_2_idx` (`rdw_w_id`),
  CONSTRAINT `fk_raw_data_work_1` FOREIGN KEY (`rdw_rd_id`) REFERENCES `raw_data` (`rd_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_raw_data_work_2` FOREIGN KEY (`rdw_w_id`) REFERENCES `work` (`w_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `schoolyear`
--

DROP TABLE IF EXISTS `schoolyear`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `schoolyear` (
  `sy_id` int(11) NOT NULL AUTO_INCREMENT,
  `sy_desc` varchar(45) NOT NULL,
  PRIMARY KEY (`sy_id`),
  UNIQUE KEY `sy_desc_UNIQUE` (`sy_desc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `student`
--

DROP TABLE IF EXISTS `student`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `student` (
  `st_id` int(11) NOT NULL AUTO_INCREMENT,
  `st_name` varchar(255) NOT NULL,
  PRIMARY KEY (`st_id`),
  UNIQUE KEY `st_name_UNIQUE` (`st_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `student_class`
--

DROP TABLE IF EXISTS `student_class`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `student_class` (
  `sc_id` int(11) NOT NULL AUTO_INCREMENT,
  `sc_st_id` int(11) NOT NULL,
  `sc_cl_id` int(11) NOT NULL,
  `sc_sy_id` int(11) NOT NULL,
  PRIMARY KEY (`sc_id`),
  UNIQUE KEY `idx_unique` (`sc_st_id`,`sc_cl_id`,`sc_sy_id`),
  KEY `fk_student_class_1_idx` (`sc_st_id`),
  KEY `fk_student_class_2_idx` (`sc_cl_id`),
  KEY `fk_student_class_3_idx` (`sc_sy_id`),
  CONSTRAINT `fk_student_class_1` FOREIGN KEY (`sc_st_id`) REFERENCES `student` (`st_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_student_class_2` FOREIGN KEY (`sc_cl_id`) REFERENCES `class` (`cl_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_student_class_3` FOREIGN KEY (`sc_sy_id`) REFERENCES `schoolyear` (`sy_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `subject`
--

DROP TABLE IF EXISTS `subject`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subject` (
  `sub_id` int(11) NOT NULL AUTO_INCREMENT,
  `sub_code` varchar(45) NOT NULL,
  `sub_desc` varchar(255) NOT NULL,
  PRIMARY KEY (`sub_id`),
  UNIQUE KEY `sub_code_UNIQUE` (`sub_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `teacher`
--

DROP TABLE IF EXISTS `teacher`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `teacher` (
  `tea_id` int(11) NOT NULL AUTO_INCREMENT,
  `tea_fullname` varchar(255) NOT NULL,
  `tea_password` varchar(45) NOT NULL,
  PRIMARY KEY (`tea_id`),
  UNIQUE KEY `tea_fullname_UNIQUE` (`tea_fullname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `teacher_subject`
--

DROP TABLE IF EXISTS `teacher_subject`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `teacher_subject` (
  `ts_id` int(11) NOT NULL AUTO_INCREMENT,
  `ts_tea_id` int(11) NOT NULL,
  `ts_sub_id` int(11) NOT NULL,
  `ts_cl_id` int(11) NOT NULL,
  `ts_sy_id` int(11) NOT NULL,
  PRIMARY KEY (`ts_id`),
  UNIQUE KEY `unique` (`ts_tea_id`,`ts_sub_id`,`ts_cl_id`,`ts_sy_id`),
  KEY `fk_teacher_subject_1_idx` (`ts_tea_id`),
  KEY `fk_teacher_subject_2_idx` (`ts_sub_id`),
  KEY `fk_teacher_subject_3_idx` (`ts_cl_id`),
  KEY `fk_teacher_subject_4_idx` (`ts_sy_id`),
  CONSTRAINT `fk_teacher_subject_1` FOREIGN KEY (`ts_tea_id`) REFERENCES `teacher` (`tea_id`),
  CONSTRAINT `fk_teacher_subject_2` FOREIGN KEY (`ts_sub_id`) REFERENCES `subject` (`sub_id`),
  CONSTRAINT `fk_teacher_subject_3` FOREIGN KEY (`ts_cl_id`) REFERENCES `class` (`cl_id`),
  CONSTRAINT `fk_teacher_subject_4` FOREIGN KEY (`ts_sy_id`) REFERENCES `schoolyear` (`sy_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `work`
--

DROP TABLE IF EXISTS `work`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `work` (
  `w_id` int(11) NOT NULL AUTO_INCREMENT,
  `w_ts_id` int(11) NOT NULL,
  `w_st_id` int(11) NOT NULL,
  `w_description` text,
  PRIMARY KEY (`w_id`),
  KEY `fk_work_1_idx` (`w_ts_id`),
  KEY `fk_work_2_idx` (`w_st_id`),
  CONSTRAINT `fk_work_1` FOREIGN KEY (`w_ts_id`) REFERENCES `teacher_subject` (`ts_id`),
  CONSTRAINT `fk_work_2` FOREIGN KEY (`w_st_id`) REFERENCES `student` (`st_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
