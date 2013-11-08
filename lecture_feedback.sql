-- Table structure for table `Comments`
CREATE TABLE IF NOT EXISTS `Comments` (
  `Room` char(30) NOT NULL,
  `Time` timestamp NOT NULL DEFAULT '0',
  `Student_hash` binary(20) NOT NULL,
  `Student_comment` varchar(333) NOT NULL,
  `Comment_flags` smallint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Room`,`Time`,`Student_hash`,`Student_comment`)
);

-- Table structure for table `Feedback_names`
CREATE TABLE IF NOT EXISTS `Feedback_names` (
  `Feedback_name` varchar(50) NOT NULL,
  `Active` tinyint(1) NOT NULL,
  PRIMARY KEY (`Feedback_name`)
);

-- Table structure for table `Lecture`
CREATE TABLE IF NOT EXISTS `Lecture` (
  `Room` varchar(30) NOT NULL,
  `Time` timestamp NOT NULL DEFAULT '0',
  `Duration` int(4) unsigned NOT NULL,
  `Subject` int(11) NOT NULL,
  `Teacher` int(11) NOT NULL,
  PRIMARY KEY (`Room`,`Time`)
);

-- Table structure for table `Values`
CREATE TABLE IF NOT EXISTS `Values` (
  `Room` char(30) NOT NULL,
  `Time` timestamp NOT NULL DEFAULT '0',
  `Student_hash` binary(20) NOT NULL,
  `Feedback_name` varchar(50) NOT NULL,
  `Feedback_value` tinyint(1) NOT NULL,
  PRIMARY KEY (`Room`,`Time`,`Student_hash`,`Feedback_name`),
  KEY `Feedback_name` (`Feedback_name`)
);

-- Constraints for table `Comments`
ALTER TABLE `Comments`
  ADD CONSTRAINT `Comments_Lecture_FK` FOREIGN KEY (`Room`, `Time`) REFERENCES `Lecture` (`Room`, `Time`);

-- Constraints for table `Values`
ALTER TABLE `Values`
  ADD CONSTRAINT `Values_Feedback_Names_FK` FOREIGN KEY (`Feedback_name`) REFERENCES `Feedback_names` (`Feedback_name`);
