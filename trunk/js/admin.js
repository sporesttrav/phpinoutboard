var currenStatus = -1;

var cp = new ColorPicker(); // DIV style
cp.writeDiv();
//modify this function to allow for special processing
function ColorPicker_pickColor(color,obj){
	/* START OF DO NOT EDIT******************************* */
	obj.hidePopup(); 
	pickColor(color);
	/* END OF DO NOT EDIT ******************************** */
	//alert($div(currentAnchor) + color);
	$div(currentAnchor).style.backgroundColor=color;
	var sid = currentAnchor.match(/[0-9]+/);
	var cid = color.match(/[0-9A-Fa-f]{6}/);
	microAjax('aj/saveStatusColor.php?color='+cid+'&statusid='+sid, function(pageData){$div('error').innerHTML= pageData})
}
//----------------------------------------------------------------------
// JavaScript Document
function changeAdminView(view) {
	clearTimeout(currentTimeout);
	switch(view) {
		case "index" :
			currentPage = "admin/index.php";
			break;
		case "color" :
			currentPage = "admin/color.php";
			break;
		case "access" :
			currentPage = "admin/outAccess.php";
			break;
		default:
			currentPage = "userTable.php";
			view = "user";
			break;
	}
	reload();
}
//------------------------------
