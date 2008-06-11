<?
include_once("include/header.php");

//
$status_query = "select status_id,status_type,status_color from emp_status order by status_order asc";
($status_result = $db->query($status_query)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, "QUERY ERROR"));

$eID = (isset($evtSelectId)) ? $evtSelectId : "evt_status";

?>
<select id="<?=$eID?>">
 <?
while($status_row = $status_result->fetch_assoc()) { 
	$selected= "";
	if(isset($status_id) && $status_id==$status_row['status_id']) {
		$selected= "selected='selected'";
	} elseif(isset($evtData['statusID']) && $status_row['status_id']==$evtData['statusID']) {
		$selected= "selected='selected'";
	} elseif(!isset($status_id) && !isset($evtData['statusID']) && $status_row['status_type']=="Out of Office - Not Working") {
		$selected= "selected='selected'";
	}
?>
  <option style="background-color:<?=htmlspecialchars($status_row['status_color'],ENT_QUOTES)?>" value="<?=htmlspecialchars($status_row['status_id'],ENT_QUOTES)?>" <?=$selected?>>
  <?=htmlspecialchars($status_row['status_type'],ENT_QUOTES)?>
  </option>
  <? } ?>
</select>
