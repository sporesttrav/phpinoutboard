<? 

include("include/header.php");

if($admin != "de_admin") die("Invalid user");

include("include/pageHeader.php");

$status_arr = array();
$status_query = "select * from emp_status order by status_order asc";
($status_result = $db->query($status_query)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, "QUERY ERROR"));

while($status = $status_result->fetch_assoc()){
	$status_arr[$status['status_id']] = $status;
}

?>
<script type="text/javascript" language="javascript">
//-----------------------------------------------------------------------------------
function allowStatus(uid,sid) {
	var cb = $div("cb["+uid+"]["+sid+"]");
	var postData = "sid="+sid+"&uid="+uid+"&access="+cb.checked;
	//alert(postData);
	microAjax('aj/admin_saveStatusAccess.php', function(pageData){$div('error').innerHTML= pageData},postData)
}
</script>

<div id="schedule" style="clear:both; position:relative; top:10px;">
  <table border="0" cellpadding="0" cellspacing="0" width="80%">
    <tr id="row">
      <td>&nbsp;</td>
      <?
		foreach($status_arr as $status_row) {?>
      <td id="th-middle" bgcolor="<?=$status_row['status_color']?>"><?=$status_row['status_type']?></td>
      <? }
	?>
    </tr>
    <?
  //-------------------------------------------------------------------------------------------
  //retrieve each name and their current status
foreach($employees as $acctID=>$emp) {
	
	$name = $employees[$acctID]['fName'] . " " . $employees[$acctID]['lName']	;	
	//-------------------------------------------------------------------------------------------
?>
    <tr id="row">
      <td nowrap="nowrap" align="right" id="th-left"><?=$name ?></td>
      <?
		foreach($status_arr as $status_row) {
			$status_query = sprintf("select * from emp_status_access 
										where acctID=%d and statusID=%d",
										$acctID,$status_row['status_id']);
			($status_result = $db->query($status_query)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", 
									$db->error, "QUERY ERROR"));
			$check = ($status_result->num_rows == 1) ? "checked='checked'" : "";
			 	
		?>
      <td align="center" width="150" id="th-middle" bgcolor="<?=$status_row['status_color']?>"><input type="checkbox" id="cb[<?=$acctID?>][<?=$status_row['status_id']?>]" onclick="allowStatus(<?=$acctID?>,<?=$status_row['status_id']?>)" <?=$check?>/></td>
      <? }
	?>
    </tr>
    <? } ?>
  </table>
  <h4>A checked box allows access from the status list on the &quot;Status View&quot; page</h4>
</div>
<div id="error">&nbsp;</div>
<? include("include/pageFooter.php"); ?>
