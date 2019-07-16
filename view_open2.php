<?php
header( "refresh:20;url=." );
require_once("include/html_open.php");
require_once("include/database.cfg.php");
require_once("include/get_auth.php");
require_once("include/get_call.php");
require_once("include/get_stock.php");
require_once("include/get_put.php");
require_once("include/get_call_spread.php");
require_once("include/get_put_spread.php");
require_once("include/binance.php");
require_once("include/defaults.cfg.php");
echo "

";
$auth_results = get_auth();
$num_green = 0;
$num_red = 0;
$num_trades = 0;
$invested_total = 0;
$current_total = 0;
$max_total = 0;
$has_crypto = false;
$stop_loss_percent = .25;
$trail_stop_percent = .10;

setlocale(LC_MONETARY, 'en_US');
$dbconnection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
echo "<h1>Open Trades</h1>";
echo '<a href="add_entry_trade.php" style="float: left;"><i class="fa fa-plus"></i></a>';
echo '<a href="config.php" style="float: right;">&#9881;</a>';
echo '<table border=0 align=center><tr><td><input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search for symbols.." title="Type in a symbol"></td>';
echo '<td><input type="text" id="myInput1" onkeyup="myFunction1()" placeholder="Search for platforms.." title="Type in a platform"></td></tr></table>';
  echo "<table border=1 id='myTable'>";
  echo "<tr class='header'>
        <th><span style='font-size:.8em'>Entry Date</span></th>
        <th><span style='font-size:.8em'>Symbol</span></th>
        <th><span style='font-size:.8em'>Qty</span></th>
        <th><span style='font-size:.8em'>Trade Strategy</span></th>
        <th><span style='font-size:.8em'>Purchase Price</span></th>
        <th><span style='font-size:.8em'>Current Price</span></th>
        <th><span style='font-size:.8em'>Percent</span></th>
        <th><span style='font-size:.8em'>Stop Loss</span></th>
        <th><span style='font-size:.8em'>Trailing Stop</span></th>
        <th><span style='font-size:.8em'>Sell Price</span></th>
        <th><span style='font-size:.8em'>Gain/Loss</span></th>
        <th><span style='font-size:.8em'>Away</span></th>
        <th><span style='font-size:.8em'>Max Price</span></th>
        <th><span style='font-size:.8em'>Max Percent</span></th>
        <th><span style='font-size:.8em'>Platform</span></th>
        <th><span style='font-size:.8em'>Trade Length</span></th>
        <th><span style='font-size:.8em'>Exiration Date</span></th>
        <th><span style='font-size:.8em'>Sell By Date</span></th>
        <th colspan='3'><span style='font-size:.8em'>Acions</span></th>
        </tr>";
  if (!$dbconnection->connect_errno) {
    $sql = "SELECT * FROM ".$trades_table." WHERE type='Entry' ORDER BY ID DESC;";
    $results = $dbconnection->query($sql);
    while($obj = $results->fetch_object()){
      $new_qty = $obj->qty;
      $skip = False;
      if ( $obj->mate_id != '' ) {
        if (\strpos($obj->mate_id, '-') !== false) {
          $sql_2 = "SELECT * FROM ".$trades_table." WHERE";
          $exits = explode("-", $obj->mate_id);
          $count = 0;
          foreach ($exits as $exit) {
            if ( $count == 0 ) {
              $sql_2 = $sql_2." ID='".$exit."'";
            } else {
              $sql_2 = $sql_2." OR ID='".$exit."'";
            }
            $count++;
          }
          $results_2 = $dbconnection->query($sql_2);
          $qty = 0;
          while($obj_2 = $results_2->fetch_object()){
            $qty = ($qty + $obj_2->qty);
          }
          if ( $qty == $obj->qty ) {
            $skip = True;
          } else {
            $skip = False;
            $new_qty = ($new_qty - $qty);
          }
        } else {
          $sql_1 = "SELECT * FROM ".$trades_table." WHERE ID='".$obj->mate_id."';";
          $results_1 = $dbconnection->query($sql_1);
          while($obj_1 = $results_1->fetch_object()){
              if ( $obj_1->qty == $obj->qty ) {
                $skip = True;
              } else {
                $skip = False;
                $new_qty = ($new_qty - $obj_1->qty);
              }
          }
        }
      }
      if ( $skip != True ){
        #########################
        $symbol = str_replace("*", "", $obj->symbol);
        $symbol = str_replace("~", "", $symbol);
        $symbol = str_replace("`", "", $symbol);
        $symbol = str_replace("?", "", $symbol);
        $sell = 0;
         
        if (strpos($obj->symbol, '*') !== false) {
          # Force 50% Profit Target
          $sell = ((((($obj->executed_price * 100)) + (abs($obj->com_fee) / $obj->qty)) * 1.5) / 100);
        } elseif (strpos($obj->symbol, '`') !== false) {
          # Force 75% Profit Target
          $sell = ((((($obj->executed_price * 100)) + (abs($obj->com_fee) / $obj->qty)) * 1.75) / 100);
        } elseif (strpos($obj->symbol, '~') !== false) {
          # Force 100% Profit Target
          $sell = ((((($obj->executed_price * 100)) + (abs($obj->com_fee) / $obj->qty)) * 2) / 100);
        } elseif ($obj->trade_strategy == 'Put Spread'){
          # Put Spread 80% Profit target
          $sell = ((((($obj->executed_price * 100)) - (abs($obj->com_fee) / $obj->qty)) * .2) / 100);
        } elseif ($obj->trade_strategy == 'Bear Call Spread'){
          # Bear Call Spread 80% Profit target
          $sell = ((((($obj->executed_price * 100)) - (abs($obj->com_fee) / $obj->qty)) * .2) / 100);
        } else {
          # 100% Profit Target
          $sell = ((((($obj->executed_price * 100)) + (abs($obj->com_fee) / $obj->qty)) * 2) / 100);
        }
        if ('Call' == $obj->trade_strategy) {
          $cur_data = get_call($symbol, $obj->strike_price, $obj->expire_date);
          $gain_loss = ($cur_data['mark'] - $obj->executed_price);
          $away_amt = ($sell - $cur_data['mark']);
          $percent_away = number_format(((($cur_data['mark']/$obj->executed_price) - 1)*100), 2);
          $stop_loss = ($obj->executed_price - ($obj->executed_price * $stop_loss_percent));
          if ($cur_data['mark'] > $obj->max_price ){
            $sql_max = "UPDATE ".$trades_table." SET max_price = '".$cur_data['mark']."' WHERE ID='".$obj->ID."';";
            $results_max = $dbconnection->query($sql_max);
            $max_price = $cur_data['mark'];
          } else {
            $max_price = $obj->max_price;
          }
          $max_percent = number_format(((($max_price/$obj->executed_price) - 1)*100), 2);
        } elseif ('Put' == $obj->trade_strategy) {
          $cur_data = get_put($symbol, $obj->strike_price, $obj->expire_date);
          $gain_loss = ($cur_data['mark'] - $obj->executed_price);
          $away_amt = ($sell - $cur_data['mark']);
          $percent_away = number_format(((($cur_data['mark']/$obj->executed_price) - 1)*100), 2);
          $stop_loss = ($obj->executed_price - ($obj->executed_price * $stop_loss_percent));
          if ($cur_data['mark'] > $obj->max_price ){
            $sql_max = "UPDATE ".$trades_table." SET max_price = '".$cur_data['mark']."' WHERE ID='".$obj->ID."';";
            $results_max = $dbconnection->query($sql_max);
            $max_price = $cur_data['mark'];
          } else {
            $max_price = $obj->max_price;
          }
          $max_percent = number_format(((($max_price/$obj->executed_price) - 1)*100), 2);
        } elseif ('Call Spread' == $obj->trade_strategy) {
          $cur_data = get_call_spread($symbol, $obj->strike_price, $obj->strike_price2, $obj->expire_date);
          $gain_loss = ($cur_data['mark'] - $obj->executed_price);
          $away_amt = ($sell - $cur_data['mark']);
          $percent_away = number_format(((($cur_data['mark']/$obj->executed_price) - 1)*100), 2);
          $stop_loss = ($obj->executed_price - ($obj->executed_price * $stop_loss_percent));
          if ($cur_data['mark'] > $obj->max_price ){
            $sql_max = "UPDATE ".$trades_table." SET max_price = '".$cur_data['mark']."' WHERE ID='".$obj->ID."';";
            $results_max = $dbconnection->query($sql_max);
            $max_price = $cur_data['mark'];
          } else {
            $max_price = $obj->max_price;
          }
          $max_percent = number_format(((($max_price/$obj->executed_price) - 1)*100), 2);
        } elseif ('Bear Call Spread' == $obj->trade_strategy) {
          $cur_data = get_call_spread($symbol, $obj->strike_price2, $obj->strike_price, $obj->expire_date);
          $gain_loss = ($obj->executed_price - $cur_data['mark']);
          $away_amt = ($cur_data['mark'] - $sell);
          $percent_away = number_format((((($cur_data['mark']/$obj->executed_price) - 1)*100)*-1), 2);
          $stop_loss = ($obj->executed_price + ($obj->executed_price * $stop_loss_percent));
          if ($cur_data['mark'] < $obj->max_price ){
            $sql_max = "UPDATE ".$trades_table." SET max_price = '".$cur_data['mark']."' WHERE ID='".$obj->ID."';";
            $results_max = $dbconnection->query($sql_max);
            $max_price = $cur_data['mark'];
          } else {
            $max_price = $obj->max_price;
          }
          $max_percent = number_format((((($max_price/$obj->executed_price) - 1)*100)*-1), 2);
        } elseif ('Put Spread' == $obj->trade_strategy) {
          $cur_data = get_put_spread($symbol, $obj->strike_price, $obj->strike_price2, $obj->expire_date);
          $gain_loss = ($obj->executed_price - $cur_data['mark']);
          $away_amt = ($cur_data['mark'] - $sell);
          $interval = ($obj->strike_price2 - $obj->strike_price);
          if ($interval < 0){
            $cur_data['mark'] = ($cur_data['mark'] * -1);
            $percent_away = number_format(((($cur_data['mark']/$obj->executed_price) - 1)*100), 2);
            $stop_loss = ($obj->executed_price - ($obj->executed_price * $stop_loss_percent));
            if ($cur_data['mark'] > $obj->max_price ){
              $sql_max = "UPDATE ".$trades_table." SET max_price = '".$cur_data['mark']."' WHERE ID='".$obj->ID."';";
              $results_max = $dbconnection->query($sql_max);
              $max_price = $cur_data['mark'];
            } else {
              $max_price = $obj->max_price;
            }
            $max_percent = number_format(((($max_price/$obj->executed_price) - 1)*100), 2);
          } else {
            $percent_away = number_format((((($cur_data['mark']/$obj->executed_price) - 1)*100)*-1), 2);
            $stop_loss = ($obj->executed_price + ($obj->executed_price * $stop_loss_percent));
            if ($cur_data['mark'] > $obj->max_price ){
              $sql_max = "UPDATE ".$trades_table." SET max_price = '".$cur_data['mark']."' WHERE ID='".$obj->ID."';";
              $results_max = $dbconnection->query($sql_max);
              $max_price = $cur_data['mark'];
            } else {
              $max_price = $obj->max_price;
            }
            $max_percent = number_format((((($max_price/$obj->executed_price) - 1)*100)*-1), 2);
          }
        } elseif ('Bull Put Spread' == $obj->trade_strategy) {
          $cur_data = get_put_spread($symbol, $obj->strike_price2, $obj->strike_price, $obj->expire_date);
          $gain_loss = ($obj->executed_price - $cur_data['mark']);
          $away_amt = ($cur_data['mark'] - $sell);
          $interval = ($obj->strike_price2 - $obj->strike_price);
          if ($interval < 0){
            $cur_data['mark'] = ($cur_data['mark'] * -1);
            $percent_away = number_format(((($cur_data['mark']/$obj->executed_price) - 1)*100), 2);
            $stop_loss = ($obj->executed_price - ($obj->executed_price * $stop_loss_percent));
            if ($cur_data['mark'] > $obj->max_price ){
              $sql_max = "UPDATE ".$trades_table." SET max_price = '".$cur_data['mark']."' WHERE ID='".$obj->ID."';";
              $results_max = $dbconnection->query($sql_max);
              $max_price = $cur_data['mark'];
            } else {
              $max_price = $obj->max_price;
            }
            $max_percent = number_format(((($max_price/$obj->executed_price) - 1)*100), 2);
          } else {
            $percent_away = number_format((((($cur_data['mark']/$obj->executed_price) - 1)*100)*-1), 2);
            $stop_loss = ($obj->executed_price + ($obj->executed_price * $stop_loss_percent));
            if ($cur_data['mark'] > $obj->max_price ){
              $sql_max = "UPDATE ".$trades_table." SET max_price = '".$cur_data['mark']."' WHERE ID='".$obj->ID."';";
              $results_max = $dbconnection->query($sql_max);
              $max_price = $cur_data['mark'];
            } else {
              $max_price = $obj->max_price;
            }
            $max_percent = number_format((((($max_price/$obj->executed_price) - 1)*100)*-1), 2);
          }
        } elseif ('Stock' == $obj->trade_strategy) {
          $cur_data = get_stock($symbol);
          $gain_loss = ($cur_data['mark'] - $obj->executed_price);
          $away_amt = ($sell - $cur_data['mark']);
          $percent_away = number_format(((($cur_data['mark']/$obj->executed_price) - 1)*100), 2);
          $stop_loss = ($obj->executed_price - ($obj->executed_price * $stop_loss_percent));
          if ($cur_data['mark'] > $obj->max_price ){
            $sql_max = "UPDATE ".$trades_table." SET max_price = '".$cur_data['mark']."' WHERE ID='".$obj->ID."';";
            $results_max = $dbconnection->query($sql_max);
            $max_price = $cur_data['mark'];
          } else {
            $max_price = $obj->max_price;
          }
          $max_percent = number_format(((($max_price/$obj->executed_price) - 1)*100), 2);
        } elseif ('Crypto' == $obj->trade_strategy) {
          $cur_data = get_crypto($symbol."BTC");
          $gain_loss = ($cur_data['mark'] - $obj->executed_price);
          $away_amt = ($sell - $cur_data['mark']);
          $percent_away = number_format(((($cur_data['mark']/$obj->executed_price) - 1)*100), 2);
          $stop_loss = ($obj->executed_price - ($obj->executed_price * $stop_loss_percent));
          if ($cur_data['mark'] > $obj->max_price ){
            $sql_max = "UPDATE ".$trades_table." SET max_price = '".$cur_data['mark']."' WHERE ID='".$obj->ID."';";
            $results_max = $dbconnection->query($sql_max);
            $max_price = $cur_data['mark'];
          } else {
            $max_price = $obj->max_price;
          }
          $max_percent = number_format(((($max_price/$obj->executed_price) - 1)*100), 2);
        }
        $trail_stop = ($cur_data['mark'] * $trail_stop_percent);
        $num_trades = ($num_trades + 1);
        #color section
        $color = 'White';
        $font_color = 'Black';
        if ($percent_away > 5 && $percent_away < 50){
          $num_green = ($num_green + 1);
          $color = 'LightGreen';
          $font_color = 'Black';
        } elseif ($percent_away >= 50){
          $num_green = ($num_green + 1);
          $color = 'DarkGreen';
          $font_color = 'White';
        } elseif ($percent_away < -5 && $percent_away > -50){
          $num_red = ($num_red + 1);
          $color = 'LightCoral';
          $font_color = 'Black';
        } elseif ($percent_away <= -50){
          $num_red = ($num_red + 1);
          $color = 'DarkRed';
          $font_color = 'White';
        }
        $current = strtotime(date("Y-m-d"));
        $date    = strtotime($obj->expire_date);
        $datediff = $date - $current;
        $difference = floor($datediff/(60*60*24));
        if($difference <= 7){
          $ex_color = 'Orange';
        } else {
          $ex_color = 'White';
        }
        $current = strtotime(date("Y-m-d"));
        $date    = strtotime($obj->sell_by_date);
        $datediff = $date - $current;
        $difference = floor($datediff/(60*60*24));
        if($difference==0){
          $sb_color = 'Red';
        } else {
          $sb_color = 'White';
        }
        #########################
        $now = time(); // or your date as well
        $your_date = strtotime($obj->executed_date);
        $datediff = $now - $your_date;
        $trade_length = round($datediff / (60 * 60 * 24));
        if ($obj->trade_strategy == 'Crypto') {
            $btc_price = get_crypto("BTCUSDT")['mark'];
            $format_line = '%(#10.11n';
            $format_num = 11;
            $format_qty = 11;
            $has_crypto = true;
        } else {
            $format_line = '%(#10n';
            $format_num = 2;
            $format_qty = 0;
        }
        
        echo "<tr bgcolor='".$color."' style='color: ".$font_color.";'>
              <td align='center'><span style='font-size:.9em'>$obj->executed_date</span></td>
              <td align='center'><span style='font-size:.8em'>$obj->symbol</span></td>
              <td align='center'><span style='font-size:.8em'>".number_format($obj->qty,$format_qty)."</span></td>
              <td align='center'><span style='font-size:.8em'>$obj->trade_strategy</span></td>
              <td align='center'><span style='font-size:.8em'>".money_format($format_line, $obj->executed_price)."</span></td>
              <td align='center'><strong><span style='font-size:1em'>".money_format($format_line, $cur_data['mark'])."</span></strong></td>
              <td align='center'><strong><span style='font-size:1em'>$percent_away%</span></strong></td>
              <td align='center'><span style='font-size:.8em'>".money_format($format_line, $stop_loss)."</span></td>
              <td align='center'><span style='font-size:.8em'>".money_format($format_line, $trail_stop)."</span></td>
              <td align='center'><span style='font-size:.8em'>".money_format($format_line, $sell)."</span></td>
              <td align='center'><span style='font-size:.8em'>".number_format($gain_loss,$format_num)."</span></td>
              <td align='center'><span style='font-size:.8em'>".number_format($away_amt,$format_num)."</span></td>
              <td align='center'><strong><span style='font-size:1em'>".money_format($format_line, $max_price)."</span></strong></td>
              <td align='center'><strong><span style='font-size:1em'>$max_percent%</span></strong></td>
              <td align='center'><span style='font-size:.8em'>$obj->platform</span></td>
              <td align='center'><span style='font-size:.8em'>$trade_length</span></td>
              <td align='center' bgcolor='".$ex_color."' style='color: Black;'><span style='font-size:.9em'>$obj->expire_date</span></td>
              <td align='center' bgcolor='".$sb_color."' style='color: Black;'><span style='font-size:.9em'>$obj->sell_by_date</span></td>
              <td align='center' bgcolor='white' style='color: Black;'><center><form method='POST' action='add_exit_trade.php'>
                  <input type='hidden' name='ID' value='$obj->ID'>
                  <button align='center' type='submit'><i class='fa fa-sign-out'></i></button></form></center></td>
              <td align='center' bgcolor='white' style='color: Black;'><center><form method='POST' action='edit_trade.php'>
                  <input type='hidden' name='ID' value='$obj->ID'>
                  <button align='center' type='submit'><i class='fa fa-edit'></i></button></form></center></td>
              <td align='center' bgcolor='white' style='color: Black;'><center><form method='POST' action='delete_trade.php'>
                  <input type='hidden' name='ID' value='$obj->ID'>
                  <button align='center' type='submit'><i class='fa fa-trash'></i></button></form></center></td>
              </tr>";
          if ($has_crypto){
            $format_line = '%(#10.6n';
            $format_num = 6;
            $ex_price = ( $obj->executed_price * $btc_price );
            $cur_price = ( $cur_data['mark'] * $btc_price );
            $sell_price = ( $sell * $btc_price );
            $gain_loss = ( $gain_loss * $btc_price );
            $away_amt = ( $away_amt * $btc_price );
            $buy_total = ( $ex_price * $obj->qty );
            $cur_total = ( $cur_price * $obj->qty );
            echo "<tr bgcolor='".$color."' style='color: ".$font_color.";'>
              <td align='center'><span style='font-size:.8em'> - </span></td>
              <td align='center'><span style='font-size:.8em'>$obj->symbol</span></td>
              <td align='center'><span style='font-size:.8em'> - </span></td>
              <td align='center'><span style='font-size:.8em'> - </span></td>
              <td align='center'><span style='font-size:.8em'>".money_format($format_line, $ex_price)."</span></td>
              <td align='center'><strong><span style='font-size:1em'>".money_format($format_line, $cur_price)."</span></strong></td>
              <td align='center'><span style='font-size:.8em'>".money_format($format_line, $sell_price)."</span></td>
              <td align='center'><span style='font-size:.8em'>".number_format($gain_loss,$format_num)."</span></td>
              <td align='center'><span style='font-size:.8em'>".number_format($away_amt,$format_num)."</span></td>
              <td align='center'><span style='font-size:.8em'> - </span></td>
              <td align='center'><span style='font-size:.8em'> - </span></td>
              <td align='center'><span style='font-size:.8em'> - </span></td>
              <td align='center'><span style='font-size:.8em'> - </span></td>
              <td align='center'><span style='font-size:.8em'> - </span></td>
              <td align='center'><span style='font-size:.8em'> - </span></td>
              <td align='center'><span style='font-size:.8em'>".money_format('%(#10n', $buy_total)."</span></td>
              <td align='center'><strong><span style='font-size:1em'>".money_format('%(#10n', $cur_total)."</span></strong></td>
              <td align='center'><span style='font-size:.8em'> - </span></td>
              <td align='center'><span style='font-size:.8em'> - </span></td>
              <td align='center'><span style='font-size:.8em'> - </span></td>
              </tr>";
          }
          if ('Stock' == $obj->trade_strategy) {
            $invested_total = (($obj->qty * $obj->executed_price)) + $invested_total;
            $current_total = (($obj->qty * $cur_data['mark'])) + $current_total;
            $max_total = (($obj->qty * $sell)) + $max_total;  
          } elseif ('Crypto' == $obj->trade_strategy) {
            $invested_total = (($obj->qty * $obj->executed_price)) + $invested_total;
            $current_total = (($obj->qty * $cur_data['mark'])) + $current_total;
            $max_total = (($obj->qty * $sell)) + $max_total;
          } else {
            $invested_total = (($obj->qty * $obj->executed_price) * 100) + $invested_total;
            $current_total = (($obj->qty * $cur_data['mark']) * 100) + $current_total;
            $max_total = (($obj->qty * $sell) * 100) + $max_total;
          }
      }
    }
    $results->close();
    unset($obj);
  }
  $gain_loss = ($current_total - $invested_total);
  $away_amt = ($max_total - $current_total);
  $percent_away = number_format(((($current_total/$invested_total) - 1)*100), 2);
  if ($has_crypto){
    $format = '%(#10.11n';
    $format_num = 11;
  } else {
    $format = '%(#10n';
    $format_num = 2;
  }
  echo "<tr>
              <td align='center'><span style='font-size:.8em'> - </span></td>
              <td align='center'><span style='font-size:.8em'> - </span></td>
              <td align='center'><span style='font-size:.8em'> - </span></td>
              <td align='center'><span style='font-size:.8em'> - </span></td>
              <td align='center'><span style='font-size:.8em'>".money_format($format, $invested_total)."</span></td>
              <td align='center'><strong><span style='font-size:1em'>".money_format($format, $current_total)."</span></strong></td>
              <td align='center'><strong><span style='font-size:1em'>$percent_away%</span></strong></td>
              <td align='center'><span style='font-size:.8em'>".money_format($format, $max_total)."</span></td>
              <td align='center'><span style='font-size:.8em'>".number_format($gain_loss,$format_num)."</span></td>
              <td align='center'><span style='font-size:.8em'>".number_format($away_amt,$format_num)."</span></td>
              <td align='center'><span style='font-size:.8em'> - </span></td>
              <td align='center'><span style='font-size:.8em'> - </span></td>
              <td align='center'><span style='font-size:.8em'> - </span></td>
              <td align='center'><span style='font-size:.8em'> - </span></td>
              <td align='center'><span style='font-size:.8em'> - </span></td>
              <td align='center'><span style='font-size:.8em'> - </span></td>
              <td align='center'><span style='font-size:.8em'> - </span></td>
              <td align='center'><span style='font-size:.8em'> - </span></td>
              <td align='center'><span style='font-size:.8em'> - </span></td>
              <td align='center'><span style='font-size:.8em'> - </span></td>
              <td align='center'><span style='font-size:.8em'> - </span></td>
              </tr>";
  if ($has_crypto){
    $format = '%(#10n';
    $format_num = 2;
    $invested_total = ($invested_total * $btc_price);
    $current_total = ($current_total * $btc_price);
    $max_total = ($max_total * $btc_price);
    $gain_loss = ($gain_loss * $btc_price);
    $away_amt = ($away_amt * $btc_price);
    echo "<tr>
              <td align='center'><span style='font-size:.8em'> - </span></td>
              <td align='center'><span style='font-size:.8em'> - </span></td>
              <td align='center'><span style='font-size:.8em'> - </span></td>
              <td align='center'><span style='font-size:.8em'> - </span></td>
              <td align='center'><span style='font-size:.8em'>".money_format($format, $invested_total)."</span></td>
              <td align='center'><strong><span style='font-size:1em'>".money_format($format, $current_total)."</span></strong></td>
              <td align='center'><strong><span style='font-size:1em'>$percent_away%</span></strong></td>
              <td align='center'><span style='font-size:.8em'>".money_format($format, $max_total)."</span></td>
              <td align='center'><span style='font-size:.8em'>".number_format($gain_loss,$format_num)."</span></td>
              <td align='center'><span style='font-size:.8em'>".number_format($away_amt,$format_num)."</span></td>
              <td align='center'><span style='font-size:.8em'> - </span></td>
              <td align='center'><span style='font-size:.8em'> - </span></td>
              <td align='center'><span style='font-size:.8em'> - </span></td>
              <td align='center'><span style='font-size:.8em'> - </span></td>
              <td align='center'><span style='font-size:.8em'> - </span></td>
              <td align='center'><span style='font-size:.8em'> - </span></td>
              <td align='center'><span style='font-size:.8em'> - </span></td>
              <td align='center'><span style='font-size:.8em'> - </span></td>
              <td align='center'><span style='font-size:.8em'> - </span></td>
              <td align='center'><span style='font-size:.8em'> - </span></td>
              </tr>";
  }
  echo "</table>";
  echo "<table border='1' id='myTable1'>
        <tr><td align='center'>Winners</td><td align='center'>".$num_green."</td></tr>
        <tr><td align='center'>Losers</td><td align='center'>".$num_red."</td></tr>
        <tr><td align='center'>Neutral</td><td align='center'>".($num_trades - $num_red - $num_green)."</td></tr>
        <tr><td align='center'>Total</td><td align='center'>".$num_trades."</td></tr>
        </table>";

echo '<br><br><br>';
echo '<button align="left" type="button" data-toggle="collapse" data-target="#demo">Auth Information</button>
  <div id="demo" class="collapse">';
  echo "Auth Results: ".$auth_results;
  echo "<BR> <a href='https://auth.tdameritrade.com/oauth?client_id=MONEY_DERS@AMER.OAUTHAP&response_type=code&redirect_uri=https://api.dersllc.com:8743'>Re-Request Auth</a>";
  echo "<BR> <a href='first_auth.php'>Re-Import Auth</a>";
  echo '</div>';




require_once("include/html_close.php");
?>

<script>
function myFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("myTable");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}
function myFunction1() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput1");
  filter = input.value.toUpperCase();
  table = document.getElementById("myTable");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[14];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}
</script>




