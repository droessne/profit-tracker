<?php
function get_crypto($symbol){
  $params = "symbol=".$symbol;
  $api_key = "rLtHblSweXAQ3mIhM8KV6L6B4ufxjphb6coBUWvqo53cl89RAIOehpzjvMh9ORcW";
  $secret = "DzueI1FK56MjVFZV0G7vK7EpCtuy4YHdtDYrJquaR8dCT10YYWUgIK5t32hKPKp9";
  $signature = hash_hmac('sha256', $params, $secret);
  $url = "https://api.binance.com/api/v3/ticker/price?".$params."&signature=".$signature;

  print_r($url);

  $curl = curl_init();

  curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_POSTFIELDS => '',
      CURLOPT_HTTPHEADER => array(
        "X-MBX-APIKEY: ".$api_key
      ),
    ));

  $response = curl_exec($curl);
  $err = curl_error($curl);

  curl_close($curl);

  if ($err) {
    echo "cURL Error #:" . $err;
  } else {
    print_r($response);
    #$data = json_decode($response);
    #$mark = $data->$symbol->mark;
    #$results = [
    #  "last" => $last,
    #  "mark" => $mark,
    #  "volatility" => $volatility,
    #  "div_yield" => $div_yield,
    #  "tot_vol" => $tot_vol,
    #  "d_2_ex" => 999
    #];
    return $results;
  }
}
get_crypto('IOTXBTC');
#print_r($results);
#echo $results['mark'];
?>
