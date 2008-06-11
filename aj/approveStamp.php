<?
chdir("../");
include_once("include/header.php");

if(!$approveArr = $input->post->getRaw('approve')) {
	die("No items to approve");
}

foreach($approveArr as $currentID) {
	//get the stamp that will be edited
	$sQuery = sprintf("select * from emp_current where currentID=%d",$currentID);
	($sresult = $db->query($sQuery)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, "QUERY ERROR"));
	if($sresult->num_rows !=1) {
		die("Invalid ID provided");
	}
	//---------------------------------------------------------------------------------------------------
	$currentStamp = $sresult->fetch_assoc() ;
	$sTime = $currentStamp['timestamp'];
	$eTime = ($currentStamp['end']!='') ? $currentStamp['end'] : time();
	if($currentStamp['edited']>=1)
		$action = "Edited";
	elseif($currentStamp['deleted']==1) 
		$action ="Deleted";
	elseif($currentStamp['added']==1)
		$action="Added";
	else
		$action = "Other";
	
	switch($action) {
		case "Edited":
			$setVars = "added=0, deleted=0, edited = NULL, approved='approved'";
			break;
		case "Deleted":
			$setVars = "added=0, deleted=1, edited=NULL, approved='approved'";
			break;
		case "Added":
			$setVars = "added=0, deleted=0, edited=NULL, approved='approved'";
			break;
		default:
			$setVars = "added=0, deleted=0, edited=NULL, approved='approved'";
			break;
	}
	//approve the event (mark it as 'approved' - set added and deleted and edited to 0)
	$upQuery = sprintf("update emp_current set %s where currentID=%d limit 1",$setVars, $currentID);
	//
	($uresult = $db->query($upQuery)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, "QUERY ERROR"));
	
	//---------------------------------------------------------------------------------------------------
	if($action=='Added') {
		//make sure the previous entry and the next entry around the stamp being added reflect the time shifts
		$sQuery = sprintf("select currentID,timestamp,end from emp_current where end >= %d 
					and not currentID=%d 
					and approved='approved' 
					and acctID = %d
					and deleted=0 ORDER BY end ASC LIMIT 1",$sTime,$currentStamp['currentID'],$currentStamp['acctID']);
		
		$eQuery = sprintf("select currentID,timestamp,end from emp_current where timestamp <= %d 
					and not currentID=%d 
					and approved='approved' 
					and acctID = %d
					and deleted=0 ORDER BY timestamp DESC LIMIT 1",$eTime,$currentStamp['currentID'],$currentStamp['acctID']);
		($sResult = $db->query($sQuery)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, "QUERY ERROR"));
		($eResult = $db->query($eQuery)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, "QUERY ERROR"));		
		
		if($sResult->num_rows==1) {
			$prevRow = $sResult->fetch_assoc();
			$upQuery = sprintf("update emp_current set end=%d where currentID = %d",$sTime,$prevRow['currentID']);
			($sResult = $db->query($upQuery)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, "QUERY ERROR"));	
		}
		if($eResult->num_rows==1) {
			$nextRow = $eResult->fetch_assoc();
			$upQuery = sprintf("update emp_current set timestamp=%d where currentID = %d",$eTime,$nextRow['currentID']);
			($eResult = $db->query($upQuery)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, "QUERY ERROR"));
		}
		//---------------------------------------------------------------------------------------------------
	} elseif($action=='Deleted') {
		//shift the end time of the previous entry to the end time of the item being deleted
		$sQuery = sprintf("select currentID,timestamp,end from emp_current where end >= %d 
					and not currentID=%d 
					and approved='approved' 
					and acctID = %d
					and deleted=0 ORDER BY end ASC LIMIT 1",$sTime,$currentStamp['currentID'],$currentStamp['acctID']);
		
		($sResult = $db->query($sQuery)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, "QUERY ERROR"));
		
		if($sResult->num_rows==1) {
			$prevRow = $sResult->fetch_assoc();
			$upQuery = sprintf("update emp_current set end=%d where currentID = %d",$eTime,$prevRow['currentID']);
			($sResult = $db->query($upQuery)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, "QUERY ERROR"));	
		}
		
		//echo "prevRow";
		//print_r($prevRow);		
	}
}
?>