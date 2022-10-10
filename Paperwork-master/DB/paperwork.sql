-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 26, 2022 at 02:28 AM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 8.0.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `paperwork`
--

-- --------------------------------------------------------

--
-- Table structure for table `email`
--

CREATE TABLE `email` (
  `formID` int(11) NOT NULL,
  `email` varchar(60) NOT NULL,
  `hasBeenSentTo` tinyint(1) NOT NULL,
  `sendOrder` int(11) NOT NULL,
  `hasApproved` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `fields`
--

CREATE TABLE `fields` (
  `FieldID` int(11) NOT NULL,
  `FormID` int(11) NOT NULL,
  `content` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `forms`
--

CREATE TABLE `forms` (
  `formID` int(11) NOT NULL,
  `formType` varchar(40) NOT NULL,
  `authorID` varchar(150) NOT NULL,
  `dateCreated` varchar(50) NOT NULL,
  `lastEdited` varchar(50) DEFAULT NULL,
  `formState` varchar(20) DEFAULT NULL,
  `formTitle` varchar(100) NOT NULL,
  `formComments` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `templatefields`
--

CREATE TABLE `templatefields` (
  `formType` varchar(40) NOT NULL,
  `FieldID` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `xPos` decimal(11,1) NOT NULL,
  `yPos` decimal(11,1) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `type` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `templatefields`
--

INSERT INTO `templatefields` (`formType`, `FieldID`, `name`, `xPos`, `yPos`, `width`, `height`, `type`) VALUES
('opsPlan', 753, 'DatePrepared', '66.3', '24.0', 50, 5, 'date'),
('opsPlan', 754, 'ActivityName1', '39.0', '34.6', 114, 4, 'text'),
('opsPlan', 755, 'DateStart', '29.1', '39.9', 58, 4, 'date'),
('opsPlan', 756, 'DateEnd', '93.7', '39.9', 60, 4, 'date'),
('opsPlan', 757, 'EventAddressHomeUnit', '53.6', '46.4', 3, 3, 'checkbox'),
('opsPlan', 758, 'EventAddress', '58.7', '45.0', 95, 4, 'text'),
('opsPlan', 759, 'Unit', '22.0', '50.0', 60, 5, 'text'),
('opsPlan', 760, 'Group', '123.1', '50.0', 30, 5, 'text'),
('opsPlan', 761, 'SQCCInitials', '179.2', '43.9', 22, 4, 'signature'),
('opsPlan', 762, 'GroupCCInitials', '179.2', '47.8', 22, 4, 'signature'),
('opsPlan', 763, 'RiskManagementForm', '158.6', '51.7', 3, 3, 'checkbox'),
('opsPlan', 764, 'WG/SEInitials', '179.2', '59.3', 22, 4, 'signature'),
('opsPlan', 765, 'WG/JAInitials', '179.2', '63.3', 22, 4, 'signature'),
('opsPlan', 766, 'WG/CCInitials', '179.2', '66.7', 22, 4, 'signature'),
('opsPlan', 767, 'EventSupportsAeroEd', '15.0', '64.4', 3, 3, 'checkbox'),
('opsPlan', 768, 'EventSupportsAEX', '32.3', '64.4', 3, 3, 'checkbox'),
('opsPlan', 769, 'EventSupportsCadetPrograms', '45.5', '64.4', 3, 3, 'checkbox'),
('opsPlan', 770, 'EventSupportsEmergencyServices', '75.0', '64.4', 3, 3, 'checkbox'),
('opsPlan', 771, 'EventSupportsPD', '111.4', '64.4', 3, 3, 'checkbox'),
('opsPlan', 772, 'EventSupportsRR/PA', '123.5', '64.4', 3, 3, 'checkbox'),
('opsPlan', 773, 'EventTypeFundraising', '68.3', '73.9', 3, 3, 'checkbox'),
('opsPlan', 774, 'EventTypeHighAdventure', '91.9', '73.9', 3, 3, 'checkbox'),
('opsPlan', 775, 'EventTypeAirportOpenHouse', '145.5', '73.9', 3, 3, 'checkbox'),
('opsPlan', 776, 'EventTypeAirshow', '180.1', '73.9', 3, 3, 'checkbox'),
('opsPlan', 777, 'EventTypeBivouac', '15.0', '79.0', 3, 3, 'checkbox'),
('opsPlan', 778, 'EventTypeDocumentsRequiringSignatures', '31.6', '79.0', 3, 3, 'checkbox'),
('opsPlan', 779, 'EventTypeFlightlineDuties/AircraftMarshaling', '85.2', '79.0', 3, 3, 'checkbox'),
('opsPlan', 780, 'EventTypeMarksmanship', '139.9', '79.0', 3, 3, 'checkbox'),
('opsPlan', 781, 'EventTypeModelRocketry', '166.7', '79.0', 3, 3, 'checkbox'),
('opsPlan', 782, 'EventTypeParkingCrowdOrAccessControl', '15.0', '84.3', 3, 3, 'checkbox'),
('opsPlan', 783, 'EventTypeOther', '68.3', '84.3', 3, 3, 'checkbox'),
('opsPlan', 784, 'EventTypeOtherFillIn', '81.5', '82.7', 120, 4, 'text'),
('opsPlan', 785, 'ActivityOpenToHostUnit', '103.2', '90.3', 3, 3, 'checkbox'),
('opsPlan', 786, 'ActivityOpenToHostUnitGroup', '123.1', '90.3', 3, 3, 'checkbox'),
('opsPlan', 787, 'ActivityOpenToWAWG', '154.7', '90.3', 3, 3, 'checkbox'),
('opsPlan', 788, 'ActivityOpenToAllWings', '172.0', '90.3', 3, 3, 'checkbox'),
('opsPlan', 789, 'AttendeesOfficersOnly', '32.8', '95.1', 3, 3, 'checkbox'),
('opsPlan', 790, 'AttendeesCadetsAndOfficers', '56.8', '95.1', 3, 3, 'checkbox'),
('opsPlan', 791, 'EstimateNumberOfAttendeesCadets', '150.3', '94.0', 17, 4, 'text'),
('opsPlan', 792, 'EstimateNumberOfAttendeesOfficers', '180.8', '94.0', 17, 4, 'text'),
('opsPlan', 793, 'EventDescriptionAndObjectives1', '14.8', '109.7', 185, 40, 'text'),
('opsPlan', 794, 'UniformOfTheDay', '44.8', '151.2', 155, 4, 'text'),
('opsPlan', 795, 'ActivityWillNotRequireParticipationCost', '27.2', '156.6', 3, 3, 'checkbox'),
('opsPlan', 796, 'ActivityWillRequireParticipationCost', '42.5', '156.6', 3, 3, 'checkbox'),
('opsPlan', 797, 'ParticipationCostPerParticipant', '93.5', '155.9', 17, 4, 'text'),
('opsPlan', 798, 'ParticipationCostAttached', '151.7', '156.3', 3, 3, 'checkbox'),
('opsPlan', 799, 'ActivityDirectorProjectOfficerAttendingYes', '144.1', '161.4', 3, 3, 'checkbox'),
('opsPlan', 800, 'ActivityDirectorProjectOfficerName', '21.2', '165.6', 87, 6, 'text'),
('opsPlan', 801, 'ActivityDirectorProjectOfficerGrade', '117.8', '165.6', 41, 6, 'text'),
('opsPlan', 802, 'ActivityDirectorProjectOfficerCAPID', '167.4', '165.6', 35, 6, 'text'),
('opsPlan', 803, 'ActivityDirectorProjectOfficerPhone', '21.2', '172.7', 87, 6, 'text'),
('opsPlan', 804, 'ActivityDirectorProjectOfficerEmail', '117.8', '172.7', 85, 6, 'text'),
('opsPlan', 805, 'SafetyOfficerAttendingYes', '144.1', '178.5', 3, 3, 'checkbox'),
('opsPlan', 806, 'SafetyOfficerName', '21.2', '183.3', 87, 6, 'text'),
('opsPlan', 807, 'SafetyOfficerGrade', '117.8', '183.3', 41, 6, 'text'),
('opsPlan', 808, 'SafetyOfficerCAPID', '167.4', '183.3', 35, 6, 'text'),
('opsPlan', 809, 'SafetyOfficerPhone', '21.2', '189.8', 87, 6, 'text'),
('opsPlan', 810, 'SafetyOfficerEmail', '117.8', '189.8', 85, 6, 'text'),
('opsPlan', 811, 'PublicAffairsOfficerAttendingYes', '144.1', '196.7', 3, 3, 'checkbox'),
('opsPlan', 812, 'PubilcAffairsOfficerAttendingNo', '153.3', '196.7', 3, 3, 'checkbox'),
('opsPlan', 813, 'RequestWGPASupport', '163.5', '196.7', 3, 3, 'checkbox'),
('opsPlan', 814, 'PublicAffairsOfficerName', '21.2', '200.9', 87, 6, 'text'),
('opsPlan', 815, 'PublicAffairsOfficerGrade', '117.8', '200.9', 41, 6, 'text'),
('opsPlan', 816, 'PublicAffairsOfficerCAPID', '167.4', '200.9', 35, 6, 'text'),
('opsPlan', 817, 'PublicAffairsOfficerPhone', '21.2', '207.6', 87, 6, 'text'),
('opsPlan', 818, 'PublicAffairsOfficerEmail', '117.8', '207.6', 85, 6, 'text'),
('opsPlan', 819, 'SpecialEquipment', '45.0', '219.6', 155, 4, 'text'),
('opsPlan', 820, 'Aircraft172/182', '27.9', '225.8', 3, 3, 'checkbox'),
('opsPlan', 821, 'Aircraft206', '44.3', '225.8', 3, 3, 'checkbox'),
('opsPlan', 822, 'AircraftGlider', '55.6', '225.8', 3, 3, 'checkbox'),
('opsPlan', 823, 'AircraftNotes', '82.0', '224.7', 118, 4, 'text'),
('opsPlan', 824, 'VehiclesCorporate', '29.8', '232.1', 3, 3, 'checkbox'),
('opsPlan', 825, 'VehiclesPOV', '49.0', '232.1', 3, 3, 'checkbox'),
('opsPlan', 826, 'VehiclesNotes', '81.7', '230.9', 118, 4, 'text'),
('opsPlan', 827, 'VehicleID1', '13.2', '245.7', 19, 4, 'text'),
('opsPlan', 828, 'VehicleLocation1', '33.7', '245.7', 35, 4, 'text'),
('opsPlan', 829, 'VehicleID2', '70.2', '245.7', 19, 4, 'text'),
('opsPlan', 830, 'VehicleLocation2', '91.0', '245.7', 35, 4, 'text'),
('opsPlan', 831, 'VehicleID3', '127.5', '245.7', 19, 4, 'text'),
('opsPlan', 832, 'VehicleLocation3', '150.1', '245.7', 35, 4, 'text'),
('opsPlan', 833, 'VehicleID4', '13.2', '250.8', 19, 4, 'text'),
('opsPlan', 834, 'VehicleLocation4', '33.7', '250.8', 35, 4, 'text'),
('opsPlan', 835, 'VehicleID5', '70.2', '250.8', 19, 4, 'text'),
('opsPlan', 836, 'VehicleLocation5', '91.0', '250.8', 35, 4, 'text'),
('opsPlan', 837, 'VehicleID6', '127.5', '250.8', 19, 4, 'text'),
('opsPlan', 838, 'VehicleLocation6', '150.1', '250.8', 35, 4, 'text'),
('opsPlan', 839, 'ActivityName2', '44.6', '314.3', 155, 5, 'text'),
('opsPlan', 840, 'EventDescriptionAndObjectives2', '14.8', '326.3', 187, 206, 'text'),
('6080', 841, 'CadetName', '32.0', '56.0', 48, 5, 'text'),
('6080', 842, 'CadetGrade', '101.0', '56.0', 45, 5, 'text'),
('6080', 843, 'CAPID', '159.0', '56.0', 42, 5, 'text'),
('6080', 844, 'UnitCharterNumber', '43.0', '61.0', 36, 5, 'text'),
('6080', 845, 'ActivityName', '103.0', '61.0', 43, 5, 'text'),
('6080', 846, 'ActivityDate', '167.0', '61.0', 33, 5, 'date'),
('6080', 847, 'GradeAndNameOfSupervisingSenior', '65.0', '83.0', 39, 5, 'text'),
('6080', 848, 'SupervisingSeniorInitial', '183.0', '83.0', 18, 5, 'text'),
('6080', 849, 'Parent/Guardian Name', '38.0', '105.0', 43, 5, 'text'),
('6080', 850, 'RelationshipToCadet', '102.0', '105.0', 44, 5, 'text'),
('6080', 851, 'ContactNumberOnDateOfActivity', '176.0', '105.0', 27, 5, 'text'),
('6080', 852, 'CAPF31', '16.3', '126.5', 2, 2, 'checkbox'),
('6080', 853, 'other', '107.8', '126.5', 2, 2, 'checkbox'),
('6080', 854, 'CAPF160', '16.3', '132.0', 2, 2, 'checkbox'),
('6080', 855, 'CAPF163', '16.3', '137.2', 2, 2, 'checkbox'),
('6080', 856, 'Parent/GuardianSignature', '82.0', '158.0', 63, 10, 'signature'),
('6080', 857, 'Parent/GuardianSignatureDate', '157.0', '158.0', 44, 10, 'date'),
('6080', 858, 'ActivityName', '35.0', '205.0', 81, 5, 'text'),
('6080', 859, 'ActivityDateTime', '149.0', '205.0', 53, 5, 'datetime-local'),
('6080', 860, 'ActivityLocation', '38.0', '211.0', 78, 5, 'text'),
('6080', 861, 'ActivityFormatClassTourLight', '135.5', '211.2', 2, 2, 'checkbox'),
('6080', 862, 'ActivityFormatBackcountry', '174.4', '212.8', 2, 2, 'checkbox'),
('6080', 863, 'ActivityFormatPhysical', '142.6', '215.2', 2, 2, 'checkbox'),
('6080', 864, 'ActivityFormatFlying', '174.3', '217.0', 2, 2, 'checkbox'),
('6080', 865, 'ParticipationFee', '38.0', '217.0', 26, 5, 'text'),
('6080', 866, 'PaymentDue', '87.0', '217.0', 29, 5, 'text'),
('6080', 867, 'TransportationYes', '49.9', '223.6', 2, 2, 'checkbox'),
('6080', 868, 'TransportationNo', '62.2', '223.6', 2, 2, 'checkbox'),
('6080', 869, 'TransportationExtraFee', '88.0', '223.0', 30, 5, 'text'),
('6080', 870, 'TransportationRallyPoint', '156.0', '223.0', 43, 5, 'text'),
('6080', 871, 'HighAdventureYes', '42.1', '228.7', 2, 2, 'checkbox'),
('6080', 872, 'HighAdventureNo', '54.3', '228.7', 2, 2, 'checkbox'),
('6080', 873, 'HighAdventureExplain', '35.0', '233.0', 82, 7, 'text'),
('6080', 874, 'CAPPointOfContactName', '158.0', '228.0', 43, 5, 'text'),
('6080', 875, 'SupervisingStaffMenOnly', '119.3', '237.4', 2, 2, 'checkbox'),
('6080', 876, 'SupervisingStaffWomenOnly', '142.2', '237.4', 2, 2, 'checkbox'),
('6080', 877, 'SupervisingStaffMenAndWomen', '169.7', '237.4', 2, 2, 'checkbox'),
('6080', 878, 'MealsProvided', '27.2', '242.4', 2, 2, 'checkbox'),
('6080', 879, 'MealsBringOwnFood', '46.5', '242.4', 2, 2, 'checkbox'),
('6080', 880, 'MealsBringMoney', '74.6', '242.4', 2, 2, 'checkbox'),
('6080', 881, 'EmergencyPhone', '145.0', '241.0', 57, 4, 'text'),
('6080', 882, 'EquipmentNeededSeeWebsiteOrFlier', '46.2', '248.0', 2, 2, 'checkbox'),
('6080', 883, 'EquipmentNeeded', '15.0', '252.0', 100, 10, 'text'),
('6080', 884, 'ActivityWebsite', '142.0', '246.0', 60, 5, 'text'),
('6080', 885, 'EstimatedTimeReturning', '120.0', '257.0', 81, 5, 'text'),
('160', 886, 'Activity', '14.2', '28.8', 124, 6, 'text'),
('160', 887, 'Date', '141.6', '28.8', 61, 6, 'date'),
('160', 888, 'PreparedByName', '14.2', '47.1', 83, 5, 'text'),
('160', 889, 'PreparedByRank', '99.9', '47.1', 39, 5, 'text'),
('160', 890, 'PreparedByDutyTitle/Position', '141.6', '47.1', 61, 5, 'text'),
('160', 891, 'PreparedByUnit', '14.2', '57.9', 61, 5, 'text'),
('160', 892, 'PreparedByEmail', '77.0', '57.9', 62, 5, 'text'),
('160', 893, 'PreparedByTelephone', '140.8', '57.9', 61, 5, 'text'),
('160', 894, 'PreparedBySignature', '14.2', '67.1', 188, 6, 'signature'),
('160', 895, 'SubActivityTaskSource1', '13.4', '133.2', 29, 16, 'text'),
('160', 896, 'HazardOutcome1', '43.7', '133.2', 40, 16, 'text'),
('160', 897, 'InitialRisk1', '84.9', '133.2', 15, 16, 'text'),
('160', 898, 'Controls1', '101.2', '133.2', 39, 16, 'text'),
('160', 899, 'HowToImplement1', '148.2', '133.2', 38, 11, 'text'),
('160', 900, 'WhoWillImplement1', '148.2', '145.7', 38, 4, 'text'),
('160', 901, 'ResidualRisk1', '187.4', '133.2', 16, 16, 'text'),
('160', 902, 'SubActivityTaskSource2', '13.4', '150.6', 29, 16, 'text'),
('160', 903, 'HazardOutcome2', '43.7', '150.6', 40, 16, 'text'),
('160', 904, 'InitialRisk2', '84.9', '150.6', 15, 16, 'text'),
('160', 905, 'Controls2', '101.2', '150.6', 39, 16, 'text'),
('160', 906, 'HowToImplement2', '148.2', '150.6', 38, 11, 'text'),
('160', 907, 'WhoWillImplement2', '148.2', '164.4', 38, 4, 'text'),
('160', 908, 'ResidualRisk2', '187.4', '150.6', 16, 16, 'text'),
('160', 909, 'SubActivityTaskSource3', '13.4', '169.8', 29, 16, 'text'),
('160', 910, 'HazardOutcome3', '43.7', '169.8', 40, 16, 'text'),
('160', 911, 'InitialRisk3', '84.9', '169.8', 15, 16, 'text'),
('160', 912, 'Controls3', '101.2', '169.8', 39, 16, 'text'),
('160', 913, 'HowToImplement3', '148.2', '169.8', 38, 11, 'text'),
('160', 914, 'WhoWillImplement3', '148.2', '181.4', 38, 4, 'text'),
('160', 915, 'ResidualRisk3', '187.4', '169.8', 16, 16, 'text'),
('160', 916, 'HighestResidualRiskLevelExtremelyHigh', '18.0', '200.1', 6, 6, 'checkbox'),
('160', 917, 'HighestResidualRiskLevelHigh', '65.3', '200.1', 6, 6, 'checkbox'),
('160', 918, 'HighestResidualRiskLevelMedium', '113.0', '200.1', 6, 6, 'checkbox'),
('160', 919, 'HighestResidualRiskLevelLow', '160.2', '200.1', 6, 6, 'checkbox'),
('160', 920, 'OverallSupervisionPlanAndRecommendedCourseOfAction', '13.4', '220.1', 188, 22, 'text'),
('160', 921, 'ApprovalOrDisapprovalApprove', '116.5', '243.8', 6, 6, 'checkbox'),
('160', 922, 'ApprovalOrDisapprovalDisapprove', '150.4', '243.8', 6, 6, 'checkbox'),
('160', 923, 'ApprovalOrDisapprovalName', '13.4', '254.7', 58, 6, 'text'),
('160', 924, 'ApprovalOrDisapprovalRank', '72.2', '254.7', 24, 6, 'text'),
('160', 925, 'ApprovalOrDisapprovalDutyTitlePosition', '97.6', '254.7', 44, 6, 'text'),
('160', 926, 'ApprovalOrDisapprovalSignature', '143.0', '254.7', 60, 6, 'signature'),
('160', 927, 'SubActivityTaskSource4', '13.4', '331.3', 29, 16, 'text'),
('160', 928, 'HazardOutcome4', '43.7', '331.3', 40, 16, 'text'),
('160', 929, 'InitialRisk4', '84.9', '331.3', 15, 16, 'text'),
('160', 930, 'Controls4', '101.2', '331.3', 39, 16, 'text'),
('160', 931, 'HowToImplement4', '148.2', '331.3', 36, 11, 'text'),
('160', 932, 'WhoWillImplement4', '148.2', '344.2', 36, 4, 'text'),
('160', 933, 'ResidualRisk4', '187.4', '331.3', 16, 16, 'text'),
('160', 934, 'SubActivityTaskSource5', '13.4', '348.9', 29, 16, 'text'),
('160', 935, 'HazardOutcome5', '43.7', '348.9', 40, 16, 'text'),
('160', 936, 'InitialRisk5', '84.9', '348.9', 15, 16, 'text'),
('160', 937, 'Controls5', '101.2', '348.9', 39, 16, 'text'),
('160', 938, 'HowToImplement5', '148.2', '348.9', 36, 11, 'text'),
('160', 939, 'WhoWillImplement5', '148.2', '361.6', 36, 4, 'text'),
('160', 940, 'ResidualRisk5', '187.4', '348.9', 16, 16, 'text'),
('160', 941, 'SubActivityTaskSource6', '13.4', '366.7', 29, 16, 'text'),
('160', 942, 'HazardOutcome6', '43.7', '366.7', 40, 16, 'text'),
('160', 943, 'InitialRisk6', '84.9', '366.7', 15, 16, 'text'),
('160', 944, 'Controls6', '101.2', '366.7', 39, 16, 'text'),
('160', 945, 'HowToImplement6', '148.2', '366.7', 36, 11, 'text'),
('160', 946, 'WhoWillImplement6', '148.2', '379.0', 36, 4, 'text'),
('160', 947, 'ResidualRisk6', '187.4', '366.7', 16, 16, 'text'),
('160', 948, 'SubActivityTaskSource7', '13.4', '384.3', 29, 16, 'text'),
('160', 949, 'HazardOutcome7', '43.7', '384.3', 40, 16, 'text'),
('160', 950, 'InitialRisk7', '84.9', '384.3', 15, 16, 'text'),
('160', 951, 'Controls7', '101.2', '384.3', 39, 16, 'text'),
('160', 952, 'HowToImplement7', '148.2', '384.3', 36, 11, 'text'),
('160', 953, 'WhoWillImplement7', '148.2', '396.8', 36, 4, 'text'),
('160', 954, 'ResidualRisk7', '187.4', '384.3', 16, 16, 'text'),
('160', 955, 'SubActivityTaskSource8', '13.4', '401.9', 29, 16, 'text'),
('160', 956, 'HazardOutcome8', '43.7', '401.9', 40, 16, 'text'),
('160', 957, 'InitialRisk8', '84.9', '401.9', 15, 16, 'text'),
('160', 958, 'Controls8', '101.2', '401.9', 39, 16, 'text'),
('160', 959, 'HowToImplement8', '148.2', '401.9', 36, 11, 'text'),
('160', 960, 'WhoWillImplement8', '148.2', '414.6', 36, 4, 'text'),
('160', 961, 'ResidualRisk8', '187.4', '401.9', 16, 16, 'text'),
('160', 962, 'SubActivityTaskSource9', '13.4', '419.8', 29, 16, 'text'),
('160', 963, 'HazardOutcome9', '43.7', '419.8', 40, 16, 'text'),
('160', 964, 'InitialRisk9', '84.9', '419.8', 15, 16, 'text'),
('160', 965, 'Controls9', '101.2', '419.8', 39, 16, 'text'),
('160', 966, 'HowToImplement9', '148.2', '419.8', 36, 11, 'text'),
('160', 967, 'WhoWillImplement9', '148.2', '432.5', 36, 4, 'text'),
('160', 968, 'ResidualRisk9', '187.4', '419.8', 16, 16, 'text'),
('160', 969, 'SubActivityTaskSource10', '13.4', '437.1', 29, 16, 'text'),
('160', 970, 'HazardOutcome10', '43.7', '437.1', 40, 16, 'text'),
('160', 971, 'InitialRisk10', '84.9', '437.1', 15, 16, 'text'),
('160', 972, 'Controls10', '101.2', '437.1', 39, 16, 'text'),
('160', 973, 'HowToImplement10', '148.2', '437.1', 36, 11, 'text'),
('160', 974, 'WhoWillImplement10', '148.2', '450.1', 36, 4, 'text'),
('160', 975, 'ResidualRisk10', '187.4', '437.1', 16, 16, 'text'),
('160', 976, 'SubActivityTaskSource11', '13.4', '455.0', 29, 16, 'text'),
('160', 977, 'HazardOutcome11', '43.7', '455.0', 40, 16, 'text'),
('160', 978, 'InitialRisk11', '84.9', '455.0', 15, 16, 'text'),
('160', 979, 'Controls11', '101.2', '455.0', 39, 16, 'text'),
('160', 980, 'HowToImplement11', '148.2', '455.0', 36, 11, 'text'),
('160', 981, 'WhoWillImplement11', '148.2', '468.1', 36, 4, 'text'),
('160', 982, 'ResidualRisk11', '187.4', '455.0', 16, 16, 'text'),
('160', 983, 'SubActivityTaskSource12', '13.4', '472.8', 29, 16, 'text'),
('160', 984, 'HazardOutcome12', '43.7', '472.8', 40, 16, 'text'),
('160', 985, 'InitialRisk12', '84.9', '472.8', 15, 16, 'text'),
('160', 986, 'Controls12', '101.2', '472.8', 39, 16, 'text'),
('160', 987, 'HowToImplement12', '148.2', '472.8', 36, 11, 'text'),
('160', 988, 'WhoWillImplement12', '148.2', '485.7', 36, 4, 'text'),
('160', 989, 'ResidualRisk12', '187.4', '472.8', 16, 16, 'text'),
('160', 990, 'SubActivityTaskSource13', '13.4', '490.4', 29, 16, 'text'),
('160', 991, 'HazardOutcome13', '43.7', '490.4', 40, 16, 'text'),
('160', 992, 'InitialRisk13', '84.9', '490.4', 15, 16, 'text'),
('160', 993, 'Controls13', '101.2', '490.4', 39, 16, 'text'),
('160', 994, 'HowToImplement13', '148.2', '490.4', 36, 11, 'text'),
('160', 995, 'WhoWillImplement13', '148.2', '504.9', 36, 4, 'text'),
('160', 996, 'ResidualRisk13', '187.4', '490.4', 16, 16, 'text'),
('160', 997, 'SubActivityTaskSource14', '13.4', '510.0', 29, 16, 'text'),
('160', 998, 'HazardOutcome14', '43.7', '510.0', 40, 16, 'text'),
('160', 999, 'InitialRisk14', '84.9', '510.0', 15, 16, 'text'),
('160', 1000, 'Controls14', '101.2', '510.0', 39, 16, 'text'),
('160', 1001, 'HowToImplement14', '148.2', '510.0', 36, 11, 'text'),
('160', 1002, 'WhoWillImplement14', '148.2', '523.6', 36, 4, 'text'),
('160', 1003, 'ResidualRisk14', '187.4', '510.0', 16, 16, 'text');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UserID` int(11) NOT NULL,
  `email` varchar(320) NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `googleID` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserID`, `email`, `firstName`, `lastName`, `googleID`) VALUES
(6, 'madmurphy27@gmail.com', 'Andrew', 'Murphy', '114590764149966303537'),
(7, 'frankss2@wwu.edu', 'Sebastien', 'Franks', '107211273524931397499'),
(8, 'sebastienfranks@gmail.com', 'Sebastien', 'Franks', '113259206635732933952'),
(9, 'cappaperwork@gmail.com', 'CAP', 'Paperwork', '100627015660909919880'),
(10, 'wendyyychew@gmail.com', 'Wendy', 'Chew', '104973306877862275171');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `forms`
--
ALTER TABLE `forms`
  ADD PRIMARY KEY (`formID`);

--
-- Indexes for table `templatefields`
--
ALTER TABLE `templatefields`
  ADD PRIMARY KEY (`FieldID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `forms`
--
ALTER TABLE `forms`
  MODIFY `formID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=224;

--
-- AUTO_INCREMENT for table `templatefields`
--
ALTER TABLE `templatefields`
  MODIFY `FieldID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1004;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
