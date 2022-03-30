<?php
	function mysql_entities_fix_string($connection, $string){
		return htmlentities(mysql_fix_string($connection, $string));
	}

	function mysql_fix_string($connection, $string){
		if (get_magic_quotes_gpc()) $string = stripslashes($string);
		return $connection->real_escape_string($string);
	}

	function mysql_fatal_error($msg){
		echo <<<_END
		$msg
		We are sorry, but it was not possible to complete the 
		requested task. 
		Please click the back button on your browser and try again.
_END;
	}
?>