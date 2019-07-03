<html>
<title> Profit Tracker </title>
<body>
<H1> Settings </H1>
<h2> Default Settings:</h2>
<?php
echo 'hello';
require_once("include/database-cfg.php");
echo 'datab good';
require_once("include/defaults.cfg.php");
echo 'default good';

$dbconnection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($dbconnection->connect_error) {
  echo "FILED CONNECTION";
  die("Connection failed: " . $conn->connect_error);
} else {
  $sql = "SELECT * FROM defaults WHERE id='1';";
  $results = $dbconnection->query($sql);
  while($obj = $results->fetch_object()){
    $platforms = explode(':', $obj->platforms);
    echo '<form action="config_db.php" method="post"><table>';
    echo '<tr>
          <td><label for="platform_add">Add Platform:</label></td>
          <td><input type="text" name="platform_add" size="17"></td></tr>';
    echo '<tr>
          <td><label for="platform_del">Delete Platform:</label></td>
          <td><select id="platform_del" name="platform_del">';
    echo '<option value="---" selected="selected">---</option>';
    foreach ($platforms as &$p) {
      echo '<option value="'.$p.'">'.$p.'</option>';
    }
    echo '</select></td></tr>';
    echo '<tr>
          <td><label for="monthly_profit_percent_target">Monthly Profits - Target Percent (ex. 0.4):</label></td>
          <td><input type="text" name="monthly_profit_percent_target" value="'.$obj->monthly_profit_percent_target.'"size="17"></td></tr>';
    echo '<tr>
          <td><label for="monthly_profit_percent_to_keep">Monthly Profits - Keep Percent (ex. 0.25):</label></td>
          <td><input type="text" name="monthly_profit_percent_to_keep" value="'.$obj->monthly_profit_percent_to_keep.'" size="17"></td></tr>';
    $sql1 = "SELECT * FROM brokers;";
    $results1 = $dbconnection->query($sql1);
    echo '<tr>
          <td><label for="active_broker">Active Broker:</label></td>
          <td><select id="active_broker" name="active_broker">';
    while($obj1 = $results1->fetch_object()){
        echo '<option value="'.$obj1->broker_id.'"';
        if ($obj1->broker_id == $obj->active_broker_id){
          echo ' selected="selected"';
        }
        echo '>'.$obj1->broker_name.'</option>';
    }
    echo '</select></td></tr>';
    echo '</table>';
    echo '<input type="hidden" name="referer" value="'.$_SERVER['HTTP_REFERER'].'">';
    echo '<input type="submit" /></form>';

  }
}
echo '<h2>Add New Broker:</h2>';
echo '<form action="create_broker_db.php" method="post"><table>';
echo '<tr>
      <td><label for="broker_name">Broker Name:</label></td>
      <td><input type="text" name="broker_name" size="17"></td></tr>';
echo '</table>';
echo '<input type="submit" /></form>';

echo '<h2>Remove Broker:</h2>';
echo '<form action="delete_broker_db.php" method="post"><table>';
echo '<tr>
      <td><label for="broker_del">Broker Name:</label></td>
      <td><select id="broker_del" name="broker">';
echo '<option value="---" selected="selected">---</option>';
if ($dbconnection->connect_error) {
  die("Connection failed: " . $conn->connect_error);
} else {
  $sql1 = "SELECT * FROM brokers;";
  $results1 = $dbconnection->query($sql1);
  while($obj1 = $results1->fetch_object()){
      echo '<option value="'.$obj1->broker_id.'">'.$obj1->broker_name.'</option>';
  }
}
echo '</select></td></tr>';
echo '</table>';
echo '<input type="submit" /></form>';

?>

</body>
</html>


