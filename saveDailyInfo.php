<?php
/*
	*Recieve data that is sent from MyCost app
	*Save the data into the 'mycost' database
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
		$note              = mysqli_real_escape_string($connect, $_POST['note']);
		$day               = mysqli_real_escape_string($connect, $_POST['day']);
		$month             = mysqli_real_escape_string($connect, $_POST['month']);
		$year              = mysqli_real_escape_string($connect, $_POST['year']);
		$expenseReasons    = mysqli_real_escape_string($connect, $_POST['expenseReasons']);
		$expenseAmounts    = mysqli_real_escape_string($connect, $_POST['expenseAmounts']);
		$expenseCategories = mysqli_real_escape_string($connect, $_POST['expenseCategories']);
		$expenseComments   = mysqli_real_escape_string($connect, $_POST['expenseComments']);
		$earningSources    = mysqli_real_escape_string($connect, $_POST['earningSources']);
		$earningAmounts    = mysqli_real_escape_string($connect, $_POST['earningAmounts']);
		$earningCategories = mysqli_real_escape_string($connect, $_POST['earningCategories']);
		$earningComments   = mysqli_real_escape_string($connect, $_POST['earningComments']);
		$totalExpense      = mysqli_real_escape_string($connect, $_POST['totalExpense']);
		$totalEarning      = mysqli_real_escape_string($connect, $_POST['totalEarning']);
		
		//If any row already exists with the same day, month and year,
		//then we will update that, otherwise we'll insert a new row       
		
		//check for existing rows
		$query  = "SELECT id FROM daily_info WHERE day = '$day' AND month = '$month'
					AND year = '$year' AND userid = '$userid'";
		$result = mysqli_query($connect, $query) or die('Server connection error');
		$count  = mysqli_num_rows($result);
		
		if($count > 0)
		{
			$row = mysqli_fetch_array($result);
			$id  = $row['id'];
			
			$query  = "UPDATE daily_info SET note = '$note', expenseReasons = '$expenseReasons',
						expenseAmounts = '$expenseAmounts', expenseCategories = '$expenseCategories',
						expenseComments = '$expenseComments', earningSources = '$earningSources',
						earningAmounts = '$earningAmounts', earningCategories = '$earningCategories',
						earningComments = '$earningComments', totalExpense = '$totalExpense',
						totalEarning = '$totalEarning' WHERE id = '$id'";
			$result = mysqli_query($connect, $query) or die('Server connection error');
			
			die('SUCCESS');
		}
		else
		{
			$query  = "INSERT INTO daily_info (note, day, month, year, expenseReasons, expenseAmounts, 
					  expenseCategories, expenseComments, earningSources, earningAmounts, earningCategories,
					  earningComments, userid, totalExpense, totalEarning) VALUES ('$note', '$day', '$month',
					  '$year', '$expenseReasons', '$expenseAmounts', '$expenseCategories', '$expenseComments',
					  '$earningSources', '$earningAmounts', '$earningCategories', '$earningComments', '$userid',
					  '$totalExpense', '$totalEarning')";
			$result = mysqli_query($connect, $query) or die('Server connection error');
			
			die('SUCCESS');
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