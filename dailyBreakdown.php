<?
include_once("include/header.php");

ob_start();

include("include/pageHeader.php"); ?>
<script language="javascript" type="text/javascript" src="js/datechooser.js"></script>
<style type="text/css">
@import "css/datechooser.css";
</style>
<script language="javascript" type="text/javascript">
//----------------------------------------------------------------------------
function updateDay(view) {
	var dataString = "day="+$div('cal_day').value;
	if($div('emp_select')) {
		dataString += "&user="+$div('emp_select').value
	}
	switch(view) {
		case "chart" :
			window.location= 'dailyBreakdown.php?'+dataString;
			break;
		default:
			microAjax('userLogTable.php', function(pageData){$div('dailytable').innerHTML= pageData},dataString);		
		break;
	}
}
function addStamp() {
	var dataString = "";
	var sTime = $div("addStart").value + " " + $div("addStartTime").value +$div("addStartAMPM").value;
	var eTime = $div("addEnd").value + " " + $div("addEndTime").value +$div("addEndAMPM").value;
	var uid = $div('addUID').value;
	var status = $div('addEventSel').value;
	var msg = $div('addMessage').value;
	dataString = "uid=" + uid;
	dataString += "&sTime=" +sTime;
	dataString += "&eTime=" +eTime;
	dataString += "&status=" +status;
	dataString += "&msg=" +msg;
	
	microAjax('aj/addStamp.php', function(pageData){
		if(pageData) {
			alert(pageData);
		} else {
			updateDay('chart')
		}
	},dataString);
}
function deleteStamp(id) {
	if(confirm("Are you sure you want to delete this stamp? Press OK to continue")) {
		microAjax('aj/deleteStamp.php', function(pageData){
			if(pageData) {
				alert(pageData);
			} else {
				updateDay('chart')
			}
		},"id="+id);
	}
}
function denyStamp() {
	var dataString="";
	var myForm = getAllFormElements($div('approveForm'));
	for (var i = 0; i < myForm.length; i++)
		dataString += myForm[i].id + "=" + myForm[i].value + "&";
	//alert(dataString);
	microAjax('aj/denyStamp.php', function(pageData){
		if(pageData) {
			alert(pageData);
		} else {
			updateDay('chart')
		}
	},dataString);
}
function approveStamp() {
	var dataString="";
	var myForm = getAllFormElements($div('approveForm'));
	for (var i = 0; i < myForm.length; i++)
		dataString += myForm[i].id + "=" + myForm[i].value + "&";
	//alert(dataString);
	microAjax('aj/approveStamp.php', function(pageData){
		if(pageData) {
			alert(pageData);
		} else {
			updateDay('chart')
		}
	},dataString);
}
</script>
<style type="text/css">
#bd-table {
}
.bd-row {
	display: table-row;
	border-style:dotted;
}

</style>
<div id="dailytable" style="clear:both; position:relative; top:20px;">
  <?
//-------------------------------------------------------------------------------------------
//the base variables
$colors = array();
$min = 15;
$slices = $min * 60;
if($today = $input->get->getRaw('day')) {
	list($month,$day,$year) = explode("/", $today);
	$today = mktime(0,0,0,$month,$day,$year);
	$tomorrow = mktime(23,0,0,$month,$day,$year);
} else {
	$today = strtotime("today 12:00 am");
	$tomorrow = strtotime("today 11:59 pm");
}
if($chartID = $input->get->testInt('user')) {
	//chart set to user
} else {
	$chartID = $currentID;
}
$end = (time() > $tomorrow) ? $tomorrow : time();
?>
  <input name='cal_day' type='text' maxlength='10' size='10' onfocus="cal.showCal(this);" id="cal_day" value="<?=Date("m/d/Y",$today)?>" />
  <? if($admin=="de_admin") { ?>
  <select id="emp_select">
    <?
	foreach($employees as $acctID=>$emp) {
		if($acctID == $chartID) {
			$selected= "selected='selected'";
		} else {
			$selected= "";
		}
		?>
    <option <?=$selected?> value="<?=$acctID?>">
    <?=$emp['fName'] . " " . $emp['lName'];?>
    </option>
    <?
	}
	?>
  </select>
  <? } ?>
  <input type="button" onclick="javascript:updateDay('chart')" value="Update" />
  </p>
  <hr />
  <h2>Daily Breakdown for
    <?=$employees[$chartID]['name']?>
    on
    <?=date("m/d/Y",$today);?>
  </h2>
  <table id="bd-table" width="800">
    <tr id="bd-row">
      <td id="middle"><strong>STATUS</strong></td>
      <td id="middle"><strong>MESSAGE</strong></td>
      <td id="right"><strong>START</strong></td>
      <td id="right"><strong>END</strong></td>
      <td id="right" colspan="2"><strong>WHO</strong></td>
    </tr>
    <?
//list out daily activities

