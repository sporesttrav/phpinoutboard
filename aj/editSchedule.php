<?
chdir("../");
include_once("include/header.php");

if(!($input->post->keyExists('id') && $evtID = $input->post->testInt('id'))) {
	die("You did not access this page properly");
}

	$evtquery = sprintf("SELECT 	emp_calendar.eventID, emp_calendar.message, emp_status.status_color, 
						emp_status.status_type, accounts.fName, accounts.lName,emp_calendar.statusID,
						emp_calendar.start,emp_calendar.end, emp_calendar.attendees, linkEventID
						FROM emp_status INNER JOIN 
						(emp_calendar INNER JOIN accounts ON emp_calendar.acctID = accounts.acctID) 
						ON emp_status.status_id = emp_calendar.statusID
						WHERE eventID = %d LIMIT 1",$evtID);
						
	($evt_result = $db->query($evtquery)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, "QUERY ERROR"));
	if($evt_result->num_rows != 1) {
		die("Invalid event");
	}
	$evtData = $evt_result->fetch_assoc();	
	
	$startHour = date("g",$evtData['start']);
	$startMin = date("i",$evtData['start']);
	$startAP = date("a",$evtData['start']);
	
	$endHour = date("g",$evtData['end']);
	$endMin = date("i",$evtData['end']);
	$endAP = date("a",$evtData['end']);
	
	$whos = explode(",",$evtData['attendees']);
	//var_dump($evtData);
	//------------------------------------------------------------------------
	//check for any subsequent events
	if(intval($evtData['linkEventID'])==0) {
		$status_query = sprintf("select * from emp_calendar where linkEventID=%d LIMIT 1",$evtID);
		($status_result = $db->query($status_query)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, $status_query));
		$linked = ($status_result->num_rows >=1);
		if(!$linked && ($evtRep=="previous" || $evtRep == "future")) die("Invalid repeating argument");
	} else {
		$linked = true;
	}
?>

