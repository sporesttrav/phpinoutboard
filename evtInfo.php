<?
include_once("include/header.php");

if(!($input->get->keyExists('id') && $evtID = $input->get->testInt('id'))) {
	die("You did not access this page properly");
}

	$evtquery = sprintf("SELECT 	emp_calendar.eventID, emp_calendar.message,  emp_calendar.acctID, 
						emp_status.status_type, accounts.fName, accounts.lName, emp_calendar.attendees,
						emp_status.status_color,emp_calendar.start,emp_calendar.end
						FROM emp_status INNER JOIN 
						(emp_calendar INNER JOIN accounts ON emp_calendar.acctID = accounts.acctID) 
						ON emp_status.status_id = emp_calendar.statusID
						WHERE eventID = %d LIMIT 1",$evtID);
						
	($evt_result = $db->query($evtquery)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, "QUERY ERROR"));
	if($evt_result->num_rows != 1) {
		die("Invalid event");
	}
	$evtData = $evt_result->fetch_assoc();	
?>
<? include("include/pageHeader.php"); ?>
<script language="javascript" type="text/javascript" src="js/datechooser.js"></script>
<style type="text/css">
@import "css/datechooser.css";
</style>
<script language="javascript" type="text/javascript">
function cancelEvent(id) {
	var postData = "id="+id;
	var page = "aj/cancelEvt.php";
	if(document.getElementsByName('cancelEvt').length>0) {
		var repObj = getCheckedValue(document.getElementsByName('cancelEvt'));
		if(repObj) {
			postData += "&rep="+repObj;
		} else {
			postData += "&rep=this";
		}
		page = "aj/cancelEvtProcess.php";
	}
	microAjax(page, function(pageData){
		if(pageData.indexOf("form") > -1) {
		  $div('evt').innerHTML= pageData;	
		} else if(pageData.indexOf("Invalid") == -1) {
			window.location = "calendar.php";	
		} else {
			alert(pageData);
		}
	},postData);
}

function editEvent(id) {
	var postData = "id=" + id;
	microAjax('aj/editSchedule.php', function(pageData) {
		 $div('evt').innerHTML= pageData;	
	}, postData);
}

function deleteEvent(id) {
	var postData = "id="+id;
	var page = "aj/deleteEvt.php";
	if(document.getElementsByName('cancelEvt').length>0) {
		var repObj = getCheckedValue(document.getElementsByName('cancelEvt'));
		if(repObj) {
			postData += "&rep="+repObj;
		} else {
			postData += "&rep=this";
		}
		page = "aj/deleteEvtProcess.php";
	}
	microAjax(page, function(pageData){
		if(pageData.indexOf("form") > -1) {
		  $div('evt').innerHTML= pageData;	
		} else if(pageData.indexOf("Invalid") == -1) {
			window.location = "calendar.php";	
		} else {
			alert(pageData);
		}
	},postData);
}
//----------------------------------------------------------------------------
function editSchedule(id) {
	var sTime = $div("start").value + " " + $div("start_hour").value + ":"+$div("start_min").value+$div("start_ampm").value;
	var eTime = $div("end").value + " " + $div("end_hour").value + ":"+$div("end_min").value+$div("end_ampm").value;
	var desc = $div('schedule_notes').value;
	var status = $div('evt_status').value;
	var attend = retrieveListSelected('emp_attend');
	var dataString="id="+id+"&start="+sTime + "&end="+eTime + "&status="+status+ "&attend="+attend+ "&desc="+desc;
	if(document.getElementsByName('cancelEvt')) {
		dataString += "&rep="+getCheckedValue(document.getElementsByName('cancelEvt'));
	}
	if(attend.length <= 0) {
		alert('You must select at least 1 person to attend (including yourself)');
		return;
	}
	disableForm($div('evt'));
	microAjax('aj/editScheduleProcess.php', function(pageData){
		disableForm($div('evt'),false);
		if(pageData.match("Invalid")) {
			alert(pageData);
		} else {
			alert(pageData);
			window.location="calendar.php";
		}
		//changeView('log');
	},dataString);
	
}
</script>
<div id="evt" style="clear:both; position:relative; top:20px;">
Start : <?=date("m/d/Y h:i a",$evtData['start']) ?><br />
End : <?=date("m/d/Y h:i a",$evtData['end']) ?><br />
Status : <?=$evtData['status_type'] ?><br />
Created by: <?=$employees[$evtData['acctID']]['name']?><br />
Attendees : <ul>
<?
$whos = explode(",",$evtData['attendees']);
foreach($whos as $who) {
	$aquery = sprintf("select fName,lName from accounts where acctID = %d",$who);
	($at_result = $db->query($aquery)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, "QUERY ERROR"));
	if($at_result->num_rows ==1) {
		$name = $at_result->fetch_assoc();
		echo "<li>".$name['fName'] . "&nbsp;" . $name['lName'] . "</li>";
	}
}
?>
</ul>
Description : <pre>
<?=$evtData['message'] ?>
</pre>
<br />
<hr width="50%" />
<? if($evtData['start'] > time() && (in_array($currentID,$whos) || $currentID ==$evtData['acctID'] || $admin =='de_admin')) { ?>
<input type="button" value="Edit Event" onclick="editEvent(<?=$evtData['eventID']?>)"/> 
<input type="button" value="Cancel Event" onclick="cancelEvent(<?=$evtData['eventID']?>)" />

<input type="button" value="Delete Event" onclick="deleteEvent(<?=$evtData['eventID']?>)" />
<? } ?>
</div>
<? include("include/pageFooter.php"); ?>