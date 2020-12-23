<?php

/*
	*Read user's daily info from database
	*Return the data to MyCost app
*/

require_once('connectDB.php');
require_once('requestVerification.php');

if(isset($_POST['token']) && isset($_POST['userid']))
{
	$token  = mysqli_real_escape_string($connect, $_POST['token']);
	$userid = mysqli_real_escape_string($connect, $_POST['userid']);
	
	if(IsAuthenticRequest($connect, $userid, $token))//request verified as authentic
	{
		$rowNum = 1;
		$data   = "";
		
		//get all data for the user from DB
		$query   = "SELECT * FROM daily_info WHERE userid = '$userid' ORDER BY year DESC";
		$result  = mysqli_query($connect, $query) or die('Server connection error');
		
		while($row = mysqli_fetch_array($result))
		{
			if($rowNum > 1)
			{
				//adds a splitting character before each row except the first one
				$data .= '^';
			}
			else
			{
				$rowNum++;
			}
			
			$data .= $row['day'] . '|' . $row['month'] . '|' . $row['year'] . '|' . $row['note'] . '|'; 
			$data .= $row['expenseReasons'] . '|' . $row['expenseAmounts'] . '|';
			$data .= $row['expenseCategories'] . '|' . $row['expenseComments'] . '|';
			$data .= $row['earningSources'] . '|' . $row['earningAmounts'] . '|';
			$data .= $row['earningCategories'] . '|' . $row['earningComments'] . '|';
			$data .= $row['totalExpense'] . '|' .$row['totalEarning'];
		}
		
		//return the data to the MyCost app
		die($data);
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