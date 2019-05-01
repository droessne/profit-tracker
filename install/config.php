<html>
<title> Profit Tracker </title>
<body>
<H1> Edit Default Settings </H1>

<?php
require_once("../include/database.cfg.php");
require_once("../include/defaults.cfg.php");

$dbconnection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($dbconnection->connect_error) {
  die("Connection failed: " . $conn->connect_error);
} else {
  $sql = "SELECT * FROM defaults WHERE id='1';";
  $results = $dbconnection->query($sql);
  while($obj = $results->fetch_object()){
    $platforms = explode(':', $obj->platforms);
    echo '<form action="edit_profit_db.php" method="post">';
    echo '<table><tr>
          <td><label for="platform_del">Delete Platform:</label></td>
          <td><select id="platform_del" name="platform">';
    foreach ($platforms as &$p) {
      echo '<option value="'.$p.'">'.$p.'</option>';
    }
    echo '</select></td></tr>';
    echo '<tr>
          <td><label for="platform_add">Add Platform:</label></td>
          <td><input type="text" name="platform_add" size="10">';
    echo '</td></tr></table>';


  }
}
?>
</body>
</html>