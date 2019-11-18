<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_boss.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_personal.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_deputy.php';

// Класс авторизации
$auth = new CAuth($site_db);

$mode = $_POST['mode'];

$current_user_id = $auth->get_current_user_id();

switch($mode)
{
	// Подтверждение добавление в список
	case 'confirm_to_worker_list':
		
		$invite_user_id = $_POST['invite_user_id'];
		
		$invited_user_id = $_POST['invited_user_id'];
		
		if($invited_user_id != $current_user_id)
		{
			exit();
		}
		
		// Обновляем
		$sql = "UPDATE ".WORKERS_TB." SET invited_user_status=1 WHERE invite_user='$invite_user_id' AND invited_user='$invited_user_id' AND deputy_id = 0";
		
		$site_db->query($sql);
		
		
		// Проверяем, есть ли новые начальники
		$new_boss_count = get_new_boss_count_for_user($invited_user_id);
	
		echo json_encode(array('new_boss_count'=>$new_boss_count));
		
	break;
	
	
	// Отклонить добавление в список
	case 'not_confirm_to_worker_list':
		
		$invite_user_id = $_POST['invite_user_id'];
		
		$invited_user_id = $_POST['invited_user_id'];
		
		if($invited_user_id != $current_user_id)
		{
			exit();
		}
		
		// Обновляем
		$sql = "UPDATE ".WORKERS_TB." SET invited_user_status=2 WHERE invite_user='$invite_user_id' AND invited_user='$invited_user_id' AND deputy_id = 0";
		
		$site_db->query($sql);
		
		// Проверяем, есть ли новые начальники
		$new_boss_count = get_new_boss_count_for_user($invited_user_id);
	
		echo json_encode(array('new_boss_count'=>$new_boss_count));
		
	break;
	
	case 'get_boss_list':
		
		$user_id = $_POST['user_id'];
		
		// Список начальников
		$boss_list = fill_boss_list($user_id);
		
		echo $boss_list;
	
	break;
}

?>