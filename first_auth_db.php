<?php
require_once("include/database.cfg.php");
require_once("include/defaults.cfg.php");

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
  $client_id = "MONEY_DERS@AMER.OAUTHAP";
  echo $client_id;
  echo "<br>";
  $redirect_uri = "https%3A%2F%2Fapi.dersllc.com%3A8743";
  echo $redirect_uri;
  echo "<br>";

  $sql = "INSERT INTO auth (ID, access_token, refresh_token, client_id, redirect_uri) VALUES(1, '".$access_token."', '".$refresh_token."', '".$client_id."', '".$redirect_uri."');";
  $results = $dbconnection->query($sql);
  if ($results) {
    echo "Auth added.";
    #header("Location: {$_SERVER['HTTP_REFERER']}");
    #die();
  } else {
    $sql = "UPDATE auth SET access_token = '".$access_token."', refresh_token = '".$refresh_token."', client_id = '".$client_id."', redirect_uri = '".$redirect_uri."' WHERE ID = 1;";
    $results = $dbconnection->query($sql);
    if ($results) {
      echo "Auth updated.";
      #header("Location: {$_SERVER['HTTP_REFERER']}");
      #die();
    } else {
      echo $results;
      echo "Sorry, adding this auth failed. Please try again";
    }
  }
}

?>
