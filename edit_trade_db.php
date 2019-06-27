<?php
require_once("include/database.cfg.php");
require_once("include/defaults.cfg.php");

$dbconnection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($dbconnection->connect_error) {
  die("Connection failed: " . $conn->connect_error);
} else {
  $id = $_POST['ID'];
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
  } elseif ('Stock' == $trade_strategy) {
    if ('Exit' == $type) {
      $total = ((((floatval($qty)) * floatval($executed_price))) + $com_fee);
    } else {
      $total = ((((floatval($qty)) * floatval($executed_price)) * -1) + $com_fee);
    }
  } elseif ('Crypto' == $trade_strategy) {
    if ('Exit' == $type) {
      $total = ((((floatval($qty)) * floatval($executed_price))) + $com_fee);
    } else {
      $total = ((((floatval($qty)) * floatval($executed_price)) * -1) + $com_fee);
    }
  }

  if ($trade_strategy == 'Call'){
    $sql = "UPDATE ".$trades_table." SET executed_date = '".$executed_date."', sell_by_date = '".$sell_by_date."', type = '".$type."', symbol = '".$symbol."', trade_strategy = '".$trade_strategy."', order_type = '".$order_type."', qty = '".$qty."', expire_date = '".$expire_date."', strike_price = '".$strike_price."', executed_price = '".$executed_price."', com_fee = '".$com_fee."', total = '".$total."', platform = '".$platform."' WHERE ID = '".$id."';"; 
  } elseif ($trade_strategy == 'Put'){
    $sql = "UPDATE ".$trades_table." SET executed_date = '".$executed_date."', sell_by_date = '".$sell_by_date."', type = '".$type."', symbol = '".$symbol."', trade_strategy = '".$trade_strategy."', order_type = '".$order_type."', qty = '".$qty."', expire_date = '".$expire_date."', strike_price = '".$strike_price."', executed_price = '".$executed_price."', com_fee = '".$com_fee."', total = '".$total."', platform = '".$platform."' WHERE ID = '".$id."';";
  } elseif ($trade_strategy == 'Stock'){
    $sql = "UPDATE ".$trades_table." SET executed_date = '".$executed_date."', sell_by_date = '".$sell_by_date."', type = '".$type."', symbol = '".$symbol."', trade_strategy = '".$trade_strategy."', order_type = '".$order_type."', qty = '".$qty."', expire_date = '2999-12-31', strike_price = '0', executed_price = '".$executed_price."', com_fee = '".$com_fee."', total = '".$total."', platform = '".$platform."' WHERE ID = '".$id."';";
  } elseif ($trade_strategy == 'Crypto'){
    $sql = "UPDATE ".$trades_table." SET executed_date = '".$executed_date."', sell_by_date = '".$sell_by_date."', type = '".$type."', symbol = '".$symbol."', trade_strategy = '".$trade_strategy."', order_type = '".$order_type."', qty = '".$qty."', expire_date = '2999-12-31', strike_price = '0', executed_price = '".$executed_price."', com_fee = '".$com_fee."', total = '".$total."', platform = '".$platform."' WHERE ID = '".$id."';";
  } else {
    $sql = "UPDATE ".$trades_table." SET executed_date = '".$executed_date."', sell_by_date = '".$sell_by_date."', type = '".$type."', symbol = '".$symbol."', trade_strategy = '".$trade_strategy."', order_type = '".$order_type."', qty = '".$qty."', expire_date = '".$expire_date."', strike_price = '".$strike_price."', executed_price = '".$executed_price."', com_fee = '".$com_fee."', total = '".$total."', platform = '".$platform."', strike_price2 = '".$strike_price2."', order_type2 = '".$order_type2."' WHERE ID = '".$id."';";
    #echo "FINSIH ME!";
  }
  #echo $sql;
  $results_2 = $dbconnection->query($sql);

  if ($results_2) {
    echo "Trade Corrected.";
    #header("Location: {$_POST['referer']}");
    #die();   
    if ($type == 'Exit'){
      echo "helo";
      $sql_5 = " SELECT * FROM ".$trades_table." WHERE type = 'Entry' AND mate_id like '%$id%';";
      $results_5 = $dbconnection->query($sql_5);
      while($obj = $results_5->fetch_object()){
        $entry_amt = $obj->total;
        $entry_date = strtotime($obj->executed_date);
        $entry_id = $obj->ID;
      }
      $sql_6 = "select * From ".$profits_table." where exit_id Like '%$id%';";
      $results_6 = $dbconnection->query($sql_6);
      while($obj = $results_6->fetch_object()){
        $profit_id = $obj->ID;
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
      $exit_id = $id;
      $sql = "UPDATE ".$profits_table." SET date='".$date."', description='".$description."', amount='".$amount."', platform='".$platform."', entry_id='".$entry_id."', exit_id='".$exit_id."' WHERE ID='".$profit_id."';";
      echo $sql;
      $results = $dbconnection->query($sql);
      if ($results) {
        echo "Profit updated.";
        if (strpos($_POST['referer'], 'closed') !== false) {
            header("Location: view_closed_form.php");
        } else {
            header("Location: {$_POST['referer']}");
        }
        die();
      } else {
        echo "Sorry, editing this profit failed. Please try again";
      }
    } else {
        if (strpos($_POST['referer'], 'closed') !== false) {
            header( "refresh:1;url=view_closed_form.php" );
        } else {
            header( "refresh:1;url=".$_POST['referer'] );
        }
        die();
    }
  } else {
    echo "Sorry, editing this trade failed. Please try again";
  }
}

?>
