<html>
<title> Profit Tracker </title>
<body>
<H1> Edit Profit </H1>

<?php
require_once("include/database.cfg.php");

$dbconnection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($dbconnection->connect_error) {
  die("Connection failed: " . $conn->connect_error);
} else {
  $id = $_POST['ID'];
  $sql = "SELECT * FROM profits WHERE ID='".$id."';";
  $results = $dbconnection->query($sql);
  while($obj = $results->fetch_object()){
    echo '<form action="edit_profit_db.php" method="post">';
    echo '<table><tr>
          <td><label for="platform">Platform:</label></td>
          <td><select id="platform" name="platform">';
    foreach ($platforms as &$p) {
      echo '<option value="'.$p.'"';
      if ($obj->platform == $p){ 
        echo ' selected="selected"';
      }
      echo '>'.$p.'</option>';
    }
    echo '</select></td></tr></table>';
    echo '<table border=1><tr>
          <th>Date</th>
          <th>Description</th>
          <th>Amount</th>
          <th>Entry ID</th>
          <th>Exit ID</th>
          </tr><tr>';
    echo '<td><input type="text" name="date" value="'.$obj->date.'" size="10"></td>';
    echo '<td><input type="text" name="description" value="'.$obj->description.'" size="50"></td>';
    echo '<td><input type="text" name="amount" value="'.$obj->amount.'" size="3"></td>';
    echo '<td><input type="text" name="entry_id" value="'.$obj->entry_id.'" size="10"></td>';
    echo '<td><input type="text" name="exit_id" value="'.$obj->exit_id.'" size="10"></td></tr></tr></table>';
    echo '<input type="hidden" name="ID" value="'.$id.'">';
    echo '<input type="submit" /></form>';
    echo '';



  }
}
?>
</body>
</html>
