<?php

require_once("include/database.cfg.php");
require_once("include/defaults.cfg.php");

$dbconnection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$id = $_POST['ID'];

if (!$dbconnection->connect_errno) {
  $sql = "DELETE FROM ".$trades_table." WHERE ID='$id';";
  #echo $sql;
  $results = $dbconnection->query($sql);
  #$results->close();
  #unset($obj);
  echo "Deleted entry!";
  if (strpos($_POST['referer'], 'closed') !== false) {
            header("Location: view_closed_form.php");
        } else {
            header("Location: {$_SERVER['HTTP_REFERER']}");
        }
  die();
} else {
  echo "Failed to Delete entry!";
}
?>

