<?php
	require_once 'login.php';
	require_once 'verify.php';

	$connection = new mysqli($hn, $un, $pw, $db);
	if ($connection->connect_error) die(mysql_fatal_error('OOOPS'));

	const SALT_LENGTH = 5;

	echo <<<_END
	<html><head><h2>Sign Up</h2><title>Final</title>
	<script src="validate_functions.js"></script>
	</head><body>
	<form action="setupusers.php" method="post" onsubmit="return validate(this)">
		<table>
		    <tr><td>Username:</td><td><input type="text" name="username"></td></tr>
		    <tr><td>Password:</td><td><input type="password" name="password"></td></tr>
	    </table>
	    <input type='submit' value='Create Account'>
	</form>
	</body></html>
_END;

	if(isset($_POST['username'], $_POST['password'])){
		$un_temp = mysql_entities_fix_string($connection, $_POST['username']);
		$pw_temp = mysql_entities_fix_string($connection, $_POST['password']);

		$taken = "SELECT * FROM users WHERE username='$un_temp'";
		$result = $connection->query($taken);
		if(mysqli_num_rows($result) == 1) die("Username Taken");
		$result->close();

		$salt1 = create_salt();
		$salt2 = create_salt();

		$token = hash('ripemd128', "$salt1$pw_temp$salt2");

		$query = "INSERT INTO users VALUES('$un_temp', '$token', '$salt1', '$salt2')";
		$result = $connection->query($query);
		if (!$result) die($connection->error);
		create_translation_table($connection, $un_temp);
		$result->close();
		echo 'Succesfully created account!<br>';
		die ("<p><a href=authenticate.php>Click here to continue</a></p>");
	}

	$connection->close();

	echo <<<_END
		<table><tr><td><p><a href=translate.php>Home</a></p></td>
			<td><pre>   </pre></td>
			<td><p><a href=authenticate.php>Login</a></p></td></tr>
		</table>
_END;

	function create_salt(){
		$seed = str_split('abcdefghijklmnopqrstuvwxyz'
                 .'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
                 .'0123456789!@#$%^&*()'); 
		shuffle($seed);
		$rand = '';
		for($i = 0; $i < SALT_LENGTH; $i++) 
			$rand .= $seed[$i];
		return $rand;
	}

	function create_translation_table($connection, $un){
		$query = "CREATE TABLE " . $un . "(
				 input VARCHAR(32) NOT NULL PRIMARY KEY,
				 translation VARCHAR(32) NOT NULL)";
		$result = $connection->query($query);
		if (!$result) die($connection->error);
		$result->close();
	}
?>