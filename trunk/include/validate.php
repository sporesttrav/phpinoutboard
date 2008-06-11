<?
	// require the inspekt library
	require_once(dirname(__FILE__)."/validate/Inspekt.php");
	
	// create a "SuperCage" to wrap all possible user input
	// the SuperCage should be created before doing *anything* else
	$input = Inspekt::makeSuperCage();

?>