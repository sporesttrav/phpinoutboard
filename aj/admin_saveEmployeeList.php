<? 
chdir("../");
include("include/header.php");

if($admin != "de_admin") die("Invalid user");

if(!($id = $input->post->testRegex('id','/^[0-9]+$/'))){
	die('Invalid id value sent');
}
if(!($access = $input->post->testRegex('access',"/^(false|true)$/"))) {
	die('Invalid action values sent');
}
if($access=="false") {
	$action = "REMOVED ACCESS";
	$query = sprintf("DELETE FROM de_emp WHERE acctID = %d",$id);
} elseif($access=="true") {
	$action = "ACCESS GRANTED";
	$query = sprintf("INSERT INTO de_emp (acctID) values(%d)",$id);
}
($result = $db->query($query)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, $query));

echo $action;
?>

