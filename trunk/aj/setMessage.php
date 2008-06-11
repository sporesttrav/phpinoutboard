<?
chdir("../");
include_once("include/header.php");

if($input->post->keyExists('msg')) {
	$currentMessage = $input->post->getRaw("msg");
} else {
	die("Invalid msg set");
}
if($input->post->keyExists('userid')) {
	$whoID = $input->post->getInt("userid");
	if($whoID != $currentID && $admin != "de_admin") {
		die("Invalid usr access");
	}
} else {
	die("Invalid usr set");
}

//update to latest message then display 
$squery = sprintf("select currentID,statusID from emp_current where acctID=%d ORDER by timestamp desc limit 1",$whoID);
($update = $db->query($squery)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, "QUERY ERROR"));
if($update->num_rows ==1) {
	$row = $update->fetch_assoc();
	
//find a previous event to mark its end time
$status_query = sprintf("select currentID from emp_current where acctID=%d ORDER BY timestamp desc limit 1",$whoID);
($status_result = $db->query($status_query)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, "QUERY ERROR"));
//make sure status id is valid
if($status_result->num_rows == 1) {
	$curRow = $status_result->fetch_assoc();
	$curQuery = sprintf("update emp_current set end=%d where currentID=%d",time(),$curRow['currentID']);
	($status_result = $db->query($curQuery)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, "QUERY ERROR"));
}

	//
	$status_query = sprintf("insert into emp_current (acctID,statusID,timestamp,message) values (%d,%d,%d,'%s')",
		$whoID,$row['statusID'],time(),$currentMessage);
	($status_result = $db->query($status_query)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, "QUERY ERROR"));
//
	}

?>