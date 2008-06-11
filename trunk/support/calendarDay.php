<? 
if(!isset($day) || $day <1 || $day >31) {
	die("invalid day");
} else {
	$currentTime = mktime(0,0,0,$m,$day,$y);
	$currentEnd = mktime(0,0,0,$m,$day+1,$y);
	$dayquery = "SELECT emp_calendar.eventID, emp_calendar.message, emp_status.status_color, 
						emp_status.status_type, accounts.fName, accounts.lName,
						emp_calendar.start,emp_calendar.end,emp_calendar.attendees, emp_calendar.cancelled
						FROM emp_status INNER JOIN 
						(emp_calendar INNER JOIN accounts ON emp_calendar.acctID = accounts.acctID) 
						ON emp_status.status_id = emp_calendar.statusID
						WHERE deleted=0 AND (((start >= $currentTime AND start < $currentEnd) 
						OR (start < $currentTime AND end >=$currentTime))) ORDER BY START";
	($day_result = $db->query($dayquery)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, "QUERY ERROR"));
	if($day_result->num_rows > 0) {
?>
<table width="100%">
  <?
	while($dayrow = $day_result->fetch_assoc()){
		$attend = explode(",",$dayrow['attendees']);
		$time = ($dayrow['status_type']=="Meeting" || $dayrow['status_type']=="Filming") ? date("g:i",$dayrow['start']) ."-". date("g:i",$dayrow['end']) : "";
	?>
  <tr bgcolor="<?=$dayrow['status_color']?>">
    <td style="font-size:10px;"><a class="tt" onclick="viewEvent(<?=$dayrow['eventID']?>)" href="#<?=$dayrow['eventID'] ?>">
      
      <?=$dayrow['status_type'] ?> <?=($dayrow['cancelled']==1) ? " - <strong>CANCELLED</strong>" : $time ?>
      
      <span class="tooltip">
      <?=date("m/d/Y h:i a",$dayrow['start']) ?>
      to<br />
      <?=date("m/d/Y h:i a",$dayrow['end']) ?>
      <br />
      Attendees : <ul>
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
      <?=$dayrow['message']?>
      </span></a></td>
  </tr>
<? 	
  } //end of while 
?>
</table>
<? } //end of if rows ?>
<? } //end of if valid ?>
