<html>
<?php
header( "refresh:15;url=." );
require_once("include/database.cfg.php");
require_once("include/defaults.cfg.php");
require_once("include/binance.php");

  setlocale(LC_MONETARY, 'en_US');
  $dbconnection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
  echo "<h1>All Profits</h1>";
  echo '<a href="add_profit.php" style="float: left;"><i class="fa fa-plus"></i></a>';
  echo '<a href="config.php" style="float: right;">&#9881;</a>';
  if (!$dbconnection->connect_errno) {
    $sql_1 = "SELECT SUM(amount) AS balance FROM ".$profits_table." WHERE platform != 'Deposit' AND platform != 'Withdrawal';";
    $results_1 = $dbconnection->query($sql_1);
    while($obj_1 = $results_1->fetch_object()){
      $total_balance = $obj_1->balance;
    }
    $sql_2 = "SELECT SUM(amount) AS balance FROM ".$profits_table." WHERE platform = 'Deposit';";
    $results_2 = $dbconnection->query($sql_2);
    while($obj_2 = $results_2->fetch_object()){
      $base_balance = $obj_2->balance;
    }
    $sql_4 = "SELECT SUM(amount) AS balance FROM ".$profits_table." WHERE platform = 'Withdrawal';";
    $results_4 = $dbconnection->query($sql_4);
    while($obj_4 = $results_4->fetch_object()){
      $used_balance = $obj_4->balance;
    }
    $sql_3 = "SELECT SUM(total) AS balance FROM ".$trades_table." WHERE type='Entry' AND mate_id IS NULL ORDER BY executed_date;";
    $results_3 = $dbconnection->query($sql_3);
    while($obj_3 = $results_3->fetch_object()){
      $trade_balance = $obj_3->balance;
    }
    $sql_5 = "SELECT COUNT(*) as count FROM ".$trades_table." WHERE trade_strategy='Crypto';";
    $results_5 = $dbconnection->query($sql_5);
    while($obj_5 = $results_5->fetch_object()){
      if  ( $obj_5->count == 0 ){
          $has_crypto = false;
      } else {
          $has_crypto = true;
      }
    }
    $available_amt = ($base_balance + $trade_balance + $total_balance + $used_balance);
    $taxes = ($total_balance * .25);
    $donate = ($total_balance * .10);
    $left_over = ($total_balance * .65);
    $prof_left = ($total_balance + $used_balance);
    $account_total = ($base_balance + $prof_left);
    if ( $has_crypto ){
      $format = '%(#10.11n';
      $btc_price = get_crypto("BTCUSDT")['mark'];
      echo "<center><table border=1><tr><th>Total Profits</th>";
      echo "<th>".money_format($format, $total_balance)." BTC</th></tr>";
      echo "<tr><td>Total Profits</td>";
      $usd1 = $total_balance * $btc_price;
      echo "<td> $".money_format('%(#10n', $usd1)."</td></tr>";
      echo "<tr><td>Base Amount</td>";
      echo "<td>".money_format($format, $base_balance)." BTC</td></tr>";
      echo "<tr><td>Base Amount</td>";
      $usd2 = $base_balance * $btc_price;
      echo "<td> $".money_format('%(#10n', $usd2)."</td></tr>";
      echo "<tr><td>Trade Amount</td>";
      echo "<td>".money_format($format, $trade_balance)." BTC</td></tr>";
      echo "<tr><td>Trade Amount</td>";
      $usd3 = $trade_balance * $btc_price;
      echo "<td> $".money_format('%(#10n', $usd3)."</td></tr>";
      echo "<tr><td>Available Amount</td>";
      echo "<td>".money_format($format, $available_amt)." BTC</td></tr>";
      echo "<tr><td>Available Amount</td>";
      $usd4 = $available_amt * $btc_price;
      echo "<td> $".money_format('%(#10n', $usd4)."</td></tr>";
      echo "<tr><td>Used Amount</td>";
      echo "<td>".money_format($format, $used_balance)." BTC</td></tr>";
      echo "<tr><td>Used Amount</td>";
      $usd5 = $used_balance * $btc_price;
      echo "<td> $".money_format('%(#10n', $usd5)."</td></tr>";
      echo "<tr><td>Profit Left</td>";
      echo "<td>".money_format($format, $prof_left)." BTC</td></tr>";
      echo "<tr><td>Profit Left</td>";
      $usd6 = $prof_left * $btc_price;
      echo "<td> $".money_format('%(#10n', $usd6)."</td></tr>";
      echo "<tr><td>Account Total</td>";
      echo "<td>".money_format($format, $account_total)." BTC</td></tr>";
      echo "<tr><td>Account Total</td>";
      $usd = $account_total * $btc_price;
      echo "<td> $".money_format('%(#10n', $usd)."</td></tr>";
      echo "<tr><td>BTC Price</td>";
      echo "<td> $".money_format('%(#10n', $btc_price)."</td></tr>";
      echo "</table><BR>";
    } else {
      $format = '%(#10n';
      echo "<center><table border=1><tr><th>Total Profits</th>";
      echo "<th>".money_format($format, $total_balance)."</th></tr>";
      echo "<tr><td>Base Amount</td>";
      echo "<td>".money_format($format, $base_balance)."</td></tr>";
      echo "<tr><td>Trade Amount</td>";
      echo "<td>".money_format($format, $trade_balance)."</td></tr>";
      echo "<tr><td>Available Amount</td>";
      echo "<td>".money_format($format, $available_amt)."</td></tr>";
      echo "<tr><td>Used Amount</td>";
      echo "<td>".money_format($format, $used_balance)."</td></tr>";
      echo "<tr><td>Profit Left</td>";
      echo "<td>".money_format($format, $prof_left)."</td></tr>";
      echo "<tr><td>Account Total</td>";
      echo "<td>".money_format($format, $account_total)."</td></tr>";
      echo "<tr><td><span style='font-size:.8em'>Amount for Taxes</span></td>";
      echo "<td><span style='font-size:.8em'>".money_format('%(#10n', $taxes)."</span></td></tr>";
      echo "<tr><td><span style='font-size:.8em'>Amount to Doante</span></td>";
      echo "<td><span style='font-size:.8em'>".money_format('%(#10n', $donate)."</span></td></tr>";
      echo "<tr><td><span style='font-size:.8em'>Amount Left Over</span></td>";
      echo "<td><span style='font-size:.8em'>".money_format('%(#10n', $left_over)."</span></td></tr>";
      echo "</table><BR>";
    }

    
    
    echo "<table border=1 width=80%>";
    echo "<tr>
          <th><span style='font-size:.8em'>Date</span></th>
          <th><span style='font-size:.8em'>Description</span></th>
          <th><span style='font-size:.8em'>Amount</span></th>
          <th><span style='font-size:.8em'>Balance</span></th>
          <th><span style='font-size:.8em'>Actions</span></th>
          </tr>";
    $sql = "SELECT * FROM ".$profits_table." WHERE platform != 'Deposit' AND platform != 'Withdrawal' ORDER BY date DESC;";
    $results = $dbconnection->query($sql);
    $count = 0;
    $last_amount = 0;
    $balance = 0;
    while($obj = $results->fetch_object()){
      if ( $count == 0 ) {
        $balance = $total_balance;
      } else {
        $balance = ($balance - $last_amount);
      }
      echo "<tr>
            <td align='center'><span style='font-size:.8em'>$obj->date</span></td>
            <td align='center'><span style='font-size:.8em'>$obj->description</span></td>
            <td align='center'><span style='font-size:.8em'>".money_format($format, $obj->amount)."</span></td>
            <td align='center'><span style='font-size:.8em'>".money_format($format, $balance)."</span></td>
            <td align='center'><table><tr>
                <td><form method='POST' action='edit_profit.php'>
                <input type='hidden' name='ID' value='$obj->ID'>
                <button type='submit'><i class='fa fa-edit'></i></button></form></td>
                <td valign='bottom'><form method='POST' action='delete_profit.php'>
                <input type='hidden' name='ID' value='$obj->ID'>
                <button type='submit'><i class='fa fa-trash'></i></button></form></td></tr></table></td>
            </tr>";
      $last_amount = $obj->amount;
      $count++;
    }
    $results->close();
    unset($obj);
    echo "<table border=1 width=80%>";
    echo "<tr>
          <th><span style='font-size:.8em'>Date</span></th>
          <th><span style='font-size:.8em'>Description</span></th>
          <th><span style='font-size:.8em'>Amount</span></th>
          <th><span style='font-size:.8em'>Balance</span></th>
          <th><span style='font-size:.8em'>Actions</span></th>
          </tr>";
    $sql = "SELECT * FROM ".$profits_table." WHERE platform = 'Deposit' ORDER BY date DESC;";
    $results = $dbconnection->query($sql);
    $count = 0;
    $last_amount = 0;
    $balance = 0;
    while($obj = $results->fetch_object()){
      if ( $count == 0 ) {
        $balance = $base_balance;
      } else {
        $balance = ($balance - $last_amount);
      }
      echo "<tr>
            <td align='center'><span style='font-size:.8em'>$obj->date</span></td>
            <td align='center'><span style='font-size:.8em'>$obj->description</span></td>
            <td align='center'><span style='font-size:.8em'>".money_format($format, $obj->amount)."</span></td>
            <td align='center'><span style='font-size:.8em'>".money_format($format, $balance)."</span></td>
            <td align='center'><table><tr>
                <td><form method='POST' action='edit_profit.php'>
                <input type='hidden' name='ID' value='$obj->ID'>
                <button type='submit'><i class='fa fa-edit'></i></button></form></td>
                <td valign='bottom'><form method='POST' action='delete_profit.php'>
                <input type='hidden' name='ID' value='$obj->ID'>
                <button type='submit'><i class='fa fa-trash'></i></button></form></td></tr></table></td>
            </tr>";
      $last_amount = $obj->amount;
      $count++;
    }
    $results->close();
    unset($obj);
    echo "<table border=1 width=80%>";
    echo "<tr>
          <th><span style='font-size:.8em'>Date</span></th>
          <th><span style='font-size:.8em'>Description</span></th>
          <th><span style='font-size:.8em'>Amount</span></th>
          <th><span style='font-size:.8em'>Balance</span></th>
          <th><span style='font-size:.8em'>Actions</span></th>
          </tr>";
    $sql = "SELECT * FROM ".$profits_table." WHERE platform = 'Withdrawal' ORDER BY date DESC;";
    $results = $dbconnection->query($sql);
    $count = 0;
    $last_amount = 0;
    $balance = 0;
    while($obj = $results->fetch_object()){
      if ( $count == 0 ) {
        $balance = $used_balance;
      } else {
        $balance = ($balance - $last_amount);
      }
      echo "<tr>
            <td align='center'><span style='font-size:.8em'>$obj->date</span></td>
            <td align='center'><span style='font-size:.8em'>$obj->description</span></td>
            <td align='center'><span style='font-size:.8em'>".money_format($format, $obj->amount)."</span></td>
            <td align='center'><span style='font-size:.8em'>".money_format($format, $balance)."</span></td>
            <td align='center'><table><tr>
                <td><form method='POST' action='edit_profit.php'>
                <input type='hidden' name='ID' value='$obj->ID'>
                <button type='submit'><i class='fa fa-edit'></i></button></form></td>
                <td valign='bottom'><form method='POST' action='delete_profit.php'>
                <input type='hidden' name='ID' value='$obj->ID'>
                <button type='submit'><i class='fa fa-trash'></i></button></form></td></tr></table></td>
            </tr>";
      $last_amount = $obj->amount;
      $count++;
    }
    $results->close();
    unset($obj);
  }
  echo "</table></center>";
?>



