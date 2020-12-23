<?php
/*
	*Read categories from database 
	*Return the categories to MyCost app
*/

require_once('connectDB.php');
require_once('requestVerification.php');

if(isset($_POST['token']) && isset($_POST['userid']))
{
	$token  = mysqli_real_escape_string($connect, $_POST['token']);
	$userid = mysqli_real_escape_string($connect, $_POST['userid']);
	
	if(IsAuthenticRequest($connect, $userid, $token))//request verified as authentic
	{			
		//get expense categories for the user from DB
		$query  = "SELECT * FROM categories WHERE userid = '$userid'";
		$result = mysqli_query($connect, $query) or die('Server connection error');
		$row    = mysqli_fetch_array($result);		
		$data   = $row['earningCategories'] . '^' . $row['expenseCategories'];
		
		//return the data to MyCost app
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