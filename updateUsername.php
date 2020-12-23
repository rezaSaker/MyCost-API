<?php

/*
	*Recieve current username and new username
	*Update the current username with the new username
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
		$currentUsername = mysqli_real_escape_string($connect, $_POST['currentUsername']);
		$newUsername = mysqli_real_escape_string($connect, $_POST['newUsername']);
		$password        = mysqli_real_escape_string($connect, $_POST['password']);
		
		//verify password
		$query  = "SELECT * FROM users WHERE username = '$currentUsername' AND id = '$userid' LIMIT 1";
		$result = mysqli_query($connect, $query) or die('Server connection error');;		
		$row    = mysqli_fetch_array($result);
		$hashedPass = $row['password'];
	
	    if(password_verify($password, $hashedPass))
	    {
			//check if new username cotains risky characters			
			if($_POST['newUsername'] != $newUsername)
			{
				die('Please choose a different username.');
			}
			
			//check if new username is already taken 
			$query  = "SELECT id FROM users WHERE username = '$newUsername'";
			$result = mysqli_query($connect, $query) or die('Server connection error');
			$count  = mysqli_num_rows($result);
			
			if($count > 0)
			{
				die('Username is already taken. Please choose a different username');
			}
			
			//update the current username with the new username
			$query = "UPDATE users SET username = '$newUsername' WHERE username = '$currentUsername' AND id = '$userid'";
			mysqli_query($connect, $query) or die('Server connection error');
			
			die('SUCCESS');
		}
		else
		{
			die('Invalid password');
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