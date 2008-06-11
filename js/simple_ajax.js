//element handlers
function $div(psID) { if(document.all) {return document.all[psID];} else { return document.getElementById(psID); }} 
function getAllFormElements(parent_node){if(parent_node==undefined){parent_node=document;}var out=new Array();formInputs=parent_node.getElementsByTagName("input");for(var i=0;i<formInputs.length;i++){if(formInputs.item(i).type=='radio' || formInputs.item(i).type=='checkbox'){if(formInputs.item(i).checked==true){out.push(formInputs.item(i));}}else{out.push(formInputs.item(i));}}formInputs=parent_node.getElementsByTagName("textarea");for(var i=0;i<formInputs.length;i++)out.push(formInputs.item(i));formInputs=parent_node.getElementsByTagName("select");for(var i=0;i<formInputs.length;i++)out.push(formInputs.item(i));return out;}
//============================================================================
function disableForm(parent_node,enable){
	if(parent_node==undefined){parent_node=document;}
	if(enable==undefined){enable=true;}
	formInputs=parent_node.getElementsByTagName("input");
	for(var i=0;i<formInputs.length;i++){
		formInputs.item(i).disabled = enable;
	}
	formInputs=parent_node.getElementsByTagName("textarea");
	for(var i=0;i<formInputs.length;i++)
		formInputs.item(i).disabled = enable;
	formInputs=parent_node.getElementsByTagName("select");
	for(var i=0;i<formInputs.length;i++)
		formInputs.item(i).disabled = enable;
}
//============================================================================
// return the value of the radio button that is checked
// return an empty string if none are checked, or
// there are no radio buttons
function getCheckedValue(radioObj) {
	if(!radioObj)
		return false;
	var radioLength = radioObj.length;
	if(radioLength == undefined)
		if(radioObj.checked)
			return radioObj.value;
		else
			return false;
	for(var i = 0; i < radioLength; i++) {
		if(radioObj[i].checked) {
			return radioObj[i].value;
		}
	}
	return false;
}
//============================================================================
//----------------------------------------------------------------------------
//ajax handler ---
/*
Here is how it works:

POST: microAjax(url, callback, dataString)
  microAjax('rpc.php', function(text){alert(text), 'foo=bar&answer=42'})
GET: microAjax(url, callback)
  microAjax('rpc.php?foo=bar&answer=42', function(text){alert(text)})

*/
function microAjax(url,cF){
this.bF=function(caller,object){
return function(){
return caller.apply(object,new Array(object));
}}
this.sC=function(object) {
if (this.r.readyState==4) {
this.cF(this.r.responseText);
}}
this.gR=function(){
if (window.ActiveXObject)
return new ActiveXObject('Microsoft.XMLHTTP');
else if (window.XMLHttpRequest)
return new XMLHttpRequest();
else
return false;
}
if (arguments[2]) this.pb=arguments[2];
else this.pb="";
this.cF=cF;
this.url=url;
this.r=this.gR();
if(this.r){
this.r.onreadystatechange=this.bF(this.sC,this);
if(this.pb!=""){
this.r.open("POST",url,true);
this.r.setRequestHeader('Content-type','application/x-www-form-urlencoded');
this.r.setRequestHeader('Connection','close');
}else{
this.r.open("GET",url,true);
}
this.r.send(this.pb);
}}
//============================================================================
//----------------------------------------------------------------------------
//something to play with XML nicely 
function loadXMLString(txt) 
{
try //Internet Explorer
  {
  xmlDoc=new ActiveXObject("Microsoft.XMLDOM");
  xmlDoc.async="false";
  xmlDoc.loadXML(txt);
  return(xmlDoc); 
  }
catch(e)
  {
  try //Firefox, Mozilla, Opera, etc.
    {
    parser=new DOMParser();
    xmlDoc=parser.parseFromString(txt,"text/xml");
    return(xmlDoc);
    }
  catch(e) {alert(e.message)}
  }
return(null);
}


