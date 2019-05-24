<?php
require_once("../include/database.cfg.php");
header( "refresh:2;url=config.php" );

$dbconnection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($dbconnection->connect_error) {
  die("Connection failed: " . $conn->connect_error);
} else {
  $broker_name = $_POST['broker_name'];
  if ($broker_name == ''){
      echo "Broker_name cannot be blank";
      die();
  }
  $sql = "SELECT MAX(broker_id) AS id FROM brokers;";
  $results = $dbconnection->query($sql);
  while($obj = $results->fetch_object()){
    $new_id = ($obj->id + 1);
  }
  $broker_trade_profit_name = 'profits_'.$new_id;
  $broker_trade_table_name = 'trades_'.$new_id;
  $sql1 = "INSERT INTO brokers (broker_id, broker_name, broker_trade_table_name, broker_trade_profit_name) VALUES('".$new_id."', '".$broker_name."', '".$broker_trade_table_name."', '".$broker_trade_profit_name."');";
  $results1 = $dbconnection->query($sql1);
  if ($results1) {
    echo $broker_name." Broker added successfully.<br>";
    $sql2 = "CREATE TABLE IF NOT EXISTS `".$broker_trade_table_name."` (
            `ID` int(11) NOT NULL AUTO_INCREMENT,
            `executed_date` date NOT NULL,
            `sell_by_date` date,
            `type` enum('Entry','Exit') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
            `symbol` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
            `trade_strategy` enum('Call','Call Spread','Put','Put Spread','Stock','Crypto') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
            `order_type` enum('Buy Open','Sell Close','Sell Open','Buy Close') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
            `qty` decimal(22,11) NOT NULL,
            `expire_date` date NOT NULL,
            `strike_price` float NOT NULL,
            `executed_price` float NOT NULL,
            `order_type2` enum('Buy Open','Sell Close','Sell Open','Buy Close') CHARACTER SET utf8 COLLATE utf8_unicode_ci,
            `strike_price2` float,
            `com_fee` float,
            `total` float,
            `platform` varchar(250),
            `mate_id` varchar(50),
            PRIMARY KEY (`ID`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12;";
    if ($dbconnection->query($sql2) === TRUE) {
       echo "Table ".$broker_trade_table_name." created successfully.<br>";
       $sql3 = "CREATE TABLE IF NOT EXISTS `".$broker_trade_profit_name."` (
                `ID` int(11) NOT NULL AUTO_INCREMENT,
                `date` date NOT NULL,
                `description` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                `amount` decimal(22,11),
                `platform` varchar(250),
                `entry_id` int(11),
                `exit_id` varchar(50),
                PRIMARY KEY (`ID`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;";
      if ($dbconnection->query($sql3) === TRUE) {
       echo "Table ".$broker_trade_profit_name." created successfully.<br>";
       #header("Location: config.php");
      } else {
         echo "Error creating profits table: " . $dbconnection->error."<br>";
      }
    } else {
       echo "Error creating trade table: " . $dbconnection->error."<br>";
    }
    die();
  } else {
    echo "Sorry, adding this broker failed. Please try again. <br>";
  }
}

?>
