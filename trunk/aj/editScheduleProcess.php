<?
chdir("../");
include_once("include/header.php");
if($input->post->keyExists('id') && $evtID = $input->post->testInt("id")) {
} else {
	die("Invalid event id");
}
if($input->post->keyExists('start') && $sTime = strtotime($input->post->getRaw("start"))) {
} else {
	die("Invalid start set");
}
if($input->post->keyExists('end') && $eTime = strtotime($input->post->getRaw("end"))) {
} else {
	die("Invalid end set");
}
if($input->post->keyExists('desc')) {
	$desc = $input->post->getRaw("desc");
} else {
	die("Invalid desc set");
}
if($status = $input->post->testInt('status')) {
} else {
	die("Invalid status set");
}
if($attstr = $input->post->testRegex('attend',"/^[0-9,]+$/")) {
} else {
	die("Invalid status set");
}
if($input->post->keyExists('rep') && $evtRep = $input->post->testRegex('rep','/^(this|future|previous)$/')) {
	//okay there doesn't need to be repetition necessarily...
} else {
	$evtRep = "this";
}
////---------------------------------------------------------------------
//make sure valid dates/times are setup
if($sTime == 0 || $eTime==0) {
	die("Invalid start or end time");
}
if($eTime <= $sTime) {
	die("Invalid End Day");
}
//------------------------------------------------------------------------
//make sure a valid event is being edited will be set
$status_query = sprintf("select * from emp_calendar where eventID=%d",$evtID);
($status_result = $db->query($status_query)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, $status_query));
if($status_result->num_rows != 1) die('Invalid event');
$evtData = $status_result->fetch_assoc();
//------------------------------------------------------------------------
//check for any subsequent events
//var_dump($evtData);
$linkTo = false;
$linkPrevFuture = false;
if(intval($evtData['linkEventID'])==0) {
	$status_query = sprintf("select * from emp_calendar where linkEventID=%d LIMIT 1",$evtID);
	($status_result = $db->query($status_query)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, $status_query));
	$linkTo = ($status_result->num_rows >=1);
	if(!$linkTo && ($evtRep=="previous" || $evtRep == "future")) die("Invalid repeating argument");
} else {
	$linkPrevFuture = true;
}
//------------------------------------------------------------------------
//make sure a valid status will be set
$status_query = sprintf("select status_id,status_type,status_color from emp_status where status_id=%d order by status_id asc",$status);
($status_result = $db->query($status_query)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error,  $status_query));
if($status_result->num_rows != 1) die('Invalid status set');
$status = $status_result->fetch_assoc();
//------------------------------------------------------------------------
//make sure valid users are found
$whos = explode(",",$attstr);
foreach($whos as $who) {
	$status_query = "select acctID from accounts where ";
	$status_query .= sprintf("acctID = %d",$who);
	($status_result = $db->query($status_query)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error,  $status_query));
	if($status_result->num_rows != 1) die('Invalid user set');
}
//----------------------------------------------------------
//insert intial row into the calendar
$change = false;
$iquery = array();
if($evtData['start']!=$sTime) {
	$change = true;
	$iquery[] = sprintf("start = %d ",$sTime);
}
if($evtData['end']!=$eTime) {
	$change = true;
	$iquery[] = sprintf("end = %d ",$eTime);
}
if($evtData['attendees']!=$attstr) {
	$change = true;
	$iquery[] = sprintf("attendees = '%s' ",$attstr);
}
if($evtData['status']!=$status['statusid']) {
	$change = true;
	$iquery[] = sprintf("status = %d ",$status['statusid']);
}
if($evtData['message']!=$desc) {
	$change = true;
	$iquery[] = sprintf("message = '%s' ",$desc);
}
if($change) {

	switch($evtRep) {
		//------------------------------------------------------------------------------
		case "previous":
			//make sure only overwrite events not occured yet
			
			//step 1 break event from the repeating ones
			$evtquery = sprintf("update emp_calendar set linkEventID=NULL, %s 
								where eventID=%d AND start > %d AND start <= %d",
								implode(",",$iquery),
								$evtData['eventID'],
								time(),$sTime);
			($status_result = $db->query($evtquery)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, $evtquery));
			
			//step 2 make sure all previous ones are now linked to it
			$evtquery = sprintf("update emp_calendar set linkEventID=%d, %s 
								where (linkEventID=%d || eventID=%d) AND start > %d AND start <= %d",
								$evtData['eventID'],implode(",",$iquery),
								$evtData['linkEventID'],$evtData['linkEventID'],
								time(),$sTime);
			($status_result = $db->query($evtquery)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, $evtquery));
			
			//step 3 - make sure all future events aren't linked back to the previous event (if there are any)
			$status_query = sprintf("select eventID from emp_calendar 
								where linkEventID=%d and start > %d ORDER BY eventID LIMIT 1",
								$evtData['eventID'],$evtData['start']);
								
			($status_result = $db->query($evtquery)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, $evtquery));
			if($status_result->num_rows == 1) {
				//if there is even 1 linked row then proceed
				$link = $status_result->fetch_assoc();
				$replacequery = sprintf("update emp_calendar set linkEventID = %d where linkEventID = %d and start > %d",$link['eventID'],$evtData['eventID'],$evtData['start']);
				($rep_result = $db->query($replacequery)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, $replacequery));
			}
			
			//step 4 - make sure all future events that are part of the same chain are linked to the next proper one
			$status_query = sprintf("select eventID from emp_calendar 
								where linkEventID=%d and start > %d ORDER BY eventID LIMIT 1",
								$evtData['linkEventID'],$evtData['start']);
								
			($status_result = $db->query($evtquery)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, $evtquery));
			if($status_result->num_rows == 1) {
				//if there is even 1 linked row then proceed
				$link = $status_result->fetch_assoc();
				$replacequery = sprintf("update emp_calendar set linkEventID = NULL where eventID = %d",$link['eventID'],$evtData['start']);
				($rep_result = $db->query($replacequery)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, $replacequery));
				$replacequery = sprintf("update emp_calendar set linkEventID = %d where linkEventID = %d and start > %d",$link['eventID'],$evtData['linkEventID'],$evtData['start']);
				($rep_result = $db->query($replacequery)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, $replacequery));
			}
			
			break;
		//------------------------------------------------------------------------------
		case "future":
			//step 1 unlink this event from the rest of the chain
			$evtquery = sprintf("update emp_calendar set linkEventID=NULL, %s 
								where eventID=%d AND start > %d AND start >= %d",
								implode(",",$iquery),
								$evtData['eventID'],
								time(),$sTime);
			($status_result = $db->query($evtquery)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, $evtquery));
			
			//step 2 update all subsequent events from the chan
			$evtquery = sprintf("update emp_calendar set linkEventID=%d, %s 
								where (linkEventID=%d || linkEventID=%d) AND start > %d AND start >= %d",
								$evtData['eventID'],implode(",",$iquery),
								$evtData['linkEventID'],$evtData['eventID'],
								time(),$sTime);
			($status_result = $db->query($evtquery)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, $evtquery));
			
			break;
		//------------------------------------------------------------------------------
		case "this" :
			//step 1 unlink this event from the rest of the chain
			//removed an AND start >= $sTime
			$evtquery = sprintf("update emp_calendar set linkEventID=NULL, %s 
								where eventID=%d AND start > %d",
								implode(",",$iquery),
								$evtData['eventID'],
								time());
			($status_result = $db->query($evtquery)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, $evtquery));
			//echo $evtquery;
			//step 2 - make sure all events that are part of the same chain are linked to the next proper one
			$status_query = sprintf("select eventID from emp_calendar 
								where linkEventID=%d and start > %d ORDER BY start LIMIT 1",
								$evtData['linkEventID'],time());
								
			($status_result = $db->query($evtquery)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, $evtquery));
			if($status_result->num_rows == 1) {
				//if there is even 1 linked row then proceed
				$link = $status_result->fetch_assoc();
				$replacequery = sprintf("update emp_calendar set linkEventID = NULL where eventID = %d",$link['eventID'],$evtData['start']);
				($rep_result = $db->query($replacequery)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, $replacequery));
				$replacequery = sprintf("update emp_calendar set linkEventID = %d where linkEventID = %d and start > %d",$link['eventID'],$evtData['linkEventID'],$evtData['start']);
				($rep_result = $db->query($replacequery)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, $replacequery));
			}
			break;
			//------------------------------------------------------------------------------
	} //end switch


	
}
?>

Start : <?=date("m/d/Y h:i a",$sTime) ?>
End : <?=date("m/d/Y h:i a",$eTime) ?>
Status : <?=$status['status_type'] ?>
<?=$step ?> Event(s) edited