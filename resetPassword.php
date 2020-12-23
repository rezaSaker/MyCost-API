<?php

require_once('../../../../../cnct/connectDB.php');
require_once('../../../../../cnct/requestVerification.php');

if(isset($_POST['email']) && isset($_POST['tempPassword']))
{
	$email = mysqli_real_escape_string($connect, $_POST['email']);
	$tempPassword = mysqli_real_escape_string($connect, $_POST['tempPassword']);

	$query = "SELECT * FROM users";
	$result = mysqli_query($connect, $query) or die('Server connection error');

	while($row = mysqli_fetch_array($result))
	{
		if(password_verify($email, $row['comparable_email']))
		{
			$id = $row['id'];
			$username = $row['username'];
			$token = $row['token'];
			$emergencyCipherKey = $row['emergency_cipher_key'];

			$newPassword = password_hash($tempPassword, PASSWORD_DEFAULT);

			$query = "UPDATE users SET password = '$newPassword' WHERE id = '$id'";
			mysqli_query($connect, $query) or die('Server connection error');

			die($id . '|' . $token . '|' . $username . '|' . $emergencyCipherKey);
		}
	}

	die('Email not found. Please enter the email that is associated with your account.');
}
else
{
	die('Server connection error');
}

?>