<?php

/*
	*Recieve current password and new password
	*Update the current password with the new password
	*Return SUCCESS or error message
*/

require_once('connectDB.php');
require_once('requestVerification.php');

if(isset($_POST['token']) && isset($_POST['userid']))
{
	$token  = mysqli_real_escape_string($connect, $_POST['token']);
	$userid = mysqli_real_escape_string($connect, $_POST['userid']);
	
	if(IsAuthenticRequest($connect, $userid, $token))//request verified as authentic
	{		
		$username        = mysqli_real_escape_string($connect, $_POST['username']);
		$currentPassword = mysqli_real_escape_string($connect, $_POST['currentPassword']);
		$newPassword     = mysqli_real_escape_string($connect, $_POST['newPassword']);
		$newCipherKey    = mysqli_real_escape_string($connect, $_POST['newCipherKey']);
		
		//checks if the current password is valid
		$query  = "SELECT * FROM users WHERE username = '$username' AND id = '$userid' LIMIT 1";
		$result = mysqli_query($connect, $query) or die('Server connection error');
		$row    = mysqli_fetch_array($result);
		
		$hashedPass = $row['password'];
		
		if(password_verify($currentPassword, $hashedPass)) //current password is valid
		{	
			//hash the new password
			$newPassword = password_hash($newPassword, PASSWORD_DEFAULT);
			
			//update the current password
			$query  = "UPDATE users SET password = '$newPassword', cipher_key = '$newCipherKey' WHERE username = '$username' AND id = '$userid'";
			$result = mysqli_query($connect, $query) or die('Server connection error');
			
			die('SUCCESS');
		}
		else
		{
			die('Invalid passoword. Please try again');
		}
	}
	else //request from unauthentic source
	{
		die('Server connection error');
	}
}
else
{
	die('Server connection error');
}

?>