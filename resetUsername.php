<?php

require_once('../../../../../cnct/connectDB.php');
require_once('../../../../../cnct/requestVerification.php');

if(isset($_POST['email']) && isset($_POST['tempUsername']))
{
	$email = mysqli_real_escape_string($connect, $_POST['email']);
	$tempUsername = mysqli_real_escape_string($connect, $_POST['tempUsername']);

	$query = "SELECT * FROM users";
	$result = mysqli_query($connect, $query) or die('Server connection error');

	while($row = mysqli_fetch_array($result))
	{
		if(password_verify($email, $row['comparable_email']))
		{
			$id = $row['id'];

			$query = "UPDATE users SET username = '$tempUsername' WHERE id = '$id'";
			mysqli_query($connect, $query) or die('Server connection error');

			die('SUCCESS');
		}
	}

	die('Email not found. Please enter the email that is associated with your account.');
}
else
{
	die('Server connection error');
}

?>