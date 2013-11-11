/* The column names that start with capitalization are using the English name
 * of the key from the TimeEdit data.
 */

-- Table structure for table `comments`
CREATE TABLE IF NOT EXISTS `comments` (
  `Room` char(30) NOT NULL,
  `time` timestamp NOT NULL DEFAULT '0000-00-00 00:00',
  `student_hash` binary(20) NOT NULL,
  `student_comment` varchar(333) NOT NULL,
  `comment_flags` smallint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Room`,`time`,`student_hash`,`student_comment`)
);

-- Table structure for table `feedback_names`
CREATE TABLE IF NOT EXISTS `feedback_names` (
  `feedback_name` varchar(50) NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`feedback_name`)
);

-- Table structure for table `lecture`
CREATE TABLE IF NOT EXISTS `lecture` (
  `Room` varchar(30) NOT NULL,
  `time` timestamp NOT NULL DEFAULT '0000-00-00 00:00',
  `duration` int(4) unsigned NOT NULL,
  `Subject` int(11) NOT NULL,
  `Teacher` int(11) NOT NULL,
  PRIMARY KEY (`Room`,`time`)
);

-- Table structure for table `values`
CREATE TABLE IF NOT EXISTS `values` (
  `Room` char(30) NOT NULL,
  `time` timestamp NOT NULL DEFAULT '0000-00-00 00:00',
  `student_hash` binary(20) NOT NULL,
  `feedback_name` varchar(50) NOT NULL,
  `feedback_value` tinyint(1) NOT NULL,
  PRIMARY KEY (`Room`,`time`,`student_hash`,`feedback_name`),
  KEY `feedback_name` (`feedback_name`)
);

-- Constraints for table `comments`
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_lecture_FK` FOREIGN KEY (`Room`, `time`) REFERENCES `lecture` (`Room`, `time`);

-- Constraints for table `values`
ALTER TABLE `values`
  ADD CONSTRAINT `Values_Lecture_FK` FOREIGN KEY (`Room`, `time`) REFERENCES `lecture` (`Room`, `time`);
  
-- Constraints for table `values`
ALTER TABLE `values`
  ADD CONSTRAINT `values_feedback_names_FK` FOREIGN KEY (`feedback_name`) REFERENCES `feedback_names` (`feedback_name`);
