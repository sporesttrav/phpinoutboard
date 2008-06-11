<? 
chdir("../");
include("includes/header.php");

if($admin != "de_admin") die("Invalid user");

if($statusChange = $input->get->testInt('statusid')) {
	//do nada
} else {
	die("Invalid status change");
}

if($newcolor = $input->get->testRegex('color',"(#?([A-Fa-f0-9]){3}(([A-Fa-f0-9]){3})?)")) {
	$newcolor = "#" . $newcolor;
} else {
	die("Invalid color change");
}

//make sure status id is a recognizable value
$status_query = "select status_id,status_type from emp_status where status_id=".$statusChange;
($status_result = $db->query($status_query)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, $status_query));
if($status_result->num_rows == 1) {
} else {
	die("Invalid status id");
}
$statusRow = $status_result->fetch_assoc();
//
$status_query = sprintf("update emp_status set status_color='%s' where status_id=%d",$newcolor,$statusChange);
($status_result = $db->query($status_query)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, $status_query));
//all done
?>
Color Saved