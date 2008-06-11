<?

if(!isset($whoID)) die("Invalid access!!!");

//
$status_query = sprintf("SELECT status_type, status_id, status_color
					FROM emp_status_access INNER JOIN emp_status ON emp_status_access.statusID = emp_status.status_id
					WHERE emp_status_access.acctID=%d
					ORDER BY emp_status.status_order",$currentID);
//$status_query = "select status_id,status_type,status_color from emp_status order by status_order asc";
($status_result = $db->query($status_query)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, "QUERY ERROR"));
if($whoID==$currentID) {
	$name = 'user_select';
} else {
	$name = 'status_select';
}
?>

<select name="<?=$name?>" id="<?=$name?>" onclick="javascript:clearTimeout(currentTimeout)" onchange="javascript:statusChange(<?=$whoID?>,this)">
 <?
while($status_row = $status_result->fetch_assoc()) { 
	$selected="";
	if(isset($status_id) && $status_id==$status_row['status_id']) {
		$selected= "selected='selected'";
	} elseif(!isset($status_id) && $status_row['status_type']=="Out of Office - Not Working") {
		$selected= "selected='selected'";
	}
?>
  <option style="background-color:<?=htmlspecialchars($status_row['status_color'],ENT_QUOTES)?>" value="<?=htmlspecialchars($status_row['status_id'],ENT_QUOTES)?>" <?=$selected?>>
  <?=htmlspecialchars($status_row['status_type'],ENT_QUOTES)?>
  </option>
  <? } ?>
</select>
