<?
include_once("include/header.php");
?>
<? include("include/pageHeader.php"); ?>
<script language="javascript" type="text/javascript" src="js/schedule.js"></script>
<script language="javascript" type="text/javascript" src="js/datechooser.js"></script>
<style type="text/css">
@import "css/datechooser.css";
</style>
<div id="schedule" style="clear:both; position:relative; top:10px;">
<h3><strong>Schedule Meeting</strong></h3>
<table width="300" border="0" cellspacing="1" cellpadding="1">
  <tr>
    <th align="left" valign="middle" nowrap="nowrap" scope="row">Starts : </th>
    <td align="left" valign="middle" nowrap="nowrap"><input name='start' type='text' maxlength='10' size='10' onfocus="cal.showCal(this);" id="start" value="<?=Date("m/d/Y")?>" /></td>
    <td align="left" valign="middle" nowrap="nowrap"><select name="start_hour" id="start_hour">
        <option selected="selected" value="6">6</option>
        <option value="7">7</option>
        <option value="8">8</option>
        <option value="9">9</option>
        <option value="10">10</option>
        <option value="11">11</option>
        <option value="12">12</option>
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
      </select>
      :
      <select name="start_min" id="start_min">
        <option selected="selected" value="00">00</option>
        <option value="15">15</option>
        <option value="30">30</option>
        <option value="45">45</option>
      </select>
      <select name="start_ampm" id="start_ampm">
        <option selected="selected" value="am">am</option>
        <option value="pm">pm</option>
      </select></td>
  </tr>
  <tr>
    <th align="left" valign="middle" nowrap="nowrap" scope="row">Ends :</th>
    <td align="left" valign="middle" nowrap="nowrap"><input name='end' type='text' maxlength='10' size='10' onfocus="cal.showCal(this);" id='end' value="<?=Date("m/d/Y")?>" /></td>
    <td align="left" valign="middle" nowrap="nowrap"><select name="end_hour" id="end_hour">
        <option selected="selected" value="6">6</option>
        <option value="7">7</option>
        <option value="8">8</option>
        <option value="9">9</option>
        <option value="10">10</option>
        <option value="11">11</option>
        <option value="12">12</option>
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
      </select>
      :
      <select name="end_min" id="end_min">
        <option selected="selected" value="00">00</option>
        <option value="15">15</option>
        <option value="30">30</option>
        <option value="45">45</option>
      </select>
      <select name="end_ampm" id="end_ampm">
        <option selected="selected" value="am">am</option>
        <option value="pm">pm</option>
      </select></td>
  </tr>
  <tr>
    <th> Who: </th>
    <td><select id="emp_attend" multiple="multiple">
        <?
	//create employee select box
	foreach($employees as $acctID=>$emp) {
		if($acctID == $currentID) {
			$selected= "selected='selected'";
		} else {
			$selected= "";
		}
		?>
        <option <?=$selected?> value="<?=$acctID?>"><?=$emp['fName'] . " " . $emp['lName'];?></option>
        <?
	}
	?>
      </select>
      <br />
      Hold CTRL for multiple </td>
  </tr>
  <tr>
    <th align="left" valign="middle" nowrap="nowrap" scope="row">Repeats :<a class="tt" href="#" style="<?=$style?>"><img border="0" src="images/help-browser.gif" alt="help" /><span class="tooltip" id="tooltip">This will make a copy of the event<br />
for the same duration between <br />
the start and end times from <br />
the specified start time to<br />
the specified &quot;Until&quot; date.</span></a></th>
    <td colspan="2" align="left" valign="middle" nowrap="nowrap"><select name="repeat" id="repeat" onchange="selectUntil()">
        <option value="none" selected="selected">None</option>
        <option value="day">Daily (M-F)</option>
        <option value="week">Weekly</option>
        <option value="month">Monthly</option>
        <option value="year">Yearly</option>
      </select>
      
      <div id="untilDiv" style="display:inline;visibility:hidden"><strong>&nbsp;&nbsp;&nbsp;Until : </strong>
        <input name='until' type='text' maxlength='10' size='10' onfocus="cal.showCal(this);" id='until' value="<?=Date("m/d/Y",strtotime("+1 year"))?>" />
      </div>
    </td>
  </tr>
  <tr>
    <th align="left" valign="middle" nowrap="nowrap" scope="row">Change <br />
      status to:</th>
    <td colspan="2" align="left" valign="middle" nowrap="nowrap"><? 
	if($admin=="de_admin" || 1==1) {
		//display status
		//display status
		$status_query = sprintf("SELECT status_type, status_id, status_color
					FROM emp_calendar_access INNER JOIN emp_status ON emp_calendar_access.statusID = emp_status.status_id
					WHERE emp_calendar_access.acctID=%d
					ORDER BY emp_status.status_order",$currentID);
		($status_result = $db->query($status_query)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, "QUERY ERROR"));
		?>
		
		<select id="evt_status">
		 <?
		while($status_row = $status_result->fetch_assoc()) { 
			$selected= "";
			if(isset($status_id) && $status_id==$status_row['status_id']) {
				$selected= "selected='selected'";
			} elseif(isset($evtData['statusID']) && $status_row['status_id']==$evtData['statusID']) {
				$selected= "selected='selected'";
			} elseif(!isset($status_id) && !isset($evtData['statusID']) && $status_row['status_type']=="Meeting") {
				$selected= "selected='selected'";
			}
		?>
		  <option style="background-color:<?=htmlspecialchars($status_row['status_color'],ENT_QUOTES)?>" value="<?=htmlspecialchars($status_row['status_id'],ENT_QUOTES)?>" <?=$selected?>>
		  <?=htmlspecialchars($status_row['status_type'],ENT_QUOTES)?>
		  </option>
		  <? } ?>
		</select>
	<? } else echo "<strong>Meeting</strong><input type='hidden' id='evt_status' value='8' />";
	?></td>
  </tr>
</table>
<p><strong>Description: </strong><br />
  <textarea id="schedule_notes"  name="schedule_notes" cols="50" rows="5"></textarea>
</p>
<input type="button" value="Schedule" onclick="javascript:processSchedule()" />
<input type="button" value="Cancel" onclick="javascript:window.history.back()" />
</div>
<? include("include/pageFooter.php"); ?>