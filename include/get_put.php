<?php
require_once("database.cfg.php");
require_once("get_auth.php");

function get_put($symbol, $strike, $expire_date){

  $dbconnection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

  if ($dbconnection->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  } else {
    $sql = "SELECT * FROM auth WHERE ID='1';";
    $results = $dbconnection->query($sql);
    while($obj = $results->fetch_object()){
      $access_token = $obj->access_token;
      $client_id = $obj->client_id;
    }
  }

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://api.tdameritrade.com/v1/marketdata/chains?apikey=".$client_id."&symbol=".$symbol."&contractType=PUT&strategy=SINGLE&strike=".$strike."&fromDate=".$expire_date."&toDate=".$expire_date,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_POSTFIELDS => "",
      CURLOPT_HTTPHEADER => array(
        "Authorization: Bearer ".$access_token,
        "cache-control: no-cache"
      ),
    ));

  $response = curl_exec($curl);
  $err = curl_error($curl);

  curl_close($curl);

  if ($err) {
    echo "cURL Error #:" . $err;
  } else {
    #echo $response;
    $data = json_decode($response);
    foreach($data->putExpDateMap as $row) {
      foreach($row as $key => $val) {
        foreach($val as $v){
          $last = $v->last;
          $mark = $v->mark;
          $volatility = $v->volatility;
          $d_2_ex = $v->daysToExpiration;
          $tot_vol = $v->totalVolume;
          $open = $v->openPrice;
          $close = $v->closePrice;
          $delta = $v->delta;
          $theta = $v->theta;
          $bid = $v->bidPrice;
          $ask = $v->askPrice;
        }
      }
    }
    $results = [
      "last" => $last,
      "mark" => $mark,
      "volatility" => $volatility,
      "d_2_ex" => $d_2_ex,
      "tot_vol" => $tot_vol,
      "open" => $open,
      "close" => $close,
      "delta" => $delta,
      "theta" => $theta,
      "bid" => $bid,
      "ask" => $ask
    ];
    return $results;
  }
}

#$results = get_put('INDA','29', '2019-05-17');
#print_r($results);

?>
