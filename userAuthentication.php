<?php

/*
	*Recieve data sent from MyCost App
	*Verify user's login credentials
	*Return the user's id 
*/

require_once('connectDB.php');
	
if(isset($_POST['username']) && isset($_POST['password']))
{	
	$username = mysqli_real_escape_string($connect, $_POST['username']);
	$password = mysqli_real_escape_string($connect, $_POST['password']);
			
	//verify user's login credentials
	$query  = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
	$result = mysqli_query($connect, $query) or die('Server connection error');;		
	$count  = mysqli_num_rows($result);
	
	if($count > 0)
	{
		$row = mysqli_fetch_array($result);
		$hashedPass = $row['password'];
		
		if(password_verify($password, $hashedPass))
		{
			$userid = $row['id'];
			$cipherKey = $row['cipher_key'];
			$email = $row['decryptable_email'];
			$isEmailVerified = $row['is_email_verified'];
			$verificationCode = $row['verification_code'];

			//generate a random string as temporary access token
			$token = RandomToken();
			
			//update the previous token with this new token
			$query = "UPDATE users SET token = '$token' WHERE id = '$userid'";
			mysqli_query($connect, $query) or die('Server connection error');
			
			die($userid . '|' . $token . '|' . $cipherKey . '|' . $email .'|' . $isEmailVerified . '|' . $verificationCode . '|' .  'Existing User'); 
			//sending 'Existing user' will prevent the app from sending 
			//verification email to the user's email			
		}
		else
		{ 
			die('Invalid username or password'); 
		}
	}
	else
	{
		die('Invalid username or password');
	}
}
else 
{ 
	die('Server connection error');
}

function RandomToken()
{	
	$charSet    = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
	$charSetLen = strlen($charSet);
	$tokenLen   = mt_rand(70, 100);
	$randStr    = "";
	
	for($i = 0; $i < $tokenLen; $i++)
	{
		$index = rand(0, $charSetLen - 1);
		$randStr .= $charSet[$index];
	}
	
	return $randStr;
}
			
?>