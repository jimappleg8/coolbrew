--
-- Table structure for table `eyelevel_contact`
--

CREATE TABLE `eyelevel_contact` (
  `ID` int(11) NOT NULL auto_increment,
  `SiteID` varchar(32) default NULL,
  `Customer` varchar(60) default NULL,
  `Email` varchar(255) default NULL,
  `Reason` varchar(20) default NULL,
  `Message` text,
  `SubmitDate` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

