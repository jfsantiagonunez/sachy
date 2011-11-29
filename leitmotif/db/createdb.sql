delimiter $$

CREATE TABLE `TblLeitmotif` (
  `idLeitmotif` varchar(36) CHARACTER SET latin1 NOT NULL,
  `leitmotif` varchar(100) CHARACTER SET latin1 NOT NULL,
  `owner` varchar(36) CHARACTER SET latin1 NOT NULL,
  `complete` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 - To Do\n1 - In Progress\n2 - Done\n3 - Abandoned\n4 - Not Need',
  `urgency` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 - Low\n1 - Medium\n2 - High',
  `priority` int(11) NOT NULL DEFAULT '0',
  `importance` tinyint(4) NOT NULL DEFAULT '0',
  `idShare` varchar(36) CHARACTER SET latin1 NOT NULL,
  `idUp` varchar(36) CHARACTER SET latin1 NOT NULL,
  `level` tinyint(4) NOT NULL DEFAULT '0',
  `fecha` datetime DEFAULT NULL,
  PRIMARY KEY (`idLeitmotif`),
  KEY `idUserFK` (`owner`),
  KEY `idShareFK` (`idShare`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8$$

delimiter $$

CREATE TABLE `TblShare` (
  `idShare` varchar(36) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`idShare`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Es como un grupo. En cuanto se crea un leitmotif , se crea u'$$

delimiter $$

CREATE TABLE `TblUser` (
  `idUser` varchar(36) CHARACTER SET latin1 NOT NULL,
  `username` varchar(20) CHARACTER SET latin1 NOT NULL,
  `email` varchar(45) CHARACTER SET latin1 NOT NULL,
  `password` varchar(45) CHARACTER SET latin1 NOT NULL,
  `firstname` varchar(45) CHARACTER SET latin1 NOT NULL,
  `lastname` varchar(45) CHARACTER SET latin1 NOT NULL,
  `userprofile` tinyint(4) NOT NULL DEFAULT '1' COMMENT '0 admin\n1 user\n2 coacher',
  PRIMARY KEY (`idUser`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8$$

delimiter $$

CREATE TABLE `TblVinculo` (
  `idUser` varchar(36) CHARACTER SET latin1 NOT NULL,
  `idShare` varchar(36) CHARACTER SET latin1 NOT NULL,
  `permiso` int(11) NOT NULL DEFAULT '1' COMMENT '0 - No access\n1 - Read\n3 - Read & Write\n7 - 3 and share\n15 - 7 and delete\n33 - 15 & Unshare',
  `profile` int(11) NOT NULL DEFAULT '0',
  `idVinculo` varchar(36) NOT NULL,
  PRIMARY KEY (`idVinculo`),
  KEY `idUserFK` (`idUser`),
  KEY `idShareFK` (`idShare`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8$$





