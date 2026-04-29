-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: %%DATABASE%%:3306
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
-- Database: `%%DATABASE%%`
--
CREATE DATABASE IF NOT EXISTS `%%DATABASE%%` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `%%DATABASE%%`;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%Shop_Request`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%Shop_Request` (
    `Id` int(11) NOT NULL AUTO_INCREMENT,
    `MerchandiseId` int(11) DEFAULT NULL COMMENT 'Requested item id',
    `UserId` int(11) DEFAULT NULL COMMENT 'Requester user`s id',
    `UserCode` TINYTEXT DEFAULT NULL COMMENT 'Request user Ip or Unique Code',
    `Collection` TINYTEXT DEFAULT NULL COMMENT 'Request collection name or payments relation, It will be null when the request is not in progress yet',
    `Group` TINYTEXT DEFAULT NULL COMMENT 'Favorited request group',
    `Count` float(11) NOT NULL DEFAULT 0 COMMENT 'Numbers of request',
    `Amount` float(11) DEFAULT NULL COMMENT 'Bought price',
    `Contact` TINYTEXT DEFAULT NULL COMMENT 'Request delivery contact',
    `Address` text DEFAULT NULL COMMENT 'Request delivery address',
    `Subject` varchar(256) DEFAULT NULL COMMENT 'Request subject',
    `Description` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Request delivery description',
    `Priority` int(11) NOT NULL DEFAULT 0 COMMENT 'Request Periority',
    `CreateTime` datetime NOT NULL DEFAULT current_timestamp(),
    `UpdateTime` datetime NOT NULL DEFAULT current_timestamp(),
    `MetaData` longtext DEFAULT NULL,
    PRIMARY KEY (`Id`)
) ENGINE = InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%Shop_Request`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%Shop_Response` (
    `Id` int(11) NOT NULL AUTO_INCREMENT,
    `MerchandiseId` int(11) DEFAULT NULL COMMENT 'Requested item id',
    `UserId` int(11) DEFAULT NULL COMMENT 'Requester user`s id',
    `UserCode` TINYTEXT DEFAULT NULL COMMENT 'Request user Ip or Unique Code',
    `Collection` TINYTEXT DEFAULT NULL COMMENT 'Related request collection name or id',
    `Group` TINYTEXT DEFAULT NULL COMMENT 'Favorited request group',
    `Count` float(11) NOT NULL DEFAULT 0 COMMENT 'Numbers in this response',
    `Amount` float(11) DEFAULT NULL COMMENT 'Bought price',
    `Contact` TINYTEXT DEFAULT NULL COMMENT 'Request delivery contact',
    `Address` text DEFAULT NULL COMMENT 'Request delivery address',
    `Subject` varchar(256) DEFAULT NULL COMMENT 'Request subject',
    `Description` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Request delivery description',
    `Priority` int(11) NOT NULL DEFAULT 0 COMMENT 'Request Periority',
    `Private` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Private content',
    `AuthorId` int(11) DEFAULT NULL COMMENT 'Responser user`s id',
    `EditorId` int(11) DEFAULT NULL COMMENT 'Editor user`s id',
    `Status` VARCHAR(255) DEFAULT NULL COMMENT 'Response status',
    `CreateTime` datetime NOT NULL DEFAULT current_timestamp(),
    `UpdateTime` datetime NOT NULL DEFAULT current_timestamp(),
    `MetaData` longtext DEFAULT NULL,
    PRIMARY KEY (`Id`)
) ENGINE = InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%Shop_Merchandise`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%Shop_Merchandise` (
    `Id` int(11) NOT NULL AUTO_INCREMENT,
    `ContentId` int(11) DEFAULT NULL COMMENT 'Related item id',
    `SupplierId` int(11) DEFAULT NULL COMMENT 'Related owner or supplier user`s id',
    `AuthorId` int(11) DEFAULT NULL COMMENT 'Author user`s id',
    `EditorId` int(11) DEFAULT NULL COMMENT 'Editor user`s id',
    `Digital` TINYINT(1) DEFAULT NULL COMMENT 'The item is digital or physical',
    `Name` varchar(256) DEFAULT NULL COMMENT 'The specific merchandise name',
    `Title` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
    `Description` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
    `Image` varchar(1024) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
    `Amount` float(11) DEFAULT NULL COMMENT 'Sell price',
    `Currency` tinytext DEFAULT NULL COMMENT 'The unit of prices',
    `Discount` float(11) NOT NULL DEFAULT 0 COMMENT 'Discount of price in percent (%)',
    `Count` float(11) NOT NULL DEFAULT 0 COMMENT 'Numbers of exists items',
    `Unit` tinytext DEFAULT NULL COMMENT 'The unit of counting',
    `Limit` float(11) DEFAULT NULL COMMENT 'Numbers of available items in each request',
    `Media` longtext DEFAULT NULL,
    `Volume` float(11) NOT NULL DEFAULT 0 COMMENT 'Total price of sold items',
    `Total` float(11) NOT NULL DEFAULT 0 COMMENT 'Total of sold items',
    `Property` longtext DEFAULT NULL,
    `PrivateGenerator` text DEFAULT NULL COMMENT 'Private content generator path',
    `PrivateSubject` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Private content title',
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
-- Table structure for table `%%PREFIX%%Shop_Discount`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%Shop_Discount` (
    `Id` int(11) NOT NULL AUTO_INCREMENT,
    `Name` varchar(256) NOT NULL COMMENT 'Discount code',
    `Title` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Discount code title',
    `MerchandiseId` int(11) DEFAULT NULL COMMENT 'Specialized item id',
    `UserId` int(11) DEFAULT NULL COMMENT 'Specialized user`s id',
    `UserCode` TINYTEXT DEFAULT NULL COMMENT 'Specialized User Ip or Unique Code',
    `Contact` TINYTEXT DEFAULT NULL COMMENT 'Specialized contact',
    `Count` int(11) NOT NULL DEFAULT 1 COMMENT 'Count of discount',
    `Value` TINYTEXT NOT NULL COMMENT 'Discount value or percentage',
    `Number` int(11) NOT NULL DEFAULT 0 COMMENT 'Numbers of used',
    `MinimumValue` float(11) DEFAULT 0 COMMENT 'Minimum discount value in percentage',
    `MaximumValue` float(11) DEFAULT 100 COMMENT 'Maximum discount value in percentage',
    `MinimumAmount` float(11) DEFAULT 0 COMMENT 'Minimum amount to use',
    `MaximumAmount` float(11) DEFAULT NULL COMMENT 'Maximum amount to use',
    `Condition` longtext DEFAULT NULL,
    `Access` int(11) DEFAULT 0,
    `Status` tinytext DEFAULT NULL,
    `CreateTime` datetime NOT NULL DEFAULT current_timestamp(),
    `StartTime` datetime NOT NULL DEFAULT current_timestamp(),
    `EndTime` datetime DEFAULT NULL,
    `MetaData` longtext DEFAULT NULL,
    PRIMARY KEY (`Id`),
    UNIQUE KEY `Name` (`Name`)
) ENGINE = InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_bin;

-- --------------------------------------------------------

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */
;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */
;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */
;