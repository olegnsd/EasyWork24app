<?php
if(preg_match('/local/', $_SERVER['HTTP_HOST']))
{
	$db_bills_host = 'localhost';
	$db_bills_name = 'easy_bills';
	$db_bills_user = 'root';
	$db_bills_password = 'Yf5CT4SWEq';
}
else
{
	$db_bills_host = 'localhost';
	$db_bills_name = 'easy_bills';
	$db_bills_user = 'bills_user';
	$db_bills_password = 'OzPWIzWweL';
}
?>