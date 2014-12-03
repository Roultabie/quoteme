--
-- MySQL 5.5.5
-- Mon, 01 Dec 2014 21:40:57 +0000
--

CREATE TABLE `qm_authors` (
   `id` mediumint(9) not null auto_increment,
   `author` varchar(255) not null,
   `hits` smallint(6) default '1',
   PRIMARY KEY (`author`),
   UNIQUE KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE `qm_contributors` (
   `id` tinyint(255) not null auto_increment,
   `name` varchar(255) not null,
   `hash` varchar(255) not null,
   `level` tinyint(5) not null default '1',
   `email` varchar(255) not null,
   `shaarli` varchar(255) not null,
   PRIMARY KEY (`id`),
   UNIQUE KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE `qm_quotes` (
   `id` int(11) not null auto_increment,
   `quote` text not null,
   `author` varchar(6255),
   `source` varchar(100) not null,
   `tags` text not null,
   `permalink` char(6) not null,
   `date` datetime not null,
   `contributor` tinyint(255),
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE `qm_tags` (
   `id` smallint(6) not null auto_increment,
   `tag` varchar(255) not null,
   `hits` mediumint(9) default '1',
   PRIMARY KEY (`tag`),
   UNIQUE KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;