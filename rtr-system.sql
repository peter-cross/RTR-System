-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 21, 2017 at 08:18 PM
-- Server version: 10.1.26-MariaDB
-- PHP Version: 7.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rtr-system`
--

-- --------------------------------------------------------

--
-- Table structure for table `cancellation`
--

CREATE TABLE `cancellation` (
  `CustomerID` int(11) NOT NULL,
  `ReservationDate` date NOT NULL,
  `Comment` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cancellation`
--

INSERT INTO `cancellation` (`CustomerID`, `ReservationDate`, `Comment`) VALUES
(1, '2017-11-22', 'Plans changed');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `CustomerID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `PhoneNumber` varchar(50) DEFAULT NULL,
  `Email` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`CustomerID`, `Name`, `PhoneNumber`, `Email`) VALUES
(1, 'Peter Cross', '604-259-8443', 'Peter.Cross@email.com');

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `Name` varchar(255) NOT NULL,
  `PhoneNumber` varchar(50) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `Password` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `employee`
--

INSERT INTO `employee` (`Name`, `PhoneNumber`, `Email`, `Password`) VALUES
('Peter Cross', '604-259-8443', 'pcross04@mylangara.bc.ca', '6042598443');

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE `location` (
  `LocationID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `location`
--

INSERT INTO `location` (`LocationID`, `Name`, `Description`) VALUES
(1, 'Main Hall', '1st floor common room'),
(2, 'Outside Deck', 'Deck with view on Bridge'),
(3, 'Semi-Private Room 1st floor', '1st floor semi-private room at the end of main hall'),
(4, '2nd floor private room 1', '2nd floor room with view on the ocean'),
(5, '2nd floor private room 2', '2nd floor room with view on the bridge north'),
(6, '2nd floor private room 3', '2nd floor room with view on the bridge south');

-- --------------------------------------------------------

--
-- Table structure for table `occasion`
--

