
--
-- Table structure for table 'jobs'
--

CREATE TABLE `jobs` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `SiteID` varchar(20) NOT NULL default '',
  `LocationID` int(11) NOT NULL default '0',
  `CategoryID` int(11) NOT NULL default '0',
  `CompanyID` int(11) NOT NULL default '0',
  `JobNum` varchar(8) NOT NULL default '',
  `Title` varchar(255) default NULL,
  `Manager` varchar(255) default NULL,
  `Summary` text,
  `Description` text,
  `Status` int(11) default NULL,
  `OnHoldNotes` text,
  `FilledNotes` text,
  `CreatedDate` datetime default NULL,
  `CreatedBy` varchar(16) NOT NULL default '',
  `RevisedDate` datetime default NULL,
  `RevisedBy` varchar(16) NOT NULL default '',
  `OnHoldDate` datetime default NULL,
  `OnHoldBy` varchar(16) NOT NULL default '',
  `FilledDate` datetime default NULL,
  `FilledBy` varchar(16) NOT NULL default '',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM;


--
-- Table structure for table 'jobs_category'
--

CREATE TABLE `jobs_category` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `SiteID` varchar(20) NOT NULL default '',
  `CategoryName` varchar(255) default NULL,
  `Status` int(11) NOT NULL default '0',
  `CreatedDate` datetime default NULL,
  `CreatedBy` varchar(16) NOT NULL default '',
  `RevisedDate` datetime default NULL,
  `RevisedBy` varchar(16) NOT NULL default '',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM;


-- 
-- Table structure for table `jobs_company`
-- 

CREATE TABLE `jobs_company` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `SiteID` varchar(20) NOT NULL default '',
  `CompanyName` varchar(255) default NULL,
  `Status` int(11) NOT NULL default '0',
  `CreatedDate` datetime default NULL,
  `CreatedBy` varchar(16) NOT NULL default '',
  `RevisedDate` datetime default NULL,
  `RevisedBy` varchar(16) NOT NULL default '',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM;


-- 
-- Table structure for table `jobs_location`
-- 

CREATE TABLE `jobs_location` (
  `ID` int(11) unsigned NOT NULL auto_increment,
  `SiteID` varchar(20) NOT NULL default '',
  `Status` int(11) NOT NULL default '0',
  `LocationName` varchar(255) default NULL,
  `City` varchar(255) default NULL,
  `State` char(2) default NULL,
  `Country` varchar(20) NOT NULL default '',
  `ContactEmail` varchar(255) default NULL,
  `CreatedDate` datetime default NULL,
  `CreatedBy` varchar(16) NOT NULL default '',
  `RevisedDate` datetime default NULL,
  `RevisedBy` varchar(16) NOT NULL default '',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM;


-- 
-- Table structure for table `jobs_resume`
-- 

CREATE TABLE `jobs_resume` (
  `ID` int(11) NOT NULL auto_increment,
  `FName` varchar(25) NOT NULL default '',
  `MName` varchar(25) default NULL,
  `LName` varchar(25) NOT NULL default '',
  `Address` text,
  `HomePhone` varchar(30) NOT NULL default '',
  `WorkPhone` varchar(30) default NULL,
  `Email` varchar(225) NOT NULL default '',
  `Resume` text NOT NULL,
  `CoverLtr` text,
  `JobID` int(11) NOT NULL default '0',
  `LocationName` varchar(255) NOT NULL default '',
  `CategoryName` varchar(255) NOT NULL default '',
  `DateSent` date NOT NULL default '0000-00-00',
  `EEOGender` varchar(20) default NULL,
  `EEOEthnicity` varchar(20) default NULL,
  `EEORace` varchar(127) default NULL,
  `EEOMultiPrime` varchar(127) default NULL,
  `EEOSignature` varchar(255) default NULL,
  `EEODate` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM;

