<?
chdir("../");
include_once("include/header.php");

if(!$approveArr = $input->post->getRaw('approve')) {
	die("No items to deny");
}

foreach($approveArr as $currentID) {
	//approve an event (mark it as 1)
	$status_query = sprintf("update emp_current set approved='denied' where currentID=%d limit 1",$currentID);
	//
	($status_result = $db->query($status_query)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, "QUERY ERROR"));
}
?>