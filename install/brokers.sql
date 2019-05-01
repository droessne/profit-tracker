CREATE TABLE IF NOT EXISTS `brokers` (
`broker_id` int(100) NOT NULL AUTO_INCREMENT,
`broker_name` varchar(50) NOT NULL,
`broker_trade_table_name` varchar(50) NOT NULL,
`broker_trade_profit_name` varchar(50) NOT NULL,
PRIMARY KEY (`broker_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12;

#DEFAULT BROKER
INSERT INTO brokers (broker_id,broker_name,broker_trade_table_name,broker_trade_profit_name) VALUES (1,'DERs','trades','profits');