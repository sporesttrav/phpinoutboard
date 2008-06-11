<? 
//base include
include_once("include/header.php");

$m= date("m");		//find todays month
$d= date("d");     // Finds today's date
$y= date("Y");     // Finds today's year

include("include/pageHeader.php"); 
?>
<script type="text/javascript" language="javascript" src="js/yetii.js"></script>
<script type="text/javascript" language="javascript">
var currentView = 'month';
var currentMonth = <?=$m?>;
var currentDay = <?=$d?>;
var currentYear = <?=$y?>;
//----------------------------------------------------------------------------
//Calendar
//----------------------------------------------------------------------------
function setView(tabId) {
	//tab 1 is month, 2 is day, 3 is week
	switch(tabId) {
		case 1:
			currentView = 'month';
			break;
		case 3:
			currentView = 'day';
			break;
		case 2:
			currentView = 'week';
			break;
	}
	today();
}
//prev/next functions--------------------------------------------------------------
function prev() {
	switch(currentView) {
		case 'month':
			prevMonth();
			break;
		case 'day':
			prevDay();
			break;
		case 'week':
			prevWeek();
			break;
	}
}
function next() {
	switch(currentView) {
		case 'month':
			nextMonth();
			break;
		case 'day':
			nextDay();
			break;
		case 'week':
			nextWeek();
			break;
	}
}
//month functions--------------------------------------------------------------
function nextMonth() {
	currentMonth++;
	var dataString = "month="+currentMonth+ "&year="+currentYear;
	displayMonth(dataString);
}

function prevMonth() {
	currentMonth--;
	var dataString = "month="+currentMonth+ "&year="+currentYear;
	displayMonth(dataString);
}
//day functions--------------------------------------------------------------
function nextDay() {
	currentDay++;
	var dataString = "month="+currentMonth+"&day="+currentDay + "&year="+currentYear;
	displayDay(dataString);
}

function prevDay() {
	currentDay--;
	var dataString = "month="+currentMonth+"&day="+currentDay + "&year="+currentYear;
	displayDay(dataString);
}
//week functions--------------------------------------------------------------
function nextWeek() {
	var dataString = "dir=next&month="+currentMonth+"&day="+currentDay + "&year="+currentYear;
	displayWeek(dataString);
}

function prevWeek() {
	var dataString = "dir=prev&month="+currentMonth+"&day="+currentDay + "&year="+currentYear;
	displayWeek(dataString);
}
//today function--------------------------------------------------------------
function today() {
	switch(currentView) {
		case 'month':
			displayMonth();
			break;
		case 'day':
			displayDay();
			break;
		case 'week':
			displayWeek();
			break;
	}

}
//display functions--------------------------------------------------------------
function displayMonth(dataString) {
	microAjax('support/monthView.php?'+dataString, function(pageData){
		if(pageData.indexOf('Invalid') > 0) {
				alert(pageData);
			} else {
				var xmlDoc = loadXMLString(pageData);
				currentMonth = parseInt(xmlDoc.getElementsByTagName("month")[0].firstChild.nodeValue);
				currentDay = parseInt(xmlDoc.getElementsByTagName("day")[0].firstChild.nodeValue);
				currentYear = parseInt(xmlDoc.getElementsByTagName("year")[0].firstChild.nodeValue);
				$div('currentDay').innerHTML = xmlDoc.getElementsByTagName("display")[0].firstChild.nodeValue;
				$div('monthView').innerHTML = xmlDoc.getElementsByTagName("calendar")[0].firstChild.nodeValue;
			}
	});
}
function displayDay(dataString) {
	microAjax('support/dayView.php?'+dataString, function(pageData){
		if(pageData.indexOf('Invalid') > 0) {
				alert(pageData);
			} else {
				var xmlDoc = loadXMLString(pageData);
				currentMonth = parseInt(xmlDoc.getElementsByTagName("month")[0].firstChild.nodeValue);
				currentDay = parseInt(xmlDoc.getElementsByTagName("day")[0].firstChild.nodeValue);
				currentYear = parseInt(xmlDoc.getElementsByTagName("year")[0].firstChild.nodeValue);
				$div('currentDay').innerHTML = xmlDoc.getElementsByTagName("display")[0].firstChild.nodeValue;
				$div('dayView').innerHTML = xmlDoc.getElementsByTagName("calendar")[0].firstChild.nodeValue;
			}
	});
}

function displayWeek(dataString) {
	microAjax('support/weekView.php?'+dataString, function(pageData){
		if(pageData.indexOf('Invalid') > 0) {
				alert(pageData);
			} else {
				var xmlDoc = loadXMLString(pageData);
				currentMonth = parseInt(xmlDoc.getElementsByTagName("month")[0].firstChild.nodeValue);
				currentDay = parseInt(xmlDoc.getElementsByTagName("day")[0].firstChild.nodeValue);
				currentYear = parseInt(xmlDoc.getElementsByTagName("year")[0].firstChild.nodeValue);
				$div('currentDay').innerHTML = xmlDoc.getElementsByTagName("display")[0].firstChild.nodeValue;
				$div('weekView').innerHTML = xmlDoc.getElementsByTagName("calendar")[0].firstChild.nodeValue;
			}
	});
}


//----------------------------------------------------------------------------
function viewEvent(id) {
	window.location= 'evtInfo.php?id='+id;
}

</script>
<style>
@import 'css/tab.css';
</style>
<div id="caltable" style="clear:both; position:relative; top:20px;">
<div style="float:left;position:relative;top:20px;">
      <input type="button" value="&lt;&lt;" onclick="prev()" />
      <input type="button" value="&gt;&gt;" onclick="next()" />
      <input type="button" value="Today" onclick="today()" />
      &nbsp;&nbsp;<strong><div style="display:inline" id="currentDay"><?=date("m/d/Y",mktime(0,0,0,$m,$d,$y))?></div>
      </strong></div>
  <div id="tab-container-1">
    
    <ul id="tab-container-1-nav">
      <li><a href="#month">Month</a></li>
      <li><a href="#week">Week</a></li>
      <li><a href="#day">Day</a></li>
    </ul>
    <div class="tab" id="month">
      <div id="monthView">
        <script>today()</script>
      </div>
    </div>
    <div class="tab" id="week">
      <div id="weekView">Week</div>
    </div>
    <div class="tab" id="day">
      <div id="dayView">Day</div>
    </div>
  </div>
</div>
<script type="text/javascript">
var tabber1 = new Yetii({

id: 'tab-container-1',
callback:setView

});
</script>
<? include("include/pageFooter.php"); ?>
