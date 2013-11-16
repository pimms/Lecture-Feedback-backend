-- phpMyAdmin SQL Dump
-- version 3.5.8.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 16, 2013 at 03:22 PM
-- Server version: 5.5.34-0ubuntu0.13.04.1
-- PHP Version: 5.4.9-4ubuntu2.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `lecture_feedback`
--

-- --------------------------------------------------------

--
-- Table structure for table `Comments`
--

CREATE TABLE IF NOT EXISTS `Comments` (
  `Room` char(30) NOT NULL,
  `Time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `Student_hash` binary(20) NOT NULL,
  `Student_comment` varchar(333) NOT NULL,
  `Comment_flags` smallint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Room`,`Time`,`Student_hash`,`Student_comment`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Feedback_names`
--

CREATE TABLE IF NOT EXISTS `Feedback_names` (
  `Feedback_name` varchar(50) NOT NULL,
  `Active` tinyint(1) NOT NULL,
  PRIMARY KEY (`Feedback_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Lecture`
--

CREATE TABLE IF NOT EXISTS `Lecture` (
  `Room` varchar(30) NOT NULL,
  `Time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `Duration` int(4) unsigned NOT NULL,
  `Subject` int(11) NOT NULL,
  `Teacher` int(11) NOT NULL,
  PRIMARY KEY (`Room`,`Time`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ReviewItem`
--

CREATE TABLE IF NOT EXISTS `ReviewItem` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `courseName` varchar(64) NOT NULL,
  `courseCode` varchar(16) NOT NULL,
  `lecturer` varchar(64) NOT NULL,
  `startTime` timestamp NULL DEFAULT NULL,
  `endTime` timestamp NULL DEFAULT NULL,
  `room` varchar(32) NOT NULL,
  `ratings` varchar(16) NOT NULL COMMENT 'Dot-separated booleans: "0.0.1.1.0"',
  `comment` text,
  `reviewTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Values`
--

CREATE TABLE IF NOT EXISTS `Values` (
  `Room` char(30) NOT NULL,
  `Time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `Student_hash` binary(20) NOT NULL,
  `Feedback_name` varchar(50) NOT NULL,
  `Feedback_value` tinyint(1) NOT NULL,
  PRIMARY KEY (`Room`,`Time`,`Student_hash`,`Feedback_name`),
  KEY `Feedback_name` (`Feedback_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Comments`
--
ALTER TABLE `Comments`
  ADD CONSTRAINT `Comments_Lecture_FK` FOREIGN KEY (`Room`, `Time`) REFERENCES `Lecture` (`Room`, `Time`);

--
-- Constraints for table `Values`
--
ALTER TABLE `Values`
  ADD CONSTRAINT `Values_Feedback_Names_FK` FOREIGN KEY (`Feedback_name`) REFERENCES `Feedback_names` (`Feedback_name`),
  ADD CONSTRAINT `Values_Lecture_FK` FOREIGN KEY (`Room`, `Time`) REFERENCES `Lecture` (`Room`, `Time`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
