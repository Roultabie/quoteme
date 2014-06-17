--
-- MySQL 5.5.37
-- Tue, 17 Jun 2014 07:39:49 +0000
--

CREATE TABLE `qm_authors` (
   `id` smallint(6) not null auto_increment,
   `author` varchar(255),
   `hits` smallint(9),
   UNIQUE KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE `qm_quotes` (
   `id` int(11) not null auto_increment,
   `quote` text not null,
   `author` smallint(6),
   `source` varchar(100) not null,
   `tags` smallint(6),
   `permalink` char(6) not null,
   `date` datetime not null,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE `qm_tags` (
   `id` smallint(6) not null auto_increment,
   `tag` varchar(255),
   `hits` mediumint(9),
   UNIQUE KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
