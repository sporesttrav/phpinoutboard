<?
include_once("include/header.php");
//
$query = "select acctID, fName, lName
				FROM accounts
				WHERE acctID = " . $currentID ."
				ORDER BY lName";
($result = $db->query($query)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, "QUERY ERROR"));
 $row = $result->fetch_assoc();
	$whoID = intval($row['acctID']);
	$query = "select currentID,status_id,status_type,status_color,message,timestamp 
				FROM emp_status 
				INNER JOIN emp_current ON emp_status.status_id = emp_current.statusID
				WHERE emp_current.acctID=".$whoID."
				AND timestamp > " . strtotime("today 6:00 am") . "
				AND approved='approved'
				ORDER BY TIMESTAMP DESC LIMIT 1";
	($current = $db->query($query)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, "QUERY ERROR"));
	if($current->num_rows<1) {
		$status = "Out of Office - Not Working";
		$color = "#999999";
		unset($message);	
		unset($status_id);
		$name = htmlspecialchars($row["fName"],ENT_QUOTES). "&nbsp;" . htmlspecialchars($row["lName"],ENT_QUOTES);
		$message = "";
	} else {
		$currentRow = $current->fetch_assoc();
		$status = htmlspecialchars($currentRow['status_type'],ENT_QUOTES);
		$color = htmlspecialchars($currentRow['status_color'],ENT_QUOTES);
		$status_id = $currentRow['status_id'];
		$currentStatId = $currentRow['currentID'];
		$message = htmlspecialchars($currentRow['message'],ENT_QUOTES);
		$name = htmlspecialchars($row["fName"],ENT_QUOTES). " &nbsp;" . htmlspecialchars($row["lName"],ENT_QUOTES);
	}
	$currentMessage = $message;
	$style = "background-color:".$color;
?>

<div id="status" style="vertical-align:middle;<?=$style?>"> <span id="middle">Current Status : </span>
  <? include "support/currentStatusSelect.php"; ?>
  <input type="text" style="display:inline;height:16px;font-size:12px;width:150px;" id="status_message[<?=$currentID?>]" onkeypress="javascript:return checkStatus(event,<?=$whoID?>,this)" value="<?=$currentMessage ?>" />
  <input value="Go" type="button" onclick="javascript:setUserMessage(<?=$whoID?>)" />
</div>
