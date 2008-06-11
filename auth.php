<?
	require_once("config.inc.php");

	//make sure they have posted all they can post
	if ($uname = $input->post->testAlnum('username')) {
		//do nothing?
		//echo $uname;
	} elseif ($uname = $input->post->testEmail('username')) {
		//do nothing?
		//echo $uname;
	} elseif ($uname = $input->post->testRegex('username','/^[A-Za-z]+.[A-Za-z]+/')) {
		//do nothing?
		//echo $uname;
	} else {
		die('Invalid User Access');
	}
	if ($passwd = $input->post->testRegex('passwd','/^[!~@\w]+$/')) { 
		//do nothing?
	}else {
		die('Invalid P Access');
	}
		
	// Create query string
	if(ereg('@', $uname)){
		$queryAuth="SELECT fName,lName,accounts.acctID, offcampus
						FROM de_emp RIGHT OUTER JOIN accounts ON de_emp.acctID = accounts.acctID 
						WHERE email='$uname' AND pass=sha('$passwd')";
	}elseif(ereg('\.', $uname)){
		$nameArray=explode(".",$uname);
		$fName=ucfirst(strtolower($nameArray[0]));
		$lName=ucfirst(strtolower($nameArray[1]));
		$queryAuth="SELECT fName,lName,accounts.acctID, offcampus
						FROM de_emp RIGHT OUTER JOIN accounts ON de_emp.acctID = accounts.acctID 
						WHERE fName='$fName' AND lName='$lName' AND pass=sha('$passwd')";
	}else{
		$lName=ucfirst(strtolower($uname));
		$queryAuth="SELECT fName,lName,accounts.acctID, offcampus
						FROM de_emp RIGHT OUTER JOIN accounts ON de_emp.acctID = accounts.acctID
						WHERE lName='$lName' AND pass=sha('$passwd')";
	}
	($authResult = $db->query($queryAuth)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, "QUERY ERROR"));
	
	if($authResult->num_rows !=1) {
		die('Invalid password try again ' . $uname);
	} else {
		//good to go start your session
		$authRow = $authResult->fetch_assoc();
		//first see if any times are available for today
		//if not set as in office
		$authStatusq = sprintf("select * from emp_current where acctID = %d and timestamp >= %s LIMIT 1",$authRow['acctID'],strtotime("today 6:00 am"));
		($authStatResult = $db->query($authStatusq)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, "QUERY ERROR"));
		if($authStatResult->num_rows == 0) {
			//set status ID to 1 - "in office" attribute
			$authStatusi = sprintf("insert into emp_current (acctID,statusID,timestamp,message,who) values (%d,%d,%d,'%s',%d)",
									$authRow['acctID'],1,time()-30,"In Office",$authRow['acctID']);
			($iStatResult = $db->query($authStatusi)) or exit(sprintf("Invalid query: %s\nWhole query: %s\n", $db->error, "QUERY ERROR"));
		}
		
		$cip = ($input->server->testIp('HTTP_CLIENT_IP')) ? $input->server->getDigits('HTTP_CLIENT_IP') : FALSE;
		$rip = ($input->server->testIp('REMOTE_ADDR')) ? $input->server->getDigits('REMOTE_ADDR') : FALSE;
		$fip = ($input->server->testIp('HTTP_X_FORWARDED_FOR')) ? $input->server->getDigits('HTTP_X_FORWARDED_FOR') : FALSE;
                
        if ($cip && $rip)    $ip_address = $cip;    
        elseif ($rip)        $ip_address = $rip;
        elseif ($cip)        $ip_address = $cip;
        elseif ($fip)        $ip_address = $fip;
		else die("No valid IP found");
		
		$cookieID = sha1($ip_address.$authRow['acctID']);
		setcookie("acctID", $cookieID, strtotime("tomorrow 06:00"));
		setcookie("version", 2, strtotime("tomorrow 06:00"));
	}
?>
