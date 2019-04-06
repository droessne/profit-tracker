<?php

function get_auth() {
    $refresh_token = "";
    $client_id = "DERS_MONEY";
    $redirect_uri = "https%3A%2F%2Fmoney.dersllc.com%3A8743";

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://api.tdameritrade.com/v1/oauth2/token",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => "grant_type=refresh_token&refresh_token=".$refresh_token."&access_type=offline&code=code&client_id=".$client_id."&redirect_uri=".$redirect_uri,
      CURLOPT_HTTPHEADER => array(
        "Content-Type: application/x-www-form-urlencoded",
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
}

get_auth();

?>
