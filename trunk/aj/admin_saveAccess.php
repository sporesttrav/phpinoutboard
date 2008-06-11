<? 
chdir("../");
include("include/header.php");

if($admin != "de_admin") die("Invalid user");

if(!($id = $input->post->testRegex('id','/^[0-9]+$/'))){
	die('Invalid values sent');
}
if(!($access = $input->post->testRegex('access',"/^(false|true)$/"))) {
	die('Invalid values sent');
}
if(!($attrib = $input->post->testRegex('attrib',"/^(edit|offcampus)$/"))) {
	die('Invalid values sent');
}
//setup the query
$val = ($access=='true') ? 1 : 0;
$query = sprintf("update de_emp set %s=%d where acctID=%d",$attrib,$val,$id);
($result = $db->query($query)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, $query));

echo $attrib ." " .$access;
?>