$select = "SELECT currentid, who, status_id,status_type, status_color, message, timestamp AS
							start, end, added, edited, deleted, approved
							FROM emp_status
							INNER JOIN emp_current ON emp_status.status_id = emp_current.statusID
							WHERE timestamp >=" . $today ."
							AND timestamp <=" . $tomorrow . "
							AND acctID =" . $chartID."
							AND NOT approved='denied'
							ORDER BY timestamp ASC";
					
($selectResult = $db->query($select)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, $select));
$status_arr = array();
$prevTime = $today;
$prevStatus = "No Status";
while ($row = $selectResult->fetch_assoc()) {
		$id = $row['currentid'];
		$status = $row['status_type'];
		$color = $row['status_color'];
		$message = $row['message'];
		$starttime = $row['start'];
		$approved = ($row['approved']=='approved');
		$denied = ($row['approved']=='denied');
		$pending = ($row['approved']=='pending');
		$deleted = ($row['deleted']==1);
		$edited = ($row['edited']==1);
		$add = ($row['added']==1);
		if(!$row['end']) {
			//the end time is blank find the next start time and use it?
			$endQuery = "select timestamp FROM emp_current
							WHERE timestamp >" . $starttime ."
							AND acctID =" . $chartID."
							AND approved = 1
							ORDER BY timestamp ASC limit 1";
			($endResult = $db->query($endQuery)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, $endQuery));
			if($endResult->num_rows==1) {
				$endRow = $endResult->fetch_assoc();
				$endtime = $endRow['timestamp'];
				//set the end time column for future lookups
				//this takes a little burden off the DB
				//only works the first time someone looks at a particular date
				$endUpdate = "UPDATE emp_current SET end=". $endRow['timestamp']."
							WHERE currentID=".$id."
							LIMIT 1";
				($endResult = $db->query($endUpdate)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, $endUpdate));
			
			} else {
				$endtime = time();
			}
		} else {
			$endtime = $row['end'];
			
		}
		$who = ($row['who']) ? $employees[$row['who']]['name'] :  $employees[$chartID]['name'];
		$style = "background-color:".$color.";";
		if(!is_array($status_arr[$status])) {
			$status_arr[$status] = array();
		}
		$status_arr[$status]['numHits']++;
		$status_arr[$status]['back']=$color;
		$status_arr[$status]['timeSpent']+=$endtime-$starttime;
		$prevStatus = $status;
		$textStyle = "";
		if($deleted || $denied) {
			$textStyle.= "text-decoration:line-through;";
		}
