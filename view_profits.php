<html>
<?php
header( "refresh:10;url=".$_SERVER['PHP_SELF'] );
require_once("include/database.cfg.php");
require_once("include/defaults.cfg.php");

function viewByPlatform($platform, $trades_table, $profits_table){
  $has_crypto = false;
  setlocale(LC_MONETARY, 'en_US');
  $dbconnection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
  #echo "<h1> ".$platform." Profits</h1>";
  if (!$dbconnection->connect_errno) {
    $sql_1 = "SELECT SUM(amount) AS balance FROM ".$profits_table." WHERE platform='".$platform."'";
    $results_1 = $dbconnection->query($sql_1);
    while($obj_1 = $results_1->fetch_object()){
      $total_balance = $obj_1->balance;
    }
    $sql_3 = "select sum(case when amount >=0 then 1 else 0 end) as pos_amt, sum(case when amount < 0 then 1 else 0 end) as neg_amt, count(*) as tot_cnt from ".$profits_table." WHERE platform='".$platform."'";
    $results_3 = $dbconnection->query($sql_3);
    while($obj_3 = $results_3->fetch_object()){
      $total_count = ($obj_3->tot_cnt);
      $win = ($obj_3->pos_amt);
      $loss = ($obj_3->neg_amt);
      $win_rate = ($win / $total_count);
    }
    $sql_2 = "SELECT * FROM ".$trades_table." WHERE type='Entry' AND platform='".$platform."' AND mate_id IS NOT NULL ORDER BY executed_date;";
    $results_2 = $dbconnection->query($sql_2);
    $total_used = 0;
    while($obj_2 = $results_2->fetch_object()){
      if ($obj_2->trade_strategy == 'Crypto') {
          $has_crypto = true;
      }
      $total_used = $total_used + abs($obj_2->total);
    }
    $percent = ($total_balance/$total_used);
    $platform_percent = sprintf("%.2f%%", $percent * 100);
    $win_percent = sprintf("%.2f%%", $win_rate * 100);
    if ($has_crypto){
      $format = '%(#10.11n';
    } else {
      $format = '%(#10n';
    }
    if ($total_balance == 0){
        echo "";
    } else {
        echo "<tr>";
        echo "<td align='center'>".$platform."</td>";
        echo "<td align='center'>".money_format($format, $total_balance)."</td>";
        echo "<td align='center'>$platform_percent</td>";
        echo "<td align='center'>".$win."</td>";
        echo "<td align='center'>".$loss."</td>";
        echo "<td align='center'>".$total_count."</td>";
        echo "<td align='center'>$win_percent</td>";
        echo "</tr>";
    }
    #echo "<table border=1 width=80%>";
    #echo "<tr>
    #      <th><span style='font-size:.8em'>Date</span></th>
    #      <th><span style='font-size:.8em'>Description</span></th>
    #      <th><span style='font-size:.8em'>Amount</span></th>
    #      <th><span style='font-size:.8em'>Balance</span></th>
    #      <th><span style='font-size:.8em'>Actions</span></th>
    #      </tr>";
    #$sql = "SELECT * FROM ".$profits_table." WHERE platform='".$platform."' ORDER BY date DESC;";
    #$results = $dbconnection->query($sql);
    #$count = 0;
    #$last_amount = 0;
    #$balance = 0;
    #while($obj = $results->fetch_object()){
    #  if ( $count == 0 ) {
    #    $balance = $total_balance;
    #  } else {
    #    $balance = ($balance - $last_amount);
    #  }
    #  echo "<tr>
    #        <td align='center'><span style='font-size:.8em'>$obj->date</span></td>
    #        <td align='center'><span style='font-size:.8em'>$obj->description</span></td>
    #        <td align='center'><span style='font-size:.8em'>".money_format($format, $obj->amount)."</span></td>
    #        <td align='center'><span style='font-size:.8em'>".money_format($format, $balance)."</span></td>
    #        <td align='center'><table><tr>
    #            <td><form method='POST' action='edit_profit.php'>
    #            <input type='hidden' name='ID' value='$obj->ID'>
    #            <button type='submit'>Edit</button></form></td>
    #            <td valign='bottom'><form method='POST' action='delete_profit.php'>
    #            <input type='hidden' name='ID' value='$obj->ID'>
    #            <button type='submit'>Del</button></form></td></tr></table></td>
    #        </tr>";
    #  $last_amount = $obj->amount;
    #  $count++;
    #}
    #$results->close();
    #unset($obj);
  }
  #echo "</table></center>";
}

require_once("include/defaults.cfg.php");
echo "<center><table border=1>";
echo "<tr>";
        echo "<th align='center'>Platform</th>";
        echo "<th align='center'>Total Profits</th>";
        echo "<th align='center'>Profit Percent</th>";
        echo "<th align='center'>Wins</th>";
        echo "<th align='center'>Losses</th>";
        echo "<th align='center'>Total Trades</th>";
        echo "<th align='center'>Win Rate</th>";
        echo "</tr>";
foreach ($platforms as &$p) {
    viewByPlatform($p, $trades_table, $profits_table);
}
echo "</table></center><BR>";

?>



