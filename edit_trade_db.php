<?php
require_once("include/database.cfg.php");

$dbconnection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($dbconnection->connect_error) {
  die("Connection failed: " . $conn->connect_error);
} else {
  $id = $_POST['ID'];
  $executed_date = $_POST['executed_date'];
  $type = $_POST['type'];
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
    if ('Exit' == $type) {
      $total = ((((floatval($qty) * 100) * floatval($executed_price))) + $com_fee);
    } else {
      $total = ((((floatval($qty) * 100) * floatval($executed_price)) * -1) + $com_fee);
    }
  } elseif ('Put' == $trade_strategy) {
    if ('Exit' == $type) {
      $total = ((((floatval($qty) * 100) * floatval($executed_price))) + $com_fee);
    } else {
      $total = ((((floatval($qty) * 100) * floatval($executed_price)) * -1) + $com_fee);
    }
  } elseif ('Call Spread' == $trade_strategy) {
    if ('Exit' == $type) {
      $total = ((((floatval($qty) * 100) * floatval($executed_price))) + $com_fee);
    } else {
      $total = ((((floatval($qty) * 100) * floatval($executed_price)) * -1) + $com_fee);
    }
  } elseif ('Put Spread' == $trade_strategy) {
     if ('Exit' == $type) {
      $total = ((((floatval($qty) * 100) * floatval($executed_price)) * -1) + $com_fee);
    } else {
      $total = ((((floatval($qty) * 100) * floatval($executed_price))) + $com_fee);
    }
  }

  if ($trade_strategy == 'Call'){
    $sql = "UPDATE trades SET executed_date = '".$executed_date."', type = '".$type."', symbol = '".$symbol."', trade_strategy = '".$trade_strategy."', order_type = '".$order_type."', qty = '".$qty."', expire_date = '".$expire_date."', strike_price = '".$strike_price."', executed_price = '".$executed_price."', com_fee = '".$com_fee."', total = '".$total."', platform = '".$platform."' WHERE ID = '".$id."';"; 
  } elseif ($trade_strategy == 'Put'){
    $sql = "UPDATE trades SET executed_date = '".$executed_date."', type = '".$type."', symbol = '".$symbol."', trade_strategy = '".$trade_strategy."', order_type = '".$order_type."', qty = '".$qty."', expire_date = '".$expire_date."', strike_price = '".$strike_price."', executed_price = '".$executed_price."', com_fee = '".$com_fee."', total = '".$total."', platform = '".$platform."' WHERE ID = '".$id."';";
  } else {
    $sql = "UPDATE trades SET executed_date = '".$executed_date."', type = '".$type."', symbol = '".$symbol."', trade_strategy = '".$trade_strategy."', order_type = '".$order_type."', qty = '".$qty."', expire_date = '".$expire_date."', strike_price = '".$strike_price."', executed_price = '".$executed_price."', com_fee = '".$com_fee."', total = '".$total."', platform = '".$platform."', strike_price2 = '".$strike_price2."', order_type2 = '".$order_type2."' WHERE ID = '".$id."';";
    echo "FINSIH ME!";
  }
  #echo $sql;
  $results_2 = $dbconnection->query($sql);

  if ($results_2) {
    echo "Trade Corrected.";
    #header("Location: {$_POST['referer']}");
    #die();   
    if ($type == 'Exit'){
      echo "helo";
      $sql_5 = " SELECT * FROM trades WHERE type = 'Entry' AND mate_id like '%$id%';";
      $results_5 = $dbconnection->query($sql_5);
      while($obj = $results_5->fetch_object()){
        $entry_amt = $obj->total;
        $entry_id = $obj->ID;
      }
      $sql_6 = "select * From profits where exit_id Like '%$id%';";
      $results_6 = $dbconnection->query($sql_6);
      while($obj = $results_6->fetch_object()){
        $profit_id = $obj->ID;
      }
      $date = $executed_date;
      if ($strike_price2 == ''){
        $description = $symbol.' '.$trade_strategy.' '.$expire_date.' $'.$strike_price;
      } else {
        $description = $symbol.' '.$trade_strategy.' '.$expire_date.' $'.$strike_price.' - $'.$strike_price2;
      }
      $amount = ($total + $entry_amt);
      $platform = $platform;
      $exit_id = $id;
      $sql = "UPDATE profits SET date='".$date."', description='".$description."', amount='".$amount."', platform='".$platform."', entry_id='".$entry_id."', exit_id='".$exit_id."' WHERE ID='".$profit_id."';";
      echo $sql;
      $results = $dbconnection->query($sql);
      if ($results) {
        echo "Profit updated.";
        header("Location: {$_POST['referer']}");
        die();
      } else {
        echo "Sorry, editing this profit failed. Please try again";
      }
    }
  } else {
    echo "Sorry, editing this trade failed. Please try again";
  }
}

?>
