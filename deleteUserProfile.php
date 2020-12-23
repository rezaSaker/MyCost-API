<?php
/*
	*Receive user id and access token
	*Verify request
	*Deleet the user profile permanently and Return SUCCESS or error message
*/

require_once('connectDB.php');
require_once('requestVerification.php');

if(isset($_POST['token']) && isset($_POST['userid']) && isset($_POST['password']))
{
	$token  = mysqli_real_escape_string($connect, $_POST['token']);
	$userid = mysqli_real_escape_string($connect, $_POST['userid']);
	$password = mysqli_real_escape_string($connect, $_POST['password']);
	
	if(IsAuthenticRequest($connect, $userid, $token))//request verified as authentic
	{		
		$query = "SELECT password FROM users WHERE id = '$userid'";
		$result = mysqli_query($connect, $query) or die('Server connection error');
		$count = mysqli_num_rows($result) or die('Server connection error');

		if($count > 0)
		{
			$row = mysqli_fetch_array($result);
			$hashedPass = $row['password'];

			if(password_verify($password, $hashedPass))
			{
				//delete the user
				$query = "DELETE FROM users WHERE id = '$userid'";
				mysqli_query($connect, $query) or die('Server connection error');

				//delete the info related to the user
				$query = "DELETE FROM daily_info WHERE userid = '$userid'";
				mysqli_query($connect, $query) or die('Server connection error');

				die('SUCCESS');
			}
			else
			{
				die('Incorrect password. Please try again.');
			}
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