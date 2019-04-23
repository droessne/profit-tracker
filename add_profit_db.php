<?php
require_once("include/database.cfg.php");

$dbconnection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($dbconnection->connect_error) {
  die("Connection failed: " . $conn->connect_error);
} else {
  $date = $_POST['date'];
  $description = $_POST['description'];
  $amount = $_POST['amount'];
  $platform = $_POST['platform'];
  $entry_id = $_POST['entry_id'];
  $exit_id = $_POST['exit_id'];
  $sql = "INSERT INTO ".$profits_table." (date, description, amount, platform, entry_id, exit_id) VALUES('".$date."', '".$description."', '".$amount."', '".$platform."', '".$entry_id."', '".$exit_id."');";
  $results = $dbconnection->query($sql);
  if ($results) {
    echo "Profit added.";
    header("Location: {$_SERVER['HTTP_REFERER']}");
    die();
  } else {
    echo "Sorry, adding this profit failed. Please try again";
  }
}

?>
