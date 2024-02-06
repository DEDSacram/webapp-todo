-- 4:18 2/6
SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

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
  `ItemID` int(11) DEFAULT NULL,
  `SubcategoryName` varchar(255) NOT NULL,
  `Order` int(11) DEFAULT NULL,
  PRIMARY KEY (`SubcategoryID`),
  KEY `ItemID` (`ItemID`),
  CONSTRAINT `Subcategories_ibfk_1` FOREIGN KEY (`ItemID`) REFERENCES `ToDoItems` (`ItemID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `ToDoItems`;
CREATE TABLE `ToDoItems` (
  `ItemID` int(11) NOT NULL AUTO_INCREMENT,
  `ListID` int(11) DEFAULT NULL,
  `ItemName` varchar(255) NOT NULL,
  PRIMARY KEY (`ItemID`),
  KEY `ListID` (`ListID`),
  CONSTRAINT `ToDoItems_ibfk_1` FOREIGN KEY (`ListID`) REFERENCES `ToDoLists` (`ListID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `ToDoLists`;
CREATE TABLE `ToDoLists` (
  `ListID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) NOT NULL,
  `ListName` varchar(255) NOT NULL,
  PRIMARY KEY (`ListID`),
  KEY `UserID` (`UserID`),
  CONSTRAINT `ToDoLists_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `Users`;
CREATE TABLE `Users` (
  `UserID` int(11) NOT NULL AUTO_INCREMENT,
  `Email` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Cookie` varchar(255) DEFAULT NULL,
  `ExpiryTime` datetime DEFAULT NULL,
  PRIMARY KEY (`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


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