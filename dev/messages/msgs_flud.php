<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';
// Массив подчиненных
	  
mysql_select_db('msg_test');

 
set_time_limit(0); 
ini_set("max_execution_time", "0");

 
 "SELECT i.dialog_id
FROM tasks_dialogs_users i
LEFT OUTER JOIN tasks_dialogs_deleted j ON j.dialog_id = i.dialog_id
AND j.user_id =145
WHERE i.user_id =145
AND j.dialog_id IS NULL ";



 	
	"SELECT j.dialog_id 
FROM tasks_dialogs_deleted  i 
right JOIN  tasks_dialogs_users j ON j.dialog_id=i.dialog_id

WHERE j.user_id=145 AND i.user_id=145 AND i.dialog_id IS NULL";
	// Выбор диалогов пользователя
	$sql = "SELECT i.dialog_id 
			FROM tasks_dialogs_users i
			LEFT JOIN tasks_dialogs_deleted j ON j.dialog_id=i.dialog_id
			WHERE i.user_id=145 AND j.user_id=145 AND j.dialog_id IS NULL";
			
	$sql = "SELECT i.dialog_id 
			FROM tasks_dialogs_users i
			LEFT OUTER JOIN tasks_dialogs_deleted j ON j.dialog_id=i.dialog_id
			WHERE i.user_id=145 AND j.user_id=145 ";
					
	//$res = $site_db->query($sql);
	//	echo $sql;
	while($row=$site_db->fetch_array($res))
	{
		$dialog_arr[] = $row['dialog_id'];
	}
	
	 
	
	$workers_arr = array(32,112,13,15,42,134,12,46,67,89,1,119,169,123,143,31,23,24,25,26,27,57,68,68,90,4,2,1,34,76,8,98,89,87,65,43,21);
	$workers = implode(',', $workers_arr);
	
	$dialogs = implode(',', $dialog_arr);
	
	//$user_arr g
	
	// Выбор доступных диалогов
	$sql = "SELECT * FROM tasks_dialogs_users WHERE dialog_id IN($dialogs) AND user_id IN($workers)";
	//$res = $site_db->query($sql);
		// echo $sql;
	while($row=$site_db->fetch_array($res))
	{
		$dialog_arr = $row['dialog_id'];
	}


	
	// Поиск диалога
	$name = 'оле';
	// Выбор доступных диалогов
	$sql = "SELECT i.* FROM tasks_dialogs_users i 
			LEFT JOIN tasks.tasks_users j ON i.user_id=j.user_id
			WHERE dialog_id IN($dialogs) AND j.user_id IN($workers) AND (j.user_surname LIKE '$name%' OR j.user_name LIKE '$name%')";
	
	//echo $sql;
	
	
	
	
	//$n = array_fill(0,201,1);
	for($i=0;$i<201;$i++)
	{
		$n[] = $i;
	}
	//print_r($n);
	
	$workers_arr = array(32,112,13,15,42,134,12,46,67,89,1,119,169,123,143,31,23,24,25,26,27,57,68,68,90,4,2,1,34,76,8,98,89,87,65,43,21,33,75,60,33,41,169,14,72);
	$workers = implode(',', $n);	
	
	
	$sql = "SELECT DISTINCT(i.dialog_id) FROM tasks_dialogs_users i
			LEFT JOIN tasks_dialogs_users j ON i.dialog_id=j.dialog_id
			WHERE i.user_id = 199 AND j.user_id IN($workers)";
	 
	 
	 
	 
	 
	 
	 
	
	// Выбор диалогов
	$sql = "SELECT DISTINCT(i.dialog_id) 
			FROM tasks_dialogs i
			LEFT JOIN tasks_dialogs_message_to_user j ON i.dialog_id = j.dialog_id
			LEFT JOIN tasks_dialogs_users z ON i.dialog_id=z.dialog_id
			LEFT JOIN tasks_dialogs_users x ON z.dialog_id=x.dialog_id
			WHERE j.user_id = 199 AND z.user_id = 199 AND x.user_id IN($workers)
			AND j.status <>2
";


