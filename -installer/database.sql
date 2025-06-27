-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 07, 2025 at 03:39 PM
-- Server version: 10.5.27-MariaDB
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

START TRANSACTION;

SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */
;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */
;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */
;
/*!40101 SET NAMES utf8mb4 */
;

--
-- Database: `localhost`
--
CREATE DATABASE IF NOT EXISTS `localhost` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `localhost`;

-- --------------------------------------------------------

--
-- Table structure for table `aseq_Request`
--

CREATE TABLE IF NOT EXISTS `aseq_Request` (
    `Id` int(11) NOT NULL AUTO_INCREMENT,
    `MerchandiseId` int(11) DEFAULT NULL COMMENT 'Requested item id',
    `UserId` int(11) DEFAULT NULL COMMENT 'Requester user`s id',
    `UserCode` TINYTEXT DEFAULT NULL COMMENT 'Request user Ip or Unique Code',
    `Collection` TINYTEXT DEFAULT NULL COMMENT 'Request collection name or payments relation, It will be null when the request is not in progress yet',
    `Like` boolean NOT NULL DEFAULT FALSE COMMENT 'Favorited request',
    `Request` boolean NOT NULL DEFAULT FALSE COMMENT 'Requested',
    `Count` int(11) NOT NULL DEFAULT 0 COMMENT 'Numbers of request',
    `Price` float(11) NOT NULL DEFAULT 0 COMMENT 'Bought price',
    `Contact` TINYTEXT DEFAULT NULL COMMENT 'Request delivery contact',
    `Address` text DEFAULT NULL COMMENT 'Request delivery address',
    `Subject` varchar(256) DEFAULT NULL COMMENT 'Request subject',
    `Description` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Request delivery description',
    `Attach` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Request attachments',
    `Priority` int(11) NOT NULL DEFAULT 0 COMMENT 'Request Periority',
    `CreateTime` datetime NOT NULL DEFAULT current_timestamp(),
    `UpdateTime` datetime NOT NULL DEFAULT current_timestamp(),
    `MetaData` longtext DEFAULT NULL,
    PRIMARY KEY (`Id`)
) ENGINE = InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `aseq_Request`
--

CREATE TABLE IF NOT EXISTS `aseq_Response` (
    `Id` int(11) NOT NULL AUTO_INCREMENT,
    `MerchandiseId` int(11) DEFAULT NULL COMMENT 'Requested item id',
    `Collection` TINYTEXT DEFAULT NULL COMMENT 'Related request collection name or id',
    `UserId` int(11) DEFAULT NULL COMMENT 'Requester user`s id',
    `UserCode` TINYTEXT DEFAULT NULL COMMENT 'Request user Ip or Unique Code',
    `Count` int(11) NOT NULL DEFAULT 0 COMMENT 'Numbers in this response',
    `Price` float(11) NOT NULL DEFAULT 0 COMMENT 'Bought price',
    `Contact` TINYTEXT DEFAULT NULL COMMENT 'Request delivery contact',
    `Address` text DEFAULT NULL COMMENT 'Request delivery address',
    `Subject` varchar(256) DEFAULT NULL COMMENT 'Request subject',
    `Description` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Request delivery description',
    `Content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Private content',
    `Attach` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Request attachments',
    `Priority` int(11) NOT NULL DEFAULT 0 COMMENT 'Request Periority',
    `AuthorId` int(11) DEFAULT NULL COMMENT 'Responser user`s id',
    `EditorId` int(11) DEFAULT NULL COMMENT 'Editor user`s id',
    `Status` int(3) NOT NULL DEFAULT 0 COMMENT 'Response status (for example -5 for Rejected -4 for Canceled -3 for Defected -2 for Unavailable -1 for Unaccepted 0 for Unchecked 1 for Accepted 2 for Prepared 3 for Sent 4 for Received 5 for Delivered)',
    `CreateTime` datetime NOT NULL DEFAULT current_timestamp(),
    `UpdateTime` datetime NOT NULL DEFAULT current_timestamp(),
    `MetaData` longtext DEFAULT NULL,
    PRIMARY KEY (`Id`)
) ENGINE = InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `aseq_Merchandise`
--

