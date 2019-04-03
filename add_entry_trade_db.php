<?php
require_once("include/database.cfg.php");

$dbconnection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($dbconnection->connect_error) {
  die("Connection failed: " . $conn->connect_error);
} else {
  $executed_date = $_POST['executed_date'];
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
  } elseif ('Put Spread' == $trade_strategy) {
    $total = (((floatval($qty) * 100) * floatval($executed_price)) + $com_fee);
  }

  if ($trade_strategy == 'Call'){
    $sql = "INSERT INTO trades (executed_date, type, symbol, trade_strategy, order_type, qty, expire_date, strike_price, executed_price, com_fee, total, platform) VALUES('" . $executed_date . "', '" . $type . "', '" . $symbol . "', '" . $trade_strategy . "', '" . $order_type . "', '" . $qty . "', '" . $expire_date . "', '" . $strike_price . "', '" . $executed_price . "', '" . $com_fee . "', '" . $total . "', '" . $platform . "');"; 
  } elseif ($trade_strategy == 'Put'){
    $sql = "INSERT INTO trades (executed_date, type, symbol, trade_strategy, order_type, qty, expire_date, strike_price, executed_price, com_fee, total, platform) VALUES('" . $executed_date . "', '" . $type . "', '" . $symbol . "', '" . $trade_strategy . "', '" . $order_type . "', '" . $qty . "', '" . $expire_date . "', '" . $strike_price . "', '" . $executed_price . "', '" . $com_fee . "', '" . $total . "', '" . $platform . "');";
  } else {
    $sql = "INSERT INTO trades (executed_date, type, symbol, trade_strategy, order_type, qty, expire_date, strike_price, executed_price, order_type2, strike_price2, com_fee, total, platform) VALUES('" . $executed_date . "', '" . $type . "', '" . $symbol . "', '" . $trade_strategy . "', '" . $order_type . "', '" . $qty . "', '" . $expire_date . "', '" . $strike_price . "', '" . $executed_price . "', '" . $order_type2 . "', '" . $strike_price2 . "', '" . $com_fee . "', '" . $total . "', '" . $platform . "');";
  }
  $results_2 = $dbconnection->query($sql);

  if ($results_2) {
    echo "Trade added.";
    header("Location: {$_POST['referer']}");
    die();
  } else {
    echo "Sorry, adding this trade failed. Please try again";
  }
}

?>
