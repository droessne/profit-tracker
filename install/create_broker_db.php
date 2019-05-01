<?php
require_once("../include/database.cfg.php");

$dbconnection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($dbconnection->connect_error) {
  die("Connection failed: " . $conn->connect_error);
} else {
  $broker_name = $_POST['broker_name'];
  $sql = "SELECT MAX(broker_id) AS id FROM brokers;";
  $results = $dbconnection->query($sql);
  while($obj = $results->fetch_object()){
    $new_id = ($obj->id + 1);
  }
  $broker_trade_profit_name = 'profits_'.$new_id;
  $broker_trade_table_name = 'trades_'.$new_id;
  $sql1 = "INSERT INTO brokers (broker_id, broker_name, broker_trade_table_name, broker_trade_profit_name) VALUES('".$new_id."', '".$broker_name."', '".$broker_trade_table_name."', '".$broker_trade_profit_name."');";
  $results1 = $dbconnection->query($sql1);
  if ($results1) {
    echo "Broker added.";
    header("Location: {$_SERVER['HTTP_REFERER']}");
    die();
  } else {
    echo "Sorry, adding this broker failed. Please try again";
  }
}

?>
