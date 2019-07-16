<?php
require_once("include/database.cfg.php");
require_once("include/defaults.cfg.php");


$dbconnection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($dbconnection->connect_error) {
  die("Connection failed: " . $conn->connect_error);
} else {
  $executed_date = $_POST['executed_date'];
  if ($_POST['sell_by_date'] == '') {
    if ($_POST['trade_strategy'] == 'Stock') {
      $sell_by_date = '2999-12-31';
    } elseif ($_POST['trade_strategy'] == 'Crypto') {
      $sell_by_date = '2999-12-31';
    } else {  
      $sell_by_date = $_POST['expire_date'];
    }
  } else {
    $sell_by_date = $_POST['sell_by_date'];
  }
  $type = 'Entry';
  $symbol = $_POST['symbol'];
  $trade_strategy = $_POST['trade_strategy'];
  $order_type = $_POST['order_type'];
  $qty = $_POST['qty'];
  $expire_date = $_POST['expire_date'];
  $strike_price = $_POST['strike_price'];
  $executed_price = $_POST['executed_price'];
  $order_type2 = $_POST['order_type2'];
  $strike_price2 = $_POST['strike_price2'];
  $com_fee = $_POST['com_fee'];
  $platform = $_POST['platform'];
  $total = 0;

  if ('Call' == $trade_strategy) {
    $total = ((((floatval($qty) * 100) * floatval($executed_price)) * -1) + $com_fee);
  } elseif ('Put' == $trade_strategy) {
    $total = ((((floatval($qty) * 100) * floatval($executed_price)) * -1) + $com_fee);
  } elseif ('Call Spread' == $trade_strategy) {
    $total = ((((floatval($qty) * 100) * floatval($executed_price)) * -1) + $com_fee);
  } elseif ('Bear Call Spread' == $trade_strategy) {
    $total = ((((floatval($qty) * 100) * floatval($executed_price))) + $com_fee);
  } elseif ('Put Spread' == $trade_strategy) {
    $total = ((((floatval($qty) * 100) * floatval($executed_price)) * -1) + $com_fee);
  } elseif ('Bull Put Spread' == $trade_strategy) {
    $total = (((floatval($qty) * 100) * floatval($executed_price)) + $com_fee);
  } elseif ('Stock' == $trade_strategy) {
    $total = ((((floatval($qty)) * floatval($executed_price)) * -1) + $com_fee);
  } elseif ('Crypto' == $trade_strategy) {
    $total = ((((floatval($qty)) * floatval($executed_price)) * -1) + $com_fee);
  }

  if ($trade_strategy == 'Call'){
    $sql = "INSERT INTO ".$trades_table." (executed_date, sell_by_date, type, symbol, trade_strategy, order_type, qty, expire_date, strike_price, executed_price, com_fee, total, platform) VALUES('" . $executed_date . "', '" . $sell_by_date . "', '" . $type . "', '" . $symbol . "', '" . $trade_strategy . "', '" . $order_type . "', '" . $qty . "', '" . $expire_date . "', '" . $strike_price . "', '" . $executed_price . "', '" . $com_fee . "', '" . $total . "', '" . $platform . "');"; 
  } elseif ($trade_strategy == 'Stock'){
    $sql = "INSERT INTO ".$trades_table." (executed_date, sell_by_date, type, symbol, trade_strategy, order_type, qty, expire_date, strike_price, executed_price, com_fee, total, platform) VALUES('" . $executed_date . "', '" . $sell_by_date . "', '" . $type . "', '" . $symbol . "', '" . $trade_strategy . "', '" . $order_type . "', '" . $qty . "', '2999-12-31', '0', '" . $executed_price . "', '" . $com_fee . "', '" . $total . "', '" . $platform . "');";
  } elseif ($trade_strategy == 'Crypto'){
    $sql = "INSERT INTO ".$trades_table." (executed_date, sell_by_date, type, symbol, trade_strategy, order_type, qty, expire_date, strike_price, executed_price, com_fee, total, platform) VALUES('" . $executed_date . "', '" . $sell_by_date . "', '" . $type . "', '" . $symbol . "', '" . $trade_strategy . "', '" . $order_type . "', '" . $qty . "', '2999-12-31', '0', '" . $executed_price . "', '" . $com_fee . "', '" . $total . "', '" . $platform . "');";
  } elseif ($trade_strategy == 'Put'){
    $sql = "INSERT INTO ".$trades_table." (executed_date, sell_by_date, type, symbol, trade_strategy, order_type, qty, expire_date, strike_price, executed_price, com_fee, total, platform) VALUES('" . $executed_date . "', '" . $sell_by_date . "', '" . $type . "', '" . $symbol . "', '" . $trade_strategy . "', '" . $order_type . "', '" . $qty . "', '" . $expire_date . "', '" . $strike_price . "', '" . $executed_price . "', '" . $com_fee . "', '" . $total . "', '" . $platform . "');";
  } else {
    $sql = "INSERT INTO ".$trades_table." (executed_date, sell_by_date, type, symbol, trade_strategy, order_type, qty, expire_date, strike_price, executed_price, order_type2, strike_price2, com_fee, total, platform) VALUES('" . $executed_date . "', '" . $sell_by_date . "', '" . $type . "', '" . $symbol . "', '" . $trade_strategy . "', '" . $order_type . "', '" . $qty . "', '" . $expire_date . "', '" . $strike_price . "', '" . $executed_price . "', '" . $order_type2 . "', '" . $strike_price2 . "', '" . $com_fee . "', '" . $total . "', '" . $platform . "');";
  }
  $results_2 = $dbconnection->query($sql);

  if ($results_2) {
    echo "Trade added.";
    header( "refresh:1;url=add_entry_trade.php" );
    die();
  } else {
    echo "Sorry, adding this trade failed. Please try again";
  }
}

?>
