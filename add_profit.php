<html>
<title> Profit Tracker </title>
<body>
<H1> Add Profit </H1>
<form action="add_profit_db.php" method="post">
<table>
<tr>
<td><label for="platform">Platform:</label></td>
<td><select id="platform" name="platform">
  <option value="Alpha-9">Alpha-9</option>
  <option value="Money Calendar Pro">Money Calendar Pro</option>
</select></td>
</tr>
</table>
<table border=1>
<tr>
<th>Date</th>
<th>Description</th>
<th>Amount</th>
<th>Entry ID</th>
<th>Exit ID</th>
</tr><tr>
<td><input type="text" name="date" value="<?php date_default_timezone_set("America/New_York"); echo date("Y-m-d"); ?>" size="10"></td>
<td><input type="text" name="description" value="" size="50"></td>
<td><input type="text" name="amount" value="" size="11"></td>
<td><input type="text" name="entry_id" value="0" size="11"></td>
<td><input type="text" name="exit_id" value="0" size="11"></td>
</tr>
</table>

<input type="submit" />
</form>

</body>
</html>
