<? 
//gives a DB like via $db
require_once(dirname(__FILE__)."/../config.inc.php");

//gets $currentID
require_once(dirname(__FILE__)."/../include/checkAuth.php");

if(!isset($currentID)) die("Invalid user access!!");

//get list of employes
//includes norma and all non inactive DE Folkes
$query = "select accounts.acctID, fName, lName
			FROM accounts INNER JOIN de_emp ON de_emp.acctID=accounts.acctID
			ORDER BY lName";
	($result = $db->query($query)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, "QUERY ERROR"));
	while ($row = $result->fetch_assoc()) {
		$employees[$row['acctID']] = $row;
		$employees[$row['acctID']]['name'] =  htmlspecialchars($row["fName"],ENT_QUOTES). " &nbsp;" . htmlspecialchars($row["lName"],ENT_QUOTES);;
	}
	
	//from now on use <?=$employees[$acctID]['fName'] . " " . $employees[$acctID]['lName'];
	
?>