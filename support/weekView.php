<?
//basic month view
include_once(dirname(__FILE__)."/../include/header.php");

if($input->get->keyExists('day') && $input->get->keyExists('dir') && $input->get->keyExists('month') && $input->get->keyExists('year') && !isset($m)&& !isset($d)&& !isset($y)) {
	$currentDir = $input->get->testRegex('dir','/^(prev|next)$/');
	$currentMonth = $input->get->testInt('month');
	$currentDay = $input->get->testInt('day');
	$currentYear = $input->get->testInt('Year');
	if($currentMonth || $currentMonth == 0) {
		$m = $currentMonth;
		$d = $currentDay;	
		$y = $currentYear;
	} else {
		die('Invalid entry');
	}
} else {
	$m= date("m");		//find todays month
	$d= date("d");     // Finds today's date
	$y= date("Y");     // Finds today's year
}

//adjust to wednesday
if(date("w",mktime(0,0,0,$m,$d,$y))!=0) $d=date("d",strtotime("Last Sunday",mktime(0,0,0,$m,$d,$y)));
$startDay = mktime(0,0,0,$m,$d,$y);
$finalDay = strtotime("Next Saturday",mktime(0,0,0,$m,$d,$y));
?>
<info><month><?=date('n',mktime(0,0,0,$m,1,$y));?></month><day><?=date('n',mktime(0,0,0,$m,$d,$y))?></day><year><?=date('Y',mktime(0,0,0,$m,1,$y));?></year><display><?=date("M d Y",$startDay)?> - <?=date("M d Y",$finalDay)?></display>
<calendar><![CDATA[
<div style="width:100%">
  <div align="center">
  </div>
  <table cellspacing='0' cellpadding='0' align=center width='100%' border='0'>
    <tr>
      <td colspan=7 align=center bgcolor="#CCCCCC"><font size='3' face='Tahoma'>
        </font> </td>
    </tr>
    <tr>
      <td width="14%"><font size='3' face='Tahoma'><b>Sun</b></font></td>
      <td width="14%"><font size='3' face='Tahoma'><b>Mon</b></font></td>
      <td width="14%"><font size='3' face='Tahoma'><b>Tue</b></font></td>
      <td width="14%"><font size='3' face='Tahoma'><b>Wed</b></font></td>
      <td width="14%"><font size='3' face='Tahoma'><b>Thu</b></font></td>
      <td width="14%"><font size='3' face='Tahoma'><b>Fri</b></font></td>
      <td width="14%"><font size='3' face='Tahoma'><b>Sat</b></font></td>
    </tr>
    <tr>
      <?
////// End of the top line showing name of the days of the week//////////

//////// Starting of the days//////////
for($currentDay=$startDay;$currentDay<=$finalDay;$currentDay+=86400){
	$day=date("d",$currentDay);
	//current Day matches today set background color
	if(mktime(0,0,0,date("m"),date("d"),date("Y"))==mktime(0,0,0,$m,$day,$yn)) {
		$style = "background-color:#CCE6FF";
	} else {
		$style = "";
	}
	// This will display the date inside the calendar cell
	?>
      <td style="border:solid;border-color:#999999;<?=$style?>" valign='top' height="100"><font size='2' face='Tahoma'>
        <?=$day?>
        <br />
        </font>
        <?
	//display events for that given day
	include(dirname(__FILE__)."/calendarDay.php");
	?>
      </td>
      <?
	
} //end of for loop to display days
?>
    </tr>
  </table>
</div>
]]>
</calendar>
</info>