CREATE TABLE IF NOT EXISTS `defaults` (
`id` int(1) NOT NULL,
`active_broker_id` int(100) NOT NULL,
`monthly_profit_percent_target` float,
`monthly_profit_percent_to_keep` float,
`platforms` varchar(2048) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12;

INSERT INTO defaults (id,active_broker_id,monthly_profit_percent_target,monthly_profit_percent_to_keep,platforms) VALUES (1,1,.4,.25,'Alpha-9:Money Calendar Pro:Weekly Money Call:Fast Fortune Club:DERs:Straight Line Profits:10 Minute Millionaire:The Money Zone:Stealth Profits Trader:Seismic Profits Alert');