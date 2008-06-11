<?
chdir("../");
include_once("include/header.php");
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
if($repeat = $input->post->testRegex('repeat','/^(day|week|month|year|none)$/')) {
} else {
	die("Invalid repeat set");
}
if($input->post->keyExists('until') && $until = strtotime($input->post->getRaw("until"))) {
} else {
	die("Invalid until date set");
}
if($status = $input->post->testInt('status')) {
} else {
	die("Invalid status set");
}
if($attstr = $input->post->testRegex('attend',"/^[0-9,]+$/")) {
} else {
	die("Invalid status set");
}
////---------------------------------------------------------------------
//make sure valid dates/times are setup
if($sTime == 0 || $eTime==0) {
	die("Invalid start or end time");
}
if($eTime <= $sTime) {
	die("Invalid End Day");
}
if($repeat!='none' && $until == 0) {
	die("Invalid until date set");
}
//------------------------------------------------------------------------
//make sure a valid status will be set
$status_query = sprintf("select status_id,status_type,status_color from emp_status where status_id=%d order by status_id asc",$status);
($status_result = $db->query($status_query)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, "QUERY ERROR"));
if($status_result->num_rows != 1) die('Invalid status set');
$status = $status_result->fetch_assoc();
//------------------------------------------------------------------------
//make sure valid users are found
$whos = explode(",",$attstr);
foreach($whos as $who) {
	$status_query = "select acctID from accounts where ";
	$status_query .= sprintf("acctID = %d",$who);
	($status_result = $db->query($status_query)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, "QUERY ERROR"));
	if($status_result->num_rows != 1) die('Invalid user set');
}
//----------------------------------------------------------
//insert intial row into the calendar
$iquery = sprintf("Insert into emp_calendar 
					(acctID,statusID,message,start,end,attendees) 
					values (%d,%d,'%s',%d,%d,'%s')",
					$currentID,
					$status['status_id'],
					$desc,
					$sTime,
					$eTime,
					$attstr);
($status_result = $db->query($iquery)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, "QUERY ERROR"));
$step = 1;
if($repeat != 'none') {
	$rootEvt = $db->insert_id;
	$currentTime = $sTime;
	$currentDur = $eTime - $sTime;
	while($currentTime < $until) {
		//first find the next day in the sequence
		switch($repeat) {
			case 'week':
				$currentTime = mktime(
					date("H",$currentTime), 
					date("i",$currentTime), 
					date("s",$currentTime), 
					date("m",$currentTime), 
					date("d",$currentTime)+7,   
					date("Y",$currentTime));
				break;
			case 'day':
				$currentTime = mktime(
					date("H",$currentTime), 
					date("i",$currentTime), 
					date("s",$currentTime), 
					date("m",$currentTime), 
					date("d",$currentTime)+1,   
					date("Y",$currentTime));
				break;
			case 'month':
				$currentTime = mktime(
					date("H",$currentTime), 
					date("i",$currentTime), 
					date("s",$currentTime), 
					date("m",$currentTime)+1, 
					date("d",$currentTime),   
					date("Y",$currentTime));
				break;
			case 'year':
				$currentTime = mktime(
					date("H",$currentTime), 
					date("i",$currentTime), 
					date("s",$currentTime), 
					date("m",$currentTime), 
					date("d",$currentTime),   
					date("Y",$currentTime)+1);
				break;
			default:
				die("Invalid repeating value");
		}	
		if(date("D",$currentTime)=="Sat" || date("D",$currentTime)=="Sun") {
			//do nothing invalid day
		} elseif($currentTime && $currentTime < $until) {
			//make sure what we have is a valid time
			$step++;
			$iquery = sprintf("Insert into emp_calendar 
					(acctID,statusID,message,start,end,attendees,linkEventId) 
					values (%d,%d,'%s',%d,%d,'%s',%d)",
					$currentID,
					$status['status_id'],
					$desc,
					$currentTime,
					($currentTime + $currentDur),
					$attstr,
					$rootEvt);
			($status_result = $db->query($iquery)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, "QUERY ERROR"));
			?>
            <!--<p>
            Start : <?=date("m/d/Y h:i a",$currentTime) ?><br />
            End : <?=date("m/d/Y h:i a",($currentTime + $currentDur)) ?><br />
            Repeats : <?=$rootEvt ?><br />
            Status : <?=$status['status_type'] ?><br />
            <?=$step?> Event(s) created
            </p>-->	
            <?
		} else {
			break;
		}
		//inserted one row of time
		
	} //end of while
} //end of inserting extra days
?>
<p>
Start : <?=date("m/d/Y h:i a",$sTime) ?><br />
End : <?=date("m/d/Y h:i a",$eTime) ?><br />
Repeats : <?=$repeat ?><br />
Status : <?=$status['status_type'] ?><br />
<?=$step?> Event(s) created
</p>
<a href="#" onclick="javascript:changeView('cal')">View Calendar</a>