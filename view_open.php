<?php
require_once("include/html_open.php");
require_once("include/database.cfg.php");
require_once("include/defaults.cfg.php");

function viewByPlatform($platform, $trades_table){
  $has_crypto = false;
  setlocale(LC_MONETARY, 'en_US');
  $dbconnection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
  $count = 0;
  if (!$dbconnection->connect_errno) {
    $sql = "SELECT * FROM ".$trades_table." WHERE type='Entry' AND platform='".$platform."' ORDER BY executed_date;";
    $results = $dbconnection->query($sql);
    $plat_total = 0;
    $plat_com = 0;
    $sql1 = "SELECT COUNT(*) as count FROM ".$trades_table." WHERE type='Entry' AND platform='".$platform."' AND mate_id IS NULL;";
    $results1 = $dbconnection->query($sql1);
    while($obj1 = $results1->fetch_object()){
      if ($obj1->count != 0){
        echo "<tr><td colspan=16><span style='font-size:1em'><center><strong>".$platform."</strong></center></span></td></tr>";
      }
    }
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
        $count = $count + 1;
        $plat_total = ($plat_total + $obj->total);
        $plat_com = ($plat_com + $obj->com_fee);
        if (strpos($obj->symbol, '*') !== false) {
          # Force 50% Profit Target
          $sell = ((((($obj->executed_price * 100)) + (abs($obj->com_fee) / $obj->qty)) * 1.5) / 100);
        } elseif (strpos($obj->symbol, '`') !== false) {
          # Force 75% Profit Target
          $sell = ((((($obj->executed_price * 100)) + (abs($obj->com_fee) / $obj->qty)) * 1.75) / 100);
        } elseif (strpos($obj->symbol, '~') !== false) {
          # Force 100% Profit Target
          $sell = ((((($obj->executed_price * 100)) + (abs($obj->com_fee) / $obj->qty)) * 2) / 100);
        } elseif ($obj->trade_strategy == 'Put Spread'){
          # Put Spread 80% Profit target
          $sell = ((((($obj->executed_price * 100)) + (abs($obj->com_fee) / $obj->qty)) * .2) / 100);
        } else {
          # 100% Profit Target
          $sell = ((((($obj->executed_price * 100)) + (abs($obj->com_fee) / $obj->qty)) * 2) / 100);
        }
        if ($obj->trade_strategy == 'Crypto') {
            $format_line = '%(#10.11n';
            $format_num = 11;
            $has_crypto = true;
        } else {
            $format_line = '%(#10n';
            $format_num = 0;
        }
        $tar_total = abs($sell);
        date_default_timezone_set("America/New_York");
        $today = date("Y-m-d");
        $new_expire = strtotime($obj->expire_date.' -7 days');
        $test_date = date("Y-m-d", $new_expire);
        if ($today >= $test_date) {
            echo "<tr bgcolor='Orange'>";
        } else {
            echo '<tr>';
        }
        echo "<td align='center'><span style='font-size:.8em'>$obj->executed_date</span></td>
              <td align='center'><span style='font-size:.8em'>$obj->symbol</span></td>
              <td align='center'><span style='font-size:.8em'>$obj->trade_strategy</span></td>
              <td align='center'><span style='font-size:.8em'>$obj->order_type</span></td>
              <td align='center'><span style='font-size:.8em'>".number_format($new_qty,$format_num)."</span></td>
              <td align='center'><span style='font-size:.8em'>$obj->expire_date</span></td>
              <td align='center'><span style='font-size:.8em'>".money_format($format_line, $obj->strike_price)."</span></td>
              <td align='center'><span style='font-size:.8em'>$obj->order_type2</span></td>
              <td align='center'><span style='font-size:.8em'>".money_format($format_line, $obj->strike_price2)."</span></td>
              <td align='center'><span style='font-size:.8em'>".money_format($format_line, $obj->executed_price)."</span></td>
              <td align='center'><span style='font-size:.8em'>".money_format($format_line, $obj->com_fee)."</span></td>
              <td align='center'><span style='font-size:.8em'>".money_format($format_line, $obj->total)."</span></td>
              <td align='center'><center><form method='POST' action='add_exit_trade.php'>
                  <input type='hidden' name='ID' value='$obj->ID'>
                  <button type='submit'><i class='fa fa-sign-out'></i></button></form></center></td>
              <td align='center'><center><form method='POST' action='edit_trade.php'>
                  <input type='hidden' name='ID' value='$obj->ID'>
                  <button type='submit'><i class='fa fa-edit'></i></button></form></center></td>
              <td align='center'><center><form method='POST' action='delete_trade.php'>
                  <input type='hidden' name='ID' value='$obj->ID'>
                  <button type='submit'><i class='fa fa-trash'></i></button></form></center></td>
              <td align='center'><span style='font-size:.8em'>".money_format($format_line, $tar_total)."</span></td>
              </tr>";
      }
    }
    $results->close();
    unset($obj);
  }
  if ($has_crypto){
    $format = '%(#10.11n';
  } else {
    $format = '%(#10n';
  }
  if ($count != 0){
    echo "<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>";
    echo "<td align='center'><span style='font-size:.8em'>".money_format($format, $plat_com)."</span></td>";
    echo "<td align='center'><span style='font-size:.8em'>".money_format($format, $plat_total)."</span></td>";
    echo "<td></td><td></td><td></td><td></td></tr>";
  }
}

require_once("include/defaults.cfg.php");
echo "<h1> Open Trades</h1>";
echo '<a href="config.php" style="float: right;">&#9881;</a>';
echo '<table border=0 align=center><tr><td><input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search for symbols.." title="Type in a symbol"></td>';
echo '</tr></table>';
echo "<table id='myTable' border=1>";
echo "<tr id='header'>
        <th><span style='font-size:.8em'>Executed Date</span></th>
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
        <th colspan=3><span style='font-size:.8em'>Action</span></th>
        <th><span style='font-size:.8em'>Sell Target</span></th>
        </tr>";
foreach ($platforms as &$p) {
    viewByPlatform($p, $trades_table);
}
echo "</table>";
require_once("include/html_close.php");

?>
<script>
function myFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("myTable");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}
</script>


