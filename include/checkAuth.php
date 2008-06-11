<?
	//setup $db connection
	require_once(dirname(__FILE__)."/../config.inc.php");

	if($acctID = $input->cookie->testAlnum('acctID')) {
		$cip = ($input->server->testIp('HTTP_CLIENT_IP')) ? $input->server->getDigits('HTTP_CLIENT_IP') : FALSE;
		$rip = ($input->server->testIp('REMOTE_ADDR')) ? $input->server->getDigits('REMOTE_ADDR') : FALSE;
		$fip = ($input->server->testIp('HTTP_X_FORWARDED_FOR')) ? $input->server->getDigits('HTTP_X_FORWARDED_FOR') : FALSE;
                
        if ($cip && $rip)    $ip_address = $cip;    
        elseif ($rip)        $ip_address = $rip;
        elseif ($cip)        $ip_address = $cip;
        elseif ($fip)        $ip_address = $fip;
		else die("No valid IP found");
		
		$query = sprintf("SELECT accounts.acctID, fName, lName, type, offcampus, edit
						FROM de_emp RIGHT OUTER JOIN accounts ON de_emp.acctID = accounts.acctID
						WHERE sha1(CONCAT('%s',CAST(accounts.acctID as CHAR)))='%s'",
						$ip_address,$acctID);
		($authResult = $db->query($query)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, $query));
		if($authResult->num_rows !=1) {
			//die('Invalid user access');
		} else {
			//good to go start your session
			$authRow = $authResult->fetch_assoc();
			if(preg_match("/^$subnet/",$ip_address) || $authRow['offcampus']==1) {
				//something to allow admin
				$admin = ($authRow['lName']=="Admin") ? "de_admin" : "de_user";
				$editAccess = ($authRow['edit']==1) ? true : false;
				$currentID = $authRow['acctID'];
			} else {
				die("Outside access disabled for your account at this time");
			}
		}
	}
?>
