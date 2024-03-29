<html>
<!--<head>
<meta http-equiv="refresh" content="5;URL=.">
</head>-->
<?php
require_once("include/database.cfg.php");
require_once("include/defaults.cfg.php");
setlocale(LC_MONETARY, 'en_US');
$dbconnection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

$cur_month = date("n");
$cur_year = date("Y");

echo "<h1> ".$cur_year." All Monthly Profits</h1>";
echo '<a href="config.php" style="float: right;">&#9881;</a>';

echo "<table border=1><tr>
      <th>Month</th>
      <th>Base Amount</th>
      <th>Profits</th>
      <th>Used</th>
      <th>Profit %</th>
      <th>Target Profit</th></tr>";

$new_base_amt = 0;
$total_profits = 0;
$total_used = 0;
$togo_amt = 0;
$profit_tables = array();
if (!$dbconnection->connect_errno) {
    $sql = "SELECT table_name FROM information_schema.tables WHERE table_name LIKE 'profits%';";
    $results = $dbconnection->query($sql);
    while($obj = $results->fetch_object()){
      array_push($profit_tables, $obj->table_name);
    }
}
for ($i = 1; $i <= $cur_month; $i++) {
  if (!$dbconnection->connect_errno) {
    $used_amt = 0;
    $full_amt = 0;
    $base_amt = 0;
    foreach ($profit_tables as $profit_table){
      $sql_1 = "SELECT SUM(amount) AS base_amt FROM ".$profit_table." where platform='Deposit' AND MONTH(date) = ".$i." AND YEAR(date) = ".$cur_year.";";
      $sql_2 = "SELECT SUM(amount) AS used_amt FROM ".$profit_table." where platform='Withdrawal' AND MONTH(date) = ".$i." AND YEAR(date) = ".$cur_year.";";
      $sql_3 = "SELECT SUM(amount) AS full_amt FROM ".$profit_table." where MONTH(date) = ".$i." AND YEAR(date) = ".$cur_year.";";

      $results_1 = $dbconnection->query($sql_1);
      while($obj_1 = $results_1->fetch_object()){
        $base_amt = ($obj_1->base_amt + $base_amt);
      }

      $results_2 = $dbconnection->query($sql_2);
      while($obj_2 = $results_2->fetch_object()){
        $used_amt = ($obj_2->used_amt + $used_amt);
      }

      $results_3 = $dbconnection->query($sql_3);
      while($obj_3 = $results_3->fetch_object()){
        $full_amt = ($obj_3->full_amt + $full_amt);
      }
    }
      $new_base_amt = ($new_base_amt + $base_amt);
      $monthNum  = $i;
      $dateObj   = DateTime::createFromFormat('!m', $monthNum);
      $monthName = $dateObj->format('F');
  
      $profit_amt = ($full_amt - $used_amt - $base_amt);
      $percent = (($profit_amt/$new_base_amt));
      $percent = sprintf("%.2f%%", $percent * 100);
      $tar_percent = $monthly_profit_percent_target;
      $tar_profit_amt = ($new_base_amt * $tar_percent);
      $tar_percent = sprintf("%.2f%%", $tar_percent * 100);
      echo "<tr>
            <td align='center'><span style='font-size:.8em'>".$monthName."</span></td>
            <td align='center'><span style='font-size:.8em'>".money_format('%(#10n', $new_base_amt)."</span></td>
            <td align='center'><span style='font-size:.8em'>".money_format('%(#10n', $profit_amt)."</span></td>
            <td align='center'><span style='font-size:.8em'>".money_format('%(#10n', $used_amt)."</span></td>
            <td align='center'><span style='font-size:.8em'>".$percent."</span></td>
            <td align='center'><span style='font-size:.8em'>".money_format('%(#10n', $tar_profit_amt)."</span></td>
            </tr>";
      $togo_amt = ($profit_amt-$tar_profit_amt);
      $new_base_amt = ($new_base_amt+($profit_amt+$used_amt));
      $total_profits = ($total_profits + $profit_amt);
      $total_used = ($total_used + $used_amt);
      $results_1->close();
      $results_2->close();
      $results_3->close();
      unset($obj_1);
      unset($obj_2);
      unset($obj_3);
    
  }
}

echo "<tr>
          <td align='center'><span style='font-size:.8em'>TOTALS</span></td>
          <td align='center'><span style='font-size:.8em'> --- </span></td>
          <td align='center'><span style='font-size:.8em'>".money_format('%(#10n', $total_profits)."</span></td>
          <td align='center'><span style='font-size:.8em'>".money_format('%(#10n', $total_used)."</span></td>
          <td align='center'><span style='font-size:.8em'> --- </span></td>
          <td align='center'><span style='font-size:.8em'>".money_format('%(#10n', $togo_amt)."</span></td>

          </tr>";
echo "</table>";

echo "<h2> Projected Profits </h2>";


echo "<table border=1><tr>
      <th>Month</th>
      <th>Base Amount</th>
      <th>Profits</th>
      <th>Profit %</th></tr>";
      
for ($i = ($cur_month+1); $i <= 12; $i++) {
    $monthNum  = $i;
    $dateObj   = DateTime::createFromFormat('!m', $monthNum);
    $monthName = $dateObj->format('F');

    $proj_percent = $monthly_profit_percent_target;
    $profit_amt = ($new_base_amt * $proj_percent);
    $proj_percent = sprintf("%.2f%%", $proj_percent * 100);

    echo "<tr>
          <td align='center'><span style='font-size:.8em'>".$monthName."</span></td>
          <td align='center'><span style='font-size:.8em'>".money_format('%(#10n', $new_base_amt)."</span></td>
          <td align='center'><span style='font-size:.8em'>".money_format('%(#10n', $profit_amt)."</span></td>
          <td align='center'><span style='font-size:.8em'>".$proj_percent."</span></td>
          </tr>";
    $new_base_amt = ($new_base_amt+($profit_amt*$monthly_profit_percent_to_keep));
    $total_profits = ($total_profits + $profit_amt);
}
echo "<tr>
          <td align='center'><span style='font-size:.8em'>TOTALS</span></td>
          <td align='center'><span style='font-size:.8em'> --- </span></td>
          <td align='center'><span style='font-size:.8em'>".money_format('%(#10n', $total_profits)."</span></td>
          <td align='center'><span style='font-size:.8em'> --- </span></td>
          </tr>";
echo "</table>";


?>

