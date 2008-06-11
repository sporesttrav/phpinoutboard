<?
chdir("../");
include_once("include/header.php");

if(!$stampID=$input->post->testInt('id')) {
	die("Invalid msg set");
}
	
//delete an event - mark it as deleted and not approved at least
$status_query = sprintf("update emp_current set added=0,edited=NULL, deleted=1, approved='pending' where currentID=%d limit 1",$stampID);
($status_result = $db->query($status_query)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, "QUERY ERROR"));


?>