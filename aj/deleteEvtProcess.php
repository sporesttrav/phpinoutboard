<?
chdir("../");
include_once("include/header.php");
if(!($input->post->keyExists('id') && $evtID = $input->post->testInt('id'))) {
	die("Invalid key entry" . $input->post->getRaw('id') );
}

if(!($input->post->keyExists('rep') && $evtRep = $input->post->testRegex('rep','/^(this|future|previous)$/'))) {
	die("Invalid request made");
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
if(intval($evtData['linkEventID'])==0) {
	$status_query = sprintf("select * from emp_calendar where linkEventID=%d LIMIT 1",$evtID);
	($status_result = $db->query($status_query)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, $status_query));
	$linked = ($status_result->num_rows >=1);
	if(!$linked && ($evtRep=="previous" || $evtRep == "future")) die("Invalid repeating argument");
} else {
	$linked = true;
}
//-------------------------------------------------------------------------
$iquery = array("deleted=1");
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
			$evtquery = sprintf("update emp_calendar set linkEventID=NULL, %s 
								where eventID=%d AND start > %d AND start >= %d",
								implode(",",$iquery),
								$evtData['eventID'],
								time(),$sTime);
			($status_result = $db->query($evtquery)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, $evtquery));
			
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
?>
<p>
Start : <?=date("m/d/Y h:i a",$evtData['start']) ?><br />
End : <?=date("m/d/Y h:i a",$evtData['end']) ?><br />
<?=$step ?> Event(s) cancelled
<?=$evtRep . $change?>
<?=$evtquery?>

</p>
<a href="#" onclick="javascript:changeView('cal')">View Calendar</a>