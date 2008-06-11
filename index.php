<? 
//gets $currentID
include "include/checkAuth.php";
if(!isset($currentID)) header("Location: login.php"); 
if($input->cookie->testInt('version')!=2) {
	header("Location: login.php"); 
}
//gives a DB like via $db
require_once("config.inc.php");
?>
<? include("include/pageHeader.php"); ?>
<script type="text/javascript">
<? 
if(isset($currentID)) {?>
//use to display reload
currentTimeout = setTimeout ( "reload()", reloadTime );
<? } ?>
</script>
	<div id="userstatus" style="clear:both;display:block; padding-top:10px;">
    <? include("userStatus.php"); ?>
    </div>
<!-- USER TABLE AREA -->
  <div id="usertable">
    <script type="text/javascript">reload()</script>
  </div>
<? include("include/pageFooter.php"); ?>
