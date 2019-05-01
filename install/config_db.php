<?php
require_once("../include/database.cfg.php");
header( "refresh:2;url=config.php" );

$dbconnection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($dbconnection->connect_error) {
  die("Connection failed: " . $conn->connect_error);
} else {
  $platform_add = $_POST['platform_add'];
  $platform_del = $_POST['platform_del'];
  $monthly_profit_percent_target = $_POST['monthly_profit_percent_target'];
  $monthly_profit_percent_to_keep = $_POST['monthly_profit_percent_to_keep'];
  $active_broker = $_POST['active_broker'];
  #Platform Add:
    $sql = "SELECT * FROM defaults WHERE id='1'"; 
    $results = $dbconnection->query($sql);
    while($obj = $results->fetch_object()){
      if ($platform_add != ''){
        $platforms = $obj->platforms.":".$platform_add;
      } else {
        $platforms = $obj->platforms;
      }
      if ($platform_del != '---'){
        $platform_array = explode(':', $obj->platforms);
        if (($key = array_search($platform_del, $platform_array)) !== false) {
          unset($platform_array[$key]);
        }
        $platforms = implode(':', $platform_array);
      }
    }

  $sql2 = "UPDATE defaults SET platforms = '".$platforms."', monthly_profit_percent_target = '".$monthly_profit_percent_target."', monthly_profit_percent_to_keep = '".$monthly_profit_percent_to_keep."', active_broker = '".$active_broker."' WHERE ID = '1';"; 
  $results_2 = $dbconnection->query($sql2);

  if ($results_2) {
    echo "Defaults Updated.";
    die();    
  } else {
    echo "Error: Updating the defaults table failed.";
  }
}

?>
