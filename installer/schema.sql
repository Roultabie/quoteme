--
-- MySQL 5.5.37
-- Tue, 26 Sep 2014 12:00:00 +0000
--

CREATE TABLE `qm_authors` (
   `author` varchar(255) not null,
   `hits` smallint(6) default '1',
   PRIMARY KEY (`author`),
   KEY `author` (`author`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `qm_quotes` (
   `id` int(11) not null auto_increment,
   `quote` text not null,
   `author` varchar(6255),
   `source` varchar(100) not null,
   `tags` text not null,
   `permalink` char(6) not null,
   `date` datetime not null,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE `qm_tags` (
   `tag` varchar(255) not null,
   `hits` mediumint(9) default '1',
   PRIMARY KEY (`tag`),
   KEY `tag` (`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;