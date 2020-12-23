<?php

/*
	*Receive data sent from MyCost app 
	*Register a new user
	*Returns the user id and an unique token 
*/

require_once('connectDB.php');

if(isset($_POST['username']) && isset($_POST['password']) 
	&& isset($_POST['activationCode']) && isset($_POST['cipherKey']))
{	
	$username              = mysqli_real_escape_string($connect, $_POST['username']);
	$password              = mysqli_real_escape_string($connect, $_POST['password']);
	$activationCode        = mysqli_real_escape_string($connect, $_POST['activationCode']);
	$cipherKey             = mysqli_real_escape_string($connect, $_POST['cipherKey']);
	$emergencyCipherKey    = mysqli_real_escape_string($connect, $_POST['emergencyCipherKey']);
	$encryptedEmail        = mysqli_real_escape_string($connect, $_POST['encryptedEmail']);
	$originalEmail         = mysqli_real_escape_string($connect, $_POST['originalEmail']);
	$emailVerificationCode = mysqli_real_escape_string($connect, $_POST['emailVerificationCode']);

	//verify the request
	$query  = "SELECT * FROM activation_codes WHERE code = '$activationCode'";
	$result = mysqli_query($connect, $query) or die('Server connection error');
	$count  = mysqli_num_rows($result);
	
	if($count < 1)
	{
		//activation code is invalid
		die('Invalid activation code');
	}
	
	//if the filtered username doesn't match the exact username as entered by the user,
	//then the username contains potential risky character and new username would be required
	if($username != $_POST['username']) 
	{
		die('Please choose a different username');
	}	
	
	//check if the username already exists
	$query  = "SELECT * FROM users WHERE username = '$username'";
	$result = mysqli_query($connect, $query) or die('Server connection error');
	$count  = mysqli_num_rows($result);
	
	if($count > 0)
	{
		die('This username is taken. Please choose a different username.');
	}

	//check if the email already exists in the database
	$query  = "SELECT comparable_email FROM users";
	$result = mysqli_query($connect, $query) or die('Server connection error');

	while($row = mysqli_fetch_array($result))
	{
		if(password_verify($originalEmail, $row['comparable_email']))
		{
			die('This email is already in use. Please choose a different email.');
		}
	}
	
	//generate a random string as temporary access token
	$token = RandomToken();
	
	//hash password
	$password = password_hash($password, PASSWORD_DEFAULT);
	$comparableEmail = password_hash($originalEmail, PASSWORD_DEFAULT);

	//save the user info
	$query  = "INSERT INTO users (username, password, token, cipher_key, emergency_cipher_key, decryptable_email, comparable_email, verification_code) VALUES ('$username', '$password', '$token', '$cipherKey', '$emergencyCipherKey', '$encryptedEmail', '$comparableEmail','$emailVerificationCode')";
	$result = mysqli_query($connect, $query) or die('Server connection error');	
	$userid = mysqli_insert_id($connect);
	
	//add a new row in category table with the default categories for this user
	$earningCategories = 'Pay cheque|Business|Gift|Bonus|Refund|Other';
	$expenseCategories = 'House rent|Car|Transit|Groccery|Medical expense|Eating outside|Other';
	
	$query  = "INSERT INTO categories (earningCategories, expenseCategories, userid) 
			  VALUES ('$earningCategories', '$expenseCategories', '$userid')";
	$result = mysqli_query($connect, $query) or die("Server connection error");

	//Activation code expire after one user
	//so delete the activation code
	$query  = "DELETE FROM activation_codes WHERE code = '$activationCode'";
	$result = mysqli_query($connect, $query) or die('Server connection error');
	
	//return the userid, the access token and the cypher key for user's data decryption 
	die($userid . '|' . $token . '|' . $cipherKey . '|' . $encryptedEmail . '|' . '0' . '|' . $emailVerificationCode . '|' . 'New User');	
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