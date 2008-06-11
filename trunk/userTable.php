<?
include_once("include/header.php");

if($input->post->keyExists('msg')) {
	$currentMessage = $input->post->getRaw("msg");
} else {
	//do nada
}

?>

<table id="container" border="0">
  <tr id="row">
    <td bgcolor="#CCCCCC" id="left"><strong>EMPLOYEE NAME</strong></td>
    <td bgcolor="#CCCCCC" id="middle"><strong>STATUS</strong></td>
  </tr>
  <?
	foreach($employees as $acctID=>$emp) {
		
	$whoID = intval($acctID);
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
		$name = $emp['name'];
		$message = "";
		$currentRow['timestamp'] = strtotime("today 6:00am");
	} else {
		$currentRow = $current->fetch_assoc();
		$status = htmlspecialchars($currentRow['status_type'],ENT_QUOTES);
		$color = htmlspecialchars($currentRow['status_color'],ENT_QUOTES);
		$status_id = $currentRow['status_id'];
		$currentStatId = $currentRow['currentID'];
		$message = htmlspecialchars($currentRow['message'],ENT_QUOTES);
		$name = $emp['name'];
	}
	$currentMessage = $message;
	$style = "background-color:".$color;
?>
  <tr id="row">
    <td nowrap="nowrap" id="left"><?=$name ?></td>
    <td id="middle" style="<?=$style ?>"><? if($admin=="de_admin" && $acctID!=$currentID) {
	//---------------------------------------------------------------------------------------------------
	
	?>
      <div id="status">
        <? include "support/currentStatusSelect.php"; ?>
      </div>
      <div style="display:inline;font-style:italic; font-size:9px">Last updated at
        <?=date("h:i",$currentRow['timestamp'])?>
      </div>
      <div id="message-text">
        <?=($message==$status) ? "" : $message?>
      </div>
      <? //end of admin area
	   //---------------------------------------------------------------------------------------------------
      } else { ?>
      <div id="status-text" style="display:inline" >
        <?=$status?>
      </div>
      -
      <div style="display:inline;font-style:italic; font-size:9px">Last updated at
        <?=date("h:i",$currentRow['timestamp'])?>
      </div>
      <div id="message-text">
        <?=($message==$status) ? "" : $message?>
      </div>
      <? } ?>
      <? if($currentID == $acctID) { ?>
      <input type="hidden" id="currentUserStatus" value="<?=$status_id?>" />
      <? } ?>
      </div></td>
  </tr>
  <?
}

$result->close();
?>
</table>
<h6> Last Updated
  <?=date('l dS \of F Y h:i:s A'); ?>
</h6>