?>
    <tr id="current<?=$id?>" class="bd-row" style="<?=$style ?>">
      <td id="middle" style="<?=$textStyle ?>"><?=$status?>
      <td id="middle" style="<?=$textStyle ?>"><?=$message?>
      </td>
      </td>
      <td id="right" nowrap="nowrap" style="<?=$textStyle ?>"><?=date('m/d/Y h:i A',$starttime); ?>
      </td>
      <td id="right" nowrap="nowrap" style="<?=$textStyle ?>"><?=date('m/d/Y h:i A',$endtime); ?>
      </td>
      <td id="right" nowrap="nowrap" style="<?=$textStyle ?>"><?=$who ?>
      </td>
      <td id="right" nowrap="nowrap" bgcolor="#F4F4F4">
      <? if($editAccess && $approved && !$deleted) { ?>
      <!-- <img src="images/appointment-new.gif" alt="Edit Time Spent" /> &nbsp; -->
      <img onclick="deleteStamp(<?=$id?>)" alt="Mark For Deletion" src="images/user-trash.gif" /> 
      
      <? } elseif($pending) {?>
      	Awaiting Approval
      <? } ?> 
      </td>
    </tr>
    <?
}
?>
  </table>
  <?=($prevStatus=="No Status") ? "<br /><strong>No status set for " .date("m-d-y",$today)."</strong>" : "";?>
  <hr />
  <? //----------------------------------------------------------------------------------------
  if($editAccess) { 
	//display the ability to add times that can be "approved"
	?>
  <h2>Add Entry for
    <?=$employees[$chartID]['name']?>
  </h2>
  <form action="" id="addItem">
    <div>
      <table id="bd-table" width="800">
        <tr id="bd-row">
          <td id="middle"><strong>STATUS</strong></td>
          <td id="middle"><strong>MESSAGE</strong></td>
          <td id="right"><strong>START</strong></td>
          <td id="right"><strong>END</strong></td>
        </tr>
        <tr>
          <td><? 
		  $evtSelectId = "addEventSel";
		  include "support/statusSelect.php"
		  ?>
          </td>
          <td><input type="text" id="addMessage" /></td>
          <td nowrap="nowrap"><input name='addStart' type='text' maxlength='10' size='8' onfocus="cal.showCal(this);" id="addStart" value="<?=date("m/d/Y",$today)?>" />
            <input maxlength='5' size='8' type="text" id="addStartTime"  value="<?=date("h:i",$today)?>"/>
            <select name="addStartAMPM" id="addStartAMPM">
              <option value="am">am</option>
              <option value="pm">pm</option>
            </select></td>
          <td nowrap="nowrap"><input name='addEnd' type='text' maxlength='10' size='8' onfocus="cal.showCal(this);" id="addEnd" value="<?=date("m/d/Y",$today)?>" />
            <input maxlength='5' size='8' type="text" id="addEndTime"  value="<?=date("h:i",$today)?>"/>
            <select name="addEndAMPM" id="addEndAMPM">
              <option value="am">am</option>
              <option value="pm">pm</option>
            </select></td>
        </tr>
      </table>
      <input type="hidden" id="addUID" value="<?=$chartID?>" />
      <input type="button" onclick="addStamp()" value="Submit for Approval" />
    </div>
  </form>
  <? }// end of if editAccess 
  //----------------------------------------------------------------------------------------
  if($editAccess && $admin=='de_admin') { 
  ?>
  <hr />
  <form action="" id="approveForm" name="approveForm">
  <div id="approve">
  <h2>Approve Time Changes </h2>
  <table id="bd-table" width="800">
    <tr id="bd-row">
      <td id="middle"><strong>STATUS</strong></td>
      <td id="middle"><strong>MESSAGE</strong></td>
      <td id="right"><strong>START</strong></td>
      <td id="right"><strong>END</strong></td>
      <td id="right"><strong>REQUESTED BY</strong></td>
      <td id="right" colspan="2"><strong>REASON</strong></td>
    </tr>
    <?
	//list out activities not yet approved
	
	$select = "SELECT currentid, who, status_id,status_type, status_color, message, timestamp AS
								start, end, edited, deleted, added
								FROM emp_status
								INNER JOIN emp_current ON emp_status.status_id = emp_current.statusID
								WHERE acctID =" . $chartID."
								AND approved = 'pending'
								ORDER BY timestamp ASC";
						
	($selectResult = $db->query($select)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, $select));
	$status_arr = array();
	$prevTime = $today;
	$prevStatus = "No Status";
	while ($row = $selectResult->fetch_assoc()) {
		$id = $row['currentid'];
		$status = $row['status_type'];
		$color = $row['status_color'];
		$message = $row['message'];
		$starttime = $row['start'];
		$endtime = $row['end'];
		$who = ($row['who']) ? $employees[$row['who']]['name'] :  $employees[$chartID]['name'];
		$style = "background-color:".$color;
		//$why = ($row['edited']>=1) ? "Edited" : ($row['deleted']==1) ? "Deleted" : ($row['added']==1) ? "Added" : "Other";
		if($row['edited']>=1)
			$why = "Edited";
		elseif($row['deleted']==1) 
			$why ="Deleted";
		elseif($row['added']==1)
			$why="Added";
		else
			$why = "Other";
			
		
?>
    <tr id="current<?=$id?>" class="bd-row" style="<?=$style ?>">
      <td id="middle"><?=$status?>
      <td id="middle"><?=$message?>
      </td>
      </td>
      <td id="right" nowrap="nowrap"><?=date('m/d/Y h:i A',$starttime); ?>
      </td>
      <td id="right" nowrap="nowrap"><?=date('m/d/Y h:i A',$endtime); ?>
      </td>
      <td id="right" nowrap="nowrap"><?=$who ?>
      </td>
      <td id="right" nowrap="nowrap"><?=$why ?>
      </td>
      <td id="right" nowrap="nowrap"><input id="approve[]" type="checkbox" value="<?=$id?>" />
      </td>
    </tr>
    <?
}
?>
    <!-- <tr>
      <td colspan="6">&nbsp;</td>
      <td><a href="#">Select All Items</a> / <a href="#">Select None </a></td>
    </tr> -->
  </table>
  </div>
  <input type="button" onclick="approveStamp()" value="Approve Selected Items" />
  <input type="button" onclick="denyStamp()" value="Deny Selected Items" />
  </div>
  <? } //end if if admin access ?>
  <!--
<hr />
<h4>User Metrics </h4>
<p>
  <?

function duration($secs) { 
	//simple function to convert seconds into a more readable format
	$vals = array('w' => (int) ($secs / 86400 / 7), 
				  'd' => $secs / 86400 % 7, 
				  'h' => $secs / 3600 % 24, 
				  'm' => $secs / 60 % 60, 
				  's' => $secs % 60); 

	$ret = array(); 

	$added = false; 
	foreach ($vals as $k => $v) { 
		if ($v > 0 || $added) { 
			$added = true; 
			$ret[] = $v . $k; 
		} 
	} 

	return join(' ', $ret); 
} 
//display routine ---------------------------------------------------------------------------
$statuses = array();
foreach($status_arr as $key=>$arr) {
	array_push($statuses,$key);
}
$times = array();
$workingSecs = 0;
$invalid = array("Out of Office - Not Working","Sick","Vacation");
foreach($statuses as $status) {
	if(!in_array($status,$invalid)){
		$workingSecs += $status_arr[$status]['timeSpent'];
	}
	echo $status .  " = " . duration($status_arr[$status]['timeSpent']) ."<br />";
}
echo "<br />Worked for ".duration($workingSecs);
?>
</p>
<hr />
-->
</div>

<? include("include/pageFooter.php");?>
