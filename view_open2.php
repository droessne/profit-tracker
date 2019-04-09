<?php

require_once("include/database.cfg.php");
require_once("include/get_auth.php");
require_once("include/get_call.php");
require_once("include/get_put.php");
require_once("include/get_call_spread.php");
get_auth();

setlocale(LC_MONETARY, 'en_US');
$dbconnection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
echo "<h1>Open Trades</h1>";
  echo "<table border=1>";
  echo "<tr>
        <th><span style='font-size:.8em'>Symbol</span></th>
        <th><span style='font-size:.8em'>Purchase Price</span></th>
        <th><span style='font-size:.8em'>Sell Price</span></th>
        <th><span style='font-size:.8em'>Current Price</span></th>
        <th><span style='font-size:.8em'>Gain/Loss</span></th>
        <th><span style='font-size:.8em'>Away</span></th>
        <th><span style='font-size:.8em'>Percent</span></th>
        <th><span style='font-size:.8em'>Platform</span></th>
        <th><span style='font-size:.8em'>Exiration Date</span></th>
        </tr>";
  if (!$dbconnection->connect_errno) {
    $sql = "SELECT * FROM trades WHERE type='Entry' ORDER BY ID DESC;";
    $results = $dbconnection->query($sql);
    while($obj = $results->fetch_object()){
      $new_qty = $obj->qty;
      $skip = False;
      if ( $obj->mate_id != '' ) {
        if (\strpos($obj->mate_id, '-') !== false) {
          $sql_2 = "SELECT * FROM trades WHERE";
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
          $sql_1 = "SELECT * FROM trades WHERE ID='".$obj->mate_id."';";
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
#########################
      $symbol = str_replace("*", "", $obj->symbol);
      $sell = 0;
      if ($obj->trade_strategy == 'Put Spread'){
        # Put Spread 80% Profit target
        $sell = ((((($obj->executed_price * 100) * $obj->qty) + abs($obj->com_fee)) * .2) / 100);
      } elseif (strpos($obj->symbol, '*') !== false) {
        # 50% Profit Target
        $sell = ((((($obj->executed_price * 100) * $obj->qty) + abs($obj->com_fee)) * 1.5) / 100);
      } else {
        # 100% Profit Target
        $sell = ((((($obj->executed_price * 100) * $obj->qty) + abs($obj->com_fee)) * 2) / 100);
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
        #$cur_data = get_call($symbol, $obj->strike_price, $obj->expire_date);
        $cur_data = 0;
      }
      #color section
      $color = 'White';
      $font_color = 'Black';
      if ($percent_away > 5 && $percent_away < 50){
        $color = 'LightGreen';
        $font_color = 'Black';
      } elseif ($percent_away > 50){
        $color = 'DarkGreen';
        $font_color = 'White';
      } elseif ($percent_away < -5 && $percent_away > -50){
        $color = 'LightCoral';
        $font_color = 'Black';
      } elseif ($percent_away < -50){
        $color = 'DarkRed';
        $font_color = 'White';
      }
      if ($cur_data['d_2_ex'] < 7){
        $ex_color = 'Orange';
      } else {
        $ex_color = 'White';
      }


#########################
      if ( $skip != True ){
        echo "<tr bgcolor='".$color."' style='color: ".$font_color.";'>
              <td align='center'><span style='font-size:.8em'>$obj->symbol</span></td>
              <td align='center'><span style='font-size:.8em'>".money_format('%(#10n', $obj->executed_price)."</span></td>
              <td align='center'><span style='font-size:.8em'>".money_format('%(#10n', $sell)."</span></td>
              <td align='center'><span style='font-size:.8em'>".money_format('%(#10n', $cur_data['mark'])."</span></td>
              <td align='center'><span style='font-size:.8em'>".number_format($gain_loss,2)."</span></td>
              <td align='center'><span style='font-size:.8em'>".number_format($away_amt,2)."</span></td>
              <td align='center'><span style='font-size:.8em'>$percent_away%</span></td>
              <td align='center'><span style='font-size:.8em'>$obj->platform</span></td>
              <td align='center' bgcolor='".$ex_color."' style='color: Black;'><span style='font-size:.9em'>$obj->expire_date</span></td>
              </tr>";
      }
    }
    $results->close();
    unset($obj);
  }
  echo "</table>";


?>