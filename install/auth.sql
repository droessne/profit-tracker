CREATE TABLE IF NOT EXISTS `auth` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `refresh_token` varchar(2500) NOT NULL,
  `client_id` varchar(13),
  `redirect_uri` varchar(50),
  `access_token` varchar(2500),
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;
