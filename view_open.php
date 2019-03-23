<?php

require_once("include/database.cfg.php");

function viewByPlatform($platform){
  setlocale(LC_MONETARY, 'en_US');
  $dbconnection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
  echo "<h1> ".$platform." Open Trades</h1>";
  echo "<table border=1>";
  echo "<tr>
        <th><span style='font-size:.8em'>Executed Date</span></th>
        <th><span style='font-size:.8em'>Type</span></th>
        <th><span style='font-size:.8em'>Symbol</span></th>
        <th><span style='font-size:.8em'>Trade Strategy</span></th>
        <th><span style='font-size:.8em'>Order Type</span></th>
        <th><span style='font-size:.8em'>Qty</span></th>
        <th><span style='font-size:.8em'>Expiration Date</span></th>
        <th><span style='font-size:.8em'>Strike Price</span></th>
        <th><span style='font-size:.8em'>Order Type 2</span></th>
        <th><span style='font-size:.8em'>Strike price 2</span></th>
        <th><span style='font-size:.8em'>Executed Price</span></th>
        <th><span style='font-size:.8em'>Commission Fee</span></th>
        <th><span style='font-size:.8em'>Total</span></th>
        <th><span style='font-size:.8em'>Action</span></th>
        <th><span style='font-size:.8em'>Sell Target</span></th>
        </tr>";
  if (!$dbconnection->connect_errno) {
    $sql = "SELECT * FROM trades WHERE type='Entry' AND platform='".$platform."' ORDER BY executed_date;";
    $results = $dbconnection->query($sql);
    $plat_total = 0;
    $plat_com = 0;
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
      if ( $skip != True ){
        $plat_total = ($plat_total + $obj->total);
        $plat_com = ($plat_com + $obj->com_fee);
        if ($obj->trade_strategy == 'Put Spread'){
            # Put Spread 80% Profit target
            $tar_total = (($obj->total * .2)/100);
        } elseif (strpos($obj->symbol, '*') !== false) {
            # 50% Profit Target
            $tar_total = (($obj->total * 1.5)/100);
        } else {
            # 100% Profit Target
            $tar_total = (($obj->total * 2)/100);
        }
        $tar_total = abs($tar_total);
        echo "<tr>
              <td align='center'><span style='font-size:.8em'>$obj->executed_date</span></td>
              <td align='center'><span style='font-size:.8em'>$obj->type</span></td>
              <td align='center'><span style='font-size:.8em'>$obj->symbol</span></td>
              <td align='center'><span style='font-size:.8em'>$obj->trade_strategy</span></td>
              <td align='center'><span style='font-size:.8em'>$obj->order_type</span></td>
              <td align='center'><span style='font-size:.8em'>$new_qty</span></td>
              <td align='center'><span style='font-size:.8em'>$obj->expire_date</span></td>
              <td align='center'><span style='font-size:.8em'>".money_format('%(#10n', $obj->strike_price)."</span></td>
              <td align='center'><span style='font-size:.8em'>$obj->order_type2</span></td>
              <td align='center'><span style='font-size:.8em'>".money_format('%(#10n', $obj->strike_price2)."</span></td>
              <td align='center'><span style='font-size:.8em'>".money_format('%(#10n', $obj->executed_price)."</span></td>
              <td align='center'><span style='font-size:.8em'>".money_format('%(#10n', $obj->com_fee)."</span></td>
              <td align='center'><span style='font-size:.8em'>".money_format('%(#10n', $obj->total)."</span></td>
              <td align='center'><table><tr>
                  <td><form method='POST' action='add_exit_trade.php'>
                  <input type='hidden' name='ID' value='$obj->ID'>
                  <button type='submit'>Exit</button></form></td>
                  <td><form method='POST' action='edit_trade.php'>
                  <input type='hidden' name='ID' value='$obj->ID'>
                  <button type='submit'>Edit</button></form></td>
                  <td valign='bottom'><form method='POST' action='delete_trade.php'>
                  <input type='hidden' name='ID' value='$obj->ID'>
                  <button type='submit'>Del</button></form></td></tr></table></td>
              <td align='center'><span style='font-size:.8em'>".money_format('%(#10n', $tar_total)."</span></td>
              </tr>";
      }
    }
    $results->close();
    unset($obj);
  }
  echo "<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>";
  echo "<td align='center'><span style='font-size:.8em'>".money_format('%(#10n', $plat_com)."</span></td>";
  echo "<td align='center'><span style='font-size:.8em'>".money_format('%(#10n', $plat_total)."</span></td>";
  echo "<td></td></tr>";
  echo "</table>";
}

require_once("include/defaults.cfg.php");
foreach ($platforms as &$p) {
    viewByPlatform($p);
}

?>



