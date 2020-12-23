<?php
/*
	*Receive day, month and year
	*Delete info of that particular date for the user
	*Return success or error message
*/

require_once('connectDB.php');
require_once('requestVerification.php');

if(isset($_POST['token']) && isset($_POST['userid']))
{
	$token  = mysqli_real_escape_string($connect, $_POST['token']);
	$userid = mysqli_real_escape_string($connect, $_POST['userid']);
	
	if(IsAuthenticRequest($connect, $userid, $token))//request verified as authentic
	{	
		$day   = $_POST['day'];
		$month = $_POST['month'];
		$year  = $_POST['year'];
		
		$query = "DELETE FROM daily_info WHERE day = '$day' AND month = '$month'
				  AND year = '$year' AND userid = '$userid'";
		mysqli_query($connect, $query) or die('Server connection error');

		die('SUCCESS');
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