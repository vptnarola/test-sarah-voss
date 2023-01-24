-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 192.168.1.203:3306
-- Generation Time: Jan 24, 2023 at 07:06 AM
-- Server version: 5.7.29
-- PHP Version: 7.4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `test_sarah`
--

-- --------------------------------------------------------

--
-- Table structure for table `state`
--

CREATE TABLE `state` (
  `id` int(11) NOT NULL,
  `stateName` varchar(32) NOT NULL,
  `abbr` varchar(32) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `state`
--

INSERT INTO `state` (`id`, `stateName`, `abbr`) VALUES
(1, 'ALABAMA', 'AL'),
(2, 'ALASKA', 'AK'),
(3, 'ALBERTA', 'AB'),
(4, 'AMERICAN SAMOA', 'AS'),
(5, 'ARIZONA', 'AZ'),
(6, 'ARKANSAS', 'AR'),
(7, 'BRITISH COLUMBIA', 'BC'),
(8, 'CALIFORNIA', 'CA'),
(9, 'CAROLINE ISLANDS', 'PW'),
(10, 'COLORADO', 'CO'),
(11, 'CONNETICUT', 'CT'),
(12, 'DELAWARE', 'DE'),
(13, 'DISTRICT OF COLUMBIA', 'DC'),
(14, 'FEDERATED STATE', 'FM'),
(15, 'FLORIDA', 'FL'),
(16, 'GEORGIA', 'GA'),
(17, 'GUAM', 'GU'),
(18, 'HAWAII', 'HI'),
(19, 'IDOHA', 'ID'),
(20, 'ILLINOIS', 'IL'),
(21, 'INDIANA', 'IN'),
(22, 'IOWA', 'IA'),
(23, 'KANSAS', 'KS'),
(24, 'KENTUCKY', 'KY'),
(25, 'LOUSIANA', 'LA'),
(26, 'MAINE', 'ME'),
(27, 'MANITOBA', 'MB'),
(28, 'MARIANA ISLANDS', 'MP'),
(29, 'MARSHALL ISLANDS', 'MH'),
(30, 'MARYLAND', 'MD'),
(31, 'MASSACHUSETTS', 'MA'),
(32, 'MICHIGAN', 'MI'),
(33, 'MINNESOTA', 'MN'),
(34, 'MISSISSIPPI', 'MS'),
(35, 'MISSOURI', 'MO'),
(36, 'MONTANA', 'MT'),
(37, 'NEBRASKA', 'NE'),
(38, 'NEVADA', 'NV'),
(39, 'NEW BRUNSWICK', 'NB'),
(40, 'NEW HAMPSHIRE', 'NH'),
(41, 'NEW JERSEY', 'NJ'),
(42, 'NEW MEXICO', 'NM'),
(43, 'NEW YORK', 'NY'),
(44, 'NEWFOUNDLAND', 'NF'),
(45, 'NORTH CAROLINA', 'NC'),
(46, 'NORTH DAKOTA', 'ND'),
(47, 'NORTHWEST TERRITORIES', 'NT'),
(48, 'NOVA SCOTIA', 'NS'),
(49, 'NUNAVUT', 'NU'),
(50, 'OHIO', 'OH'),
(51, 'OKLAHOMA', 'OK'),
(52, 'ONTARIO', 'ON'),
(53, 'OREGON', 'OR'),
(54, 'PENNSYLVANIA', 'PA'),
(55, 'PRINCE EDWARD ISLAND', 'PE'),
(56, 'PUERTO RICO', 'PR'),
(57, 'QUEBEC', 'PQ'),
(58, 'RHODE ISLAND', 'RI'),
(59, 'SASKATCHEWAN', 'SK'),
(60, 'SOUTH CAROLINA', 'SC'),
(61, 'SOUTH DAKOTA', 'SD'),
(62, 'TENNESSEE', 'TN'),
(63, 'TEXAS', 'TX'),
(64, 'UTAH', 'UT'),
(65, 'VERMONT', 'VT'),
(66, 'VIRGIN ISLANDS', 'VI'),
(67, 'VIRGINIA', 'VA'),
(68, 'WASHINGTON', 'WA'),
(69, 'WEST VIRGINIA', 'WV'),
(70, 'WISCONSIN', 'WI'),
(71, 'WYOMING', 'WY'),
(72, 'YUKON TERRITORY', 'YT'),
(73, 'ARMED FORCES - EUROPE', 'AE'),
(74, 'ARMED FORCES - AMERICAS', 'AA'),
(75, 'ARMED FORCES - PACIFIC', 'AP');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_address`
--

CREATE TABLE `tbl_address` (
  `id` int(11) NOT NULL,
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `city` varchar(32) DEFAULT NULL,
  `stateAbbr` varchar(32) DEFAULT NULL,
  `zipCode` varchar(32) DEFAULT NULL,
  `options` varchar(32) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `state`
--
ALTER TABLE `state`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_address`
--
ALTER TABLE `tbl_address`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `state`
--
ALTER TABLE `state`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `tbl_address`
--
ALTER TABLE `tbl_address`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
