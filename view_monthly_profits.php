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
setlocale(LC_MONETARY, 'en_US');
$dbconnection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

echo "<h1> Monthly Profits</h1>";

$cur_month = date("n");
$cur_year = date("Y");

echo "<table border=1><tr>
      <th>Month</th>
      <th>Base Amount</th>
      <th>Profits</th>
      <th>Used</th>
      <th>Profit %</th></tr>";

$new_base_amt = 0;

for ($i = 1; $i <= $cur_month; $i++) {
  if (!$dbconnection->connect_errno) {
    $sql_1 = "SELECT SUM(amount) AS base_amt FROM profits where platform='Deposit' AND MONTH(date) = ".$i." AND YEAR(date) = ".$cur_year.";";
    $sql_2 = "SELECT SUM(amount) AS used_amt FROM profits where platform='Withdrawal' AND MONTH(date) = ".$i." AND YEAR(date) = ".$cur_year.";";
    $sql_3 = "SELECT SUM(amount) AS full_amt FROM profits where MONTH(date) = ".$i." AND YEAR(date) = ".$cur_year.";";

    $results_1 = $dbconnection->query($sql_1);
    while($obj_1 = $results_1->fetch_object()){
      $base_amt = $obj_1->base_amt;
    }
    $new_base_amt = ($new_base_amt + $base_amt);

    $results_2 = $dbconnection->query($sql_2);
    while($obj_2 = $results_2->fetch_object()){
      $used_amt = $obj_2->used_amt;
    }

    $results_3 = $dbconnection->query($sql_3);
    while($obj_3 = $results_3->fetch_object()){
      $full_amt = $obj_3->full_amt;
    }

    $monthNum  = $i;
    $dateObj   = DateTime::createFromFormat('!m', $monthNum);
    $monthName = $dateObj->format('F');

    $profit_amt = ($full_amt - $used_amt - $base_amt);
    $percent = (($profit_amt/$new_base_amt));
    $percent = sprintf("%.2f%%", $percent * 100);
    echo "<tr>
          <td align='center'><span style='font-size:.8em'>".$monthName."</span></td>
          <td align='center'><span style='font-size:.8em'>".money_format('%(#10n', $new_base_amt)."</span></td>
          <td align='center'><span style='font-size:.8em'>".money_format('%(#10n', $profit_amt)."</span></td>
          <td align='center'><span style='font-size:.8em'>".money_format('%(#10n', $used_amt)."</span></td>
          <td align='center'><span style='font-size:.8em'>".$percent."</span></td>
          </tr>";
    $new_base_amt = ($new_base_amt+($profit_amt+$used_amt));
    $results_1->close();
    $results_2->close();
    $results_3->close();
    unset($obj_1);
    unset($obj_2);
    unset($obj_3);
  }
}
echo "</table>";

?>

