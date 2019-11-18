<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';

// user_id 105
// workers id 217

 delete();

$num = 1;
$new_boss_arr[] = add_user($num);

$num++;
while($num<1155 && $n < 1000)
{  
 $i++;
 
	$boss_arr = $new_boss_arr;
	$new_boss_arr = array();
	foreach($boss_arr as $boss_id)
	{
		$tmprand = rand(1,10);
		
		if($i==1)
		{
			$workers_count = 10;
		}
		else if($i==2)
		{
			$workers_count = rand(1,5);
		}
		else if($i==2)
		{
			$workers_count = rand(2,7);
		}
		else if($tmprand==4)
		{
			$workers_count = rand(2,30);
		}
		else $workers_count = 0;
		
		for($j=0; $j<$workers_count; $j++)
		{
			$worker_id = add_user($num);
			add_workers($boss_id, $worker_id);
			$new_boss_arr[] = $worker_id;
			$num++;

		}
	}
	
	$n++;
 
}

function delete()
{
	global $site_db;
	
	$sql = "DELETE FROM tasks_users WHERE user_id > 105";
	
	$site_db->query($sql);
	
	$sql = "DELETE FROM tasks_workers WHERE id > 217";
	
	$site_db->query($sql);
	
	 
}
function add_workers($invite_user, $invited_user)
{
	global $site_db;
	
	$sql = "INSERT INTO tasks_workers SET invite_user='$invite_user', invited_user='$invited_user', invited_user_status=1";
	
	$site_db->query($sql);
}
function add_user($num)
{
	global $site_db;
	
	$user_login = "test".$num;
	$user_surname = 'Surname'.$num;
	$user_name = 'Name'.$num;
	$user_middlename = 'Middlename'.$num;
	
	
	$sql = "INSERT INTO tasks_users SET user_surname='$user_surname', user_name='$user_name', user_middlename='$user_middlename', user_activated=1, user_login='$user_login', user_password='a96b5eaa5cd42de7d3a826a7e6f6be41'";
	
	$site_db->query($sql);
	
	$user_id = $site_db->get_insert_id();
	
	$position = 'Должность'.$num;
	
	// Добавляем должность
	$sql = "INSERT INTO ".USERS_POSITIONS_TB." SET position_name='$position', user_id='$user_id', position_date=NOW()";
			
	$site_db->query($sql);
	
	return $user_id;
}


		  
/*$sql = "SELECT * FROM  tasks_messages";	  

$res = $site_db->query($sql);

while($row=$site_db->fetch_array($res))*/

	
	
?>
