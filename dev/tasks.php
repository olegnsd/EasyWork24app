<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';

 
import();

function import()
{
	global $site_db;
	clear();
	$sql = "SELECT * FROM tasks_user_tasks WHERE 1   ";
	
	$res = $site_db->query($sql);
				
	while($row=$site_db->fetch_array($res))
	{
		$is_own = 0;
		
		if($row['task_from_user']==$row['task_to_user'])
		{
			$is_own = 1;
		}
		
		$work_status = 1;
		
		
		if($row['task_finished_confirm']==1)
		{
			$work_status = 2;
			$step_status = 4;
			
			$finished_date = $row['task_finished_date'];
		}
		else if(is_date_exists($row['task_finished_fail_date']))
		{
			$work_status = 2;
			$step_status = 5;
			
			$finished_date = $row['task_finished_fail_date'];
		}
		else if($row['task_status']==0)
		{
			$work_status = 1;
			
			if($is_own)
			{
				$step_status = 1;
			}
			else $step_status = 0;
			 
		}
		else if($row['task_status']==1)
		{
			$step_status = 1;
		}
		else if($row['task_status']==2)
		{
			$step_status = 2;
		}
		else if($row['task_status']==3)
		{
			if($is_own)
			{
				$work_status = 2;
			}
			$step_status = 3;
			
			$finished_date = $row['task_finished_date'];
		}
		
		$text = value_proc($row['task_text'],0,1);
		$task_theme = value_proc($row['task_theme'],0,1);
		 
		// добавление задачи
		$sql = "INSERT INTO tasks_tasks 
				SET task_theme='".$task_theme."', task_text='".$text."', task_max_date='".$row['task_max_date']."', task_priority='".$row['task_priority']."',
				task_difficulty='".$row['task_difficulty']."', task_rating='".$row['task_rating']."', date_add='".$row['task_date']."', user_id='".$row['task_from_user']."', work_status='$work_status', step_status='$step_status', is_own='$is_own', date_status_1='".$row['task_confirm_date']."',
				date_status_2='".$row['task_in_proc_date']."', date_status_3='".$finished_date."', deleted='".$row['task_deleted']."'";
		
		$site_db->query($sql);
		
		$task_id = $site_db->get_insert_id();
		
		$sql = "INSERT INTO tasks_tasks_users SET task_id='$task_id', user_id='".$row['task_from_user']."', role=1";
		$site_db->query($sql);
		
		$sql = "INSERT INTO tasks_tasks_users SET task_id='$task_id', user_id='".$row['task_to_user']."', role=2";
		$site_db->query($sql);
		
		tasks_files($row['task_id'], $task_id);
		
		// отчеты
		reports($row, $task_id);
	}
	
	echo 'ok';
}
function tasks_files($old_task_data, $task_id)
{
	global $site_db;
	
	$sql = "UPDATE tasks_files_in_contents SET content_id='$task_id', `check`=1 WHERE content_id='$old_task_data' AND content_type=6 AND `check`=0";
	$site_db->query($sql);
}

function reports($old_task_data, $task_id)
{
	global $site_db;
	
	$old_task_id = $old_task_data['task_id'];
	
	$sql = "SELECT * FROM tasks_users_tasks_reports WHERE task_id='$old_task_id'";
	
	$res = $site_db->query($sql);
				
	while($row=$site_db->fetch_array($res))
	{
		 
		$report_text = value_proc($row['report_text'],0,1);
		
		$sql = "INSERT INTO tasks_tasks_reports SET task_id='$task_id', report_user_id='".$row['report_user_id']."', report_date='".$row['report_date']."', report_text='".$report_text."'";
		
		$site_db->query($sql);
		
		$new_report_id = $site_db->get_insert_id();
		
		$sql = "UPDATE tasks_files_in_contents SET content_id='$new_report_id' , `check`=1 WHERE content_id='".$row['report_id']."' AND content_type=7 AND `check`=0";
		$site_db->query($sql);
	}
}


function clear()
{
	global $site_db;
	
	$sql = "TRUNCATE table tasks_tasks";
	$site_db->query($sql);
	
	$sql = "TRUNCATE table tasks_tasks_users";
	$site_db->query($sql);
	
	$sql = "TRUNCATE table tasks_tasks_reports";
	$site_db->query($sql);
	
	 
}
?>