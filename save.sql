-- Adminer 4.8.1 MySQL 11.2.2-MariaDB-1:11.2.2+maria~ubu2204 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `ListSubcategories`;
CREATE TABLE `ListSubcategories` (
  `ListSubcategoryID` int(11) NOT NULL AUTO_INCREMENT,
  `ListID` int(11) NOT NULL,
  `SubcategoryName` varchar(255) NOT NULL,
  PRIMARY KEY (`ListSubcategoryID`),
  KEY `ListID` (`ListID`),
  CONSTRAINT `ListSubcategories_ibfk_1` FOREIGN KEY (`ListID`) REFERENCES `ToDoLists` (`ListID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `ListSubcategories` (`ListSubcategoryID`, `ListID`, `SubcategoryName`) VALUES
(4,	50,	'Test Subcategory 1'),
(5,	50,	'Test Subcategory 2'),
(6,	50,	'Test Subcategory 3');

DROP TABLE IF EXISTS `SharedToDoLists`;
CREATE TABLE `SharedToDoLists` (
  `ShareID` int(11) NOT NULL AUTO_INCREMENT,
  `ListID` int(11) NOT NULL,
  `ShareCode` varchar(255) NOT NULL,
  PRIMARY KEY (`ShareID`),
  UNIQUE KEY `ShareCode` (`ShareCode`),
  KEY `ListID` (`ListID`),
  CONSTRAINT `SharedToDoLists_ibfk_1` FOREIGN KEY (`ListID`) REFERENCES `ToDoLists` (`ListID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `SharedToDoListUsers`;
CREATE TABLE `SharedToDoListUsers` (
  `ShareID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  PRIMARY KEY (`ShareID`,`UserID`),
  KEY `SharedToDoListUsers_ibfk_2` (`UserID`),
  CONSTRAINT `SharedToDoListUsers_ibfk_1` FOREIGN KEY (`ShareID`) REFERENCES `SharedToDoLists` (`ShareID`),
  CONSTRAINT `SharedToDoListUsers_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `Subcategories`;
CREATE TABLE `Subcategories` (
  `SubcategoryID` int(11) NOT NULL AUTO_INCREMENT,
  `ItemID` int(11) NOT NULL,
  `SubcategoryName` varchar(255) NOT NULL,
  `Order` int(11) NOT NULL,
  PRIMARY KEY (`SubcategoryID`),
  KEY `ItemID` (`ItemID`),
  CONSTRAINT `Subcategories_ibfk_1` FOREIGN KEY (`ItemID`) REFERENCES `ToDoItems` (`ItemID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `Subcategories` (`SubcategoryID`, `ItemID`, `SubcategoryName`, `Order`) VALUES
(13,	5,	'Subcategory 3 (Not NULL)',	1),
(14,	5,	'Subcategory 4 (Not NULL)',	2);

DROP TABLE IF EXISTS `ToDoItems`;
CREATE TABLE `ToDoItems` (
  `ItemID` int(11) NOT NULL AUTO_INCREMENT,
  `ListID` int(11) DEFAULT NULL,
  `ItemName` varchar(255) NOT NULL,
  PRIMARY KEY (`ItemID`),
  KEY `ListID` (`ListID`),
  CONSTRAINT `ToDoItems_ibfk_1` FOREIGN KEY (`ListID`) REFERENCES `ToDoLists` (`ListID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `ToDoItems` (`ItemID`, `ListID`, `ItemName`) VALUES
(5,	50,	'Dummy Task 1'),
(6,	50,	'Dummy Task 2');

DROP TABLE IF EXISTS `ToDoLists`;
CREATE TABLE `ToDoLists` (
  `ListID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) NOT NULL,
  `ListName` varchar(255) NOT NULL,
  PRIMARY KEY (`ListID`),
  KEY `UserID` (`UserID`),
  CONSTRAINT `ToDoLists_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `ToDoLists` (`ListID`, `UserID`, `ListName`) VALUES
(50,	6,	'Dummy List');

DROP TABLE IF EXISTS `Users`;
CREATE TABLE `Users` (
  `UserID` int(11) NOT NULL AUTO_INCREMENT,
  `Email` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Cookie` varchar(255) DEFAULT NULL,
  `ExpiryTime` datetime DEFAULT NULL,
  PRIMARY KEY (`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `Users` (`UserID`, `Email`, `Password`, `Cookie`, `ExpiryTime`) VALUES
(6,	'AHOOOOOYYY@yahoo.com',	'$2y$10$vXMmybDVlxgMaZwR/Jl3ruJLeHAB9cpr/gfXGOxUKDgaSGwwQkQtW',	'0b944db43bc9b1c1f1fde8645abfdafc379741021f067625571c63e768f8d98e',	'2024-03-07 14:25:41');

DROP TABLE IF EXISTS `UserSettings`;
CREATE TABLE `UserSettings` (
  `SettingID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `SettingName` varchar(255) NOT NULL,
  `SettingValue` varchar(255) NOT NULL,
  PRIMARY KEY (`SettingID`),
  KEY `UserID` (`UserID`),
  CONSTRAINT `UserSettings_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- 2024-02-06 16:27:47