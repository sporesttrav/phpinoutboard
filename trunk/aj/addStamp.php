<?
chdir("../");
include_once("include/header.php");


if($input->post->keyExists('msg')) {
	$currentMessage = $input->post->getRaw("msg");
}

if(!$whoID = $input->post->testInt('uid')) {
	die("Invalid usr set");
}

if(!$evt_status = $input->post->testInt('status')) {
	die("Invalid evt_status set");
}

if(!($input->post->keyExists('sTime') && $sTime = strtotime($input->post->getRaw("sTime")))) {
	die("Invalid start set");
}
if(!($input->post->keyExists('eTime') && $eTime = strtotime($input->post->getRaw("eTime")))) {
	die("Invalid end set");
}

if($sTime >= $eTime) {
	die("Invalid times set");
}

//--------------------------------------------------------------------------------
//make sure adding to an available window
//check for events falling within the added time
$sQuery = sprintf("select currentID from emp_current where timestamp >= %d and end <= %d and not end= null and approved='approved' and deleted=0",$sTime,$eTime);
($sResult = $db->query($sQuery)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, "QUERY ERROR"));
if($sResult->num_rows > 1) {
	die("Invalid times - you are overlapping ".$sResult->num_rows." rows. Please delete 1 or more events before adding.");
}
//check for longer events (those that would overlap the added time)
$sQuery = sprintf("select currentID from emp_current where timestamp < %d and end > %d and not end=NULL and approved='approved' and deleted=0",$sTime,$eTime);
($sResult = $db->query($sQuery)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, "QUERY ERROR"));
if($sResult->num_rows > 1) {
	print_r($sResult->fetch_assoc());
	die("Invalid times - you are overlapping an event. Please edit the event first to create a time availability.");
}
//-------------------------------------
//insert into the system 
$insertQuery = sprintf("insert into emp_current (acctID,statusID,timestamp,end,message,who,added,approved) values (%d,%d,%d,%d,'%s',%d,%d,'%s')",
				$whoID,
				$evt_status,
				$sTime,
				$eTime,
				$currentMessage,
				$currentID,
				1,
				'pending'
				);
($insert = $db->query($insertQuery)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, "QUERY ERROR"));
	


?>