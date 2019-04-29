CREATE TABLE IF NOT EXISTS `brokers` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `broker_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `trade_table` varchar(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `profits_table` varchar(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `active` boolean,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;
