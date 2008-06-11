<style type="text/css">
/*       */
ul.left_col {
	float: left;
}
ul.center {
	float: left;
}
ul.menu {
	padding-top:10px;
	font-family:Geneva, Arial, Helvetica, sans-serif;
	font-size: 12px;
	margin: 0px;
	padding-left: 0px;
	width: auto;
	list-style-type: none;
	margin-right:15px;
}
ul.menu li {
	background-color: #f4f4f4;
}
</style>
<div id="top_menu" style="margin-top:0px; padding-bottom:10px;">
<ul class="menu center">
    <li><img align="middle" alt="v2" src="images/DELogin_sm.jpg" /></li>
  </ul>
  <ul class="menu center">
    <li><a href="index.php">Status View</a></li>
  </ul>
  <ul class="menu center">
    <li><a href="newSchedule.php">Schedule Event</a></li>
  </ul>
  <ul class="menu center">
    <li><a href="calendar.php">Calendar</a></li>
  </ul>
  <!--
  <ul class="menu center">
    <li><a href="#" onclick="javascript:changeView('log')">Daily Log</a></li>
  </ul>
  -->
  <ul class="menu center">
    <li><a href="dailyBreakdown.php">Daily Breakdown</a></li>
  </ul>
  
    <ul class="menu center">
    <li><a href="logout.php">Logout</a></li>
  </ul>
  <? if(isset($currentID) && isset($admin) && $admin=="de_admin") { ?>
  <ul class="menu center">
    <li><a href="admin.php">Administrative</a></li>
  </ul>
  <? } ?>
 </div>