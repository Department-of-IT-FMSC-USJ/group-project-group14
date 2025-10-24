-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 24, 2025 at 06:19 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `parking`
--

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

DROP TABLE IF EXISTS `payment`;
CREATE TABLE IF NOT EXISTS `payment` (
  `payment_ID` int NOT NULL AUTO_INCREMENT,
  `payment_type` varchar(50) DEFAULT NULL,
  `payment_time` time DEFAULT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `driver_ID` int DEFAULT NULL,
  `payment_details` text,
  PRIMARY KEY (`payment_ID`),
  KEY `driver_ID` (`driver_ID`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf16;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`payment_ID`, `payment_type`, `payment_time`, `payment_date`, `amount`, `driver_ID`, `payment_details`) VALUES
(1, 'Cash', '14:57:39', '2025-10-16', 100.00, 5, NULL),
(2, 'Debit Card', '15:05:28', '2025-10-16', 0.00, 5, '{\"cardholder_name\":\"doe\",\"card_number\":\"***************3153\",\"expiry_date\":\"12\\/27\",\"mobile_number\":\"\"}'),
(3, 'Cash', '15:05:46', '2025-10-16', 100.00, 5, NULL),
(4, 'Cash', '10:56:18', '2025-10-21', 100.00, 6, '');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
