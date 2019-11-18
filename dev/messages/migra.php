<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';

// Массив подчиненных
// Добавить last dialog_edit_date
	//  truncate();
	  
$sql = "SELECT * FROM  tasks_messages";	  

$res = $site_db->query($sql);


$dialog = 1;
	 
while($row=$site_db->fetch_array($res))
{
	$message_id = $row['message_id'];
	
	$dialog_users_1 = $row['message_from_user_id'].'_'.$row['message_to_user_id'];
	$dialog_users_2 = $row['message_to_user_id'].'_'.$row['message_from_user_id'];
	
	if(!array_key_exists($dialog_users_1, $DIALOGS_ARR) && !array_key_exists($dialog_users_2, $DIALOGS_ARR))
	{
		$dialog = get_new_dialog_id();
		
		$DIALOGS_ARR[$dialog_users_1] = $dialog;
		
		$user_1 =  $row['message_from_user_id'];
		$user_2 =  $row['message_to_user_id'];
		
		// Пользователи для диалога
		set_dialog_users($dialog, $user_1);
		set_dialog_users($dialog, $user_2);
		 
		//$sql = "UPDATE tasks_messages SET dialog='$dialog' WHERE message_id='$dialog'";
		//$site_db->query($sql);
		
		
		//$dialog++;
		
	}
	else
	{
		if(array_key_exists($dialog_users_1, $DIALOGS_ARR))
		{
			$dialog = $DIALOGS_ARR[$dialog_users_1];
		}
		else if(array_key_exists($dialog_users_2, $DIALOGS_ARR))
		{
			$dialog = $DIALOGS_ARR[$dialog_users_2];
		}
		 
	//	$sql = "UPDATE tasks_messages SET dialog='$dialog_s' WHERE message_id='$dialog'";
	//	$site_db->query($sql);
	}
	
	$user_id = $row['message_from_user_id'];
	
	$message_date = to_mktime($row['message_date']);
	
	 $text  = addslashes($row['message_text']);
	
	$sql = "INSERT INTO tasks_dialogs_messages SET dialog_id='$dialog', user_id='$user_id', message_text='$text', message_date='".$message_date."'";
	
	$site_db->query($sql);
	
	
	$user_from =  $row['message_from_user_id'];
	$user_to =  $row['message_to_user_id'];
		
		 
	$_message_id = $site_db->get_insert_id();
	
	$status = 1; 
	if($row['message_from_user_deleted'])
	{
		$status = 2;
		$read_date = '';
	}
	
	$sql = "INSERT INTO tasks_dialogs_message_to_user SET message_id='$_message_id', dialog_id='$dialog', user_id='$user_from', status='$status', read_date='$read_date'";
	
	$site_db->query($sql);
	
	
	 
	$status = 0;
	if($row['message_to_user_noticed'])
	{
		$status = 1;
	}
	
	if($row['message_to_user_deleted'])
	{
		$status = 2;
	}
	
	$read_date = $row['read_date'];
	 
	 
	 $sql = "INSERT INTO tasks_dialogs_message_to_user SET message_id='$_message_id', dialog_id='$dialog', user_id='$user_to', status='$status', read_date='$read_date'";
	
	$site_db->query($sql);
	
	
 
	
	//$dialogs[] = $row['message_from_user_id'];
}
	   
echo "<pre>", print_r($DIALOGS_ARR);	  
	  
	  
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
	
	$sql = "INSERT INTO tasks_dialogs SET dialog_date_add=NOW(), last_edit_date=NOW()";
	
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
//truncate();
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
