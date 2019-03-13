<?php
require_once("include/database.cfg.php");

$dbconnection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($dbconnection->connect_error) {
  die("Connection failed: " . $conn->connect_error);
} else {
  $id = $_POST['ID'];
  $date = $_POST['date'];
  $description = $_POST['description'];
  $amount = $_POST['amount'];
  $entry_id = $_POST['entry_id'];
  $exit_id = $_POST['exit_id'];
  $platform = $_POST['platform'];

    $sql = "UPDATE profits SET date = '".$date."', description = '".$description."', amount = '".$amount."', entry_id = '".$entry_id."', exit_id = '".$exit_id."', platform = '".$platform."' WHERE ID = '".$id."';"; 
  $results_2 = $dbconnection->query($sql);

  if ($results_2) {
    echo "Trade Corrected.";
    header("Location: {$_POST['referer']}");
    die();    
  } else {
    echo "Sorry, editing this trade failed. Please try again";
  }
}

?>
