<?php

/*
	*Connect to the database
	*This file is required by all other file in MyCost API
*/

DEFINE('SERVER', 'server address');
DEFINE('USERNAME', 'username');
DEFINE('PASSWORD', 'password');
DEFINE('DATABASE', 'database name');

$connect = mysqli_connect(SERVER, USERNAME, PASSWORD, DATABASE) or die('Server connection error');

?>