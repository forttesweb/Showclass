<?php

//Database_connection.php

class Database_connection
{
	function connect()
	{
		$connect = new PDO("mysql:host=localhost; dbname=siteen85_showclass", "
siteen85_showclass", "@#Xterra99");

		return $connect;
	}
}

?>