<?php

require_once("include/database.cfg.php");

$dbconnection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$id = $_POST['ID'];

if (!$dbconnection->connect_errno) {
  $sql = "SELECT * FROM trades WHERE ID='$id';";
  #echo $sql;
  $results = $dbconnection->query($sql);
  while($obj = $results->fetch_object()){
    $executed_date = $obj->executed_date;
    $type = 'Exit';
    $symbol = $obj->symbol;
    $trade_strategy = $obj->trade_strategy;
    $order_type = $obj->order_type;
    $qty = $obj->qty;
    $expire_date = $obj->expire_date;
    $strike_price = $obj->strike_price;
    $executed_price = $obj->executed_price;
    $order_type2 = $obj->order_type2;
    $strike_price2 = $obj->strike_price2;
    $com_fee = $obj->com_fee;
    $platform = $obj->platform;
  }
}
echo '
  <html>
  <title> Profit Tracker </title>
  <body>
  <H1> Add Exit Trade </H1>
  <form action="add_exit_trade_db.php" method="post">
  <table>
  <tr>
  <td><label for="platform">Platform:</label></td>
  <td><select id="platform" name="platform" >
  <option value="Alpha-9"';
  if ($platform == 'Alpha-9'){ 
    echo ' selected';
  }
echo '>Alpha-9</option>
  <option value="Money Calendar Pro"';
  if ($platform == 'Money Calendar Pro'){ 
    echo ' selected';
  }
echo '>Money Calendar Pro</option>
  </select></td></tr>
  ';
echo '
  <tr>
  <td><label for="type">Execution Date:</label></td>
  <td><input type="text" name="executed_date" value="'.$executed_date.'" size="10" ></td>
  </tr></table>
  ';
echo '
  <table border=1>
  <tr>
  <th>Symbol</th>
  <th>Strategy</th>
  <th>Order Type</th>
  <th>Qty</th>
  <th>Exipre Date</th>
  <th>Strike Price</th>
  <th>Executed Price</th>
  <th>Commission Fee</th></tr>
  ';
echo '
  <tr>
  <td><input type="text" name="symbol" value="'.$symbol.'" size="6" ></td>
  <td><select id="trade_strategy" name="trade_strategy" >
    <option value="Call"';
    if ($trade_strategy == 'Call'){
      echo ' selected';
    }
  echo '>Call</option>
    <option value="Call Spread"';
    if ($trade_strategy == 'Call Spread'){
      echo ' selected';
    }
  echo '>Call Spread</option>
    <option value="Put"';
    if ($trade_strategy == 'Put'){
      echo ' selected';
    }
  echo '>Put</option>
    <option value="Put Spread"';
    if ($trade_strategy == 'Put Spread'){
      echo ' selected';
    }
  echo '>Put Spread</option>
  </select></td>
  ';
echo '
  <td><select id="order_type" name="order_type" >
    <option value="Buy Open">Buy Open</option>
    <option value="Sell Close"';
    if ($order_type == 'Buy Open'){
      echo ' selected';
    }
echo '>Sell Close</option>
    <option value="Sell Open">Sell Open</option>
    <option value="Buy Close"';
    if ($order_type == 'Sell Open'){
      echo ' selected';
    }
echo '>Buy Close</option>
  </select></td>';
echo '
<td><input type="text" name="qty" value="'.$qty.'" size="3"></td>
<td><input type="text" name="expire_date" value="'.$expire_date.'" size="10" ></td>
<td><input type="text" name="strike_price" value="'.$strike_price.'" size="10" ></td>
<td><input type="text" name="executed_price" value="" size="10"></td>
<td><input type="text" name="com_fee" value="" size="12"></td>
</tr>
<tr>
<td></td>
<td></td>
<td><select id="order_type2" name="order_type2" >
  <option value=""';
    if ($order_type2 == ''){
      echo ' selected';
    }
echo '></option>
  <option value="Buy Open">Buy Open</option>
  <option value="Sell Close"';
    if ($order_type2 == 'Buy Open'){
      echo ' selected';
    }
echo '>Sell Close</option>
  <option value="Sell Open">Sell Open</option>
  <option value="Buy Close"';
    if ($order_type2 == 'Sell Open'){
      echo ' selected';
    }
echo '>Buy Close</option>
</select></td>
<td></td>
<td></td>
<td><input type="text" name="strike_price2" value="'.$strike_price2.'" size="10" ></td>
<td></td><td><input type="hidden" name="ID" value="'.$id.'"></td></tr></table>
<input type="submit" /></form></body></html>';
?>
