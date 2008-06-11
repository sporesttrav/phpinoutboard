<?
chdir("../");
include_once("include/header.php");

if(!($input->post->keyExists('id') && $evtID = $input->post->testInt('id'))) {
	die("Invalid key entry" . $input->post->getRaw('id') );
}

$evtquery = sprintf("SELECT emp_calendar.linkEventID, emp_calendar.eventID, emp_calendar.message,  
	emp_status.status_type, accounts.fName, accounts.lName,
	emp_status.status_color,emp_calendar.start,emp_calendar.end
	FROM emp_status INNER JOIN 
	(emp_calendar INNER JOIN accounts ON emp_calendar.acctID = accounts.acctID) 
	ON emp_status.status_id = emp_calendar.statusID
	WHERE eventID = %d LIMIT 1",$evtID);
	
($evt_result = $db->query($evtquery)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, "QUERY ERROR"));
if($evt_result->num_rows != 1) {
	die("Invalid event" . $evtID);
}
$evtData = $evt_result->fetch_assoc();

//------------------------------------------------------------------------
//check for any subsequent events
if(intval($evtData['linkEventID'])==0) {
	$status_query = sprintf("select * from emp_calendar where linkEventID=%d LIMIT 1",$evtID);
	($status_result = $db->query($status_query)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, $status_query));
	$linked = ($status_result->num_rows >=1);
	if(!$linked && ($evtRep=="previous" || $evtRep == "future")) die("Invalid repeating argument");
} else {
	$linked = true;
}
?>
Start : <?=date("m/d/Y h:i a",$evtData['start']) ?><br />
End : <?=date("m/d/Y h:i a",$evtData['end']) ?><br />
Status : <?=$evtData['status_type'] ?><br />
Description : <?=$evtData['message'] ?><br />
<hr width="50%" />

<form action="" method="post" enctype="multipart/form-data" name="cancel">
<? if($linked) { ?>
<input id="cancelthisEvt" name="cancelEvt" type="radio" value="this" checked="checked"/> This
<input id="cancelFutureEvt" name="cancelEvt" type="radio" value="future"/> This and future
<input id="cancelPreviousEvt" name="cancelEvt" type="radio" value="previous"/> This and previous<br />
<? } else { ?>
<input id="cancelthisEvt" name="cancelEvt" type="hidden" value="this"/>
<? } ?>
<input type="button" value="Cancel Event" onclick="cancelEvent(<?=$evtData['eventID']?>)" />
</form>

