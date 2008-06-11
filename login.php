<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>DE Calendar Login</title>
<link rel="shortcut icon" href="favicon.ico" >
<script src="js/simple_ajax.js" language="javascript" type="text/javascript"></script>
<script type="text/javascript">
//----------------------------------------------------------------------------
//Authentication 
//----------------------------------------------------------------------------
function checkAuthStatus(event)
{
	if (event.keyCode==13 || event.which==13) {
		authUser();
	}
}
function authUser()
{
	var myForm = getAllFormElements($div('authForm'));
	var dataString = "";
	
	$div('login').style.visibility = "hidden";
	for (var i = 0; i < myForm.length; i++)
		dataString += myForm[i].id + "=" + myForm[i].value + "&";
		microAjax('auth.php', function(pageData){
			if(pageData.length>0) {
				$div('login').style.visibility = "visible";
				alert(pageData);
			} else {
				$div('login').innerHTML = "";
				alert('Login Successful');
				//currentTimeout = setTimeout ( "reload()", reloadTime );
				window.location="index.php"
			}
		},dataString)
}
</script>
<style type="text/css">
input {
	font-size:18px;
	font-family:Georgia, "Times New Roman", Times, serif;
}
#login {
	font-family:Georgia, "Times New Roman", Times, serif;
	font-size:18px;
	font-style:normal;
	font-weight:bold;
	width:400px;
	display:block;
}
.spiffy {
	display:block
}
.spiffy * {
	display:block;
	height:1px;
	overflow:hidden;
	font-size:.01em;
	background:#CCCCCC
}
.spiffy1 {
	margin-left:3px;
	margin-right:3px;
	padding-left:1px;
	padding-right:1px;
	border-left:1px solid #e2e2e2;
	border-right:1px solid #e2e2e2;
	background:#d6d6d6
}
.spiffy2 {
	margin-left:1px;
	margin-right:1px;
	padding-right:1px;
	padding-left:1px;
	border-left:1px solid #f0f0f0;
	border-right:1px solid #f0f0f0;
	background:#d3d3d3
}
.spiffy3 {
	margin-left:1px;
	margin-right:1px;
	border-left:1px solid #d3d3d3;
	border-right:1px solid #d3d3d3;
}
.spiffy4 {
	border-left:1px solid #e2e2e2;
	border-right:1px solid #e2e2e2
}
.spiffy5 {
	border-left:1px solid #d6d6d6;
	border-right:1px solid #d6d6d6
}
.spiffyfg {
	background:#CCCCCC
}
.style1 {
	color: #FF0000;
	border: solid blue 3px;
	width: 50%;
	background-color: #FFFFCC;
	padding: 20px;
	text-decoration: blink;
}
</style>
</head>
<body>
<center>
<div id='login' align='center'> <b class="spiffy"> <b class="spiffy1"><b></b></b> <b class="spiffy2"><b></b></b> <b class="spiffy3"></b> <b class="spiffy4"></b> <b class="spiffy5"></b></b>
  <div class="spiffyfg"> <br />
    <img src="images/DELogin.jpg" alt="DE Calendar" width="337" height="76" />
    <form id="authForm" onsubmit="javascript:authUser();" action="">
      <table cellpadding="2" cellspacing="2">
        <tr valign="middle">
          <td align="right"> Username :</td>
          <td><input onkeypress="javascript:return checkAuthStatus(event)" type="text" value="" id="username" />
          </td>
        </tr>
        <tr valign="middle">
          <td align="right"> Password :</td>
          <td><input onkeypress="javascript:return checkAuthStatus(event)" type="password" value="" id='passwd' />
          </td>
        </tr>
      </table>
      <div align="center">
        <input id="authLogin" type="button" value="Submit" onclick="javascript:authUser();" />
      </div>
    </form>
    &nbsp;
  </div>
  
  <b class="spiffy"> <b class="spiffy5"></b> <b class="spiffy4"></b> <b class="spiffy3"></b> <b class="spiffy2"><b></b></b> <b class="spiffy1"><b></b></b></b> </div>
</center>
</body>
</html>