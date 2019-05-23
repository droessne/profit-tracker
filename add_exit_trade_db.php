<?php
require_once("include/database.cfg.php");
require_once("include/defaults.cfg.php");

$dbconnection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($dbconnection->connect_error) {
  die("Connection failed: " . $conn->connect_error);
} else {
  $executed_date = $_POST['executed_date'];
  $type = 'Exit';
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
  $id = $_POST['ID'];

  if ('Call' == $trade_strategy) {
    $total = ((((floatval($qty) * 100) * floatval($executed_price))) + $com_fee);
  } elseif ('Put' == $trade_strategy) {
    $total = ((((floatval($qty) * 100) * floatval($executed_price))) + $com_fee);
  } elseif ('Call Spread' == $trade_strategy) {
    $total = ((((floatval($qty) * 100) * floatval($executed_price))) + $com_fee);
  } elseif ('Put Spread' == $trade_strategy) {
    $total = ((((floatval($qty) * 100) * floatval($executed_price)) * -1) + $com_fee);
  } elseif ('Stock' == $trade_strategy) {
    $total = ((((floatval($qty)) * floatval($executed_price))) + $com_fee);
  } elseif ('Crypto' == $trade_strategy) {
    $total = ((((floatval($qty)) * floatval($executed_price))) + $com_fee);
  }

  if ($trade_strategy == 'Call'){
    $sql = "INSERT INTO ".$trades_table." (executed_date, type, symbol, trade_strategy, order_type, qty, expire_date, strike_price, executed_price, com_fee, total, platform, mate_id) VALUES('" . $executed_date . "', '" . $type . "', '" . $symbol . "', '" . $trade_strategy . "', '" . $order_type . "', '" . $qty . "', '" . $expire_date . "', '" . $strike_price . "', '" . $executed_price . "', '" . $com_fee . "', '" . $total . "', '" . $platform . "', '" . $id . "');";
  } elseif ($trade_strategy == 'Put'){
    $sql = "INSERT INTO ".$trades_table." (executed_date, type, symbol, trade_strategy, order_type, qty, expire_date, strike_price, executed_price, com_fee, total, platform, mate_id) VALUES('" . $executed_date . "', '" . $type . "', '" . $symbol . "', '" . $trade_strategy . "', '" . $order_type . "', '" . $qty . "', '" . $expire_date . "', '" . $strike_price . "', '" . $executed_price . "', '" . $com_fee . "', '" . $total . "', '" . $platform . "', '" . $id . "');";
  } elseif ($trade_strategy == 'Stock'){
    $sql = "INSERT INTO ".$trades_table." (executed_date, type, symbol, trade_strategy, order_type, qty, expire_date, strike_price, executed_price, com_fee, total, platform, mate_id) VALUES('" . $executed_date . "', '" . $type . "', '" . $symbol . "', '" . $trade_strategy . "', '" . $order_type . "', '" . $qty . "', '" . $expire_date . "', '" . $strike_price . "', '" . $executed_price . "', '" . $com_fee . "', '" . $total . "', '" . $platform . "', '" . $id . "');";
  } elseif ($trade_strategy == 'Crypto'){
    $sql = "INSERT INTO ".$trades_table." (executed_date, type, symbol, trade_strategy, order_type, qty, expire_date, strike_price, executed_price, com_fee, total, platform, mate_id) VALUES('" . $executed_date . "', '" . $type . "', '" . $symbol . "', '" . $trade_strategy . "', '" . $order_type . "', '" . $qty . "', '" . $expire_date . "', '" . $strike_price . "', '" . $executed_price . "', '" . $com_fee . "', '" . $total . "', '" . $platform . "', '" . $id . "');";
  } else {
    $sql = "INSERT INTO ".$trades_table." (executed_date, type, symbol, trade_strategy, order_type, qty, expire_date, strike_price, executed_price, order_type2, strike_price2, com_fee, total, platform, mate_id) VALUES('" . $executed_date . "', '" . $type . "', '" . $symbol . "', '" . $trade_strategy . "', '" . $order_type . "', '" . $qty . "', '" . $expire_date . "', '" . $strike_price . "', '" . $executed_price . "', '" . $order_type2 . "', '" . $strike_price2 . "', '" . $com_fee . "', '" . $total . "', '" . $platform . "', '" . $id . "');";
  }
  #echo $sql;
  $results_2 = $dbconnection->query($sql);

  if ($results_2) {
    echo "Exit Trade added.\n\n\n";
    $sql_3 = "SELECT * FROM ".$trades_table." WHERE mate_id='$id';";
    #echo $sql_3;
    $results_3 = $dbconnection->query($sql_3);
    $exit_id = '';
    while($obj = $results_3->fetch_object()){
      #echo $obj->ID;
      if ($exit_id == ''){
        $exit_id = $obj->ID;
      } else {
        $exit_id = $exit_id.'-'.$obj->ID;
      }
    }
#echo $exit_id;
    $sql_4 = "UPDATE ".$trades_table." SET mate_id = '".$exit_id."' WHERE ID = '".$id."';";
    #echo $sql_4;
    $results_4 = $dbconnection->query($sql_4);

    if ($results_4) {
      echo "Entry Trade updated.\n";

      $sql_5 = "SELECT * FROM ".$trades_table." WHERE ID='$id';";
      $results_5 = $dbconnection->query($sql_5);
      while($obj = $results_5->fetch_object()){
        $entry_amt = $obj->total;
        $entry_date = strtotime($obj->executed_date);
      }
      $date = $executed_date;
      $exit_date = strtotime($executed_date);
      $datediff = $exit_date - $entry_date;
      $trade_length = round($datediff / (60 * 60 * 24));
      $percent = number_format( ((abs($total)/abs($entry_amt)) - 1) * 100, 2).'%';
      if ($strike_price2 == ''){
        if ($trade_strategy == 'Stock'){
          $description = $symbol.' '.$trade_strategy.' sold on '.$executed_date.' at $'.$executed_price.' per share @ '.$percent.' profit in '.$trade_length.' days.';
        } elseif ($trade_strategy == 'Crypto'){
          $description = $symbol.' '.$trade_strategy.' sold on '.$executed_date.' at $'.$executed_price.' per share @ '.$percent.' profit in '.$trade_length.' days.';
        } else {
          $description = $symbol.' '.$trade_strategy.' '.$expire_date.' $'.$strike_price.' @ '.$percent.' profit in '.$trade_length.' days.';
        }
      } else {
        $description = $symbol.' '.$trade_strategy.' '.$expire_date.' $'.$strike_price.' - $'.$strike_price2.' @ '.$percent.' profit in '.$trade_length.' days.';
      }
      $amount = ($total + $entry_amt);
      $platform = $platform;
      $entry_id = $id;
      $exit_id = $exit_id;
      $sql = "INSERT INTO ".$profits_table." (date, description, amount, platform, entry_id, exit_id) VALUES('".$date."', '".$description."', '".$amount."', '".$platform."', '".$entry_id."', '".$exit_id."');";
      $results = $dbconnection->query($sql);
      if ($results) {
        echo "Profit added.";
        header("Location: {$_POST['referer']}");
        die();
      } else {
        echo "Sorry, adding this profit failed. Please try again";
      }
    } else {
      echo "Sorry, updating the entry trade failed. Please try again";
    }
  } else {
    echo "Sorry, adding this exit trade failed. Please try again";
  }
}

?>
