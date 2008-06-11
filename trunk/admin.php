<? 

include("include/header.php");

if($admin != "de_admin") die("Invalid user");

include("include/pageHeader.php");
?>
<style type="text/css">
body a:link,a:visited,a:active,a:hover {
	color:#000033;
	text-decoration:none;
	font-weight:bold;
	font-size:14px;
	font-family:sans-serif;
}
body a:hover {
	text-decoration:underline;
}
</style>
<div style="display:block;clear:both;">
  <ul>
    <li style="list-style:none"><a href="admin_outAccess.php"><img src="images/go-home.gif" border=0 alt="Change User Access" /> Change User Access</a><br />
      <a href="admin_statusAccess.php"><img src="images/status.gif" border=0 alt="Change Status Access" /> Change Status Access</a><br />
      <a href="admin_calAccess.php"><img src="images/status.gif" border=0 alt="Change Calendar Access" /> Change Calendar Access</a><br />
      <a href="admin_employeeList.php"><img src="images/status.gif" border=0 alt="Change DE Employees" /> Change DE Employees</a><br />
    </li>
  </ul>
</div>
<? include("include/pageFooter.php"); ?>
