<?php

/*
	*Recieve a new email address
	*Update the current email with the new email
	*Return SUCCESS or error message
*/

require_once('../../../../../cnct/connectDB.php');
require_once('../../../../../cnct/requestVerification.php');
	
if(isset($_POST['token']) && isset($_POST['userid']))
{
	$token  = mysqli_real_escape_string($connect, $_POST['token']);
	$userid = mysqli_real_escape_string($connect, $_POST['userid']);
	
	if(IsAuthenticRequest($connect, $userid, $token))//request verified as authentic
	{		
		$username         = mysqli_real_escape_string($connect, $_POST['username']);
		$password         = mysqli_real_escape_string($connect, $_POST['password']);
		$decryptableEmail = mysqli_real_escape_string($connect, $_POST['encryptedEmail']);
		$originalEmail    = mysqli_real_escape_string($connect, $_POST['originalEmail']);
		$emailVerificationCode = mysqli_real_escape_string($connect, $_POST['emailVerificationCode']);
		$emergencyCipherKey = mysqli_real_escape_string($connect, $_POST['emergencyCipherKey']);
		//verify password
		$query  = "SELECT * FROM users WHERE username = '$username' AND id = '$userid' LIMIT 1";
		$result = mysqli_query($connect, $query) or die('Server connection error');;		
		$row    = mysqli_fetch_array($result);
		$hashedPass = $row['password'];
	
	    if(password_verify($password, $hashedPass))
	    {	
			//check if new username is already taken 
			$query  = "SELECT comparable_email FROM users";
			$result = mysqli_query($connect, $query) or die('Server connection error');

			//check if the new email is already used
			while($row = mysqli_fetch_array($result))
			{
				if(password_verify($originalEmail, $row['comparable_email']))
				{
					die('This email is already in use. Please choose a different email.');
				}
			}

			//if the email is not used by anyone else
			$comparableEmail = password_hash($originalEmail, PASSWORD_DEFAULT);

			//update the current email with the new email
			$query = "UPDATE users SET emergency_cipher_key = '$emergencyCipherKey', comparable_email = '$comparableEmail', decryptable_email = '$decryptableEmail', verification_code = '$emailVerificationCode', is_email_verified = '0' WHERE username = '$username' AND id = '$userid'";
			mysqli_query($connect, $query) or die('Server connection error');
			
			die($emailVerificationCode);
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