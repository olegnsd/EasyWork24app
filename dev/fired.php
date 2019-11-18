<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';

$sql = "SELECT * FROM tasks_users_remove_from_work"; 

$res = $site_db->query($sql);
				
	while($row=$site_db->fetch_array($res))
	{
		$sql = "UPDATE tasks_users SET is_fired = 1 WHERE user_id='".$row['user_id']."'";
		$site_db->query($sql);
	}
	
	
	
	
 
?>