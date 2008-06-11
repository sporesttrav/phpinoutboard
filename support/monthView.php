<?
//basic month view
include_once(dirname(__FILE__)."/../include/header.php");

if($input->get->keyExists('year') &&$input->get->keyExists('month') && !isset($m)&& !isset($d)&& !isset($y)) {
	$currentMonth = $input->get->testInt('month');
	$currentYear = $input->get->testInt('year');
	if($currentMonth || $currentMonth == 0) {
		$m= $currentMonth;
		$d= 1;		       
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
<info><month><?=date('n',mktime(0,0,0,$m,1,$y));?></month><day><?=$d?></day><year><?=date('Y',mktime(0,0,0,$m,1,$y));?></year><display><?=date("M Y",mktime(0,0,0,$m,$d,$y))?></display>
<?

$no_of_days = date('t',mktime(0,0,0,$m,1,$y)); // This is to calculate number of days in a month

$mn=date('M',mktime(0,0,0,$m,1,$y)); // Month is calculated to display at the top of the calendar

$yn=date('Y',mktime(0,0,0,$m,1,$y)); // Year is calculated to display at the top of the calendar

$j= date('w',mktime(0,0,0,$m,1,$y)); // This will calculate the week day of the first day of the month

for($k=1; $k<=$j; $k++){ // Adjustment of date starting
$adj .="<td style='border:solid;border-color:#999999;'>&nbsp;</td>";
}
?>
<calendar><![CDATA[
<div style="width:100%">
  <div align="center">
  </div>
  <table cellspacing='0' cellpadding='0' align=center width='100%' border='0'>
    <tr>
      <td colspan=7 align=center bgcolor="#CCCCCC"><font size='3' face='Tahoma'>
        <?=$mn?>
        <?=$yn?>
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
for($day=1;$day<=$no_of_days;$day++){
	echo $adj;
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
	$adj='';
	$j ++;if($j==7){
		//reset the row for a new week
	?>
    </tr>
    <tr>
      <?
		$j=0;
	}
} //end of for loop to display days
	//for the remaining days in month
	for($remDays=$j;$remDays<7 && $remDays !=0;$remDays++){
		echo "<td style='border:solid;border-color:#999999;'>&nbsp;</td>";
	}
?>
    </tr>
  </table>
</div>
]]>
</calendar>
</info>