$n = 'оле';
	// Выбор диалогов ПОИСК ПО ИМЕНИ
	$sql = "SELECT DISTINCT(i.dialog_id) 
			FROM tasks_dialogs i
			LEFT JOIN tasks_dialogs_message_to_user j ON i.dialog_id = j.dialog_id
			LEFT JOIN tasks_dialogs_users z ON i.dialog_id=z.dialog_id
			LEFT JOIN tasks_dialogs_users x ON z.dialog_id=x.dialog_id
			LEFT JOIN tasks.tasks_users u ON x.user_id=u.user_id
			WHERE j.user_id = 199 AND z.user_id = 199 AND x.user_id IN($workers)
			AND j.status <>2 AND (u.user_surname LIKE '$n%' OR u.user_name LIKE '$n%')
";


// Выбор диалогов
	$sql = "SELECT SQL_NO_CACHE DISTINCT(i.dialog_id), i.last_edit_date
			FROM tasks_dialogs i
			LEFT JOIN tasks_dialogs_message_to_user j ON i.dialog_id = j.dialog_id
			LEFT JOIN tasks_dialogs_users z ON i.dialog_id=z.dialog_id
			LEFT JOIN tasks_dialogs_users x ON z.dialog_id=x.dialog_id
			WHERE j.user_id = 199 AND z.user_id = 199 AND x.user_id IN($workers)
			AND j.status <>2   ";
			
 	
			

echo $sql;

	//echo $sql;
	
	
	


 // truncate();

// flud();

function flud()
{
	
	for($i=0; $i<100; $i++)
	{
		/*$user_1 = 199;
		$user_2 = $i+1;*/
		
		$stop = 0;
		while(!$stop)
		{
			$user_1 = rand(1,200);
			
			$user_2 = rand(1, 200);
			
			if($user_1!=$user_2)
			{
				$stop = 1;
			}
		}

		$dialog_id = get_new_dialog_id();
		 
		set_dialog_users($dialog_id,$user_1);
		set_dialog_users($dialog_id,$user_2);
		 
		set_dialog_msgs($dialog_id, array($user_1, $user_2), 25000); 
	}
	
}
	 
	
function set_dialog_msgs($dialog_id, $user_arr, $num)
{
	global $site_db;
	
	 
	
	for($i=0; $i<$num; $i++)
	{
		$user_rand = array_rand($user_arr);
	
		$user_id = $user_arr[$user_rand];
	
		$sql = "INSERT INTO tasks_dialogs_messages SET dialog_id='$dialog_id', user_id='$user_id', message_text='$text', message_date=NOW()";
		$site_db->query($sql);
		$message_id = $site_db->get_insert_id();
		
		$status[0] = 0;
		$status[1] = 0;
		$status[$user_rand] = 1;
		
		$sql = "INSERT INTO tasks_dialogs_message_to_user SET message_id='$message_id', dialog_id='$dialog_id', user_id='".$user_arr[0]."', status='".$status[0]."'";
		$site_db->query($sql);
		
		$sql = "INSERT INTO tasks_dialogs_message_to_user SET message_id='$message_id', dialog_id='$dialog_id', user_id='".$user_arr[1]."', status='".$status[1]."'";
		$site_db->query($sql);
		
	}
}
function get_new_dialog_id()
{
	global $site_db;
	
	$sql = "INSERT INTO tasks_dialogs SET dialog_date_add=NOW()";
	
	$site_db->query($sql);
	
	$dialog_id = $site_db->get_insert_id();
	
	return $dialog_id;
}	

function set_dialog_users($dialog_id, $user_id)
{
	global $site_db;
	
	$sql = "INSERT INTO tasks_dialogs_users SET dialog_id = '$dialog_id', user_id='$user_id'"; 
	
	$site_db->query($sql);
}

function truncate()
{
	global $site_db;
	
	$sql = "TRUNCATE TABLE tasks_dialogs";
	$site_db->query($sql);
	
	$sql = "TRUNCATE TABLE tasks_dialogs_users";
	$site_db->query($sql);
	
	$sql = "TRUNCATE TABLE tasks_dialogs_messages";
	$site_db->query($sql);
	
	$sql = "TRUNCATE TABLE tasks_dialogs_message_to_user";
	$site_db->query($sql);
	
	exit();
}

function flud_dialog_deleted()
{
	global $site_db;
	
	for($i=0; $i<10000; $i++)
	{
		$dialog_id = rand(1,9300);
		$user_id = rand(1,200);
		
		$sql = "INSERT INTO tasks_dialogs_deleted SET 	dialog_id='$dialog_id', user_id='$user_id', date=NOW()";
		
		$site_db->query($sql);
	}
	
	echo 1;
	exit();
}
?>
