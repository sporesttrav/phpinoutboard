<? 
chdir("../");
include("include/header.php");

if($admin != "de_admin") die("Invalid user");
foreach ($input->post->_source as $a => $v)
	$postQueryString.= ($postQueryString=="" ? "" : "&" ) ."$a=$v";
if(!($uid = $input->post->testRegex('uid','/^[0-9]+$/'))){
	die('Invalid values sent');
}
if(!($sid = $input->post->testRegex('sid','/^[0-9]+$/'))){
	die('Invalid values sent');
}
if(!($access = $input->post->testRegex('access',"/^(false|true)$/"))) {
	die('Invalid values sent');
}
if($access=="false") {
	$action = "REMOVED ACCESS";
	$query = sprintf("DELETE FROM emp_calendar_access WHERE acctID = %d && statusID = %d LIMIT 1",$uid,$sid);
} else {
	$action = "ACCESS GRANTED";
	$query = sprintf("INSERT INTO emp_calendar_access (acctID,statusID) values(%d,%d)",$uid,$sid);
}
($result = $db->query($query)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, $query));

echo $action;
?>

