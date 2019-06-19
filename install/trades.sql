CREATE TABLE IF NOT EXISTS `trades` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `executed_date` date NOT NULL,
  `sell_by_date` date,
  `type` enum('Entry','Exit') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `symbol` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `trade_strategy` enum('Call','Call Spread','Put','Put Spread','Stock','Crypto') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `order_type` enum('Buy Open','Sell Close','Sell Open','Buy Close') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `qty` int NOT NULL,
  `expire_date` date NOT NULL,
  `strike_price` float NOT NULL,
  `executed_price` float NOT NULL,
  `order_type2` enum('Buy Open','Sell Close','Sell Open','Buy Close') CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `strike_price2` float,
  `com_fee` float,
  `total` float,
  `platform` varchar(250),
  `mate_id` varchar(50),
  `max_price` float,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;


ALTER TABLE `trades` MODIFY COLUMN 
  `trade_strategy` enum('Call','Call Spread','Put','Put Spread','Stock','Crypto') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;