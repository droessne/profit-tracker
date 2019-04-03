<html>
<title> Profit Tracker </title>
<body>
<H1> Edit Trade </H1>

<?php
require_once("include/database.cfg.php");
require_once("include/defaults.cfg.php");

$dbconnection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($dbconnection->connect_error) {
  die("Connection failed: " . $conn->connect_error);
} else {
  $id = $_POST['ID'];
  $sql = "SELECT * FROM trades WHERE ID='".$id."';";
  $results = $dbconnection->query($sql);
  while($obj = $results->fetch_object()){
    echo '<form action="edit_trade_db.php" method="post">';
    echo '<table><tr>
          <td><label for="platform">Platform:</label></td>
          <td><select id="platform" name="platform">';
    foreach ($platforms as &$p) {
      echo '<option value="'.$p.'"';
      if ($obj->platform == $p){ 
        echo ' selected="selected"';
      }
      echo '>'.$p.'</option>';
    }
    echo '</select></td></tr>';
    echo '<tr><td><label for="type">Type:</label></td>
          <td><select id="type" name="type">
            <option value="Entry"';
    if ($obj->type == 'Entry'){
      echo 'selected="selected"';
    }
                              echo '>Entry</option>
            <option value="Exit"';
    if ($obj->type == 'Exit'){
      echo 'selected="selected"';
    }
                                         echo '>Exit</option>
          </select></td></tr>';
    echo '<tr>
          <td><label for="type">Execution Date:</label></td>
          <td><input type="text" name="executed_date" value="'.$obj->executed_date.'" size="10"></td>
          </tr></table>';
    echo '<table border=1><tr>
          <th>Symbol</th>
          <th>Strategy</th>
          <th>Order Type</th>
          <th>Qty</th>
          <th>Exipre Date</th>
          <th>Strike Price</th>
          <th>Executed Price</th>
          <th>Commission Fee</th>
          </tr><tr>';
    echo '<td><input type="text" name="symbol" value="'.$obj->symbol.'" size="6"></td>';
    echo '<td><select id="trade_strategy" name="trade_strategy">
          <option value="Call"';
          if ($obj->trade_strategy == 'Call'){
            echo ' selected="selected"';
          }
                         echo '>Call</option>
          <option value="Call Spread"';
          if ($obj->trade_strategy == 'Call Spread'){
            echo ' selected="selected"';
          }
                                echo '>Call Spread</option>
          <option value="Put"';
          if ($obj->trade_strategy == 'Put'){
            echo ' selected="selected"';
          }
                        echo '>Put</option>
          <option value="Put Spread"';
          if ($obj->trade_strategy == 'Put Spread'){
            echo ' selected="selected"';
          }
                               echo '>Put Spread</option>
          </select></td>';
    echo '<td><select id="order_type" name="order_type">
          <option value="Buy Open"';
          if ($obj->order_type == 'Buy Open'){
            echo ' selected="selected"';
          }
                             echo '>Buy Open</option>
          <option value="Sell Close"';
          if ($obj->order_type == 'Sell Close'){
            echo ' selected="selected"';
          }
                               echo '>Sell Close</option>
          <option value="Sell Open"';
          if ($obj->order_type == 'Sell Open'){
            echo ' selected="selected"';
          }
                              echo '>Sell Open</option>
          <option value="Buy Close"';
          if ($obj->order_type == 'Buy Close'){
            echo ' selected="selected"';
          }
                              echo '>Buy Close</option>
          </select></td>';
    echo '<td><input type="text" name="qty" value="'.$obj->qty.'" size="3"></td>';
    echo '<td><input type="text" name="expire_date" value="'.$obj->expire_date.'" size="10"></td>';
    echo '<td><input type="text" name="strike_price" value="'.$obj->strike_price.'" size="10"></td>';
    echo '<td><input type="text" name="executed_price" value="'.$obj->executed_price.'" size="10"></td>';
    echo '<td><input type="text" name="com_fee" value="'.$obj->com_fee.'" size="12"></td></tr>';
    echo '<tr><td></td><td></td>';
    echo '<td><select id="order_type2" name="order_type2">
            <option value=""';
            if ($obj->order_type2 == ''){
              echo ' selected="selected"';
            }
                       echo '></option>
            <option value="Buy Open"';
            if ($obj->order_type2 == 'Buy Open'){
              echo ' selected="selected"';
            }
                               echo '>Buy Open</option>
            <option value="Sell Close"';
            if ($obj->order_type2 == 'Sell Close'){
              echo ' selected="selected"';
            }
                                 echo '>Sell Close</option>
            <option value="Sell Open"';
            if ($obj->order_type2 == 'Sell Open'){
              echo ' selected="selected"';
            }
                                echo '>Sell Open</option>
            <option value="Buy Close"';
            if ($obj->order_type2 == 'Buy Close'){
              echo ' selected="selected"';
            }
                                echo '>Buy Close</option>
          </select></td>';
    echo '<td></td><td></td>';
    echo '<td><input type="text" name="strike_price2" value="'.$obj->strike_price2.'" size="10"></td>';
    echo '<td><input type="hidden" name="ID" value="'.$id.'"></td></tr></table>';
    echo '<input type="hidden" name="referer" value="'.$_SERVER['HTTP_REFERER'].'">';
    echo '<input type="submit" /></form>';
    echo '';



  }
}
?>
</body>
</html>
