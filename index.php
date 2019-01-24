<!DOCTYPE html>
<html>
<head>
<title>Profit Tracker</title>
<link rel="icon" type="image/png" href="favicon.png" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
html, body { height: 98%; }
body {font-family: Arial;}

/* Style the tab */
.tab {
  overflow: hidden;
  border: 1px solid #ccc;
  background-color: #f1f1f1;
}

/* Style the buttons inside the tab */
.tab button {
  background-color: inherit;
  float: left;
  border: none;
  outline: none;
  cursor: pointer;
  padding: 14px 16px;
  transition: 0.3s;
  font-size: 17px;
}

/* Change background color of buttons on hover */
.tab button:hover {
  background-color: #ddd;
}

/* Create an active/current tablink class */
.tab button.active {
  background-color: #ccc;
}

/* Style the tab content */
.tabcontent {
  display: none;
  padding: 1px 1px;
  border: none;
  min-height: 88%;
}
.content {
  position: absolute;
  padding: 1px 1px;
  border: none;
  height: 84%;
  width: 97%;
}
</style>
</head>
<body>

<h1 align='center'>Profit Tracker</h1>
<div class="tab">
  <button class="tablinks" onclick="openTab(event, 'add_entry_trade')">Add Trade</button>
  <button class="tablinks" onclick="openTab(event, 'view_open')">Open Trades</button>
  <button class="tablinks" onclick="openTab(event, 'view_closed')">Closed Trades</button>
  <button class="tablinks" onclick="openTab(event, 'add_profit')">Add Profit</button>
  <button class="tablinks" onclick="openTab(event, 'view_profits')">Profits</button>
</div>


<div id="add_entry_trade" class="tabcontent">
  <iframe id="add_entry_trade2" src="add_entry_trade.php" class="content"></iframe>
</div>

<div id="view_open" class="tabcontent">
  <iframe id="view_open2" src="view_open.php" class="content"></iframe>
</div>

<div id="view_closed" class="tabcontent">
  <iframe id="view_closed2" src="view_closed.php" class="content"></iframe>
</div>

<div id="add_profit" class="tabcontent">
  <iframe id="add_profit2" src="add_profit.php" class="content"></iframe>
</div>

<div id="view_profits" class="tabcontent">
  <iframe id="view_profits2" src="view_profits.php" class="content"></iframe>
</div>

<script>
document.getElementsByClassName('tablinks')[1].click()
function openTab(evt, pageName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(pageName).style.display = "block";
  evt.currentTarget.className += " active";
}
</script>
   
</body>
</html> 

