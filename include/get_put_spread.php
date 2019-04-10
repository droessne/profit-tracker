<?php
require_once("database.cfg.php");
require_once("get_auth.php");

function get_put_spread($symbol, $strike, $strike2, $expire_date){

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
    $interval = ($strike2 - $strike);

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://api.tdameritrade.com/v1/marketdata/chains?apikey=".$client_id."&symbol=".$symbol."&contractType=PUT&strategy=VERTICAL&interval=".$interval."&strike=".$strike,
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
    $ex_date = explode("-", $expire_date);
    $month = date("M", mktime(0, 0, 0, $ex_date[1], 1, 0));
    $data = json_decode($response);
    foreach($data->monthlyStrategyList as $row) {
      if ($row->month == $month){
        if ($row->day == $ex_date[2]){
          $d_2_ex = $row->daysToExp;
          $last = "N/A";
          $volatility = "N/A";
          $tot_vol = "N/A";
          foreach($row->optionStrategyList as $row1) {
                $mark = (($row1->strategyAsk + $row1->strategyBid)/2);
          }
        }
      }
    }
    $results = [
      "last" => $last,
      "mark" => $mark,
      "volatility" => $volatility,
      "d_2_ex" => $d_2_ex,
      "tot_vol" => $tot_vol
    ];
    return $results;
  }
}

#$results = get_put_spread('DTE','115', '120', '2019-05-17');
#print_r($results);

?>
