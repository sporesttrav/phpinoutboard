// JavaScript Document
//---------------------------------------------------------------------------------
//Event Scheduler
//---------------------------------------------------------------------------------
function selectUntil() {
	var repObj = $div('repeat');
	if(repObj && repObj.value!='none') {
		$div("untilDiv").style.visibility = "visible"
	} else {
		$div("untilDiv").style.visibility = "hidden"
	}
}
//--------------------------------------------------------------------------------
function processSchedule() {
	var sTime = $div("start").value + " " + $div("start_hour").value + ":"+$div("start_min").value+$div("start_ampm").value;
	var eTime = $div("end").value + " " + $div("end_hour").value + ":"+$div("end_min").value+$div("end_ampm").value;
	var desc = $div('schedule_notes').value;
	var status = $div('evt_status').value;
	var repeat = $div('repeat').value;
	var untilDate = $div('until').value;
	var attend = retrieveListSelected('emp_attend');
	var dataString="start="+sTime + "&end="+eTime + "&status="+status+ "&repeat="+repeat+ "&until="+untilDate+ "&attend="+attend+ "&desc="+desc;
	if(attend.length <= 0) {
		alert('You must select at least 1 person to attend');
		return;
	}
	disableForm($div('usertable'));
	microAjax('aj/newScheduleProcess.php', function(pageData){
		disableForm($div('usertable'),false);
		if(pageData.match("Invalid")) {
			alert(pageData);
		} else {
			window.location = "calendar.php";
		}
		//changeView('log');
	},dataString);
	
}
