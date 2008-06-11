// JavaScript Document
var ver =0.21;
var currentTimeout = 0;
var currentStatus = -1;
var reloadTime = 60*1000; // # of seconds * 1000
var currentPage = "userTable.php";
var currentSpan;
var statusTo;
var mousex = 0;
var mousey = 0;
//----------------------------------------------------------------------------
//used to generate random strings
function randomPassword(length)
{
  chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890_";
  pass = "";
  for(x=0;x<length;x++)
  {
    i = Math.floor(Math.random() * 63);
    pass += chars.charAt(i);
  }
  return pass;
}
//----------------------------------------------------------------------------
function changeView(view) {
	clearTimeout(currentTimeout);
	switch(view) {
		case "daily" :
			currentPage = "scheduleTable.php";
			break;
		case "log" :
			currentPage = "userLogTable.php";
			break;
		case "cal" :
			currentPage = "calendar.php";
			break;
		case "chart" :
			currentPage = "dailyBreakdown.php";
			break;
		case "schedule" :
			currentPage = "newSchedule.php";
			break;
		case "logout" :
			window.location = "logout.php";
			break;
		default:
			currentPage = "userTable.php";
			view = "user";
			break;
	}
	var exdate=new Date();
	exdate.setDate(exdate.getDate()+1); 
	document.cookie = "currentP=" + view;
	reload();
}
//----------------------------------------------------------------------------
function checkStatus(event,usr,field)
{
	if (event.keyCode==13 || event.which==13) {
		setMessage(usr,field.value);
		if ($div('user_select').blur) $div('user_select').blur(); else $div('user_select').focus();
	}
}
//----------------------------------------------------------------------------
//used to reload the current table to display any changes
function setMessage(usr,message)
{
	var dataString = "userid=" + usr;
	dataString += "&msg=" + escape(message);
	microAjax('aj/setMessage.php', function(pageData){
		reload();
	},dataString);
}
//----------------------------------------------------------------------------
//used to reload the current table to display any changes
function setUserMessage(usr)
{
	var dataString = "userid=" + usr;
	var message = $div('status_message['+ usr + ']').value;
	
	dataString += "&msg=" + escape(message);
	microAjax('aj/setMessage.php', function(pageData){
		changeView();
	},dataString);
}
//----------------------------------------------------------------------------
function selectUser(obj) {
		var dataString = "?id="+obj.value;
		var randVar = randomPassword(32);
		dataString+='&rand='+randVar;
		microAjax(currentPage+dataString, function(pageData){$div('usertable').innerHTML= pageData});		
	}
//----------------------------------------------------------------------------
//used to reload the current table to display any changes
function reload()
{
	var randVar = randomPassword(32);
	var dataString = "";
	microAjax(currentPage+'?rand='+randVar, function(pageData){
		$div('usertable').innerHTML= pageData;
		var cVal = $div('user_select').options[$div('user_select').selectedIndex].value;
		if(currentStatus!=cVal || cVal !=$div('currentUserStatus').value) {
			currentStatus = $div('currentUserStatus').value;
			microAjax('userStatus.php?rand='+randVar, function(pageData){
			$div('userstatus').innerHTML= pageData
			changeView();
			});
		}
	},dataString);
	if(currentPage=="userTable.php") {
		clearTimeout(currentTimeout);
		currentTimeout = setTimeout ( "reload()", reloadTime );
	}
	
}
//----------------------------------------------------------------------------
//used to change the status of the current user
function statusChange(usrID,obj) {
	var selectValue = obj.value;
	var selectedOption = obj.options[obj.selectedIndex].text;
	var dataString = "";
	var randVar = randomPassword(32); 
	$div('userstatus').innerHTML = "Saving...";
	dataString = "userid=" + usrID + "&statusid="+selectValue;
	microAjax('aj/changeStatus.php', function(pageData) {
		if(pageData.indexOf('Invalid')>-0) {
			//error
			alert(pageData);
			return false;
		} 
		//$div("status_message").value= selectedOption;
		//currentStatus = selectValue;
		microAjax('userStatus.php?rand='+randVar, function(pageData){
			$div('userstatus').innerHTML= pageData
			changeView();
		});	
		
	},dataString)
}


//---------------------------------------------------------------------------------
//Log
//---------------------------------------------------------------------------------
function displayStatus(name,starttime,endtime) {
	
	var dataString = "userid="+name+"&start="+starttime+"&end="+endtime;
	currentSpan = $div("tooltip"+name+""+starttime);
	currentSpan.innerHTML = "Loading...";
	microAjax('displayStatus.php', function(pageData) {
		currentSpan.innerHTML = pageData;
	},dataString);
}
//----------------------------------------------------------------------------
function hideStatus() {
	if($div("status_desc"))
		$div("status_desc").innerHTML = "Mouse over a cell...";
}
//----------------------------------------------------------------------------
function retrieveListSelected(divId) {
	var field = $div(divId);
	var i;
	var returnStr = Array();
	for(i=field.options.length-1;i>=0;i--)
	{
	if(field.options[i].selected)
		returnStr.push(field.options[i].value);
	}
	return returnStr.toString();
}