CREATE TABLE `occasion` (
  `OccasionID` int(11) NOT NULL,
  `Description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `occasion`
--

INSERT INTO `occasion` (`OccasionID`, `Description`) VALUES
(1, 'Corporate Event'),
(2, 'Birthday'),
(3, 'Aniversary'),
(4, 'Baby Shower'),
(5, 'New Year'),
(6, 'Lunar New Year'),
(7, 'Valentines Day'),
(8, 'St.Patrick\'s Day'),
(9, 'Canada Day'),
(10, 'Helloween'),
(11, 'Christmas'),
(12, 'Other');

-- --------------------------------------------------------

--
-- Table structure for table `parkingspace`
--

CREATE TABLE `parkingspace` (
  `SpaceID` int(11) NOT NULL,
  `LotNumber` int(11) NOT NULL,
  `VehicleSize` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `parkingspace`
--

INSERT INTO `parkingspace` (`SpaceID`, `LotNumber`, `VehicleSize`) VALUES
(1, 1, 'M'),
(2, 2, 'M'),
(3, 3, 'M'),
(4, 4, 'M'),
(5, 5, 'L'),
(6, 6, 'L'),
(7, 7, 'S'),
(8, 8, 'S'),
(9, 9, 'S'),
(10, 10, 'XL');

-- --------------------------------------------------------

--
-- Table structure for table `reservation`
--

CREATE TABLE `reservation` (
  `CustomerID` int(11) NOT NULL,
  `ReservationDate` date NOT NULL,
  `ReservationTime` varchar(20) NOT NULL,
  `Period` int(11) NOT NULL,
  `OccasionID` int(11) NOT NULL,
  `Instructions` varchar(255) DEFAULT NULL,
  `Token` varchar(255) DEFAULT NULL,
  `AccessCode` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `reservation`
--

INSERT INTO `reservation` (`CustomerID`, `ReservationDate`, `ReservationTime`, `Period`, `OccasionID`, `Instructions`, `Token`, `AccessCode`) VALUES
(1, '2017-11-21', '5:00 PM', 2, 12, '', 'mufmnjwx5qq9n76640a0h1loh5xybh0to80x', 'P2P'),
(1, '2017-11-22', '5:00 PM', 2, 12, '', '119x1q4dk7mmwjcwxxedif9hsfdoh7sse40d', 'I3P');

-- --------------------------------------------------------

--
-- Table structure for table `reservedparkingspace`
--

CREATE TABLE `reservedparkingspace` (
  `SpaceID` int(11) NOT NULL,
  `CustomerID` int(11) NOT NULL,
  `ReservationDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `reservedtable`
--

CREATE TABLE `reservedtable` (
  `TableID` int(11) NOT NULL,
  `CustomerID` int(11) NOT NULL,
  `ReservationDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `reservedtable`
--

INSERT INTO `reservedtable` (`TableID`, `CustomerID`, `ReservationDate`) VALUES
(1, 1, '2017-11-21'),
(5, 1, '2017-11-22');

-- --------------------------------------------------------

--
-- Table structure for table `restauranttable`
--

CREATE TABLE `restauranttable` (
  `TableID` int(11) NOT NULL,
  `Row` int(11) NOT NULL,
  `Spot` int(11) NOT NULL,
  `LocationID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `restauranttable`
--

INSERT INTO `restauranttable` (`TableID`, `Row`, `Spot`, `LocationID`) VALUES
(1, 1, 1, 1),
(2, 1, 2, 1),
(3, 1, 3, 1),
(4, 1, 4, 1),
(5, 2, 1, 1),
(6, 2, 2, 1),
(7, 2, 3, 1),
(8, 2, 4, 1),
(9, 3, 1, 1),
(10, 3, 2, 1),
(11, 3, 3, 1),
(12, 3, 4, 1),
(13, 1, 1, 2),
(14, 1, 2, 2),
(15, 1, 3, 2),
(16, 1, 4, 2),
(17, 1, 5, 2),
(18, 2, 1, 2),
(19, 2, 2, 2),
(20, 2, 3, 2),
(21, 2, 4, 2),
(22, 2, 5, 2),
(23, 1, 1, 3),
(24, 1, 2, 3),
(25, 1, 1, 4),
(26, 1, 1, 5),
(27, 1, 1, 6);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cancellation`
--
ALTER TABLE `cancellation`
  ADD PRIMARY KEY (`CustomerID`,`ReservationDate`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`CustomerID`);

--
-- Indexes for table `location`
--
ALTER TABLE `location`
  ADD PRIMARY KEY (`LocationID`);

--
-- Indexes for table `occasion`
--
ALTER TABLE `occasion`
  ADD PRIMARY KEY (`OccasionID`);

--
-- Indexes for table `parkingspace`
--
ALTER TABLE `parkingspace`
  ADD PRIMARY KEY (`SpaceID`);

--
-- Indexes for table `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`CustomerID`,`ReservationDate`),
  ADD KEY `OccasionID` (`OccasionID`);

--
-- Indexes for table `reservedparkingspace`
--
ALTER TABLE `reservedparkingspace`
  ADD PRIMARY KEY (`SpaceID`,`CustomerID`,`ReservationDate`),
  ADD KEY `CustomerID` (`CustomerID`,`ReservationDate`);

--
-- Indexes for table `reservedtable`
--
ALTER TABLE `reservedtable`
  ADD PRIMARY KEY (`TableID`,`CustomerID`,`ReservationDate`),
  ADD KEY `CustomerID` (`CustomerID`,`ReservationDate`);

--
-- Indexes for table `restauranttable`
--
ALTER TABLE `restauranttable`
  ADD PRIMARY KEY (`TableID`),
  ADD KEY `LocationID` (`LocationID`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cancellation`
--
ALTER TABLE `cancellation`
  ADD CONSTRAINT `cancellation_ibfk_1` FOREIGN KEY (`CustomerID`,`ReservationDate`) REFERENCES `reservation` (`CustomerID`, `ReservationDate`);

--
-- Constraints for table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `reservation_ibfk_1` FOREIGN KEY (`CustomerID`) REFERENCES `customer` (`CustomerID`),
  ADD CONSTRAINT `reservation_ibfk_2` FOREIGN KEY (`OccasionID`) REFERENCES `occasion` (`OccasionID`);

--
-- Constraints for table `reservedparkingspace`
--
ALTER TABLE `reservedparkingspace`
  ADD CONSTRAINT `reservedparkingspace_ibfk_1` FOREIGN KEY (`SpaceID`) REFERENCES `parkingspace` (`SpaceID`),
  ADD CONSTRAINT `reservedparkingspace_ibfk_2` FOREIGN KEY (`CustomerID`,`ReservationDate`) REFERENCES `reservation` (`CustomerID`, `ReservationDate`);

--
-- Constraints for table `reservedtable`
--
ALTER TABLE `reservedtable`
  ADD CONSTRAINT `reservedtable_ibfk_1` FOREIGN KEY (`TableID`) REFERENCES `restauranttable` (`TableID`),
  ADD CONSTRAINT `reservedtable_ibfk_2` FOREIGN KEY (`CustomerID`,`ReservationDate`) REFERENCES `reservation` (`CustomerID`, `ReservationDate`);

--
-- Constraints for table `restauranttable`
--
ALTER TABLE `restauranttable`
  ADD CONSTRAINT `restauranttable_ibfk_1` FOREIGN KEY (`LocationID`) REFERENCES `location` (`LocationID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
