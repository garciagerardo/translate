<?php //authenticate2.php
	require_once 'login.php';
	require_once 'verify.php';

	$connection = new mysqli($hn, $un, $pw, $db);
	if($connection->connect_error) die(mysql_fatal_error('OOOPS'));

	if(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])){
		$un_temp = mysql_entities_fix_string($connection, $_SERVER['PHP_AUTH_USER']);
		$pw_temp = mysql_entities_fix_string($connection, $_SERVER['PHP_AUTH_PW']);

		$query = "SELECT * FROM users WHERE username='$un_temp'";
		$result = $connection->query($query);

		if(!$result) die(mysql_fatal_error('OOOPS'));
		elseif($result->num_rows){
			$row = $result->fetch_array(MYSQLI_NUM);
			$result->close();
			$salt1 = $row[2]; 
			$salt2 = $row[3];
			$token = hash('ripemd128', "$salt1$pw_temp$salt2");

			if ($token == $row[1]) {
				session_start();
				$_SESSION['username'] = $un_temp;
				$_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
				$_SESSION['check'] = hash('ripemd128', $_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']);
				echo "$Hi $row[0], you are now logged in<br>";
				die("<p><a href=translate.php>Click here to continue</a></p>");
			}
			else die("Invalid username/password combination");
		}
		else die("Invalid username/password combination");
	}
	else {
		header('WWW-Authenticate: Basic realm="Restricted Section“');
		header('HTTP/1.0 401 Unauthorized');
		echo "Please enter your username and password";
	}

	echo <<<_END
		<table><tr><td><p><a href=translate.php>Home</a></p></td>
			<td><pre>   </pre></td>
			<td><p><a href=setupusers.php>Sign Up</a></p></td></tr>
		</table>
_END;

	$connection->close();
?>