--
-- Setup Script for phpInOutBoard
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE IF NOT EXISTS `accounts` (
  `acctID` smallint(6) NOT NULL auto_increment,
  `dept` int(11) default NULL,
  `type` varchar(25) default NULL,
  `pass` varchar(40) default NULL,
  `email` varchar(100) default NULL,
  `lName` varchar(30) default NULL,
  `fName` varchar(30) default NULL,
  `personID` varchar(11) default NULL,
  `mailed` char(1) default NULL,
  `dh` char(1) default NULL,
  `priPh` varchar(10) default NULL,
  `priExt` varchar(6) default NULL,
  `bldg` varchar(4) default NULL,
  `room` varchar(10) default NULL,
  `ms` smallint(6) default NULL,
  `pref` varchar(5) default NULL,
  `sta` char(2) default NULL,
  PRIMARY KEY  (`acctID`),
  KEY `lName` (`lName`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `de_emp`
--

CREATE TABLE IF NOT EXISTS `de_emp` (
  `de_id` smallint(11) NOT NULL auto_increment,
  `acctID` smallint(11) NOT NULL,
  `super` smallint(11) default NULL,
  `edit` tinyint(1) NOT NULL default '0',
  `offcampus` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`de_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `emp_calendar`
--

CREATE TABLE IF NOT EXISTS `emp_calendar` (
  `eventID` int(11) NOT NULL auto_increment,
  `statusID` int(11) NOT NULL,
  `acctID` int(11) NOT NULL,
  `start` int(11) NOT NULL,
  `end` int(11) NOT NULL,
  `linkEventID` int(11) default NULL,
  `message` varchar(255) default NULL,
  `attendees` varchar(255) default NULL,
  `cancelled` tinyint(1) NOT NULL default '0',
  `deleted` tinyint(1) NOT NULL default '0',
  `occurred` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`eventID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `emp_calendar_access`
--

CREATE TABLE IF NOT EXISTS `emp_calendar_access` (
  `calendar_access_id` smallint(11) NOT NULL auto_increment,
  `acctID` smallint(11) NOT NULL,
  `statusID` smallint(11) NOT NULL,
  PRIMARY KEY  (`calendar_access_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `emp_current`
--

CREATE TABLE IF NOT EXISTS `emp_current` (
  `currentID` int(11) NOT NULL auto_increment,
  `acctID` int(11) NOT NULL,
  `statusID` int(11) NOT NULL,
  `timestamp` int(20) NOT NULL,
  `end` int(20) default NULL,
  `message` varchar(255) NOT NULL,
  `who` int(11) NOT NULL,
  `added` tinyint(1) NOT NULL default '0',
  `edited` int(11) default NULL COMMENT 'should be linked to a current ID of the item edited',
  `deleted` tinyint(1) NOT NULL default '0',
  `approved` enum('approved','denied','pending','edited') NOT NULL default 'approved',
  PRIMARY KEY  (`currentID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `emp_request`
--

CREATE TABLE IF NOT EXISTS `emp_request` (
  `requestID` int(11) NOT NULL auto_increment,
  `whoID` smallint(6) NOT NULL,
  `page` varchar(50) NOT NULL,
  `params` varchar(255) NOT NULL,
  `when` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`requestID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `emp_status`
--

CREATE TABLE IF NOT EXISTS `emp_status` (
  `status_id` int(11) NOT NULL auto_increment,
  `status_type` varchar(50) NOT NULL,
  `status_color` varchar(7) NOT NULL default '#333333',
  `status_schedule` tinyint(1) NOT NULL default '1',
  `status_order` smallint(6) NOT NULL default '999',
  PRIMARY KEY  (`status_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `emp_status_access`
--

CREATE TABLE IF NOT EXISTS `emp_status_access` (
  `status_access_id` smallint(11) NOT NULL auto_increment,
  `acctID` smallint(11) NOT NULL,
  `statusID` smallint(11) NOT NULL,
  PRIMARY KEY  (`status_access_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

INSERT INTO `emp_status` (`status_id`, `status_type`, `status_color`, `status_schedule`, `status_order`) VALUES
(1, 'In Office', '#CCFFCC', 0, 1),
(2, 'Break', '#99CCCC', 0, 3),
(3, 'Out of Office - Not Working', '#999999', 0, 5),
(8, 'Meeting', '#FFCC66', 1, 4),
(5, 'Vacation', '#9999CC', 1, 6),
(6, 'Sick', '#9999FF', 1, 7),
(7, 'Out of Office - Working', '#FFFF99', 1, 2),
(9, 'Filming', '#DF6568', 1, 9);
