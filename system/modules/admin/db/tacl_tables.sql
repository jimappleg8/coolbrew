CREATE TABLE `adm_action` (
  `ID` int(11) NOT NULL auto_increment,
  `Ident` varchar(80) NOT NULL default '',
  `Descr` varchar(255) NOT NULL default '',
  `ResourceID` int(11) NOT NULL default '0',
  `Enabled` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `adm_idxActionIdentResourceId` (`Ident`,`ResourceID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `adm_group` (
  `ID` int(11) NOT NULL auto_increment,
  `Ident` varchar(80) NOT NULL default '',
  `Descr` varchar(255) NOT NULL default '',
  `Parent` int(11) NOT NULL default '0',
  `Enabled` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `adm_member` (
  `ID` int(11) NOT NULL auto_increment,
  `Ident` varchar(80) NOT NULL default '',
  `Descr` varchar(255) NOT NULL default '',
  `Enabled` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `adm_idxMemberIdent` (`Ident`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `adm_membership` (
  `MemberID` int(11) NOT NULL default '0',
  `GroupID` int(11) NOT NULL default '0',
  UNIQUE KEY `adm_idxMembership` (`MemberID`,`GroupID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `adm_permission` (
  `ResourceID` int(11) default NULL,
  `ActionID` int(11) default NULL,
  `GroupID` int(11) NOT NULL default '0',
  `MemberID` int(11) NOT NULL default '0',
  `Access` int(11) NOT NULL default '0',
  `Enabled` tinyint(1) NOT NULL default '1',
  UNIQUE KEY `adm_idxPermission` (`ResourceID`,`ActionID`,`GroupID`,`MemberID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `adm_resource` (
  `ID` int(11) NOT NULL auto_increment,
  `Ident` varchar(80) NOT NULL default '',
  `Descr` varchar(255) NOT NULL default '',
  `Parent` int(11) NOT NULL default '0',
  `Enabled` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

