<?php

$page = $_SERVER['PHP_SELF'];
$sec = "5";
?>
<html>
<head>
<meta http-equiv="refresh" content="<?php echo $sec?>;URL='<?php echo $page?>'">
</head>
<?php
require_once("include/database.cfg.php");
require_once("include/defaults.cfg.php");

  setlocale(LC_MONETARY, 'en_US');
  $dbconnection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
  echo "<h1>Profits</h1>";
  if (!$dbconnection->connect_errno) {
    $sql_1 = "SELECT SUM(amount) AS balance FROM profits WHERE platform != 'Deposit';";
    $results_1 = $dbconnection->query($sql_1);
    while($obj_1 = $results_1->fetch_object()){
      $total_balance = $obj_1->balance;
    }
    $sql_2 = "SELECT SUM(amount) AS balance FROM profits WHERE platform = 'Deposit';";
    $results_2 = $dbconnection->query($sql_2);
    while($obj_2 = $results_2->fetch_object()){
      $base_balance = $obj_2->balance;
    }
    $sql_3 = "SELECT SUM(total) AS balance FROM trades WHERE type='Entry' AND mate_id IS NULL ORDER BY executed_date;";
    $results_3 = $dbconnection->query($sql_3);
    while($obj_3 = $results_3->fetch_object()){
      $trade_balance = $obj_3->balance;
    }
    $available_amt = ($base_balance - $trade_balance);
    $taxes = ($total_balance * .25);
    $donate = ($total_balance * .10);
    $left_over = ($total_balance * .65);
    echo "<table border=1><tr><th>Total Profits</th>";
    echo "<th>".money_format('%(#10n', $total_balance)."</th></tr>";
    echo "<table border=1><tr><td>Base Amount</td>";
    echo "<td>".money_format('%(#10n', $base_balance)."</td></tr>";
    echo "<table border=1><tr><td>Currrent Trade Amount</td>";
    echo "<td>".money_format('%(#10n', $trade_balance)."</td></tr>";
    echo "<table border=1><tr><td>Available Amount</td>";
    echo "<td>".money_format('%(#10n', $available_amt)."</td></tr>";
    echo "<tr><td><span style='font-size:.8em'>Amount for Taxes</span></td>";
    echo "<td><span style='font-size:.8em'>".money_format('%(#10n', $taxes)."</span></td></tr>";
    echo "<tr><td><span style='font-size:.8em'>Amount to Doante</span></td>";
    echo "<td><span style='font-size:.8em'>".money_format('%(#10n', $donate)."</span></td></tr>";
    echo "<tr><td><span style='font-size:.8em'>Amount Left Over</span></td>";
    echo "<td><span style='font-size:.8em'>".money_format('%(#10n', $left_over)."</span></td></tr>";
    echo "</table><BR>";
    echo "<table border=1>";
    echo "<tr>
          <th><span style='font-size:.8em'>Date</span></th>
          <th><span style='font-size:.8em'>Description</span></th>
          <th><span style='font-size:.8em'>Amount</span></th>
          <th><span style='font-size:.8em'>Balance</span></th>
          <th><span style='font-size:.8em'>Actions</span></th>
          </tr>";
    $sql = "SELECT * FROM profits WHERE platform != 'Deposit' ORDER BY date DESC;";
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
            <td align='center'><span style='font-size:.8em'>".money_format('%(#10n', $obj->amount)."</span></td>
            <td align='center'><span style='font-size:.8em'>".money_format('%(#10n', $balance)."</span></td>
            <td align='center'><table><tr>
                <td><form method='POST' action='edit_profit.php'>
                <input type='hidden' name='ID' value='$obj->ID'>
                <button type='submit'>Edit</button></form></td>
                <td valign='bottom'><form method='POST' action='delete_profit.php'>
                <input type='hidden' name='ID' value='$obj->ID'>
                <button type='submit'>Del</button></form></td></tr></table></td>
            </tr>";
      $last_amount = $obj->amount;
      $count++;
    }
    $results->close();
    unset($obj);
  }
  echo "</table>";
?>



