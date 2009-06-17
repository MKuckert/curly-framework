CREATE TABLE IF NOT EXISTS `testdata` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(200) character set utf8 NOT NULL,
  `contents` text character set utf8 NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=4 ;

INSERT INTO `testdata` (`id`, `title`, `contents`, `created`) VALUES
(1, 'Home', 'Die Startseite', '2008-12-01 22:50:15'),
(2, 'Impressum', 'Die Verantwortlichen sind:', '2008-12-01 10:00:00'),
(3, 'Content', 'Hier ist echt viel Inhalt!', '2008-12-03 22:54:52');
