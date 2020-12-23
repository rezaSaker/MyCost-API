<?php
/*
	*Receive category name and type
	*Save the category into the database
	*Return SUCEESS or error info
*/

require_once('connectDB.php');
require_once('requestVerification.php');

if(isset($_POST['token']) && isset($_POST['userid']))
{
	$token  = mysqli_real_escape_string($connect, $_POST['token']);
	$userid = mysqli_real_escape_string($connect, $_POST['userid']);
	
	if(IsAuthenticRequest($connect, $userid, $token))//request verified as authentic
	{	
		$categoryNames = mysqli_real_escape_string($connect, $_POST['categoryNames']);
		$categoryType  = mysqli_real_escape_string($connect, $_POST['categoryType']);			
		
		$query = "";		
		if($categoryType == 'Expense')
		{			
			$query = "UPDATE categories SET expenseCategories = '$categoryNames' WHERE userid = '$userid'";
		}
		else
		{
			$query = "UPDATE categories SET earningCategories = '$categoryNames' WHERE userid = '$userid'";
		}
		
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