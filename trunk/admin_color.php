<? 

include("include/header.php");

if($admin != "de_admin") die("Invalid user");

//get $db connection

$status_query = "select status_id,status_type,status_color from emp_status order by status_id desc";
($status_result = $db->query($status_query)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $mysqli->error, $query));
?>
<? if(isset($currentID) && isset($admin) && $admin=="de_admin") { ?>
<script src="js/color_pick.js" language="javascript" type="text/javascript"></script>
<script language="javascript" type="text/javascript" src="js/admin.js"></script>
<? } ?>
<form action="">
  <table id="status_select">
    <tr>
      <th>Status Code</th>
      <th>Status Color</th>
    </tr>
    <?
while($status_row = $status_result->fetch_assoc()) {
?>
    <tr>
      <td><?=$status_row['status_type']?>
      </td>
      <td style="background-color:<?=$status_row['status_color']?>" onclick="cp.select(document.forms[0].color<?=$status_row['status_id']?>,'pick[<?=$status_row['status_id']?>]');return false;" id="pick[<?=$status_row['status_id']?>]"><input type="hidden" name="color<?=$status_row['status_id']?>" size="20" value="<?=$status_row['status_color']?>">&nbsp;
      </td>
    </tr>
    <? } ?>
  </table>
</form>
<div id="error"></div>

