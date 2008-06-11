<?
chdir("../");
include_once("include/header.php");
//
if($statusChange = $input->post->testInt('statusid')) {
	//do nada
} else {
	die("Invalid status change");
}
if($userChange = $input->post->testInt('userid')) {
	//do nada
} else {
	die("Invalid status change");
}
//
if(isset($currentID)) {
	$acct = $currentID;
} else {
	die('Invalid user access');
}
//make sure status id is a recognizable value
$status_query = sprintf("select status_id,status_type from emp_status where status_id=%d",$statusChange);
($status_result = $db->query($status_query)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, "QUERY ERROR"));
//make sure status id is valid
if($status_result->num_rows != 1) {
	die("Invalid status access");
}
//Get the current Status id
$statusRow = $status_result->fetch_assoc();

//make sure user id is a recognizable value
$status_query = sprintf("select acctID from accounts where acctID=%d",$userChange);
($status_result = $db->query($status_query)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, "QUERY ERROR"));
//make sure status id is valid
if($status_result->num_rows != 1) {
	die("Invalid user access");
}

//find a previous event to mark its end time
$status_query = sprintf("select currentID from emp_current where acctID=%d ORDER BY timestamp desc limit 1",$userChange);
($status_result = $db->query($status_query)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, "QUERY ERROR"));
//make sure status id is valid
if($status_result->num_rows == 1) {
	$curRow = $status_result->fetch_assoc();
	$curQuery = sprintf("update emp_current set end=%d where currentID=%d",time(),$curRow['currentID']);
	($status_result = $db->query($curQuery)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, "QUERY ERROR"));
}


//
$status_query = sprintf("insert into emp_current (acctID,statusID,timestamp,message,who) values (%d,%d,%d,'%s',%d)",
	$userChange,$statusChange,time(),$statusRow['status_type'],$currentID);
($status_result = $db->query($status_query)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, "QUERY ERROR"));
//
?>