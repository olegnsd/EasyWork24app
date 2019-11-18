<?php
function fill_content()
{
	global $auth, $current_user_id;
	
	if(get_current_user_users_arrs(array(0,1,0,0,1)))
	{
		// Определяем раздел
		$o = isset($_GET['o']) && $_GET['o']!='' ? $_GET['o'] : 'main';
	}
	else
	{
		// Определяем раздел
		$o = isset($_GET['o']) && $_GET['o']!='' ? $_GET['o'] : 'tasks';
	}
	
	//if($_GET['un']==1)
	//unset($_SESSION['upload_token']);
	// Раздел
	switch($o)
	{	
		case 'ucontrol':
			
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_ucontrol.php';
			
			$body_content = fill_ucontrol();
			
			$nav_obj = 'ucontrol';
			
		break;
		case 'tasks':
			
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_tasks1.php';
			
			if($_GET['tid'])
			{
				$body_content = fill_task($_GET['tid']);
			}
			else
			{
				$body_content = fill_tasks();
			}
			 
			
			$nav_obj = 'tasks';
			
		break;
		case 'c_structure':
			
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_org.php';
			
			$body_content = fill_structure();
			
			$nav_obj = 'c_structure';
			
		break;
		
		case 'org':
		
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_org.php';
			
			$body_content = fill_org();
			
			$nav_obj = 'org';
			
		break;
		case 'disk_gdrive_auth':
			
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_disk.php'; 
			
			$body_content = disk_gdrive_auth();
		break;
		case 'disk_doc_edit':
			
			include_once($_SERVER['DOCUMENT_ROOT'].'/classes/class.Upload.php');
			include_once($_SERVER['DOCUMENT_ROOT'].'/classes/class.File.php');
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_disk.php'; 
			 
			$body_content = disk_doc_edit($_GET['id']);
			
		break;
		case 'pub_file':
			
			include_once($_SERVER['DOCUMENT_ROOT'].'/classes/class.Upload.php');
			include_once($_SERVER['DOCUMENT_ROOT'].'/classes/class.File.php');
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_disk.php'; 
			
			$body_content = fill_pub_document($_GET['id']);
			 
		break;
		case 'disk_download':
			
			include_once($_SERVER['DOCUMENT_ROOT'].'/classes/class.Upload.php');
			include_once($_SERVER['DOCUMENT_ROOT'].'/classes/class.File.php');
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_disk.php'; 
			
			$body_content = fill_disk_download($_GET['id'], $_GET['wh'], $_GET['force_download'],$_GET['cont_id']);
			
			
		break;
		case 'disk':
			
			include_once($_SERVER['DOCUMENT_ROOT'].'/classes/class.Upload.php');
			include_once($_SERVER['DOCUMENT_ROOT'].'/classes/class.File.php');
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_disk.php'; 
			
			if($_GET['f'])
			{
				$body_content = fill_file();
			}
			else
			{
				$body_content = fill_disk();
			}
			
			$nav_obj = 'disk';
			
		break;
		case 'posttr':
			
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_post_tracking.php';
			include_once $_SERVER['DOCUMENT_ROOT'].'/classes/class.RussianPostAPI.php'; 
			$body_content = fill_posttr();
			
			$nav_obj = 'posttr';
			
		break;
		case 'evcal':
			 
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_calendar_of_events.php';
			
			$body_content = fill_evcal();
			
			$nav_obj = 'evcal';
			
		break;
		case 'cnews':
			
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_cnews.php';
			
			$body_content = fill_cnews();
			
			$nav_obj = 'cnews';
			
		break;
		case 'grhy':
			
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_grhy.php';
			
			$body_content = fill_grhy();
			
			$nav_obj = 'grhy';
			
		break;
		case 'projects':
			
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_projects.php';
			
			if($_GET['id'])
			{
				$body_content = fill_show_project($_GET['id']);
			}
			// Проекты пользователя
			else
			{
				$body_content = fill_projects();
			}
			
			$nav_obj = 'tasks_projects';
			
		break;
		case 'rfw':
			
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_notes.php';
			
			$body_content = fill_user_removed_from_work_notice($user_id);
			
			$nav_obj = 'rfw';
			
		break;
		case 'notes':
		
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_notes.php';
			
			$body_content = fill_notes($user_id);
			
			$nav_obj = 'notes';
			
		break;
		case 'external':
		
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_external.php';
			
			$body_content = fill_external($user_id);
			
			$nav_obj = 'external';
			
		break;
		case 'deputy':
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_colleagues.php';
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_workers.php';
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_deputy.php';
			
			if($_GET['wks'])
			{
				$user_id = '';
			}
			else
			{
				$user_id = $current_user_id;
			}
			
			$body_content = fill_deputy($user_id);
			
			$nav_obj = 'deputy';
			
		break;
		case 'reprimand':
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_reprimand.php';
			
			if($_GET['wks'])
			{
				$user_id = '';
			}
			else
			{
				$user_id = $current_user_id;
			}
			
			$body_content = fill_reprimand($user_id);
			
			$nav_obj = 'reprimand';
			
		break;
		case 'ofdocs':
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_ofdocs.php';
			
			if($_GET['wks'])
			{
				$user_id = '';
			}
			else
			{
				$user_id = $current_user_id;
			}
			
			$body_content = fill_ofdocs($user_id);
			
			$nav_obj = 'ofdocs';
			
		break;
		case 'planning':
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_planning.php';
			
			if($_GET['wks'])
			{
				$user_id = '';
			}
			else
			{
				$user_id = $current_user_id;
			}
			
			$body_content = fill_planning($user_id);
			
			$nav_obj = 'planning';
		break;
		case 'finances':
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_finances.php';
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_clients.php';
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_colleagues.php';
			
			if($_GET['finance_id'])
			{
				$body_content = fill_edit_finance($_GET['finance_id']);
				
				 
			}
			else
			{
				$user_id = $_GET['user_id'] ? $_GET['user_id'] : $current_user_id;
				
				$body_content = fill_finances($user_id);
			}
			 
			$nav_obj = 'finance';
		break;
		// Коллеги
		case 'colleagues':
			
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_colleagues.php';
			
			$body_content = fill_colleagues($_GET['user_id']);
			
			$nav_obj = 'colleagues';
			
		break;
		// Автотранспорт
		case 'auto':
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_auto.php';
			
			$body_content = fill_auto($_GET['user_id']);
			
			$nav_obj = 'auto';
		break;
		
		// Видеонаблюдения
		case 'cam':
			
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_camera.php';
			
			$body_content = fill_cam($_GET['user_id']);
			
			$nav_obj = 'camera';
	
		break;
		
		// Рабочее время
		case 'wktime':
			
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_parse.php';
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_deputy.php';
			
			$user_id = $_GET['user_id'];
			
			if($_GET['cmp'])
			{
				$body_content = fill_worktime_user_computer($_GET['id']);
				
				$nav_obj = 'computer';
			}
			else
			{
				$body_content = fill_worktime($user_id);
				
				$nav_obj = 'wktime';
			}
			
		break;
		
		// Финансы
		case 'money':
			
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_boss.php';
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_workers.php';
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_money.php';
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_colleagues.php';
			
			$body_content = fill_user_money($_GET['id']);
		
			$nav_obj = 'money';
		break;
		
		// Имущества
		case 'goods':
			
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_goods.php';
			
			$user_id = $_GET['user_id'];
			
			$good_id = $_GET['good_id'];
			
			// редактирование имущества
			if($good_id)
			{
				$body_content = fill_good_edit($good_id);
			}
			else
			{
				$body_content = fill_goods($user_id);
			}
			$nav_obj = 'goods';
			
		break;
		// Дерево приглашений пользователя
		case 'deals':
			
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_deals.php';
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_clients.php';
			
			$user_id = $_GET['user_id'] ? $_GET['user_id'] : 0;
			
			$deal_id = $_GET['deal_id'] ? $_GET['deal_id'] : 0;
			
			$downl_call = $_GET['downl_call'] ? $_GET['downl_call'] : 0;
			
			// Если просматриваем сделку
			if($deal_id && !$downl_call)
			{
				$body_content = fill_deal_edit($deal_id);
				
				$nav_obj = 'deal_edit';
			}
			else
			{
				$body_content = fill_deals($user_id, $deal_id, $downl_call);
				
				$nav_obj = 'deals';
			}
			
			 
			
		break;
		
		// Дерево приглашений пользователя
		case 'clients':
			
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_deals.php';
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_clients.php';
			
			
			$user_id = $_GET['user_id'] ? $_GET['user_id'] : 0;
			
			if($_GET['import']==1)
			{
				$body_content = fill_client_import($_GET['id']);
				
			}
			else if($_GET['show']==1 && $_GET['id'])
			{
				$body_content = fill_show_client($_GET['id']);
				
			}
			else if($_GET['msg']==1 && $_GET['id'])
			{
				include_once $_SERVER['DOCUMENT_ROOT'].'/client/includes/functions_messages.php';
				
				$body_content = fill_client_messages($_GET['id'], 0, 1);
			}
			else if($_GET['files']==1 && $_GET['id'])
			{
				include_once $_SERVER['DOCUMENT_ROOT'].'/client/includes/functions_files.php';
				
				$body_content = fill_clients_files($_GET['id'], 'user');
			}
			else
			{
				$body_content = fill_clients($user_id);
			}
			
			$nav_obj = 'clients';
			
		break;
		
		// Дерево приглашений пользователя
		/*case 'task_to_users':
			
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_tasks.php';
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_deputy.php';
			
			$body_content = fill_tasks_from_user_to_users($current_user_id);
			
			$nav_obj = 'task_to_users';
			
		break;*/
		
		// Дерево приглашений пользователя
		case 'tree':
			
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_users.php';
			
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_tree.php';
			
			$body_content = fill_user_tree($_GET['user_id']);
			$nav_obj = 'tree';
		break;
		// Контакты
		case 'contacts':
			
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_contacts.php';
			 
			// Если осуществляется страница поиска
			if($_GET['wks'])
			{
				$body_content = fill_contacts(0);
			}
			else
			{
				$body_content = fill_contacts($current_user_id);
			}
			
			$nav_obj = 'contacts';
			
		break;
		case 'personal':
			
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_users.php';
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_efficiency.php';
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_deputy.php';
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_colleagues.php';
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_reprimand.php';
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_money.php';
			
			$user_id = $_GET['user_id'];
			
			$user_id = $user_id ? $user_id : $current_user_id;
			
			$body_content = fill_personal($user_id);
			
			$nav_obj = 'personal';
			
		break;
		// Загрузить файл клиента
		case 'cl_download':
			
			include_once $_SERVER['DOCUMENT_ROOT'].'/client/includes/functions_files.php';
			
			$file_id = $_GET['file_id'];
			
			$body_content = fill_download_cl($file_id);
			
		break;
		
		// Загрузить файл
		case 'download':
			
			$file_id = $_GET['file_id'];
			
			$body_content = fill_download($file_id);
			
		break;
		
		// Загрузить файл
		case 'download_wk_file':
			
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_work.php';
			
			$file_id = $_GET['file_id'];
			
			$body_content = fill_work_file_download($file_id);
			
		break;
		
		// Загрузить файл
		case 'parse_download':
			
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_parse.php';
			
			$file_id = $_GET['file_id'];
			 
			$body_content = fill_parse_download($file_id);
	
			
		break;
		// Список моих сотрудников
		case 'main':
		case 'workers':
		
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_users.php';
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_deputy.php';
			$body_content = fill_workers($current_user_id);
			
			$nav_obj = 'workers';
		break;
		
		// Список моих файлов
		case 'files':
			
			$body_content = fill_files();
			
			$nav_obj = 'files';
		break;
		
		case 'work':
			
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_deputy.php';
			if($_GET['id'])
			{
				$body_content = fill_user_work($_GET['id']);
			}
			else
			{
				$body_content = fill_user_work($current_user_id);
				
				//$body_content = fill_my_work($current_user_id);
				 
			}
			$nav_obj = 'work';
		break;
		// Список заданий
		/*case 'tasks':
			
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_deputy.php';
			
			if(!$_GET['id'] || $_GET['id'] == $current_user_id)
			{
				$body_content = fill_my_tasks($current_user_id);
				
				$nav_obj = 'my_tasks';
			}
			else
			{
				 $body_content = fill_worker_tasks($_GET['id']);
				
				$nav_obj = 'tasks';
			}
			
		break;*/
		
		// Регистрация
		case 'registration':
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_registration.php';
			$body_content = fill_registration();
			$nav_obj = 'registration';
		break;
		
		// Настройки профиля
		case 'settings':
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_users.php'; 
			$body_content = fill_user_settings();
			$nav_obj = 'settings';
			
		break;
		// Настройки профиля
		case 'boss':
		
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_users.php';
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_deputy.php';
			$body_content = fill_boss($current_user_id);
			$nav_obj = 'boss';
			
		break;
		// Форма авторизации
		case 'auth':
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_auth.php'; 
			if($auth->check_auth())
			{  
				header('Location: /');
			}
			// Выводим форму для авторизации
			
			$body_content = no_auth_fill();
			
		break;
		
		// Отзывы
		case 'comments':
			
			// Выводим форму для авторизации
			
			$body_content = fill_user_comments($_GET['id']);
			
		break;
		
		case 'msgs':
			
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_users.php';
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_deputy.php';
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_colleagues.php';
		 
			 
			// Удаляем подготовленные к удалению сообщения
			//verification_deleted_messages($current_user_id);
			 
			if($_GET['id'])
			{
				$body_content = fill_dialog($_GET['id'],'',1);
				
				$nav_obj = 'msgs';
			}
			else if($_GET['group_add'])
			{
				$body_content = fill_add_group_messages($current_user_id);
				
				$nav_obj = 'msgs_group_add';
			}
			else if($_GET['group_id'])
			{
				$body_content = fill_group_messages($_GET['group_id']);
				
				$nav_obj = 'msgs_group';
			}
			else
			{
				$body_content = fill_messages_dialog();
				 
				$nav_obj = 'dialogs';
			}
			 
		break;
		
		// Выход авторизованного пользователя
		case 'exit':
		
			$auth->auth_exit();
			
		break;
		
		default:
			$nav_obj = 'error404';
			$body_content = fill_404($o);
			
		break;
	}
		
	// html каркас
	$html_tpl = file_get_contents('templates/carcass/html.tpl');
	
	// Каркас контента
	$body_content_tpl = file_get_contents('templates/carcass/body_content.tpl');
	$body_content_without_blocks_tpl = file_get_contents('templates/carcass/body_content_without_blocks.tpl');
	$body_content_no_padding_tpl = file_get_contents('templates/carcass/body_content_no_padding.tpl');
	$body_content_grhy_tpl = file_get_contents('templates/carcass/body_content_grhy.tpl');
	$body_content_disk_system_tpl = file_get_contents('templates/carcass/body_content_disk_system.tpl');
	$body_content_doc_edit_tpl = file_get_contents('templates/carcass/body_content_doc_edit.tpl');
	$body_content_pub_file_tpl = file_get_contents('templates/carcass/body_content_pub_file.tpl');
	$body_content_grhy_tpl = file_get_contents('templates/carcass/body_content_grhy.tpl');
	 
	if($o=='tasks' || $o=='ucontrol' || $o=='projects' || $o=='settings')
	{
		$PARS['{BODY}'] = $body_content_no_padding_tpl;
	}
	else {
	switch($o)
	{
		case 'pub_file':
			$html_tpl = file_get_contents('templates/carcass/html_system.tpl');
			$PARS['{BODY}'] = $body_content_pub_file_tpl;
		break;
		case 'disk_gdrive_auth':
			$PARS['{BODY}'] = $body_content_disk_system_tpl;
		break;	
		
		case 'disk_doc_edit':
			$PARS['{BODY}'] = $body_content_doc_edit_tpl;
			$html_tpl = file_get_contents('templates/carcass/html_empty.tpl');
		break;
		
		case 'auth':
			$PARS['{BODY}'] = $body_content_without_blocks_tpl;
		break;
		case 'msgs':
			$PARS['{BODY}'] = $body_content_no_padding_tpl;
		break;
		 
		case 'grhy':
			$PARS['{BODY}'] = $body_content_grhy_tpl;
		break;
		default:
			$PARS['{BODY}'] = $body_content_tpl;
		break;
	}}
	
	$html_tpl = fetch_tpl($PARS, $html_tpl);
	
	
	$PARS = array();
	
	// Заполнение блока <head>
	$PARS['{HEAD}'] = fill_head($o);
	$PARS['{CONTENT}'] = $body_content;
	$PARS['{NAV}'] = fill_nav($nav_obj);
	
	// Для страницы авторизации - не выводим некоторые блоки
	if($o=='auth')
	{
		$PARS['{TOP_PANEL}'] = '';
		$PARS['{FOOTER}'] = '';
	}
	else
	{
		$PARS['{LEFT_MENU}'] = fill_left_menu($o);
		$PARS['{TOP_PANEL}'] = fill_top_panel($o);
		$PARS['{USER_NOTICE_BLOCK}'] = fill_user_notice_block($o);
		$PARS['{FOOTER}'] = file_get_contents('templates/carcass/footer.tpl');
	}
	
	$html_result = fetch_tpl($PARS, $html_tpl);
	
	echo $html_result;
}

