<?php
//https://auth.tdameritrade.com/oauth?client_id=DERS_MONEY@AMER.OAUTHAP&response_type=code&redirect_uri=https://money.dersllc.com:8743

require_once("database.cfg.php");

function get_auth() {
    $dbconnection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$dbconnection->connect_errno) {
        $sql = "SELECT * FROM auth WHERE ID='1';";
        $results = $dbconnection->query($sql);
        while($obj = $results->fetch_object()){
          $refresh_token = $obj->refresh_token;
          $redirect_uri = $obj->redirect_uri;
          $client_id = $obj->client_id;
        }
    }
    $curl = curl_init();
    
    $fields = array(
	'grant_type' => urlencode('refresh_token'),
	'refresh_token' => urlencode($refresh_token),
	'access_type' => urlencode('offline'),
	'code' => urlencode('code'),
	'client_id' => urlencode($client_id),
	'redirect_uri' => urlencode($redirect_uri)
    );
    foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
    rtrim($fields_string, '&');
    
    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://api.tdameritrade.com/v1/oauth2/token",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => $fields_string,
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
      $data = json_decode($response);

      $sql = "UPDATE auth SET access_token = '".$data->access_token."', refresh_token = '".$data->refresh_token."' WHERE ID = 1;";
      $results = $dbconnection->query($sql);
      if ($results) {
        echo "Auth updated.";
        #header("Location: {$_SERVER['HTTP_REFERER']}");
        #die();
      } else {
        echo $results;
        echo "Sorry, adding this auth failed. Please try again";
      }
      echo $response;
    }
}

get_auth();

?>