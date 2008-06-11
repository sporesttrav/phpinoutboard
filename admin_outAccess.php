<? 

include("include/header.php");

if($admin != "de_admin") die("Invalid user");

include("include/pageHeader.php");

?>
<script type="text/javascript" language="javascript">
//-----------------------------------------------------------------------------------
function allowHome(id) {
	var cb = $div("offcampus"+id);
	var postData = "id="+id+"&access="+cb.checked+"&attrib=offcampus";
	microAjax('aj/admin_saveAccess.php', function(pageData){$div('error').innerHTML= pageData},postData)
}
//-----------------------------------------------------------------------------------
function allowEdit(id) {
	var cb = $div("edit"+id);
	var postData = "id="+id+"&access="+cb.checked+"&attrib=edit";
	microAjax('aj/admin_saveAccess.php', function(pageData){$div('error').innerHTML= pageData},postData)
}
</script>
<table id="container" border="0" cellpadding="0" cellspacing="0">
  <tr id="row">
  	<td>&nbsp;</td>
    <td>Off Campus Access</td>
     <td>Edit Time Access</td>
  </tr>
  <?
  //-------------------------------------------------------------------------------------------
  //retrieve each name and their current status
$query = "select accounts.acctID, fName, lName, edit, super, offcampus
			FROM (accounts INNER JOIN de_emp ON de_emp.acctID=accounts.acctID)
			ORDER BY lName";
($result = $db->query($query)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, "QUERY ERROR"));
///////
while ($row = $result->fetch_assoc()) {
	$name = htmlspecialchars($row["fName"],ENT_QUOTES). "&nbsp;" . htmlspecialchars($row["lName"],ENT_QUOTES);		
	$offcampus = ($row['offcampus']==1) ? "checked='checked'" : "";
	$edit = ($row['edit']==1) ? "checked='checked'" : "";
	//-------------------------------------------------------------------------------------------
?>
  <tr id="row">
    <td nowrap="nowrap" align="right" id="th-left"><?=$name ?></td>
    <td align="center" id="th-middle"><input type="checkbox" id="offcampus<?=$row['acctID']?>" onclick="allowHome(<?=$row['acctID']?>)" <?=$offcampus?>/></td>
    <td align="center" id="th-middle"><input type="checkbox" id="edit<?=$row['acctID']?>" onclick="allowEdit(<?=$row['acctID']?>)" <?=$edit?>/></td>
  </tr>
<? } ?>
</table>
<h4>A checked box allows access from home.</h4>
<div id="error">&nbsp;</div>
<? include("include/pageFooter.php"); ?>