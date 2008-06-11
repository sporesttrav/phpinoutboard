<?
//basic month view
include_once(dirname(__FILE__)."/../include/header.php");

if($input->get->keyExists('day') &&$input->get->keyExists('month') && !isset($m)&& !isset($d)&& !isset($y)) {
	$currentMonth = $input->get->testInt('month');
	$currentDay = $input->get->testInt('day');
	$currentYear = $input->get->testInt('year');
	if($currentMonth || $currentMonth == 0) {
		$m= $currentMonth;
		$d= $currentDay;		       
		$y = $currentYear;
	} else {
		die('Invalid month entry' + $input->post->getRaw('month'));
	}
} else {
	$m= date("m");		//find todays month
	$d= date("d");     // Finds today's date
	$y= date("Y");     // Finds today's year
}
?>
<info><month><?=date('n',mktime(0,0,0,$m,$d,$y));?></month><day><?=date("j",mktime(0,0,0,$m,$d,$y))?></day><year><?=date('Y',mktime(0,0,0,$m,$d,$y));?></year><display><?=date("M d Y",mktime(0,0,0,$m,$d,$y))?></display>
<?
$mn=date('M',mktime(0,0,0,$m,$d,$y)); // Month is calculated to display at the top of the calendar

$yn=date('Y',mktime(0,0,0,$m,$d,$y)); // Year is calculated to display at the top of the calendar

//////// Starting of the days//////////
$day = date("d",mktime(0,0,0,$m,$d,$yn));
?>
<calendar><![CDATA[
<div style="width:100%">
  <div align="center">
  </div>
  <table cellspacing='0' cellpadding='0' align=center width='100%' border='0'>
    <tr>
      <td colspan=<?=count($employees)+1?> align=center bgcolor="#CCCCCC"><font size='3' face='Tahoma'>
        <?=$mn?>
        <?=$day?>
        <?=$yn?>
        </font> </td>
    </tr>
<? 
	////// End of the top line showing name of the days of the week//////////
	for($currentTime=mktime(6,0,0,$m,$day,$y) ; $currentTime<=mktime(18,0,0,$m,$day,$y); $currentTime+=900) { 
    //900 is for 15 minutes DISPLAY A ROW
?>
    <tr style="border-bottom-style:solid; border-bottom-width:thin; border-bottom-color:#000;">
    	<td nowrap="nowrap" width="75"><?=date("h:i a",$currentTime)?></td>
<?
		//setup a basic row style
		if(mktime(0,0,0,date("m"),date("d"),date("Y"))==mktime(0,0,0,$m,$day,$yn)) {
			$style = "background-color:#CCE6FF";
		} else {
			$style = "";
		}

		$currentEnd = $currentTime+900;
		$dayquery = "SELECT emp_calendar.eventID, emp_calendar.message, emp_status.status_color, 
							emp_status.status_type, accounts.fName, accounts.lName,
							emp_calendar.start,emp_calendar.end,emp_calendar.attendees, emp_calendar.cancelled
							FROM emp_status INNER JOIN 
							(emp_calendar INNER JOIN accounts ON emp_calendar.acctID = accounts.acctID) 
							ON emp_status.status_id = emp_calendar.statusID
							WHERE deleted=0 AND start = $currentTime ORDER BY START";
		($day_result = $db->query($dayquery)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, "QUERY ERROR"));
		while($dayrow = $day_result->fetch_assoc()){
				$attend = explode(",",$dayrow['attendees']);
				$time = ($dayrow['status_type']=="Meeting" || $dayrow['status_type']=="Filming") ? 
							date("g:i",$dayrow['start']) ."-". date("g:i",$dayrow['end']) : "";
				$rows = ($dayrow['end']-$dayrow['start']) / 900;
?>
    <td style="border-style:solid; border-width:thin;" valign="top" width="200" bgcolor="<?=$dayrow['status_color']?>" rowspan="<?=$rows?>" style="font-size:10px;" onclick="viewEvent(<?=$dayrow['eventID']?>)"><a class="tt" href="#<?=$dayrow['eventID'] ?>">
<? //DISPLAY DATA CODE-------------------------------------------------------------------------------------- ?>
    <?=$dayrow['status_type'] ?> <?=($dayrow['cancelled']==1) ? " - <strong>CANCELLED</strong>" : $time ?><br />
    <?=date("m/d/Y h:i a",$dayrow['start']) ?> to <?=date("m/d/Y h:i a",$dayrow['end']) ?>
      <br />
      Attendees : <ul >
		<?
        $whos = explode(",",$dayrow['attendees']);
        foreach($whos as $who) {
            $aquery = sprintf("select fName,lName from accounts where acctID = %d",$who);
            ($at_result = $db->query($aquery)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, "QUERY ERROR"));
            if($at_result->num_rows ==1) {
                $name = $at_result->fetch_assoc();
                echo "<li>".$name['fName'] . "&nbsp;" . $name['lName'] . "</li>";
            }
        }
		?>
        </ul>
      <?=substr($dayrow['message'],0,50)."..."?>
      	
      </a>
 <? //END DATA CODE-------------------------------------------------------------------------------------- ?>
      </td>
<? 	} //end of while ?> 
	</tr> 
<? } //end of if for loop ?>
    
  </table>
</div>
]]>
</calendar>
</info>