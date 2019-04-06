<?php
require_once("include/database.cfg.php");

$dbconnection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($dbconnection->connect_error) {
  die("Connection failed: " . $conn->connect_error);
} else {
  $json = $_POST['json'];
  $data = json_decode($json);


  $access_token = $data->access_token;
  echo $access_token;
  echo "<br>";
  $refresh_token = $data->refresh_token;
  echo $refresh_token;
  echo "<br>";
  $client_id = "DERS_MONEY";
  echo $client_id;
  echo "<br>";
  $redirect_uri = "https%3A%2F%2Fmoney.dersllc.com%3A8743";
  echo $redirect_uri;
  echo "<br>";

  $sql = "INSERT INTO auth (access_token, refresh_token, client_id, redirect_uri) VALUES('".$access_token."', '".$refresh_token."', '".$client_id."', '".$redirect_uri."');";
  $results = $dbconnection->query($sql);
  if ($results) {
    echo "Auth added.";
    #header("Location: {$_SERVER['HTTP_REFERER']}");
    #die();
  } else {
    echo $results;
    echo "Sorry, adding this auth failed. Please try again";
  }
}

?>
