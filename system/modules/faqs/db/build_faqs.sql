
#
# Table structure for table 'faqs_list'
#

CREATE TABLE `faqs_list` (
  `ID` int(11) unsigned NOT NULL auto_increment,
  `SiteID` varchar(20) NOT NULL default '',
  `FaqCode` varchar(155) NOT NULL default '',
  `Name` varchar(155) NOT NULL default '',
  `IsHTMLDefault` varchar(255) default NULL,
  `CreatedDate` datetime NOT NULL default '2003-01-01 12:00:00',
  `CreatedBy` varchar(16) default NULL,
  `RevisedDate` datetime default NULL,
  `RevisedBy` varchar(16) default NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `CategoryName` (`FaqCode`,`SiteID`)
);

#
# Table structure for table 'faqs_item'
#

CREATE TABLE faqs_item (
  ID int(11) unsigned NOT NULL auto_increment,
  ListID int(11),
  Title char(255),
  ShortQuestion text,
  Question text,
  Answer text,
  FlagAsNew char(1) NOT NULL default 'n',
  Status int(11) NOT NULL,
  Sort int(11),
  IsHTML tinyint(1),
  CreatedDate datetime NOT NULL default '2003-01-01 12:00:00',
  CreatedBy varchar(16) default NULL,
  RevisedDate datetime default NULL,
  RevisedBy varchar(16) default NULL,
  PRIMARY KEY (ID)
);

