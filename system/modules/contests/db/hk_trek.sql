USE coolbrew;

CREATE TABLE `hk_trek` (
  `ID` int(11) unsigned NOT NULL auto_increment,
  `Lang` char(5) default NULL,
  `FirstName` char(255) default NULL,
  `LastName` char(255) default NULL,
  `Address` char(255) default NULL,
  `City` char(255) default NULL,
  `Province` char(2) default NULL,
  `Postal` char(10) default NULL,
  `Birthdate` date default NULL,
  `Gender` char(1) default NULL,
  `Phone` char(20) default NULL,
  `Email` char(255) default NULL,
  `Submitted` date default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


