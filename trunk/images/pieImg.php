<? 

include "../checkAuth.php"; //gets $currentID 
if(!isset($currentID)) die("Invalid user access!!!");

include ( "/var/www/public_html/includes/jpgraph/jpgraph.php");
include ("/var/www/public_html/includes/jpgraph/jpgraph_pie.php");

//db connection $db
require_once("../config.inc.php");

if($chartID = $input->get->testInt('id')) {
	//id is set
} else {
	$chartID = $currentID;
}
if($today = $input->post->testInt('start')) {
	$today = $today;
} else {
	$today = strtotime("today 12:00 am");
}
if($tom = $input->post->testInt('end')) {
	$tomorrow = $tom;
} else {
	$tomorrow = strtotime("today 11:59 pm");
}

$end = (time() > $tomorrow) ? $tomorrow : time();

$select = "SELECT 	accounts.fName, 
					accounts.lName, 
					emp_current.timestamp, 
					emp_current.message, 
					emp_status.status_type, 
					emp_status.status_color
					FROM emp_status 
					INNER JOIN (accounts 
					INNER JOIN emp_current 
					ON accounts.acctID = emp_current.acctID) 
					ON emp_status.status_id = emp_current.statusID
					WHERE accounts.acctID=". $currentID
					." AND emp_current.timestamp<".$tomorrow
					." AND emp_current.timestamp>".$today
					." ORDER BY TIMESTAMP";
					
($selectResult = $db->query($select)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, $select));
$status_arr = array();
$prevTime = $today;
$prevStatus = "Out of Office - Not Working";
$status_arr[$prevStatus]['color']="#000000";
$name = "";
while ($row = $selectResult->fetch_assoc()) {
		$name = $row['fName'] . " " . $row['lName'];
		$status = $row['status_type'];
		$color = $row['status_color'];
		$message = $row['message'];
		$time = $row['timestamp'];
		$style = "background-color:".$color;
		$status_arr[$status]['numHits']++;
		$status_arr[$status]['color']=$color;
		$status_arr[$prevStatus]['timeSpent'] += ($row['timestamp'] - $prevTime);
		$prevTime = $row['timestamp'];
		$prevStatus = $status;
}
$status_arr[$prevStatus]['timeSpent'] += ($end - $prevTime);
$status_arr["Out of Office - Not Working"]['timeSpent'] += ($tomorrow - $end);
$statuses = array();
foreach($status_arr as $key=>$arr) {
	array_push($statuses,$key);
}
$data = array();
$colors = array();
foreach($statuses as $status) {
	//echo "<br />" .$status . " - " . $status_arr[$status]['color'];
	array_push($data,$status_arr[$status]['timeSpent']);
	array_push($colors,$status_arr[$status]['color']);
}
$graph  = new PieGraph (640,400);

$graph->title-> Set( $name . "\nDaily User Breakdown\n12am - 12pm");

$p1 = new PiePlot( $data);
$p1->SetSliceColors($colors); 
$p1->SetLegends($statuses); 
$graph->Add( $p1);
$graph->Stroke(); 
?>