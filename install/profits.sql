CREATE TABLE IF NOT EXISTS `profits` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `description` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `amount` float,
  `platform` enum('Alpha-9','Money Calendar Pro') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `entry_id` int(11),
  `exit_id` varchar(50),
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;
