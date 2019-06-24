<?php

require_once("include/database.cfg.php");
require_once("include/defaults.cfg.php");

function viewByPlatform($platform, $trades_table){
  if ($_POST['time_frame'] == 'ytd'){
    $year = date("Y");
    $sql_add = " AND executed_date BETWEEN '".$year."-01-01' AND '".$year."-12-31'";
  } elseif ($_POST['time_frame'] == 'last_month') {
    $previous_month = strtotime("-1 month +1 day");
    $start_date = date("Y-m-d",$previous_month);
    $end_date = date("Y-m-d");
    $sql_add = " AND executed_date BETWEEN '".$start_date."' AND '".$end_date."'";
  } elseif ($_POST['time_frame'] == 'last_week') {
    $previous_week = strtotime("-1 week +1 day");
    $start_date = date("Y-m-d",$previous_week);
    $end_date = date("Y-m-d");
    $sql_add = " AND executed_date BETWEEN '".$start_date."' AND '".$end_date."'";
  } else {
    $sql_add = '';
  }
  setlocale(LC_MONETARY, 'en_US');
  $dbconnection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
  echo "<h1> ".$platform." Closed Trades</h1>";
  if (!$dbconnection->connect_errno) {
    $sql = "SELECT * FROM ".$trades_table." WHERE type='Exit' AND mate_id IS NOT NULL AND platform='".$platform."'".$sql_add.";";
    $results = $dbconnection->query($sql);
    while($obj = $results->fetch_object()){
      $trade_total = $obj->total;
      $trade_com = $obj->com_fee;
      if ($obj->trade_strategy == 'Crypto') {
            $format_line = '%(#10.11n';
            $format_num = 11;
        } else {
            $format_line = '%(#10n';
            $format_num = 0;
        }

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
        $sql_2 = "SELECT * FROM ".$trades_table." WHERE type='Entry' AND mate_id='$obj->ID';";
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
                <td align='center'><span style='font-size:.8em'>".number_format($obj2->qty,$format_num)."</span></td>
                <td align='center'><span style='font-size:.8em'>$obj2->expire_date</span></td>
                <td align='center'><span style='font-size:.8em'>".money_format($format_line, $obj2->strike_price)."</span></td>
                <td align='center'><span style='font-size:.8em'>".money_format($format_line, $obj2->executed_price)."</span></td>
                <td align='center'><span style='font-size:.8em'>$obj2->order_type2</span></td>
                <td align='center'><span style='font-size:.8em'>".money_format($format_line, $obj2->strike_price2)."</span></td>
                <td align='center'><span style='font-size:.8em'>".money_format($format_line, $obj2->com_fee)."</span></td>
                <td align='center'><span style='font-size:.8em'>".money_format($format_line, $obj2->total)."</span></td>
                <td align='center'><table><tr>
                  <td><form method='POST' action='edit_trade.php'>
                  <input type='hidden' name='ID' value='$obj2->ID'>
                  <button type='submit'>Edit</button></form></td>
                  <td valign='bottom'><form method='POST' action='delete_trade.php'>
                  <input type='hidden' name='ID' value='$obj2->ID'>
                  <button type='submit'>Del</button></form></td></tr></table></td>
                </tr>";
        }
        echo "<tr>
            <td align='center'><span style='font-size:.8em'>$obj->executed_date</span></td>
            <td align='center'><span style='font-size:.8em'>$obj->type</span></td>
            <td align='center'><span style='font-size:.8em'>$obj->symbol</span></td>
            <td align='center'><span style='font-size:.8em'>$obj->trade_strategy</span></td>
            <td align='center'><span style='font-size:.8em'>$obj->order_type</span></td>
            <td align='center'><span style='font-size:.8em'>$obj->qty</span></td>
            <td align='center'><span style='font-size:.8em'>$obj->expire_date</span></td>
            <td align='center'><span style='font-size:.8em'>".money_format($format_line, $obj->strike_price)."</span></td>
            <td align='center'><span style='font-size:.8em'>".money_format($format_line, $obj->executed_price)."</span></td>
            <td align='center'><span style='font-size:.8em'>$obj->order_type2</span></td>
            <td align='center'><span style='font-size:.8em'>".money_format($format_line, $obj->strike_price2)."</span></td>
            <td align='center'><span style='font-size:.8em'>".money_format($format_line, $obj->com_fee)."</span></td>
            <td align='center'><span style='font-size:.8em'>".money_format($format_line, $obj->total)."</span></td>
            <td align='center'><table><tr>
                <td><form method='POST' action='edit_trade.php'>
                <input type='hidden' name='ID' value='$obj->ID'>
                <button type='submit'>Edit</button></form></td>
                <td valign='bottom'><form method='POST' action='delete_trade.php'>
                <input type='hidden' name='ID' value='$obj->ID'>
                <button type='submit'>Del</button></form></td></tr></table></td>
            </tr>";
       #$per = ($trade_total/$obj->total);
       echo  abs($obj2->total);
       $percent = number_format( ( abs($trade_total) / abs($obj2->total) ) * 100, 2).'%';
       echo "<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td align='center'><span style='font-size:.8em'>".money_format($format_line, $trade_com)."</span></td><td align='center'><span style='font-size:.8em'>".money_format($format_line, $trade_total)."</span></td><td><span style='font-size:.8em'>".$percent."</span></td></tr>";
       echo "</table>";
     }
     $results->close();
     unset($obj);
  }
}

#require_once("include/defaults.cfg.php");
#foreach ($platforms as &$p) {
#    viewByPlatform($p);
#}
viewByPlatform($_POST['platform'], $trades_table);

?>