// Блок уведомлений под левым меню
function fill_user_notice_block()
{
	global $site_db, $user_obj, $current_user_id;
	
	$user_bdate_tomorrow_tpl = file_get_contents('templates/notice/user_bdate_tomorrow.tpl');
	
	$user_bdate_today_tpl = file_get_contents('templates/notice/user_bdate_today.tpl');
	
	$bdate_tomorrow = date('m-d', time() + 3600 * 24);
	 
	
	// Выбираем пользователей, у которые завтра др
	$sql = "SELECT * FROM ".USERS_TB." WHERE user_bdate LIKE '%$bdate_tomorrow' AND user_id != '$current_user_id' AND is_fired=0";
	 
	$res = $site_db->query($sql);
	
	$users_ids = array();
		 
	while($row=$site_db->fetch_array($res, 1))
	{
		// Данные пользователя
		$user_obj->fill_user_data($row['user_id']);
		
		$user_surname = $user_obj->get_user_surname();
		
		$user_name = $user_obj->get_user_name();
			
		$user_middlename = $user_obj->get_user_middlename();
			
		$user_position = $user_obj->get_user_position();
		
	 	$users_bdate_tomorrow_arr[] = $user_surname.' '.$user_name.' '.$user_middlename.' <span  style="font-weight:normal">'.$user_position.'</span>';
		
		$users_ids[] = $row['user_id'];
	}
	
	if($users_bdate_tomorrow_arr)
	{
		asort($users_ids);
		
		// Составляем название куки
		$notice_users_ids = implode('_', $users_ids);
		$cookie_name_hiden = 'tomorrow'.$notice_users_ids.'1';
		// Если пользователь удалил уведомление не выводим его
		if(!$_COOKIE[$cookie_name_hiden])
		{
			$PARS['{USERS_LIST}'] = implode(', ', $users_bdate_tomorrow_arr);
			$PARS['{USERS_IDS}'] = $notice_users_ids;
			$notice_tommorow_bday = fetch_tpl($PARS, $user_bdate_tomorrow_tpl);
		}
	}
	
	
	$bdate_today = date('m-d');
	
	// Выбираем пользователей, у которые сегодня др
	$sql = "SELECT * FROM ".USERS_TB." WHERE user_bdate LIKE '%$bdate_today' AND user_id != '$current_user_id' AND is_fired=0";
	 
	$res = $site_db->query($sql);
	
	$users_ids = array();
		 
	while($row=$site_db->fetch_array($res, 1))
	{
		// Данные пользователя
		$user_obj->fill_user_data($row['user_id']);
		
		$user_surname = $user_obj->get_user_surname();
		
		$user_name = $user_obj->get_user_name();
			
		$user_middlename = $user_obj->get_user_middlename();
			
		$user_position = $user_obj->get_user_position();
		
	 	$users_bdate_today_arr[] = $user_surname.' '.$user_name.' '.$user_middlename.' <span  style="font-weight:normal">'.$user_position.'</span>';
		
		$users_ids[] = $row['user_id'];
	}
	
	if($users_bdate_today_arr)
	{
		asort($users_ids);
		// Составляем название куки
		$notice_users_ids = implode('_', $users_ids);
		$cookie_name_hiden = 'today'.$notice_users_ids.'1';
		// Если пользователь удалил уведомление не выводим его
		if(!$_COOKIE[$cookie_name_hiden])
		{
			$PARS['{USERS_IDS}'] = $notice_users_ids;
			$PARS['{USERS_LIST}'] = implode(', ', $users_bdate_today_arr);
			$notice_today_bday = fetch_tpl($PARS, $user_bdate_today_tpl);
		}
		 
	}
	
	return $notice_today_bday.$notice_tommorow_bday;
	
}
?>
