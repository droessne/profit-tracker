<html>
<title> Profit Tracker </title>
<body>
<H1> View Closed Trades </H1>
<a href="config.php" style="float: right;">&#9881;</a>
<form action="view_closed.php" method="post">
<table>
<tr>
<td><label for="platform">Platform:</label></td>
<td><select id="platform" name="platform">
<?php
  require_once("include/defaults.cfg.php");
  foreach ($platforms as &$p) {
    echo '<option value="'.$p.'">'.$p.'</option>';
  }
?>
</select></td>
</tr><tr>
<td><label for="time_frame">Time Frame:</label></td>
<td><select id="time_frame" name="time_frame">
<option value="all">All</option>
<option value="last_month">Last Month</option>
<option value="last_week">Last Week</option>
<option value="ytd">YTD</option>
</select></td>
</tr>
</table>
<?php echo '<input type="hidden" name="referer" value="view_closed_form.php">'; ?>
<input type="submit" />
</form>

</body>
</html>
