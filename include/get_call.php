<?php
require_once("include/database.cfg.php");
require_once("include/get_auth.php");

$dbconnection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($dbconnection->connect_error) {
  die("Connection failed: " . $conn->connect_error);
} else {
  $sql = "SELECT * FROM auth WHERE ID='1';";
  $results = $dbconnection->query($sql);
  while($obj = $results->fetch_object()){
    $access_token = $obj->access_token;
  }
}

  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.tdameritrade.com/v1/marketdata/chains?apikey=DERS_MONEY&symbol=HLF&contractType=CALL",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_POSTFIELDS => "",
    CURLOPT_HTTPHEADER => array(
      "Authorization: Bearer ".$access_token.",
      "cache-control: no-cache"
    ),
  ));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}
?>
