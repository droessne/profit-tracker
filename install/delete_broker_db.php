<?php
require_once("../include/database.cfg.php");
header( "refresh:5;url=config.php" );

$dbconnection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($dbconnection->connect_error) {
  die("Connection failed: " . $conn->connect_error);
} else {
  $broker_id = $_POST['broker_del'];
  print_r($_POST);
  if ($broker_id == ''){
      echo "Error: Broker_id cannot be blank";
      die();
  }
  $sql = "SELECT * FROM brokers WHERE broker_id='".$broker_id."';";
  $results = $dbconnection->query($sql);
  while($obj = $results->fetch_object()){
    $trade_table = $obj->broker_trade_table_name;
    $profit_table = $obj->broker_trade_profit_name;
  }
  $sql1 = "DROP TABLE ".$profit_table.";";
  $results1 = $dbconnection->query($sql1);
  if ($results1) {
    echo $profit_table." deleted successfully.<br>";
    $sql2 = "DROP TABLE ".$trade_table.";";
    if ($dbconnection->query($sql2) === TRUE) {
       echo $trade_table." deleted successfully.<br>";
       $sql3 = "DELETE FROM brokers WHERE broker_id='$broker_id';";
       if ($dbconnection->query($sql3) === TRUE) {
         echo "Broker deleted successfully.<br>";
      } else {
         echo "Error: Failed to delete broker from brokers table.<br>";
      }
    } else {
       echo "Error: Failed to delete trade table. <br>";
    }
    die();
  } else {
    echo "Error: Failed to delete profits table. <br>";
  }
}

?>
