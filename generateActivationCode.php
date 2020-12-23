<?php

/*
	*Generate a random activation code
	*Returns the code to MyCost app
*/

require_once('connectDB.php');

$activationCode = "";

while(1)
{
	$activationCode = RandomCode();

	$isUniqueCode = 0;

	$query  = "SELECT * FROM activation_codes WHERE code = '$activationCode'";
	$result = mysqli_query($connect, $query) or die('Server connection error');
	$count  = mysqli_num_rows($result);

	if($count == 0)
	{
		$isUniqueCode = 1;
	}

	if($isUniqueCode)
	{
		$query = "INSERT INTO activation_codes (code) VALUES ('$activationCode')";
		mysqli_query($connect, $query) or die('Server connection error');
		
		die($activationCode);
	}
}

function RandomCode()
{
	$charSet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
	$charSetLen = strlen($charSet);
	$randStr = "";
		
	for($i = 0; $i < 11; $i++)
	{
		$index = rand(0, $charSetLen - 1);
		$randStr .= $charSet[$index];
	}
		
	return $randStr;
}