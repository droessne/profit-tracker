CREATE TABLE IF NOT EXISTS `trades` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `executed_date` date NOT NULL,
  `type` enum(`Entry`,`Exit`),
  `symbol` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `trade_strategy` enum(`Call`,`Call Spread`,`Put`,`Put Spread`),
  `order_type` enum(`Buy Open`,`Sell Close`,`Sell Open`,`Buy Close`),
  `qty` int NOT NULL,
  `expire_date` date NOT NULL,
  `strike_price` float NOT NULL,
  `executed_price` float NOT NULL,
  `order_type2` enum(`Buy Open`,`Sell Close`,`Sell Open`,`Buy Close`),
  `strike_price2` float NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;
