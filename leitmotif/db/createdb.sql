delimiter $$
delimiter $$

CREATE TABLE `TblUser` (
  `idUser` varchar(16) NOT NULL,
  `username` varchar(20) NOT NULL,
  `email` varchar(45) NOT NULL,
  `password` varchar(45) NOT NULL,
  `firstname` varchar(45) NOT NULL,
  `lastname` varchar(45) NOT NULL,
  `userprofile` tinyint(4) NOT NULL DEFAULT '1' COMMENT '0 admin\n1 user\n2 coacher',
  PRIMARY KEY (`idUser`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1$$

delimiter $$

CREATE TABLE `TblShare` (
  `idShare` varchar(16) NOT NULL,
  PRIMARY KEY (`idShare`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Es como un grupo. En cuanto se crea un leitmotif , se crea u'$$




CREATE TABLE `TblLeitmotif` (
  `idLeitmotif` varchar(16) NOT NULL,
  `leitmotif` varchar(100) NOT NULL,
  `owner` varchar(16) NOT NULL,
  `complete` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 - To Do\n1 - In Progress\n2 - Done\n3 - Abandoned\n4 - Not Need',
  `urgency` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 - Low\n1 - Medium\n2 - High',
  `priority` int(11) NOT NULL DEFAULT '0',
  `importance` tinyint(4) NOT NULL DEFAULT '0',
  `idShare` varchar(16) NOT NULL,
  `idUp` varchar(16) NOT NULL,
  `level` tinyint(4) NOT NULL DEFAULT '0',
  `fecha` datetime DEFAULT NULL,
  PRIMARY KEY (`idLeitmotif`),
  KEY `idUserFK` (`owner`),
  KEY `idShareFK` (`idShare`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1$$

delimiter $$

CREATE TABLE `TblVinculo` (
  `idUser` varchar(16) NOT NULL,
  `idShare` varchar(16) NOT NULL,
  `permiso` int(11) NOT NULL DEFAULT '1' COMMENT '0 - No access\n1 - Read\n3 - Read & Write\n7 - 3 and share\n15 - 7 and delete\n33 - 15 & Unshare',
  `profile` int(11) DEFAULT '0',
  PRIMARY KEY (`idUser`),
  KEY `idUserFK` (`idUser`),
  KEY `idShareFK` (`idShare`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1$$

