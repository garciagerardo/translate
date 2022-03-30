<?php
	require_once 'login.php';
	require_once 'verify.php';

	$connection = new mysqli($hn, $un, $pw, $db);
	if($connection->connect_error) die(mysql_fatal_error('OOOPS'));
	session_start();

	if($_SESSION['ip'] != $_SERVER['REMOTE_ADDR'] && $_SESSION['check'] != hash('ripemd128', $_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'])){
		destroy_session_and_data();
	}
	else{
		if(isset($_SESSION['username'])){
			$username = $_SESSION['username']; 
		}	
	}

	echo <<<_END
	<html><head><h2>Translate</h2><title>Final</title>
	</head><body>
_END;

	if(!$username){
		echo <<<_END
		<table><tr><td><p><a href=setupusers.php>Sign Up</a></p></td>
			<td><pre>   </pre></td>
			<td><p><a href=authenticate.php>Login</a></p></td></tr>
		</table>
_END;
	}
	echo <<<_END
	<form action="translate.php" method="post">
		<br>Input to Translate<br><input type="text" name="translate">
		<input type='submit' value='Submit'>
	</form>
_END;

	if(isset($_POST['translate'])){
		$phrase = mysql_entities_fix_string($connection, $_POST['translate']);
		$words = explode(' ', strtolower($phrase));
		$trans = '';
		$table = 'default_translation';

		if($username){
			$query = "SELECT * FROM " . $username;
			$result = $connection->query($query);
			if(!$result) die(mysql_fatal_error('OOOPS'));
			elseif($result->num_rows) $table = $username;
			$result->close();
		}

		for($i = 0; $i < count($words); $i++){
			$query = "SELECT * FROM " . $table . " WHERE input='$words[$i]'";
			$result = $connection->query($query);
			if(!$result) die(mysql_fatal_error('OOOPS'));
			elseif($result->num_rows){
				$row = $result->fetch_array(MYSQLI_NUM);
				$trans = $trans . ' ' . $row[1];
			}
			$result->close();
		}

		echo "<b>$trans</b><br>";
	}

	if($username){
		echo <<<_END
		<form action="translate.php" method="post" enctype="multipart/form-data">
			<br>Please submit a text file thet has a word and its translation in the same<br>
				line, where the words are seperated by a space, then a colon, and then <br>
				another space character.<br>
				<b><u>Example:</u><br>
					hello : hola</b><br><br>
			Select File: <input type="file" name="filename"><br>
			<input type="submit" value="Submit">
		</form>
_END;
		if($_FILES){
			if($_FILES['filename']['type'] == 'text/plain'){
				$contents = file_get_contents($_FILES['filename']['tmp_name']);
				$contents = mysql_entities_fix_string($connection, $contents);
				$contents = strtolower($contents);
				$rows = explode('\n', $contents);
				for($i = 0; $i < count($rows); $i++){
					$cols = explode(" : ", $rows[$i]);
					$query = "INSERT INTO default_translation VALUES('$cols[0]', '$cols[1]')";
					$result = $connection->query($query);
					if(!$result) die(mysql_fatal_error('OOOPS'));
					$result->close();
				}
			}
			else echo "This is not an accepted file type, only text files are allowed<br>";
		}
	}

	$connection->close();

	function destroy_session_and_data(){
		$_SESSION = array();
		setcookie(session_name(), '', time() - 2592000, '/');
		session_destroy();
	}

?>