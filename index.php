<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';

if($_GET['info']==5) phpinfo(); 

if($_GET['o']=='pub_file')
{
	 
}
else
{
	// ѕроверка авторизации
	if(!$_SESSION['user_id'] && $_GET['o'] != 'auth' && $_GET['o']!='disk_gdrive_auth')
	{
		header('Location: /auth');
		exit();
	}
	
	ini_set('log_errors', 'On');
	ini_set('error_log', $_SERVER['DOCUMENT_ROOT'].'/log.txt');
	
	// ѕеренаправл€ет пользовател€ на нужные страницы при необходимости
	redirect_user_to_page($current_user_id);
	
	// Ќевыполненные задачи обновл€ем по дате
	//tasks_date_to_actual($current_user_id);
		
	// «апись в базу дату последнего посещени€ пользовател€ на сайте
	set_last_user_visit_date($current_user_id);
	
	
	 
}

include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_worktime.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_content.php';
 

fill_content($o);

?>
