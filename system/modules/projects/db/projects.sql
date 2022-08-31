
CREATE TABLE `projects` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `ProjectTypeID` int(11) NOT NULL default '0',
  `BaseCampID` varchar(20) default NULL,
  `ProjectName` varchar(255) default NULL,
  `ClientName` varchar(255) default NULL,
  `ClientEmail` varchar(255) default NULL,
  `ClientPhone` varchar(255) default NULL,
  `Description` text,
  `RequestedDueDate` date default NULL,
  `DueDateNotes` text,
  `Status` int(11) default NULL,
  `CreatedDate` datetime default NULL,
  `RevisedDate` datetime default NULL,
  `RevisedBy` varchar(16) NOT NULL default '',
  `CompletedDate` datetime default NULL,
  `CompletedBy` varchar(16) NOT NULL default '',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM;


CREATE TABLE `projects_checked` (
  `ProjectID` int(11) NOT NULL default '0',
  `ChecklistID` int(11) NOT NULL default '0',
  `Status` int(11) default NULL,
  `CompletedDate` datetime default NULL,
  `CompletedBy` varchar(16) NOT NULL default '',
  PRIMARY KEY  (`ProjectID`)
) ENGINE=MyISAM;


CREATE TABLE `projects_checklist` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `ParentID` int(11) NOT NULL default '0',
  `ItemType` varchar(63) NOT NULL default 'task',
  `ItemName` varchar(255) default NULL,
  `URL` varchar(255) default NULL,
  `Sort` int(11) default NULL,
  `Color` varchar(6) default NULL,
  `BgColor` varchar(6) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=38 ;


CREATE TABLE `projects_type` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `TypeName` varchar(255) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM;


CREATE TABLE `projects_type_default` (
  `ProjectTypeID` int(11) NOT NULL default '0',
  `Property` varchar(255) default NULL,
  `PropertyID` int(11) NOT NULL default '0'
) ENGINE=MyISAM;


CREATE TABLE `projects_timeblock` (
  `ScheduleID` int(11) NOT NULL default '0',
  `BlockName` varchar(127) default NULL,
  `StartDate` date,
  `EndDate` date,
  `WorkDays` int(11) NOT NULL default '0',
  `Status` varchar(20) NOT NULL default 'incomplete',
  `CreatedDate` datetime default NULL,
  `CreatedBy` varchar(16) NOT NULL default '',
  `RevisedDate` datetime default NULL,
  `RevisedBy` varchar(16) NOT NULL default '',
) ENGINE=MyISAM;


CREATE TABLE `projects_resource` (
  `ID` varchar(63) NOT NULL default '',
  `FirstName` varchar(127) NOT NULL default '',
  `LastName` varchar(127) NOT NULL default '',
  `HoursPerDay` int(11) NOT NULL default '0'
  `CreatedDate` datetime default NULL,
  `CreatedBy` varchar(16) NOT NULL default '',
  `RevisedDate` datetime default NULL,
  `RevisedBy` varchar(16) NOT NULL default '',
) ENGINE=MyISAM;


CREATE TABLE `projects_schedule` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `ProjectID` int(11) NOT NULL default '0',
  `Resource` varchar(127) NOT NULL default '',
  `Description` text default NULL,
  `Status` varchar(63) NOT NULL default '',
  `CreatedDate` datetime default NULL,
  `CreatedBy` varchar(16) NOT NULL default '',
  `RevisedDate` datetime default NULL,
  `RevisedBy` varchar(16) NOT NULL default '',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM;


CREATE TABLE `projects_blockname` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `BlockName` varchar(255) default NULL,
  `PrimaryWork` smallint NOT NULL default 0,
  `Sort` int(11) default NULL,
  `Color` varchar(6) default NULL,
  `BgColor` varchar(6) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM;

