<?php
require_once("database.cfg.php");
$dbconnection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (!$dbconnection->connect_errno) {
  $sql = "SELECT * FROM defaults WHERE id='1';";
  $results = $dbconnection->query($sql);
  while($obj = $results->fetch_object()){
    $active_broker_id = $obj->active_broker_id;
    $monthly_profit_percent_target = $obj->monthly_profit_percent_target;
    $monthly_profit_percent_to_keep = $obj->monthly_profit_percent_to_keep;
    $platforms = explode(":", $obj->platforms);
  }
  if (isset ($active_broker_id)){
    $sql1 = "SELECT * FROM brokers WHERE broker_id=".$active_broker_id.";";
    $results1 = $dbconnection->query($sql1);
    while($obj1 = $results1->fetch_object()){
      $webpage_title = $obj1->broker_name;
      $profits_table = $obj1->broker_trade_profit_name;
      $trades_table = $obj1->broker_trade_table_name;
    }
  }
}
?>