CREATE TABLE IF NOT EXISTS `aseq_Merchandise` (
    `Id` int(11) NOT NULL AUTO_INCREMENT,
    `ContentId` int(11) DEFAULT NULL COMMENT 'Related item id',
    `SupplierId` int(11) DEFAULT NULL COMMENT 'Related owner or supplier user`s id',
    `AuthorId` int(11) DEFAULT NULL COMMENT 'Author user`s id',
    `EditorId` int(11) DEFAULT NULL COMMENT 'Editor user`s id',
    `Digital` boolean DEFAULT NULL COMMENT 'The item is digital or physical',
    `Count` float(11) NOT NULL DEFAULT 0 COMMENT 'Numbers of exists items',
    `CountUnit` tinytext DEFAULT NULL COMMENT 'The unit of counting',
    `Price` float(11) NOT NULL DEFAULT 0 COMMENT 'Sell price',
    `PriceUnit` tinytext DEFAULT NULL COMMENT 'The unit of prices',
    `Limit` float(11) NOT NULL DEFAULT NULL COMMENT 'Numbers of available items in each request',
    `Discount` float(11) NOT NULL DEFAULT 0 COMMENT 'Discount of price',
    `Total` float(11) NOT NULL DEFAULT 0 COMMENT 'Total of sold items',
    `Volume` float(11) NOT NULL DEFAULT 0 COMMENT 'Total price of sold items',
    `Property` longtext DEFAULT NULL,
    `PrivateGenerator` text DEFAULT NULL COMMENT 'Private content generator path',
    `PrivateTitle` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Private content title',
    `PrivateMessage` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Private content',
    `PrivateAttach` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Private content attachments',
    `Access` int(11) DEFAULT 0,
    `Status` tinytext DEFAULT NULL COMMENT '-1 for Buy 0 for Unpublished and 1 for Sale Merchandise',
    `CreateTime` datetime NOT NULL DEFAULT current_timestamp(),
    `UpdateTime` datetime NOT NULL DEFAULT current_timestamp(),
    `MetaData` longtext DEFAULT NULL,
    PRIMARY KEY (`Id`)
) ENGINE = InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `aseq_History`
--

CREATE TABLE IF NOT EXISTS `aseq_History` (
    `Id` int(11) NOT NULL AUTO_INCREMENT,
    `ContentId` int(11) DEFAULT NULL COMMENT 'Visited item id',
    `UserId` int(11) DEFAULT NULL COMMENT 'Visitor user`s id',
    `Visit` int(11) NOT NULL DEFAULT 0 COMMENT 'Numbers of visit',
    `CreateTime` datetime NOT NULL DEFAULT current_timestamp(),
    `UpdateTime` datetime NOT NULL DEFAULT current_timestamp(),
    `MetaData` longtext DEFAULT NULL,
    PRIMARY KEY (`Id`)
) ENGINE = InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `aseq_Payment`
--

CREATE TABLE IF NOT EXISTS `aseq_Payment` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Relation` VARCHAR(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `Transaction` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `Verify` boolean DEFAULT NULL COMMENT 'The transaction is verified',
  `Source` tinytext DEFAULT NULL,
  `SourceEmail` tinytext DEFAULT NULL,
  `SourceContent` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `SourcePath` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `Value` float DEFAULT NULL,
  `Unit` tinytext DEFAULT NULL,
  `Network` tinytext DEFAULT NULL,
  `Identifier` tinytext DEFAULT NULL,
  `Destination` tinytext DEFAULT NULL,
  `DestinationEmail` tinytext DEFAULT NULL,
  `DestinationContent` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `DestinationPath` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `Others` text DEFAULT NULL,
  `CreateTime` datetime NOT NULL DEFAULT current_timestamp(),
  `UpdateTime` datetime NOT NULL DEFAULT current_timestamp(),
  `MetaData` longtext DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */
;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */
;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */
;