<?php

require_once("include/database.cfg.php");

function viewByPlatform($platform){
  setlocale(LC_MONETARY, 'en_US');
  $dbconnection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
  echo "<h1> ".$platform." Closed Trades</h1>";
  if (!$dbconnection->connect_errno) {
    $sql = "SELECT * FROM trades WHERE type='Entry' AND mate_id IS NOT NULL AND platform='".$platform."';";
    $results = $dbconnection->query($sql);
    while($obj = $results->fetch_object()){
      $trade_total = $obj->total;
      $trade_com = $obj->com_fee;
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
            <th><span style='font-size:.8em'>Executed Price</span></th>
            <th><span style='font-size:.8em'>Order Type 2</span></th>
            <th><span style='font-size:.8em'>Strike price 2</span></th>
            <th><span style='font-size:.8em'>Commission Fee</span></th>
            <th><span style='font-size:.8em'>Total</span></th>
            <th><span style='font-size:.8em'>Action</span></th>
        </tr>";
      echo "<tr>
            <td align='center'><span style='font-size:.8em'>$obj->executed_date</span></td>
            <td align='center'><span style='font-size:.8em'>$obj->type</span></td>
            <td align='center'><span style='font-size:.8em'>$obj->symbol</span></td>
            <td align='center'><span style='font-size:.8em'>$obj->trade_strategy</span></td>
            <td align='center'><span style='font-size:.8em'>$obj->order_type</span></td>
            <td align='center'><span style='font-size:.8em'>$obj->qty</span></td>
            <td align='center'><span style='font-size:.8em'>$obj->expire_date</span></td>
            <td align='center'><span style='font-size:.8em'>".money_format('%(#10n', $obj->strike_price)."</span></td>
            <td align='center'><span style='font-size:.8em'>".money_format('%(#10n', $obj->executed_price)."</span></td>
            <td align='center'><span style='font-size:.8em'>$obj->order_type2</span></td>
            <td align='center'><span style='font-size:.8em'>".money_format('%(#10n', $obj->strike_price2)."</span></td>
            <td align='center'><span style='font-size:.8em'>".money_format('%(#10n', $obj->com_fee)."</span></td>
            <td align='center'><span style='font-size:.8em'>".money_format('%(#10n', $obj->total)."</span></td>
            <td align='center'><table><tr>
                <td><form method='POST' action='edit_trade.php'>
                <input type='hidden' name='ID' value='$obj->ID'>
                <button type='submit'>Edit</button></form></td>
                <td valign='bottom'><form method='POST' action='delete_trade.php'>
                <input type='hidden' name='ID' value='$obj->ID'>
                <button type='submit'>Del</button></form></td></tr></table></td>
            </tr>";
       $sql_2 = "SELECT * FROM trades WHERE type='Exit' AND mate_id='$obj->ID';";
       $results_2 = $dbconnection->query($sql_2);
       while($obj2 = $results_2->fetch_object()){
          $trade_total = $trade_total + $obj2->total;
          $trade_com = $trade_com + $obj2->com_fee;
          echo "<tr>
                <td align='center'><span style='font-size:.8em'>$obj2->executed_date</span></td>
                <td align='center'><span style='font-size:.8em'>$obj2->type</span></td>
                <td align='center'><span style='font-size:.8em'>$obj2->symbol</span></td>
                <td align='center'><span style='font-size:.8em'>$obj2->trade_strategy</span></td>
                <td align='center'><span style='font-size:.8em'>$obj2->order_type</span></td>
                <td align='center'><span style='font-size:.8em'>$obj2->qty</span></td>
                <td align='center'><span style='font-size:.8em'>$obj2->expire_date</span></td>
                <td align='center'><span style='font-size:.8em'>".money_format('%(#10n', $obj2->strike_price)."</span></td>
                <td align='center'><span style='font-size:.8em'>".money_format('%(#10n', $obj2->executed_price)."</span></td>
                <td align='center'><span style='font-size:.8em'>$obj2->order_type2</span></td>
                <td align='center'><span style='font-size:.8em'>".money_format('%(#10n', $obj2->strike_price2)."</span></td>
                <td align='center'><span style='font-size:.8em'>".money_format('%(#10n', $obj2->com_fee)."</span></td>
                <td align='center'><span style='font-size:.8em'>".money_format('%(#10n', $obj2->total)."</span></td>
                <td align='center'><table><tr>
                  <td><form method='POST' action='edit_trade.php'>
                  <input type='hidden' name='ID' value='$obj2->ID'>
                  <button type='submit'>Edit</button></form></td>
                  <td valign='bottom'><form method='POST' action='delete_trade.php'>
                  <input type='hidden' name='ID' value='$obj2->ID'>
                  <button type='submit'>Del</button></form></td></tr></table></td>
                </tr>";
       }
       #$per = ($trade_total/$obj->total);
       $percent = number_format( (abs($trade_total)/abs($obj->total)) * 100, 2).'%';
       echo "<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td align='center'><span style='font-size:.8em'>".money_format('%(#10n', $trade_com)."</span></td><td align='center'><span style='font-size:.8em'>".money_format('%(#10n', $trade_total)."</span></td><td><span style='font-size:.8em'>".$percent."</span></td></tr>";
       echo "</table>";
     }
     $results->close();
     unset($obj);
  }
}

viewByPlatform('Alpha-9');

viewByPlatform('Money Calendar Pro');

?>



