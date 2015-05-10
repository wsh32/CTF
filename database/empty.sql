CREATE TABLE `solves` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`team` int(11) NOT NULL,
	`challenge` int(11) NOT NULL,
	`date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`additional` int(11) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `team` (`team`,`challenge`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `bonus` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`team` int(11) NOT NULL,
	`value` int(11) NOT NULL,
	`date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`type` text NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `challenges` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`title` text NOT NULL,
	`description` text NOT NULL,
	`score` int(11) NOT NULL,
	`key` text NOT NULL,
	`correctmessage` text NOT NULL,
	`incorrectmessage` text NOT NULL,
	`hidden` tinyint(1) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `teams` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` text NOT NULL,
	`score` int(11) NOT NULL,
	`registration` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`password` text NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

