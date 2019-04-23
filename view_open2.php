<?php

require_once("include/database.cfg.php");
require_once("include/get_auth.php");
require_once("include/get_call.php");
require_once("include/get_put.php");
require_once("include/get_call_spread.php");
require_once("include/get_put_spread.php");
get_auth();
$num_green = 0;
$num_red = 0;
$num_trades = 0;

setlocale(LC_MONETARY, 'en_US');
$dbconnection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
echo "<h1>Open Trades</h1>";
  echo "<table border=1>";
  echo "<tr>
        <th><span style='font-size:.8em'>Symbol</span></th>
        <th><span style='font-size:.8em'>Trade Strategy</span></th>
        <th><span style='font-size:.8em'>Purchase Price</span></th>
        <th><span style='font-size:.8em'>Current Price</span></th>
        <th><span style='font-size:.8em'>Sell Price</span></th>
        <th><span style='font-size:.8em'>Gain/Loss</span></th>
        <th><span style='font-size:.8em'>Away</span></th>
        <th><span style='font-size:.8em'>Percent</span></th>
        <th><span style='font-size:.8em'>Platform</span></th>
        <th><span style='font-size:.8em'>Exiration Date</span></th>
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
        $sell = 0;
        if ($obj->trade_strategy == 'Put Spread'){
          # Put Spread 80% Profit target
          $sell = ((((($obj->executed_price * 100)) + (abs($obj->com_fee) / $obj->qty)) * .2) / 100);
        } elseif (strpos($obj->symbol, '*') !== false) {
          # 50% Profit Target
          $sell = ((((($obj->executed_price * 100)) + (abs($obj->com_fee) / $obj->qty)) * 1.5) / 100);
        } else {
          # 100% Profit Target
          $sell = ((((($obj->executed_price * 100)) + (abs($obj->com_fee) / $obj->qty)) * 2) / 100);
        }
        if ('Call' == $obj->trade_strategy) {
          $cur_data = get_call($symbol, $obj->strike_price, $obj->expire_date);
          $gain_loss = ($cur_data['mark'] - $obj->executed_price);
          $away_amt = ($sell - $cur_data['mark']);
          $percent_away = number_format(((($cur_data['mark']/$obj->executed_price) - 1)*100), 2);
        } elseif ('Put' == $obj->trade_strategy) {
          $cur_data = get_put($symbol, $obj->strike_price, $obj->expire_date);
          $gain_loss = ($cur_data['mark'] - $obj->executed_price);
          $away_amt = ($sell - $cur_data['mark']);
          $percent_away = number_format(((($cur_data['mark']/$obj->executed_price) - 1)*100), 2);
        } elseif ('Call Spread' == $obj->trade_strategy) {
          $cur_data = get_call_spread($symbol, $obj->strike_price, $obj->strike_price2, $obj->expire_date);
          $gain_loss = ($cur_data['mark'] - $obj->executed_price);
          $away_amt = ($sell - $cur_data['mark']);
          $percent_away = number_format(((($cur_data['mark']/$obj->executed_price) - 1)*100), 2);
        } elseif ('Put Spread' == $obj->trade_strategy) {
          $cur_data = get_put_spread($symbol, $obj->strike_price, $obj->strike_price2, $obj->expire_date);
          $gain_loss = ($obj->executed_price - $cur_data['mark']);
          $away_amt = ($cur_data['mark'] - $sell);
          $percent_away = number_format((((($cur_data['mark']/$obj->executed_price) - 1)*100)*-1), 2);
        }
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
        if ($cur_data['d_2_ex'] <= 7){
          $ex_color = 'Orange';
        } else {
          $ex_color = 'White';
        }
        #########################
        echo "<tr bgcolor='".$color."' style='color: ".$font_color.";'>
              <td align='center'><span style='font-size:.8em'>$obj->symbol</span></td>
              <td align='center'><span style='font-size:.8em'>$obj->trade_strategy</span></td>
              <td align='center'><span style='font-size:.8em'>".money_format('%(#10n', $obj->executed_price)."</span></td>
              <td align='center'><strong><span style='font-size:1em'>".money_format('%(#10n', $cur_data['mark'])."</span></strong></td>
              <td align='center'><span style='font-size:.8em'>".money_format('%(#10n', $sell)."</span></td>
              <td align='center'><span style='font-size:.8em'>".number_format($gain_loss,2)."</span></td>
              <td align='center'><span style='font-size:.8em'>".number_format($away_amt,2)."</span></td>
              <td align='center'><strong><span style='font-size:1em'>$percent_away%</span></strong></td>
              <td align='center'><span style='font-size:.8em'>$obj->platform</span></td>
              <td align='center' bgcolor='".$ex_color."' style='color: Black;'><span style='font-size:.9em'>$obj->expire_date</span></td>
              </tr>";
      }
    }
    $results->close();
    unset($obj);
  }
  echo "</table>";
  echo "<table border='1'>
        <tr><td align='center'>Winners</td><td align='center'>".$num_green."</td></tr>
        <tr><td align='center'>Losers</td><td align='center'>".$num_red."</td></tr>
        <tr><td align='center'>Neutral</td><td align='center'>".($num_trades - $num_red - $num_green)."</td></tr>
        <tr><td align='center'>Total</td><td align='center'>".$num_trades."</td></tr>
        </table>";

?>