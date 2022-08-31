
-- 
-- Table structure for table `adm_brand`
-- 

CREATE TABLE IF NOT EXISTS `adm_brand` (
  `ID` int(11) unsigned NOT NULL auto_increment,
  `Name` varchar(255) NOT NULL default '',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8;


-- 
-- Table structure for table `adm_link`
-- 

CREATE TABLE IF NOT EXISTS `adm_link` (
  `ID` int(11) unsigned NOT NULL auto_increment,
  `SiteID` varchar(20) NOT NULL default '',
  `Title` varchar(255) NOT NULL default '',
  `Description` text default NULL,
  `URL` varchar(255) NOT NULL default '',
  `AdminOnly` tinyint(1) unsigned default '0',
  `Dashboard` tinyint(1) unsigned default '0',
  `OpenWhere` varchar(16) DEFAULT 'same' NOT NULL,
  `Sort` int(10) unsigned DEFAULT 0 NOT NULL
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8;


-- 
-- Table structure for table `adm_menu`
-- 

CREATE TABLE `adm_menu` (
  `ID` int(11) unsigned NOT NULL auto_increment,
  `ModuleID` varchar(64) NOT NULL default '',
  `MenuName` varchar(20) NOT NULL default 'main',
  `Link` varchar(255) default NULL,
  `LinkText` varchar(64) default NULL,
  `Type` varchar(10) NOT NULL default '',
  `SelectRequired` tinyint(1) unsigned default '0',
  `Sort` int(11) default '0',
  `Position` varchar(6) NOT NULL default 'left',
  `CreatedDate` datetime NOT NULL default '2003-01-01 12:00:00',
  `CreatedBy` varchar(16) default NULL,
  `RevisedDate` datetime default NULL,
  `RevisedBy` varchar(16) default NULL
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8;


-- 
-- Table structure for table `adm_module`
-- 

CREATE TABLE `adm_module` (
  `ID` varchar(64) NOT NULL default '',
  `Name` varchar(255) NOT NULL default '',
  `Description` text default NULL,
  `Extends` varchar(255) NOT NULL default '',
  `CreatedDate` datetime NOT NULL default '2003-01-01 12:00:00',
  `CreatedBy` varchar(16) default NULL,
  `RevisedDate` datetime default NULL,
  `RevisedBy` varchar(16) default NULL
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8;


-- 
-- Table structure for table `adm_people`
-- 

CREATE TABLE `adm_people` (
  `Username` varchar(20) NOT NULL default '',
  `Password` varchar(40) NOT NULL default '',
  `FirstName` varchar(100) default NULL,
  `LastName` varchar(100) default NULL,
  `Email` varchar(200) default NULL,
  `FirstLogin` datetime default NULL,
  `LastLogin` datetime default NULL,
  `InUse` tinyint(1) unsigned NOT NULL default '0',
  `UserDisabled` tinyint(1) unsigned NOT NULL default '0',
  `Status` int(11) NOT NULL default '0',
  `CreatedDate` datetime NOT NULL default '2003-01-01 12:00:00',
  `CreatedBy` varchar(16) default NULL,
  `RevisedDate` datetime default NULL,
  `RevisedBy` varchar(16) default NULL
  PRIMARY KEY (`Username`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8;


-- 
-- Table structure for table `adm_people_menu`
-- 

CREATE TABLE `adm_people_menu` (
  `Username` varchar(20) NOT NULL default '',
  `SiteID` varchar(20) NOT NULL default '',
  `MenuID` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=UTF8;


-- 
-- Table structure for table `adm_people_module`
-- 

CREATE TABLE `adm_people_module` (
  `Username` varchar(20) NOT NULL default '',
  `SiteID` varchar(20) NOT NULL default '',
  `ModuleID` varchar(64) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=UTF8;


-- 
-- Table structure for table `adm_site`
-- 

CREATE TABLE `adm_site` (
  `ID` varchar(20) NOT NULL default '',
  `Description` text default NULL,
  `BrandName` varchar(128) NOT NULL default '',
  `BaseURL` varchar(255) NOT NULL default '',
  `BasePath` varchar(255) NOT NULL default '',
  `StoreID` varchar(128) default NULL
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8;


-- 
-- Table structure for table `adm_site_brand`
-- 

CREATE TABLE `adm_site_brand` (
  `ID` int(11) unsigned NOT NULL auto_increment,
  `SiteID` varchar(20) NOT NULL default '',
  `BrandID` int(11) unsigned NOT NULL
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8;


-- 
-- Table structure for table `adm_site_domain`
-- 

CREATE TABLE `adm_site_domain` (
  `ID` int(11) unsigned NOT NULL auto_increment,
  `Domain` varchar(255) NOT NULL default '',
  `SiteID` varchar(20) NOT NULL default '',
  `DomainID` varchar(64) NOT NULL default '',
  `RegistrarVendor` int(11) unsigned default NULL
  `DNSVendor` int(11) unsigned default NULL
  `PrimaryDomain` tinyint(1) unsigned NOT NULL default '0'
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8;


-- 
-- Table structure for table `adm_site_module`
-- 

CREATE TABLE `adm_site_module` (
  `ID` int(11) unsigned NOT NULL auto_increment,
  `SiteID` varchar(20) NOT NULL default '',
  `ModuleID` int(11) unsigned NOT NULL
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8;


-- 
-- Table structure for table `adm_site_vendor`
-- 

CREATE TABLE `adm_site_vendor` (
  `ID` int(11) unsigned NOT NULL auto_increment,
  `SiteID` varchar(20) NOT NULL default '',
  `ServiceDesc` text default NULL,
  `URL` varchar(255) NOT NULL default '',
  `Status` varchar(8) NOT NULL default 'current'
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8;


-- 
-- Table structure for table `adm_vendor`
-- 

CREATE TABLE `adm_vendor` (
  `ID` int(11) unsigned NOT NULL auto_increment,
  `VendorName` varchar(255) NOT NULL default ''
  `Address` text default NULL,
  `VendorURL` varchar(255) NOT NULL default ''
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8;


-- 
-- Table structure for table `adm_vendor_service`
-- 

CREATE TABLE `adm_vendor_service` (
  `ID` int(11) unsigned NOT NULL auto_increment,
  `Name` varchar(255) NOT NULL default ''
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8;


-- 
-- Table structure for table `ci_sessions`
-- 

CREATE TABLE IF NOT EXISTS `ci_sessions` (
  `session_id` varchar(40) DEFAULT '0' NOT NULL,
  `ip_address` varchar(16) DEFAULT '0' NOT NULL,
  `user_agent` varchar(50) NOT NULL,
  `last_activity` int(10) unsigned DEFAULT 0 NOT NULL,
  `session_data` text NOT NULL
  PRIMARY KEY (`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8;