<h3><strong>Edit Meeting</strong></h3>
<table width="300" border="0" cellspacing="1" cellpadding="1">
  <tr>
    <th align="left" valign="middle" nowrap="nowrap" scope="row">Starts : </th>
    <td align="left" valign="middle" nowrap="nowrap"><input name='start' type='text' maxlength='10' size='10' onfocus="cal.showCal(this);" id="start" value="<?=date("m/d/Y",$evtData['start'])?>" /></td>
    <td align="left" valign="middle" nowrap="nowrap"><select name="start_hour" id="start_hour">
        <option <?=($startHour == 6) ? "selected='selected'" : '';?> value="6">6</option>
        <option <?=($startHour == 7) ? "selected='selected'" : '';?> value="7">7</option>
        <option <?=($startHour == 8) ? "selected='selected'" : '';?> value="8">8</option>
        <option <?=($startHour == 9) ? "selected='selected'" : '';?> value="9">9</option>
        <option <?=($startHour == 10) ? "selected='selected'" : '';?> value="10">10</option>
        <option <?=($startHour == 11) ? "selected='selected'" : '';?> value="11">11</option>
        <option <?=($startHour == 12) ? "selected='selected'" : '';?> value="12">12</option>
        <option <?=($startHour == 1) ? "selected='selected'" : '';?> value="1">1</option>
        <option <?=($startHour == 2) ? "selected='selected'" : '';?> value="2">2</option>
        <option <?=($startHour == 3) ? "selected='selected'" : '';?> value="3">3</option>
        <option <?=($startHour == 4) ? "selected='selected'" : '';?> value="4">4</option>
        <option <?=($startHour == 5) ? "selected='selected'" : '';?> value="5">5</option>
      </select>
      :
      <select name="start_min" id="start_min">
        <option <?=($startMin == "00") ? "selected='selected'" : '';?> value="00">00</option>
        <option <?=($startMin == "15") ? "selected='selected'" : '';?> value="15">15</option>
        <option <?=($startMin == "30") ? "selected='selected'" : '';?> value="30">30</option>
        <option <?=($startMin == "45") ? "selected='selected'" : '';?> value="45">45</option>
      </select>
      <select name="start_ampm" id="start_ampm">
        <option <?=($startAP == "am") ? "selected='selected'" : '';?> value="am">am</option>
        <option <?=($startAP == "pm") ? "selected='selected'" : '';?> value="pm">pm</option>
      </select></td>
  </tr>
  <tr>
    <th align="left" valign="middle" nowrap="nowrap" scope="row">Ends :</th>
    <td align="left" valign="middle" nowrap="nowrap"><input name='end' type='text' maxlength='10' size='10' onfocus="cal.showCal(this);" id='end' value="<?=Date("m/d/Y",$evtData['end'])?>" /></td>
    <td align="left" valign="middle" nowrap="nowrap"><select name="end_hour" id="end_hour">
        <option <?=($endHour == 6) ? "selected='selected'" : '';?> value="6">6</option>
        <option <?=($endHour == 7) ? "selected='selected'" : '';?> value="7">7</option>
        <option <?=($endHour == 8) ? "selected='selected'" : '';?> value="8">8</option>
        <option <?=($endHour == 9) ? "selected='selected'" : '';?> value="9">9</option>
        <option <?=($endHour == 10) ? "selected='selected'" : '';?> value="10">10</option>
        <option <?=($endHour == 11) ? "selected='selected'" : '';?> value="11">11</option>
        <option <?=($endHour == 12) ? "selected='selected'" : '';?> value="12">12</option>
        <option <?=($endHour == 1) ? "selected='selected'" : '';?> value="1">1</option>
        <option <?=($endHour == 2) ? "selected='selected'" : '';?> value="2">2</option>
        <option <?=($endHour == 3) ? "selected='selected'" : '';?> value="3">3</option>
        <option <?=($endHour == 4) ? "selected='selected'" : '';?> value="4">4</option>
        <option <?=($endHour == 5) ? "selected='selected'" : '';?> value="5">5</option>
      </select>
      :
      <select name="end_min" id="end_min">
        <option <?=($endMin == "00") ? "selected='selected'" : '';?> value="00">00</option>
        <option <?=($endMin == "15") ? "selected='selected'" : '';?> value="15">15</option>
        <option <?=($endMin == "30") ? "selected='selected'" : '';?> value="30">30</option>
        <option <?=($endMin == "45") ? "selected='selected'" : '';?> value="45">45</option>
      </select>
      <select name="end_ampm" id="end_ampm">
        <option <?=($endAP == "am") ? "selected='selected'" : '';?> value="am">am</option>
        <option <?=($endAP == "pm") ? "selected='selected'" : '';?> value="pm">pm</option>
      </select></td>
  </tr>
  <tr>
    <th> Who: </th>
    <td><select name="emp_attend" multiple="multiple" id="emp_attend">
        <?
	//create employee select box	//create employee select box
	foreach($employees as $acctID=>$emp) {
		if(in_array($acctID,$whos)) {
			$selected= "selected='selected'";
		} else {
			$selected= "";
		}
		?>
        <option <?=$selected?> value="<?=$acctID?>"><?=$employees[$acctID]['fName'] . " " . $employees[$acctID]['lName'];?></option>
        <?
	}
	?>
      </select>
      <br />
      Hold CTRL for multiple </td>
  </tr>
  <tr>
    <th align="left" valign="middle" nowrap="nowrap" scope="row">Change <br />
      status to:</th>
    <td colspan="2" align="left" valign="middle" nowrap="nowrap"><? 
	if($admin=="de_admin" || 1==1) {
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
	<? } else echo "<strong>".$evtData['status_type']."</strong><input type='hidden' id='evt_status' value='".$evtData['statusID']."' />";
	?></td>
  </tr>
</table>
<p><strong>Description: </strong><br />
  <textarea id="schedule_notes"  name="schedule_notes" cols="50" rows="5"><?=$evtData['message']?>
</textarea>
</p>
<? if($linked) { ?>
<p>Edit which events : 
<input id="cancelthisEvt" name="cancelEvt" type="radio" value="this" checked="checked"/> This
<input id="cancelFutureEvt" name="cancelEvt" type="radio" value="future"/> This and future
<input id="cancelPreviousEvt" name="cancelEvt" type="radio" value="previous"/> This and previous</p>
<? } ?>
<p>
<input type="button" value="Schedule" onclick="javascript:editSchedule(<?=$evtData['eventID']?>)" />
<input type="button" value="Cancel" onclick="javascript:window.history.back()" />
</p>