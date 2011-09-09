delimiter $$

CREATE TABLE `TblCalendar` (
  `idCalendar` int(11) NOT NULL AUTO_INCREMENT,
  `idUser` int(11) NOT NULL,
  `email` varchar(45) NOT NULL,
  `password` varchar(45) NOT NULL,
  `title` varchar(45) NOT NULL,
  `notification` varchar(10) NOT NULL,
  PRIMARY KEY (`idCalendar`),
  KEY `idUserFK` (`idUser`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1$$

delimiter $$

CREATE TABLE `TblEvaluation` (
  `idEvaluation` int(11) NOT NULL AUTO_INCREMENT,
  `idUser` int(11) NOT NULL,
  `idGoal` int(11) NOT NULL,
  `timestamp` datetime NOT NULL,
  PRIMARY KEY (`idEvaluation`),
  KEY `igUserFK` (`idUser`),
  KEY `idGoalFK` (`idGoal`)
) ENGINE=MyISAM AUTO_INCREMENT=40 DEFAULT CHARSET=latin1$$

delimiter $$

CREATE TABLE `TblEvent` (
  `idEvent` int(11) NOT NULL AUTO_INCREMENT,
  `startdate` date NOT NULL,
  `enddate` date NOT NULL,
  `starttime` time NOT NULL,
  `endtime` time NOT NULL,
  `title` varchar(45) NOT NULL,
  `idTask` int(11) NOT NULL,
  `idCalendar` int(11) NOT NULL,
  `idHrefGcal` varchar(256) NOT NULL,
  `link` varchar(256) NOT NULL,
  PRIMARY KEY (`idEvent`),
  KEY `idTaskFK` (`idTask`),
  KEY `idCalendarFK` (`idCalendar`)
) ENGINE=MyISAM AUTO_INCREMENT=47 DEFAULT CHARSET=latin1$$

delimiter $$

CREATE TABLE `TblGoal` (
  `idGoal` int(11) NOT NULL AUTO_INCREMENT,
  `titleGoal` varchar(45) NOT NULL,
  `descGoal` varchar(256) DEFAULT NULL,
  `idUser` int(11) DEFAULT NULL,
  PRIMARY KEY (`idGoal`),
  KEY `idUserFK` (`idUser`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=latin1$$

delimiter $$

CREATE TABLE `TblLogDairy` (
  `idTblLogDairy` int(11) NOT NULL AUTO_INCREMENT,
  `logText` varchar(1024) NOT NULL,
  `logDate` datetime NOT NULL,
  `idUser` int(11) NOT NULL,
  PRIMARY KEY (`idTblLogDairy`),
  KEY `idUserFK` (`idUser`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=latin1$$

delimiter $$

CREATE TABLE `TblLogDairyGoalRelationship` (
  `idTblLogDairyGoalRelationship` int(11) NOT NULL AUTO_INCREMENT,
  `idLogDairy` int(11) NOT NULL,
  `idGoal` int(11) NOT NULL,
  PRIMARY KEY (`idTblLogDairyGoalRelationship`),
  KEY `idLogDairyFK` (`idLogDairy`),
  KEY `idGoalFK` (`idGoal`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1$$

delimiter $$

CREATE TABLE `TblStepEvaluation` (
  `keyStep` int(11) NOT NULL AUTO_INCREMENT,
  `idEvaluation` int(11) NOT NULL,
  `strength` varchar(256) DEFAULT NULL,
  `weakness` varchar(256) DEFAULT NULL,
  `idStep` int(11) NOT NULL,
  `idStrategy` int(11) DEFAULT NULL,
  PRIMARY KEY (`keyStep`),
  KEY `idEvaluationFK` (`idEvaluation`),
  KEY `idStepFK` (`idStep`),
  KEY `idStrategyFK` (`idStrategy`)
) ENGINE=MyISAM AUTO_INCREMENT=79 DEFAULT CHARSET=latin1$$

delimiter $$

CREATE TABLE `TblStepEvaluationDict` (
  `idStep` int(11) NOT NULL AUTO_INCREMENT,
  `nameStep` varchar(45) NOT NULL,
  `descStep` varchar(256) NOT NULL,
  PRIMARY KEY (`idStep`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1$$

delimiter $$

CREATE TABLE `TblStrategy` (
  `idStrategy` int(11) NOT NULL AUTO_INCREMENT,
  `titleStrategy` varchar(45) NOT NULL,
  `descStrategy` varchar(1024) DEFAULT NULL,
  `idGoal` int(11) NOT NULL,
  PRIMARY KEY (`idStrategy`),
  KEY `idGoalFK` (`idGoal`)
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=latin1$$

delimiter $$

CREATE TABLE `TblTask` (
  `idTask` int(11) NOT NULL AUTO_INCREMENT,
  `titleTask` varchar(45) NOT NULL,
  `descTask` varchar(256) DEFAULT NULL,
  `idStrategy` int(11) NOT NULL,
  PRIMARY KEY (`idTask`),
  KEY `idStrategyFK` (`idStrategy`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=latin1$$

delimiter $$

CREATE TABLE `TblUser` (
  `idUser` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `email` varchar(45) NOT NULL,
  `password` varchar(45) NOT NULL,
  `firstname` varchar(45) NOT NULL,
  `lastname` varchar(45) NOT NULL,
  PRIMARY KEY (`idUser`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1$$

delimiter $$

CREATE TABLE `TblVision` (
  `idTblVision` int(11) NOT NULL AUTO_INCREMENT,
  `idUser` int(11) DEFAULT NULL,
  `descVision` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`idTblVision`),
  KEY `idUserFK` (`idUser`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1$$

