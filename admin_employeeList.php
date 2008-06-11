<? 

include("include/header.php");

if($admin != "de_admin") die("Invalid user");

include("include/pageHeader.php");

?>
<script type="text/javascript" language="javascript">
//-----------------------------------------------------------------------------------
function saveUser(id,action) {
	//action is either true/false 
	//true is when adding false is when removing
	var postData = "id="+id+"&access="+action;
	microAjax('aj/admin_saveEmployeeList.php', function(pageData){$div('error').innerHTML= pageData},postData)
}

// -------------------------------------------------------------------
// hasOptions(obj)
//  Utility function to determine if a select object has an options array
// -------------------------------------------------------------------
function hasOptions(obj) {
	if (obj!=null && obj.options!=null) { return true; }
	return false;
	}
	
	// -------------------------------------------------------------------
// removeSelectedOptions(select_object)
//  Remove all selected="selected"options from a list
//  (Thanks to Gene Ninestein)
// -------------------------------------------------------------------
function removeSelectedOptions(from) { 
	if (!hasOptions(from)) { return; }
	if (from.type=="select-one") {
		from.options[from.selectedIndex] = null;
		}
	else {
		for (var i=(from.options.length-1); i>=0; i--) { 
			var o=from.options[i]; 
			if (o.selected) { 
				from.options[i] = null; 
				} 
			}
		}
	from.selectedIndex = -1; 
	} 
	
// -------------------------------------------------------------------
// sortSelect(select_object)
//   Pass this function a SELECT object and the options will be sorted
//   by their text (display) values
// -------------------------------------------------------------------
function sortSelect(obj) {
	var o = new Array();
	if (!hasOptions(obj)) { return; }
	for (var i=0; i<obj.options.length; i++) {
		o[o.length] = new Option( obj.options[i].text, obj.options[i].value, obj.options[i].defaultSelected, obj.options[i].selected) ;
		}
	if (o.length==0) { return; }
	o = o.sort( 
		function(a,b) { 
			if ((a.text+"") < (b.text+"")) { return -1; }
			if ((a.text+"") > (b.text+"")) { return 1; }
			return 0;
			} 
		);

	for (var i=0; i<o.length; i++) {
		obj.options[i] = new Option(o[i].text, o[i].value, o[i].defaultSelected, o[i].selected);
		}
	}
	
	// -------------------------------------------------------------------
// moveSelectedOptions(select_object,select_object[,autosort(true/false)[,regex]])
//  This function moves options between select boxes. Works best with
//  multi-select boxes to create the common Windows control effect.
//  Passes all selected="selected"values from the first object to the second
//  object and re-sorts each box.
//  If a third argument of 'false' is passed, then the lists are not
//  sorted after the move.
//  If a fourth string argument is passed, this will function as a
//  Regular Expression to match against the TEXT or the options. If 
//  the text of an option matches the pattern, it will NOT be moved.
//  It will be treated as an unmoveable option.
//  You can also put this into the <SELECT> object as follows:
//    onDblClick="moveSelectedOptions(this,this.form.target)
//  This way, when the user double-clicks on a value in one box, it
//  will be transferred to the other (in browsers that support the 
//  onDblClick() event handler).
// -------------------------------------------------------------------
function moveSelectedOptions(from,to) {
	// Unselect matching options, if required
	if (arguments.length>3) {
		var regex = arguments[3];
		if (regex != "") {
			unSelectMatchingOptions(from,regex);
			}
		}
	// Move them over
	if (!hasOptions(from)) { return; }
	for (var i=0; i<from.options.length; i++) {
		var o = from.options[i];
		if (o.selected) {
			if (!hasOptions(to)) { var index = 0; } else { var index=to.options.length; }
			to.options[index] = new Option( o.text, o.value, false, false);
			//modification to save using AJAX
			if(to.name=="de_list") {
					saveUser(to.options[index].value,true);
				} else if(to.name=="main_list")  {
					saveUser(to.options[index].value,false);
				} else {
					//do nada
				}
			}
		}
	// Delete them from original
	for (var i=(from.options.length-1); i>=0; i--) {
		var o = from.options[i];
		if (o.selected) {
			from.options[i] = null;
			}
		}
	if ((arguments.length<3) || (arguments[2]==true)) {
		sortSelect(from);
		sortSelect(to);
		}
	from.selectedIndex = -1;
	to.selectedIndex = -1;
	}
</script>

<form action="">
  <table id="container" border="0" cellpadding="0" cellspacing="0">
    <tr id="row">
      <td>All Users</td>
      <td></td>
      <td>DE Users</td>
    </tr>
    <tr id="row">
      <td nowrap="nowrap" align="right" id="th-left"><select id='main_list' name='main_list' multiple="multiple" size=20 style="width:200px;">
          <?
  //-------------------------------------------------------------------------------------------
  //retrieve each name and their current status
$query = "select accounts.acctID, fName, lName
			FROM accounts
			WHERE acctID NOT IN(select de_emp.acctID from de_emp)
			ORDER BY fName";
($result = $db->query($query)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, "QUERY ERROR"));
///////
while ($row = $result->fetch_assoc()) {
	$name = htmlspecialchars($row["fName"],ENT_QUOTES). "&nbsp;" . htmlspecialchars($row["lName"],ENT_QUOTES);		
	$check = ($row['calendar']==1) ? "checked='checked'" : "";
	//-------------------------------------------------------------------------------------------
?>
          <option value="<?=$row['acctID']?>">
          <?=$name ?>
          </option>
          <? } ?>
        </select>
      </td>
      <td valign="middle" align="center"><input type="button" name="right" value="&gt;&gt;" onclick="moveSelectedOptions(this.form['main_list'],this.form['de_list'],true)">
        <br/>
        <input type="button" name="left" value="&lt;&lt;" onclick="moveSelectedOptions(this.form['de_list'],this.form['main_list'],true)">
        <br /></td>
      <td align="center" id="th-middle"><select id='de_list' name='de_list' multiple="multiple" size=20 style="width:200px;">
          <?
  //-------------------------------------------------------------------------------------------
  //retrieve each name and their current status
$query = "select accounts.acctID, fName, lName
			FROM accounts INNER JOIN de_emp ON de_emp.acctID=accounts.acctID
			ORDER BY fName";
($result = $db->query($query)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, "QUERY ERROR"));
///////
while ($row = $result->fetch_assoc()) {
	$name = htmlspecialchars($row["fName"],ENT_QUOTES). "&nbsp;" . htmlspecialchars($row["lName"],ENT_QUOTES);		
	$check = ($row['calendar']==1) ? "checked='checked'" : "";
	//-------------------------------------------------------------------------------------------
?>
          <option value="<?=$row['acctID']?>">
          <?=$name ?>
          </option>
          <? } ?>
        </select></td>
    </tr>
  </table>
  <script type="text/javascript">sortSelect($div('main_list'))</script>
  <script type="text/javascript">sortSelect($div('de_list'))</script>
</form>
<div id="error">&nbsp;</div>
<? include("include/pageFooter.php"); ?>
