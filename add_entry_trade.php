<html>
<title> Profit Tracker </title>
<body>
<H1> Add Entry Trade </H1>
<form action="add_entry_trade_db.php" method="post">
<table>
<tr>
<td><label for="platform">Platform:</label></td>
<td><select id="platform" name="platform">
  <option value="Alpha-9">Alpha-9</option>
  <option value="Money Calendar Pro">Money Calendar Pro</option>
</select></td>
</tr>
<tr>
<td><label for="type">Execution Date:</label></td>
<td><input type="text" name="executed_date" value="<?php date_default_timezone_set("America/New_York"); echo date("Y-m-d"); ?>" size="10"></td>
</tr>
</table>
<table border=1>
<tr>
<th>Symbol</th>
<th>Strategy</th>
<th>Order Type</th>
<th>Qty</th>
<th>Exipre Date</th>
<th>Strike Price</th>
<th>Executed Price</th>
<th>Commission Fee</th>
</tr><tr>
<td><input type="text" name="symbol" value="" size="6"></td>
<td><select id="trade_strategy" name="trade_strategy">
  <option value="Call">Call</option>
  <option value="Call Spread">Call Spread</option>
  <option value="Put">Put</option>
  <option value="Put Spread">Put Spread</option>
</select></td>
<td><select id="order_type" name="order_type">
  <option value="Buy Open">Buy Open</option>
  <option value="Sell Close">Sell Close</option>
  <option value="Sell Open">Sell Open</option>
  <option value="Buy Close">Buy Close</option>
</select></td>
<td><input type="text" name="qty" value="" size="3"></td>
<td><input type="text" name="expire_date" value="<?php date_default_timezone_set("America/New_York"); echo date("Y-m-d"); ?>" size="10"></td>
<td><input type="text" name="strike_price" value="" size="10"></td>
<td><input type="text" name="executed_price" value="" size="10"></td>
<td><input type="text" name="com_fee" value="" size="12"></td>
</tr>
<tr>
<td></td>
<td></td>
<td><select id="order_type2" name="order_type2">
  <option value=""></option>
  <option value="Buy Open">Buy Open</option>
  <option value="Sell Close">Sell Close</option>
  <option value="Sell Open">Sell Open</option>
  <option value="Buy Close">Buy Close</option>
</select></td>
<td></td>
<td></td>
<td><input type="text" name="strike_price2" value="" size="10"></td>
<td></td>
<td></td>
</tr>
</table>

<input type="submit" />
</form>

</body>
</html>
