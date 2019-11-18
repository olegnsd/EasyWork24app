<?php
#### Проекты
// Страница - Заданий сотрудника
function fill_projects($to_user_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$main_tpl = file_get_contents('templates/projects/projects.tpl');
	
	if(!$_GET['part']==1 && !$_GET['part']==2)
	{
		// Форма добавления нового проекта
		$add_form = fill_projects_add_form();
	}

	// Очистка массива удаленных контактов
	if($_SESSION['project_delete'])
	{
		$_SESSION['project_delete'] = '';
	}
	
	// Верхнее меню
	$top_menu = fill_projects_top_menu();
	
	// На странице Проекты в которых я участвую, выводим все задачи, в которых участвует юзер
	if($_GET['part']==1)
	{
		// Диаграмма ганта по всем задачам пользователя в проектах, в которых он участвует
		$user_part_projects_gant = fill_user_part_projects_gant($current_user_id);
	}
	
	// закрытые проекты
	$closed = value_proc($_GET['closed']);
	
	$projects_list_content = fill_projects_list_content($closed);
	
	$PARS['{ADD_FORM}'] = $add_form;
	
	$PARS['{TOP_MENU}'] = $top_menu;
	
	$PARS['{USER_PART_PROJECTS_GANT}'] = $user_part_projects_gant;
	
	$PARS['{PROJECTS_LIST_CONTENT}'] = $projects_list_content;
	
	$PARS['{CLOSED}'] = $closed;
	
	return fetch_tpl($PARS, $main_tpl);
}

function fill_projects_list_content($closed = 0)
{
	global $site_db, $current_user_id, $current_user_obj;
	
	$more_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/projects/more_btn.tpl');
	
	$projects_list_content_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/projects/projects_list_content.tpl');
	
	if($_GET['part']==1 || $_GET['part']==4)
	{
		// Кол-во проектов
		$projects_count = get_user_participation_projects_count($current_user_id, $closed);
		
		// Список проектов, в которых пользователь участвует
		$projects_list = fill_participation_projects_list($current_user_id, $closed);
		
		$is_part = 1;
	}
	else if($_GET['part']==5)
	{
		if(!$current_user_obj->get_is_admin())
		{
			header('Location: /projects');
			exit();
		}
		// Выбираем последний добавленный проект
		$sql = "SELECT project_id FROM ".PROJECTS_TB." WHERE deleted<>1 ORDER by project_id DESC LIMIT 1";
		
		$row = $site_db->query_firstrow($sql);
		
		if($row['project_id'])
		{
			$_SESSION['last_project_id'] = $row['project_id'];
		}
		
		// Кол-во проектов
		$projects_count = get_user_projects_all_count();
		
		// Список проектов
		$projects_list = fill_projects_all_list();
		
		$is_part = 5; 
	}
	else
	{
		// Выбираем последний добавленный проект
		$sql = "SELECT project_id FROM ".PROJECTS_TB." WHERE deleted<>1 ORDER by project_id DESC LIMIT 1";
		
		$row = $site_db->query_firstrow($sql);
		
		if($row['project_id'])
		{
			$_SESSION['last_project_id'] = $row['project_id'];
		}
		
		// Кол-во проектов
		$projects_count = get_user_projects_count($current_user_id, $closed);
		
		// Список проектов
		$projects_list = fill_projects_list($current_user_id, $closed);
		
		$is_part = 0;
	}
	
	// Кол-во страниц
	$pages_count = ceil($projects_count/PROJECTS_PER_PAGE);
		
	// Если страниц больше 1
	if($pages_count > 1)
	{
		$more_btn = $more_btn_tpl;
	}
	
	$PARS['{PROJECTS_LIST}'] = $projects_list;
	
	$PARS['{MORE_PROJECTS_BTN}'] = $more_btn;
	
	$PARS['{IS_PART}'] = $is_part;
	
	$PARS['{PAGES_COUNT}'] = $pages_count;
	
	return fetch_tpl($PARS, $projects_list_content_tpl);
}


// Диаграмма ганта по всем текущим задачам проекта
function fill_user_part_projects_gant($user_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$user_part_gant_tpl = file_get_contents('templates/projects/user_part_gant.tpl');
	
	$sql = "SELECT i.project_id FROM ".PROJECTS_TB." i
			LEFT JOIN ".PROJECT_TASKS_TB." j ON i.project_id=j.project_id
			WHERE j.user_id='$user_id' AND i.deleted<>1 AND j.task_completed = 0 AND i.project_closed=0";
	
	$res = $site_db->query($sql);
		 
	while($row=$site_db->fetch_array($res))
	{
		$projects_arr[] = $row['project_id'];
	}
	 
	if($projects_arr)
	{
		$projects_ids = implode(', ', $projects_arr);
	}
	else
	{
		return '';
	}
	
	/*$sql = "SELECT j.*, i.project_name FROM ".PROJECTS_TB." i
			LEFT JOIN ".PROJECT_TASKS_TB." j ON i.project_id=j.project_id
			WHERE j.user_id='$user_id' AND i.deleted<>1 AND ((j.task_completed IN(0,1) AND j.user_id!=i.user_id) OR (j.user_id=i.user_id AND j.task_completed = 0)) ORDER by i.project_id DESC";*/
	
	$sql = "SELECT i.*, j.project_name FROM ".PROJECT_TASKS_TB." i
			LEFT JOIN ".PROJECTS_TB." j ON i.project_id=j.project_id
			WHERE j.project_id IN($projects_ids) ORDER by j.project_id DESC";
			 
	$res = $site_db->query($sql);
		 
	while($row=$site_db->fetch_array($res))
	{
		if(preg_match('/0000/', $row['date_start']) || preg_match('/0000/', $row['date_finish']))
		{
			continue;
		}
		
		if($row['user_id']==$current_user_id)
		{
			$this_user_task = 1;			
		}
		else
		{
			$this_user_task = 0;	
		}
		
		$this_tasks_obj['task_id'] = $row['task_id'];
		$this_tasks_obj['start'] = $row['date_start'];
		$this_tasks_obj['finish'] = $row['date_finish'];
		$this_tasks_obj['date_finished'] = $row['task_date_finished'];
		$this_tasks_obj['completed'] = $row['task_completed'];
		$this_tasks_obj['task_desc'] = iconv('cp1251', 'utf-8', '"<b>'.$row['project_name'].'"</b><br> '.$row['task_desc']);
		$this_tasks_obj['to_project_link'] = 1;
		$this_tasks_obj['project_id'] = $row['project_id'];
		$this_tasks_obj['after_task_id'] = $row['after_task_id'];
		$this_tasks_obj['this_user_task'] = $this_user_task;
		
		$tasks_obj[$row['task_id']] = $this_tasks_obj;
	}
	
	if(!$tasks_obj)
	{
		return '';
	}
	
	$PARS['{TASKS_OBJ}'] = json_encode($tasks_obj);
	
	return fetch_tpl($PARS, $user_part_gant_tpl);
}

// Верхнее меню
function fill_projects_top_menu()
{
	global $site_db, $current_user_id, $user_obj, $current_user_obj;
	
	$top_menu_tpl = file_get_contents('templates/projects/top_menu.tpl');
	$top_menu_admin_tpl = file_get_contents('templates/projects/top_menu_admin.tpl');
	
	$new_count_in_user_project_counts = get_new_projects_reports_counts($current_user_id);
	$new_count_in_user_project_counts += get_new_task_completed_counts($current_user_id, '', 'user_projects');
	$new_count_in_user_project_counts += get_project_task_new_reports_count('', 'user_projects');
	 
	$new_count_in_user_project_counts = $new_count_in_user_project_counts ? ' (+ '.$new_count_in_user_project_counts.')' : '';
	
	
	$new_count_in_part_projects = get_new_projects_count($current_user_id);
	$new_count_in_part_projects += get_project_task_new_reports_count('', 'user_part_projects');
	$new_count_in_part_projects += get_new_task_completed_counts($current_user_id, '', 'user_part_projects');
	
	$new_count_in_part_projects = $new_count_in_part_projects ? ' (+ '.$new_count_in_part_projects.')' : '';
	
	// для админа выводим вкладку Все
	if($current_user_obj->get_is_admin())
	{
		 $top_menu_admin =  $top_menu_admin_tpl;
	}
	$PARS['{TOP_ADMIN}'] = $top_menu_admin;
	 
	$top_menu_tpl = fetch_tpl($PARS, $top_menu_tpl); 
	
		
	if($_GET['part']==1)
	{
		$active_menu_2 = 'active';
	}
	else if($_GET['part']==2)
	{
		$active_menu_3 = 'active';
	}
	else if($_GET['part']==4)
	{
		$active_menu_4 = 'active';
	}
	else if($_GET['part']==5)
	{
		$active_menu_5 = 'active';
	}
	else
	{
		$active_menu_1 = 'active';
	}
	
	$PARS_1['{ACTIVE_1}'] = $active_menu_1;
	
	$PARS_1['{ACTIVE_2}'] = $active_menu_2;
	
	$PARS_1['{ACTIVE_3}'] = $active_menu_3;
	
	$PARS_1['{ACTIVE_4}'] = $active_menu_4;
	
	$PARS_1['{ACTIVE_5}'] = $active_menu_5;
	
	$PARS_1['{NEW_COUNT_IN_USER_PROJECT}'] = $new_count_in_user_project_counts;
	$PARS_1['{NEW_COUNT_IN_PART_PROJECT}'] = $new_count_in_part_projects;
	
	return fetch_tpl($PARS_1, $top_menu_tpl);
}

function get_user_participation_projects_count($user_id, $closed)
{
	global $site_db, $current_user_id;
	
	$and_closed = get_query_part_closed_projects($closed);
	
	// Выбор проектов
	$sql = "SELECT COUNT(DISTINCT (i.project_id)) as count FROM ".PROJECTS_TB." i
			LEFT JOIN ".PROJECT_TASKS_TB." j ON i.project_id=j.project_id
			WHERE (j.user_id='$user_id' OR i.project_head='$user_id' OR j.added_by_user_id='$user_id') AND i.deleted<>1 $and_closed";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}
// Кол-во проектов пользователя
function get_user_projects_all_count()
{
	global $site_db, $current_user_id;
	
	$and_closed = get_query_part_closed_projects($closed);
	
	// Выбор проектов
	$sql = "SELECT COUNT(*) as count FROM ".PROJECTS_TB." WHERE deleted<>1";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}
// Кол-во проектов пользователя
function get_user_projects_count($user_id, $closed)
{
	global $site_db, $current_user_id;
	
	$and_closed = get_query_part_closed_projects($closed);
	
	// Выбор проектов
	$sql = "SELECT COUNT(*) as count FROM ".PROJECTS_TB." WHERE user_id='$user_id' AND deleted<>1 $and_closed";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}

// Получает часть запроса 
function get_query_part_closed_projects($closed = 0)
{
	if($closed==1)
	{
		$and_closed = " AND project_closed='1'";
	}
	else if($closed == 0)
	{
		$and_closed = " AND project_closed='0'";
	}
	
	return $and_closed;
}

// Список проектов
function fill_participation_projects_list($user_id, $closed, $page = 1)
{
	global $site_db, $current_user_id, $user_obj;
	
	$participation_projects_list_no_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/projects/participation_projects_list_no.tpl');
	
	$page = $page ? $page : 1;
	
	$and_closed = get_query_part_closed_projects($closed);
	
	// Страничность
	$begin_pos = PROJECTS_PER_PAGE * ($page-1);
	
	$limit = " LIMIT ".$begin_pos.",".PROJECTS_PER_PAGE;
	
	// Выбор проектов
	$sql = "SELECT DISTINCT (i.project_id), i.* FROM ".PROJECTS_TB." i
			LEFT JOIN ".PROJECT_TASKS_TB." j ON i.project_id=j.project_id
			WHERE (j.user_id='$user_id' OR i.project_head='$user_id' OR j.added_by_user_id='$user_id') AND i.deleted<>1 $and_closed ORDER by i.project_id DESC $limit";
	 
	$res = $site_db->query($sql);
		
	while($row=$site_db->fetch_array($res))
	{
		$project_list .= fill_project_list_item($row);
	}
	
	if(!$project_list)
	{
		$project_list = $participation_projects_list_no_tpl;
	}
	
	return $project_list;
}

// Список проектов
function fill_projects_all_list($page = 1)
{
	global $site_db, $current_user_id, $user_obj;
	
	$projects_list_no_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/projects/projects_list_no.tpl');
	
	$and_closed = get_query_part_closed_projects($closed);
	
	// Удаленные в этой сессии клиенты
	$deleted_projects_ids = implode(', ', $_SESSION['project_delete']);
	
	if($deleted_projects_ids)
	{
		$and_deleted_projects = " OR project_id IN($deleted_projects_ids) ";
	}
	 
	// Последний добавленный пользователем проект
	if($_SESSION['last_project_id'])
	{
		$and_project_id = " AND project_id <= '".$_SESSION['last_project_id']."' ";
	}
	
	$page = $page ? $page : 1;
	
	// Страничность
	$begin_pos = PROJECTS_PER_PAGE * ($page-1);
	
	$limit = " LIMIT ".$begin_pos.",".PROJECTS_PER_PAGE;
	
	// Выбор проектов
	$sql = "SELECT * FROM ".PROJECTS_TB." WHERE (deleted<>1 $and_deleted_projects) $and_project_id ORDER by project_id DESC $limit";
	
	
	$res = $site_db->query($sql);
		
	while($row=$site_db->fetch_array($res))
	{
		$project_list .= fill_project_list_item($row);
	}
	
	if(!$project_list)
	{
		$project_list = $projects_list_no_tpl;
	}

	return $project_list;
}

// Список проектов
function fill_projects_list($user_id, $closed, $page = 1)
{
	global $site_db, $current_user_id, $user_obj;
	
	$projects_list_no_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/projects/projects_list_no.tpl');
	
	$and_closed = get_query_part_closed_projects($closed);
	
	// Удаленные в этой сессии клиенты
	$deleted_projects_ids = implode(', ', $_SESSION['project_delete']);
	
	if($deleted_projects_ids)
	{
		$and_deleted_projects = " OR project_id IN($deleted_projects_ids) ";
	}
	 
	// Последний добавленный пользователем проект
	if($_SESSION['last_project_id'])
	{
		$and_project_id = " AND project_id <= '".$_SESSION['last_project_id']."' ";
	}
	
	$page = $page ? $page : 1;
	
	// Страничность
	$begin_pos = PROJECTS_PER_PAGE * ($page-1);
	
	$limit = " LIMIT ".$begin_pos.",".PROJECTS_PER_PAGE;
	
	// Выбор проектов
	$sql = "SELECT * FROM ".PROJECTS_TB." WHERE user_id='$user_id' AND (deleted<>1 $and_deleted_projects) $and_project_id $and_closed ORDER by project_id DESC $limit";
	
	
	$res = $site_db->query($sql);
		
	while($row=$site_db->fetch_array($res))
	{
		$project_list .= fill_project_list_item($row);
	}
	
	if(!$project_list)
	{
		$project_list = $projects_list_no_tpl;
	}

	return $project_list;
}

// Элемент списка проектов
function fill_project_list_item($project_data)
{
	global $site_db, $user_obj, $current_user_id, $current_user_obj;
	
	$projects_list_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/projects/projects_list_item.tpl');
	
	$projects_list_delete_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/projects/projects_list_delete_btn.tpl');
	
	$new_project_item_notice_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/projects/new_project_item_notice.tpl');
	
	$project_confirm_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/projects/project_confirm_btn.tpl');
	
	// Выбор задач проекта, с которыми пользователь еще не ознакомлен
	$sql = "SELECT *, task_id, task_confirm FROM ".PROJECT_TASKS_TB." 
			WHERE project_id='".$project_data['project_id']."'";
	
	$res = $site_db->query($sql);
	
	$projects_tasks_completed = 0;
		
	while($row=$site_db->fetch_array($res))
	{
		$projects_tasks_completed_arr[] = $row['task_completed'];
		
		// Если для текущего пользователя есть задачи, которые он еще не принял
		if($row['user_id']==$current_user_id && $row['task_confirm']==0 && $project_data['user_id']!=$current_user_id)
		{
			$project_task_not_confirm = 1;
		}
	}
	
	if($project_data['project_head']==$current_user_id && $project_data['project_head_confirmed']==0 && $project_data['user_id']!=$current_user_id)
	{
		$project_task_not_confirm = 1;
	}
	
	// Если все задачи в проекте выполнены
	if(!in_array(0, $projects_tasks_completed_arr) && count($projects_tasks_completed_arr) > 0)
	{
		$project_tasks_completed = 'cont_completed_row';
	}
	
	$check_not_confirm_tasks_data = $site_db->query_firstrow($sql);
	
	
	
	$date_start = $project_data['date_start']>0 ? datetime($project_data['date_start'], '%d.%m.%Y') : '';
	$date_finish = $project_data['date_finish']>0 ? datetime($project_data['date_finish'], '%d.%m.%Y') : '';
	
	if($project_data['user_id']==$current_user_id || $current_user_obj->get_is_admin())
	{
		$PARS['{PROJECT_ID}'] = $project_data['project_id'];
		$projects_list_delete_btn = fetch_tpl($PARS, $projects_list_delete_btn_tpl);
	}
	
	// пользователь не ознакомился с задачей в проекте, подсвечиваем проект как новый
	if($project_task_not_confirm)
	{
		$PARS['{PROJECT_ID}'] = $project_data['project_id'];
		$not_confirm = 'not_confirm_row';
		$project_confirm_btn = fetch_tpl($PARS,$project_confirm_btn_tpl);	
	}
	
	// Новые отчеты для заданий проекта
	$project_new_notice_count += get_project_task_new_reports_count($project_data);
	
	// Подсчет новых кооментариев выводим только создателю проекта
	//if($project_data['user_id']==$current_user_id)
	//{
		// Кол-во новых отчетов для репортажа
		$project_new_notice_count += get_new_projects_reports_counts($current_user_id, $project_data['project_id']);
		$project_new_notice_count += get_new_task_completed_counts($current_user_id, $project_data['project_id']);
		
	//}
	
	// Формируем блок новых уведомлений для проекта
	if($project_new_notice_count)
	{
		$PARS_1['{COUNT}'] = $project_new_notice_count;
			
		$project_new_notice_count_bl = fetch_tpl($PARS_1, $new_project_item_notice_tpl);
	}
	
	 
	
	 
	
	// Заполянем объект подчиненного, кому сделан выговор
	$user_obj->fill_user_data($project_data['user_id']);

	$user_id = $row['user_id'];
	// название клиента для поля фсбк
	$PARS_3['{CLASS}'] = 'selected';
	$PARS_3['{VALUE}'] = $row['user_id'];
	
	$user_surname = $user_obj->get_user_surname();
	$user_name = $user_obj->get_user_name();
	$user_middlename = $user_obj->get_user_middlename();
	
	$PARS['{USER_SURNAME}'] = $user_surname;
	$PARS['{USER_NAME}'] = $user_name;
	$PARS['{USER_MIDDLENAME}'] = $user_middlename;
	$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
			
	$PARS['{PROJECT_ID}'] = $project_data['project_id'];
	
	$PARS['{DATE_FINISH}'] = $date_finish;
	
	$PARS['{PROJECT_NAME}'] = $project_data['project_name'];
	
	$PARS['{DATE_START}'] = $date_start;
	
	$PARS['{DATE_FINISH}'] = $date_finish;
	
	$PARS['{DELETE_BTN}'] = $projects_list_delete_btn;
	
	$PARS['{PROJECT_NEW_NOTICE_COUNT}'] = $project_new_notice_count_bl;
	 
	$PARS['{REFERER}'] = $_GET['part']==1 ? '&referer=part' : '';
	
	$PARS['{NOT_CONFIRM}'] = $not_confirm; 
	
	$PARS['{CONFIRM_BTN}'] = $project_confirm_btn;
	
	$PARS['{PROJECT_COMPLETED_CLASS}'] = $project_tasks_completed;
	
	return fetch_tpl($PARS, $projects_list_item_tpl);
}

function fill_project_edit_form($project_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$project_edit_form_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/projects/project_edit_form.tpl');
	
	$project_data = get_project_data($project_id);
	
	// ответственное лицо
	// Заполянем объект подчиненного
	if($project_data['project_head'])
	{
		$user_obj->fill_user_data($project_data['project_head']);
	
		$PARS_3['{CLASS}'] = 'selected';
		$PARS_3['{VALUE}'] = $project_data['project_head'];
		
		$user_surname = $user_obj->get_user_surname();
		$user_name = $user_obj->get_user_name();
		$user_middlename = $user_obj->get_user_middlename();
		
		$user_name = $user_name[0];
		$user_middlename = $user_middlename[0];
	
		$PARS_3['{NAME}'] = $user_surname.' '.$user_name[0].'. '.$user_middlename[0].'., '.$user_obj->get_user_position();
		
		$project_head = fetch_tpl($PARS_3, file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/option_fcbk.tpl'));
	}
	
	
	$PARS['{PROJECT_ID}'] = $project_id;
	
	$PARS['{PROJECT_HEAD}'] = $project_head;
	
	$PARS['{PROJECT_NAME}'] = $project_data['project_name'];
	
	$PARS['{PROJECT_DESC}'] = $project_data['project_desc'];
	
	return fetch_tpl($PARS, $project_edit_form_tpl);
}

// Просмотр и редактирвоание проекта
function fill_show_project($project_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$project_desc_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/projects/project_desc.tpl');
	
	$project_edit_tools_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/projects/project_edit_tools.tpl');

	
	// Данные проекта
	$sql = "SELECT * FROM ".PROJECTS_TB." WHERE project_id='$project_id'";
	
	$project_data = $site_db->query_firstrow($sql);
	
	
	// Если текущий пользователь является создателем проекта
	if($project_data['user_id']==$current_user_id)
	{
		$project_task_item_tpl = file_get_contents('templates/projects/project_task_item.tpl');
		$project_task_user_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/option_fcbk.tpl');
		$show_project_tpl = file_get_contents('templates/projects/show_project.tpl');
	}
	else
	{
		$project_task_item_tpl = file_get_contents('templates/projects/project_task_for_participation_user_item.tpl');
		$project_task_user_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/projects/project_task_user.tpl');
		$show_project_tpl = file_get_contents('templates/projects/show_project_participation_user.tpl');
	}
		
		
	$show_project_tpl = file_get_contents('templates/projects/show_project.tpl');
	
		
	// Проверка на доступность к проекту
	if(!check_project_access_for_user($current_user_id, $project_id) || $project_data['deleted'])
	{
		header('Location: /projects');
		exit();
	}
	
	// Если указано описание к проекту
	if($project_data['project_desc'])
	{
		$PARS_2['{DESC}'] = $project_data['project_desc'];
		$project_desc = fetch_tpl($PARS_2, $project_desc_tpl);
	}
	
	// Задачи проекта
	$project_tasks_list = fill_project_tasks_list($project_data); 

	// Список отчетов
	$report_block = fill_project_report_block($project_data);
	
	if(check_project_access_for_user($current_user_id, $project_data['project_id'], 1))
	{
		// Кнопка закрытия проекта
		$project_close_btn_arr = get_project_close_btn($project_data);
	}
 
	// Список файлов для отчета
	$files_list = get_attached_files_to_content($project_data['project_id'], 8, 2);
	
	// ответственное лицо
	// Заполянем объект подчиненного
	if($project_data['project_head'])
	{
		$user_obj->fill_user_data($project_data['project_head']);
	
		$user_surname = $user_obj->get_user_surname();
		$user_name = $user_obj->get_user_name();
		$user_middlename = $user_obj->get_user_middlename();
		
		$user_name = $user_name[0];
		$user_middlename = $user_middlename[0];
	
		$project_head = $user_surname.' '.$user_name[0].'. '.$user_middlename[0].'., '.$user_obj->get_user_position();
	}
	else
	{
		$project_head = 'Нет';
	}
	
	// кнопка редактирования
	if($project_data['project_head']==$current_user_id || $project_data['user_id']==$current_user_id)
	{
		$project_edit_tools = fetch_tpl($PARS, $project_edit_tools_tpl);
	}
			
	$PARS['{PROJECT_EDIT_TOOLS}'] = $project_edit_tools;
			
	$PARS['{FILES_LIST}'] = $files_list;
		
	$PARS['{PROJECT_ID}'] = $project_data['project_id'];
	
	$PARS['{PROJECT_NAME}'] = $project_data['project_name'];
	
	$PARS['{PROJECT_TASKS_LIST}'] = $project_tasks_list;
	
	$PARS['{PROJECT_DESC}'] = htmlspecialchars_decode($project_desc);
	
	$PARS['{REPORT_BLOCK}'] = $report_block;
	
	$PARS['{PROJECT_CLOSE_BTN}'] = $project_close_btn_arr['btn'];
	
	$PARS['{PROJECT_CLOSED_STR}'] = $project_close_btn_arr['str_status'];
	
	$PARS['{PROJECT_HEAD}'] = $project_head;
	
	return fetch_tpl($PARS, $show_project_tpl);
	
}

// Возвращает кнопку закрытия проекта
function get_project_close_btn($project_data, $project_id)
{
	global $site_db;
	
	$project_close_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/projects/project_close_btn.tpl');
	
	$project_open_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/projects/project_open_btn.tpl');
	
	if(!$project_data && $project_id)
	{
		// Выбор пользователя, кто добавлял проект
		$sql = "SELECT * FROM ".PROJECTS_TB." WHERE project_id='$project_id'";
	
		$project_data = $site_db->query_firstrow($sql);
	}
	
	if($project_data['project_closed']==1)
	{
		$btn_tpl = $project_open_btn_tpl;
		
		$str_status = 'Проект закрыт';
	}
	else
	{
		$btn_tpl = $project_close_btn_tpl;
	}
	
	$PARS['{PROJECT_ID}'] = $project_data['project_id'];
	
	return array('btn' => fetch_tpl($PARS, $btn_tpl), 'str_status' => $str_status);
}
// Задачи проекта
function fill_project_tasks_list($project_data)
{
	global $site_db, $current_user_id, $user_obj, $current_user_obj;
	
	$project_id = $project_data['project_id'];
	
	$project_task_desc_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/projects/project_task_desc.tpl');
	
	// Если текущий пользователь является создателем проекта
	if($project_data['user_id']==$current_user_id)
	{
		$project_task_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/projects/project_task_item.tpl');
		$project_task_user_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/option_fcbk.tpl');
	}
	else
	{
		$project_task_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/projects/project_task_for_participation_user_item.tpl');
		$project_task_user_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/projects/project_task_user.tpl');
	}
	
	// Выбор задач проекта
	$sql = "SELECT * FROM ".PROJECT_TASKS_TB." WHERE project_id='$project_id' ORDER by task_id ASC";
	
	$res = $site_db->query($sql);
	
	while($row=$site_db->fetch_array($res))
	{
		$project_tasks_arr[$row['task_id']] = $row;
	}
	
	$num = 1;
	foreach($project_tasks_arr as $task_id => $row)
	{
		if($project_data['user_id'] == $current_user_id || $row['added_by_user_id']==$current_user_id || $project_data['project_head']==$current_user_id || $current_user_obj->get_is_admin())
		{
			$project_task_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/projects/project_task_item.tpl');
			$project_task_user_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/option_fcbk.tpl');
		}
		else
		{
			$project_task_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/projects/project_task_for_participation_user_item.tpl');
			$project_task_user_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/projects/project_task_user.tpl');
		}
		
		$selected_user = '';
		
		if($row['user_id'])
		{
			// Заполянем объект подчиненного
			$user_obj->fill_user_data($row['user_id']);

			$user_id = $row['user_id'];
			$PARS_3['{TASK_ID}'] = $row['task_id'];
			// название клиента для поля фсбк
			$PARS_3['{CLASS}'] = 'selected';
			$PARS_3['{VALUE}'] = $row['user_id'];
			
			$user_surname = $user_obj->get_user_surname();
			$user_name = $user_obj->get_user_name();
			$user_middlename = $user_obj->get_user_middlename();
			
			$user_name = $user_name[0];
			$user_middlename = $user_middlename[0];
	
			$PARS_3['{NAME}'] =$user_surname.' '.$user_name[0].'. '.$user_middlename[0].'., '.$user_obj->get_user_position();
			
			$selected_user = fetch_tpl($PARS_3, $project_task_user_tpl);
		}
		
		// Если задача должна выполниться после определенной задачи
		if($row['after_task_id1'])
		{
			$after_task_id = $row['after_task_id'];
			
			$date_start = formate_date($project_tasks_arr[$after_task_id]['date_start'],1);
		}
		else
		{
			$date_start = formate_date($row['date_start'], 1);
			$date_finish = formate_date($row['date_finish'], 1);
		}
		
		$task_desc = '';
		if($row['task_desc']!='')
		{
			// Описание для текстареи
			$PARS_1['{TASK_ID}'] = $row['task_id'];
			$PARS_1['{TASK_DESC}'] = nl2br($row['task_desc']);
			$task_desc = fetch_tpl($PARS_1, $project_task_desc_tpl);
		}
		// текстовое описание
		$task_text_desc = $row['task_desc'];
				
		// Кнопка завершения задания 
		$complete_btn = get_project_task_complete_btn_tpl($row, $project_data);
		 
		$user_task  = '';
		if($row['user_id']==$current_user_id)
		{
			$user_task = 'user_task';
		}
		
		$task_reports_new_count = '';
		if($row['user_id']==$current_user_id || $row['added_by_user_id']==$current_user_id)
		{
			$task_reports_new_count = get_new_project_task_reports_count($row['task_id']);
			$task_reports_new_count = $task_reports_new_count ? ' (+ '.$task_reports_new_count.')' : '';
		}
		 
		$PARS_1['{TASK_REPORTS_COUNT}'] = $task_reports_new_count;
		$PARS_1['{TASK_ID}'] = $row['task_id'];
		$PARS_1['{DATE_START}'] = $date_start;
		$PARS_1['{DATE_FINISH}'] = $date_finish;
		$PARS_1['{SELECTED_USER}'] = $selected_user;
		$PARS_1['{TASK_DESC}'] = $task_desc;
		$PARS_1['{TASK_TEXT_DESC}'] = $task_text_desc;
		$PARS_1['{COMPLETED_BTN}'] = $complete_btn;
		$PARS_1['{COMPLETED}'] = $row['task_completed'];
		$PARS_1['{USER_TASK}'] = $user_task;
		$PARS_1['{AFTER_TASK_ID}'] = $row['after_task_id'];
		$PARS_1['{DATE_FINISHED}'] = $row['task_date_finished'];
		$PARS_1['{NUM}'] = $num;
		
		$project_tasks_list .=  fetch_tpl($PARS_1, $project_task_item_tpl);
		
		$projects_arr[] = $row[''];
		
		$num++;
	}
	
	$PARS_1['{TASKS_AFTER_OPTIONS}'] = $tasks_after_options;
	
	return $project_tasks_list;
}
// Возвращает шаблон кнопки выполнения задачи
function get_project_task_complete_btn_tpl($task_data, $project_data)
{
	global $current_user_id;
	
	$project_task_not_comlete_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/projects/project_task_not_comlete_btn.tpl');	

	$project_task_confirm_comleted_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/projects/project_task_confirm_comleted_btn.tpl');
	$project_task_comlete_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/projects/project_task_comlete_btn.tpl');
	
	$project_task_comleted_str_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/projects/project_task_comleted_str.tpl');
	
	if($task_data['added_by_user_id']==$current_user_id && $task_data['user_id'] != $current_user_id && $task_data['task_completed']==1)
	{
		$btn_tpl = $project_task_confirm_comleted_btn_tpl;
	}
	else if($task_data['task_completed']==2 || ($task_data['task_completed']==1 && $task_data['user_id'] != $current_user_id))
	{
		$btn_tpl = $project_task_comleted_str_tpl;
	}
	else if($task_data['user_id'] == $current_user_id)
	{ 
	 	// Задача выполнена
		if($task_data['task_completed']==1)
		{
			$btn_tpl = $project_task_not_comlete_btn_tpl;
		}
		else if(!$task_data['task_completed'])
		{
			$btn_tpl = $project_task_comlete_btn_tpl;
		}
	}
	
	$PARS['{TASK_ID}'] = $task_data['task_id'];
	
	return  fetch_tpl($PARS, $btn_tpl);
}
function fill_project_report_block($project_data)
{
	global $site_db, $current_user_id;
	
	$project_id = $project_data['project_id'];
	
	$report_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/projects/report_block.tpl');
	
	$project_more_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/projects/project_more_btn.tpl');

	// Если проект  закрыт
	if($project_data['project_closed']==1)
	{
		$report_add_form_display = 'none';
	}
	else
	{
		$report_add_form_display = 'block';
	}
	
	// Очистка массива удаленных контактов
	if($_SESSION['project_report_delete'])
	{
		$_SESSION['project_report_delete'] = '';
	}
		
	// Кол-во отчетов
	$reports_count = get_project_reports_count($project_data['project_id']);
	
	// Кол-во страниц
	$pages_count = ceil($reports_count/WORK_REPORTS_PER_PAGE);
		
	// Если страниц больше 1
	if($pages_count > 1)
	{
		$more_btn = $project_more_btn_tpl;
	}
	
	// Выбираем последний добавленный отчет
	$sql = "SELECT * FROM ".PROJECT_REPORTS_TB." WHERE project_id='$project_id' ORDER by report_id DESC LIMIT 1";
		
	$row = $site_db->query_firstrow($sql);
	
	if($row['report_id'])
	{
		$_SESSION['last_project_report_id'] = $row['report_id'];
	}
	
	$add_report_btn_value = $project_data['user_id']==$current_user_id ? 'добавить комментарий' : 'добавить отчет';
	
	// Список отчетов
	$report_list = fill_project_report_list($project_data);
	
	$PARS['{PROJECT_ID}'] = $project_id;
	
	$PARS['{REPORT_LIST}'] = $report_list;
		
	$PARS['{PROJECT_ID}'] = $project_id;
		
	$PARS['{ADD_REPORT_BTN_VALUE}'] = $add_report_btn_value;
	
	$PARS['{MORE_BTN}'] = $more_btn;
	
	$PARS['{PAGES_COUNT}'] = $pages_count;
	
	$PARS['{REPORT_ADD_FORM}'] = $report_add_form;
	
	$PARS['{REPORT_ADD_FORM_DISPLAY}'] = $report_add_form_display;
	
	$report_block = fetch_tpl($PARS, $report_block_tpl);
	
	return $report_block;
}

// Кол-во отчетов проекта
function get_project_reports_count($project_id)
{
	global $site_db;
	
	// Выбор отчетов для задания
	$sql = "SELECT COUNT(*) as count FROM ".PROJECT_REPORTS_TB." WHERE project_id='$project_id' AND deleted = 0";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}
// Список отчетов
function fill_project_report_list($project_data, $page = 1)
{
	global $site_db,  $user_obj, $current_user_id;
	
	$report_no_reports_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/projects/report_no_reports.tpl');
	
	$page = $page ? $page : 1;
	
	// Последний добавленный пользователем контакт
	if($_SESSION['last_project_report_id'])
	{
		$and_report_id = " AND report_id <= '".$_SESSION['last_project_report_id']."' ";
	}
	
	// Удаленные в этой сессии клиенты
	$deleted_reports_ids = implode(', ', $_SESSION['project_report_delete']);
	
	if($deleted_reports_ids)
	{
		$and_deleted_reports = " OR report_id IN($deleted_reports_ids) ";
	}
	
	// Страничность
	$begin_pos = WORK_REPORTS_PER_PAGE * ($page-1);
	
	$limit = " LIMIT ".$begin_pos.",".WORK_REPORTS_PER_PAGE;
	
	// Выбор отчетов для задания
	$sql = "SELECT * FROM ".PROJECT_REPORTS_TB." WHERE (deleted = 0 $and_deleted_reports) AND project_id='".$project_data['project_id']."' $and_report_id ORDER by report_id DESC $limit";
	 
	$res = $site_db->query($sql);
	
	
	while($report_data=$site_db->fetch_array($res, 1))
	{ 
		$report_list .= fill_project_reports_item($project_data, $report_data);
	}
	
	if(!$report_list)
	{
		$report_list = $report_no_reports_tpl;
	}
	
	return $report_list;
}

// Заполнение элемента комментария
function fill_project_reports_item($project_data, $report_data)
{
	global $user_obj, $current_user_id;
	
	$report_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/projects/report_item.tpl');
	
	$report_confirm_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/projects/report_confirm_btn.tpl');
	
	$report_item_edit_tools_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/projects/report_item_edit_tools.tpl');
	
	$report_not_confirm = '';
			
	$confirm_btn = '';
		 
	// Отчет не подтвержден
	if($project_data['user_id'] == $current_user_id && !$report_data['report_confirm'] && $report_data['user_id']!=$project_data['user_id'])
	{
		$report_not_confirm = 'not_confirm';
		
		$PARS_1['{PROJECT_ID}'] = $report_data['project_id'];
		
		$PARS_1['{REPORT_ID}'] = $report_data['report_id'];
			
		$confirm_btn = fetch_tpl($PARS_1, $report_confirm_btn_tpl);
	}
	
	if($project_data['user_id'] == $current_user_id || $report_data['user_id'] == $current_user_id)
	{
		$PARS_1['{PROJECT_ID}'] = $report_data['project_id'];
		
		$PARS_1['{REPORT_ID}'] = $report_data['report_id'];
		
		$edit_tools = fetch_tpl($PARS_1, $report_item_edit_tools_tpl);
	}
		
	$user_obj->fill_user_data($report_data['user_id']);
	
	// Превью аватарки пользователя
	$user_avatar_src = get_user_preview_avatar_src($report_data['user_id'], $user_obj->get_user_image());

	$PARS['{USER_ID}'] = $report_data['user_id'];
	
	$PARS['{NAME}'] = $user_obj->get_user_name();
	
	$PARS['{MIDDLENAME}'] = $user_obj->get_user_middlename();
	
	$PARS['{SURNAME}'] = $user_obj->get_user_surname();
	
	$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
	
	$PARS['{AVATAR_SRC}'] = $user_avatar_src;
	
	$PARS['{DATE}'] = datetime($report_data['report_date'], '%j %M в %H:%i');
	
	$PARS['{TEXT}'] = htmlspecialchars_decode($report_data['report_text']);
	
	$PARS['{REPORT_ID}'] = $report_data['report_id'];
	
	$PARS['{PROJECT_ID}'] = $report_data['project_id'];
	
	$PARS['{CONFIRM_BTN}'] = $confirm_btn;
	
	$PARS['{REPORT_NOT_CONFIRM_CLASS}'] = $report_not_confirm;
	
	$PARS['{EDIT_TOOLS}'] = $edit_tools;
	
	$report_item = fetch_tpl($PARS, $report_item_tpl);
	
	return 	$report_item;
}
// Форма добавления выговора
function fill_projects_add_form()
{
	global $site_db, $current_user_id, $user_obj;
	
	$add_form_tpl = file_get_contents('templates/projects/add_form.tpl');
	
	$user_workers_arr = get_current_user_users_arrs(array(0,1,0,0,1));
	
	if(!$user_workers_arr)
	{
		//sreturn '';
	}
	
	foreach($user_workers_arr as $boss_id)
	{
		$user_obj->fill_user_data($boss_id);
		
		$user_name = $user_obj->get_user_name();
		
		$user_middlename = $user_obj->get_user_middlename();
		
		$user_surname = $user_obj->get_user_surname();
		
		$user_position = $user_obj->get_user_position();
		
		$PARS['{VALUE}'] = $boss_id;
		
		$PARS['{NAME}'] = $user_surname.' '.$user_name.' '.$user_middlename;
		
		$PARS['{SELECTED}'] = '';
		
		$boss_list .= fetch_tpl($PARS, $option_tag_tpl);
	}
	
	
	$PARS['{WORKERS_LIST}'] = $boss_list;
	
	return fetch_tpl($PARS, $add_form_tpl);
}

function check_project_tasks($project_tasks_arr)
{
	$error = array();
	
	$num = 1;
	foreach($project_tasks_arr as $task_id => $task_data)
	{
		if($task_data['after_task_id'] > 0 || preg_match('/rand/', $task_data['after_task_id']))
		{
			$pre_task_date_finish = formate_to_norm_date($project_tasks_arr[$task_data['after_task_id']]['date_finish']);
			$this_task_date_start = formate_to_norm_date($task_data['date_start']);
			
			if(to_mktime($this_task_date_start)<=to_mktime($pre_task_date_finish))
			{
				$error['pre_date'][$task_id] = $task_id;
			}
			 
		}
		$num++;
	}
	
	$num = 1;	
	//Обработка ошибок задач
	foreach($project_tasks_arr as $num => $data)
	{
		$data = (array)$data;
		if(!$data['date_start'] || (!date_rus_validate($data['date_start']) && date_rus_validate($data['date_finish'])))
		{
			$error['date'][$num]['date_start'] = 1;
		}
		if(!$data['date_finish'] || (!date_rus_validate($data['date_finish']) && date_rus_validate($data['date_start'])))
		{
			$error['date'][$num]['date_finish'] = 1;
		}
		
		if(!$error['date'][$num] && to_mktime(formate_to_norm_date($data['date_start'])) > to_mktime(formate_to_norm_date($data['date_finish'])))		{
			$error['date'][$num]['valid'] = 1;
		}
		if($error['date'][$num])
		{
			$error['date'][$num]['task_id'] = $data['task_id'];
		}
	}
 
	return $error;
}
// Проверка, есть ли доступ к проекту для пользователя
function check_project_access_for_user($user_id, $project_id, $check_for_admin)
{
	global $site_db, $current_user_id, $user_obj, $current_user_obj;
	
	// Выбор пользователя, кто добавлял проект
	$sql = "SELECT * FROM ".PROJECTS_TB." WHERE project_id='$project_id'";
	
	$row = $site_db->query_firstrow($sql);
	
	if($current_user_obj->get_is_admin())
	{
		return true;
	}
	else if($row['user_id']==$user_id)
	{
		return true;
	}
	else if($row['project_head']==$user_id)
	{
		return true;
	}
	else if($row['user_id']!=$user_id && $check_for_admin==1)
	{
		return false;
	}
	
	// Проверка, участвует ли пользователь в проекте
	$sql = "SELECT task_id, user_id FROM ".PROJECT_TASKS_TB."
			WHERE user_id='$user_id' AND project_id='$project_id' LIMIT 1";
	 
	$row = $site_db->query_firstrow($sql);
	
	if($row['task_id'])
	{
		return true;
	}
	else return false;
}

// Кол-во новых отчетов ко всем проектам, которые создавал пользователь
function get_new_projects_reports_counts($user_id, $project_id)
{
	global $site_db;
	
	if($project_id)
	{
		 // Выбор проектов
		$sql = "SELECT COUNT(*) as count FROM ".PROJECT_REPORTS_TB."
				WHERE user_id!='$user_id' AND project_id='$project_id' AND deleted<>1 AND report_confirm=0";
	}
	else
	{
		// Выбор проектов
		$sql = "SELECT COUNT(*) as count FROM ".PROJECT_REPORTS_TB." i
				LEFT JOIN ".PROJECTS_TB." j ON i.project_id=j.project_id
				WHERE i.user_id!='$user_id' AND i.deleted<>1  AND j.deleted=0 AND j.user_id='$user_id' AND i.report_confirm=0";
	}
	 
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}

// Кол-во новых отчетов ко всем проектам, которые создавал пользователь
function get_new_task_completed_counts($user_id, $project_id, $what)
{
	global $site_db, $current_user_id;
	
	 
	 
	
	if($project_id)
	{
		/*$sql = "SELECT COUNT(*) as count FROM ".PROJECT_TASKS_TB."
				WHERE user_id!='$user_id' AND project_id='$project_id' AND task_completed=1";*/
		
		$sql = "SELECT COUNT(*) as count FROM ".PROJECTS_TB." i
				LEFT JOIN ".PROJECT_TASKS_TB." j ON i.project_id=j.project_id
				WHERE i.project_id='$project_id' AND j.user_id!='$current_user_id' AND i.deleted<>1 AND j.task_completed=1 AND j.added_by_user_id='$current_user_id' AND i.project_closed=0";	
				 
	}
	else
	{
		// Для списка проектов, в которых пользователь участвует
		if($what=='user_part_projects')
		{
			$and_project = " AND i.user_id!='$user_id'";
		}
		// Для списка проектов пользователя
		else if($what=='user_projects')
		{
			$and_project = " AND i.user_id='$user_id'";
		}
		
		$sql = "SELECT COUNT(*) as count FROM ".PROJECTS_TB." i
				LEFT JOIN ".PROJECT_TASKS_TB." j ON i.project_id=j.project_id
				WHERE j.user_id!='$user_id' AND i.deleted<>1 AND j.task_completed=1 AND j.added_by_user_id='$user_id' AND i.project_closed=0 $and_project";
	}
	 
	$row = $site_db->query_firstrow($sql);
	 
	return $row['count'];
}

// КОл-во новых непринятых проэктов пользователя
function get_new_projects_count($user_id)
{
	global $site_db;
	
	// Выбор проектов
	$sql = "SELECT COUNT(DISTINCT(i.project_id)) as count FROM ".PROJECTS_TB." i
			LEFT JOIN ".PROJECT_TASKS_TB." j ON i.project_id=j.project_id
			WHERE ((j.user_id='$user_id' AND j.task_confirm=0) OR (i.project_head='$user_id' AND i.project_head_confirmed=0)) AND i.deleted<>1 AND i.user_id!='$user_id' AND i.project_closed=0";
	 
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}
// Данные проекта
function get_project_data($project_id)
{
	global $site_db;
	
	// Выбор проектов
	$sql = "SELECT * FROM ".PROJECTS_TB." WHERE project_id='$project_id'";
	
	$project_data = $site_db->query_firstrow($sql);
	
	return $project_data;
}

function get_project_task_comments_block($task_id)
{
	global $site_db;
	
	$comms_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/projects/project_task_comments.tpl');
	
	// Выбираем задачу
	$sql = "SELECT * FROM ".PROJECT_TASKS_TB." WHERE task_id='$task_id'";
					
	$task_data = $site_db->query_firstrow($sql);
	 
	// список отчетов
	$reports_list = fill_project_task_reports_list($task_data);
	
	$PARS['{TASK_ID}'] = $task_id;
	
	$PARS['{REPORTS_LIST}'] = $reports_list;
	
	return fetch_tpl($PARS, $comms_tpl);
}

// Список отчетов
function fill_project_task_reports_list($task_data, $page = 1)
{
	global $site_db,  $user_obj, $current_user_id;
	
	$report_no_reports_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/projects/report_no_reports.tpl');
	
	$page = $page ? $page : 1;
	
	// Выбор отчетов для задания
	$sql = "SELECT * FROM ".PROJECTS_TASKS_REPORTS_TB." WHERE deleted = 0 AND task_id='".$task_data['task_id']."' ORDER by report_id DESC";
	 
	$res = $site_db->query($sql);
	
	while($report_data=$site_db->fetch_array($res, 1))
	{ 
		$report_list .= fill_project_task_reports_item($task_data, $report_data);
	}
	
	if(!$report_list)
	{
		$report_list = $report_no_reports_tpl;
	}
	
	return $report_list;
}

// Заполнение элемента комментария
function fill_project_task_reports_item($task_data, $report_data)
{
	global $user_obj, $current_user_id;
	
	$report_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/projects/task_report_item.tpl');
	
	$report_confirm_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/projects/task_report_confirm_btn.tpl');
	$task_report_noticed_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/projects/task_report_noticed_btn.tpl');
	
	$report_item_edit_tools_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/projects/task_report_item_edit_tools.tpl');
	
	$report_not_confirm = '';
			
	$confirm_btn = '';
		 
	 
	
	if(!$report_data['report_confirm'])
	{
		// Отчет не подтвержден
		if($task_data['added_by_user_id'] == $current_user_id && $report_data['user_id']!=$current_user_id)
		{
			$report_not_confirm = 'not_confirm';
			
			$PARS_1['{REPORT_ID}'] = $report_data['report_id'];
			
			$PARS_1['{TASK_ID}'] = $report_data['task_id'];
				
			$confirm_btn = fetch_tpl($PARS_1, $report_confirm_btn_tpl);
		}
		
		// Отчет не подтвержден
		if($task_data['user_id'] == $current_user_id && $report_data['user_id']!=$current_user_id)
		{
			$report_not_confirm = 'not_confirm';
			
			$PARS_1['{REPORT_ID}'] = $report_data['report_id'];
			
			$PARS_1['{TASK_ID}'] = $report_data['task_id'];
				
			$confirm_btn = fetch_tpl($PARS_1, $task_report_noticed_btn_tpl);
		}
	}
	
	
	if($report_data['user_id'] == $current_user_id)
	{
		$PARS_1['{TASK_ID}'] = $task_data['task_id'];
		
		$PARS_1['{REPORT_ID}'] = $report_data['report_id'];
		
		$edit_tools = fetch_tpl($PARS_1, $report_item_edit_tools_tpl);
	}
		
	$user_obj->fill_user_data($report_data['user_id']);
	
	// Превью аватарки пользователя
	$user_avatar_src = get_user_preview_avatar_src($report_data['user_id'], $user_obj->get_user_image());
	
	$PARS['{TASK_ID}'] = $task_data['task_id'];
	
	$PARS['{USER_ID}'] = $report_data['user_id'];
	
	$PARS['{NAME}'] = $user_obj->get_user_name();
	
	$PARS['{MIDDLENAME}'] = $user_obj->get_user_middlename();
	
	$PARS['{SURNAME}'] = $user_obj->get_user_surname();
	
	$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
	
	$PARS['{AVATAR_SRC}'] = $user_avatar_src;
	
	$PARS['{DATE}'] = datetime($report_data['report_date'], '%j %M в %H:%i');
	
	$PARS['{TEXT}'] = htmlspecialchars_decode($report_data['report_text']);
	
	$PARS['{REPORT_ID}'] = $report_data['report_id'];
	
	$PARS['{PROJECT_ID}'] = $report_data['project_id'];
	
	$PARS['{CONFIRM_BTN}'] = $confirm_btn;
	
	$PARS['{REPORT_NOT_CONFIRM_CLASS}'] = $report_not_confirm;
	
	$PARS['{EDIT_TOOLS}'] = $edit_tools;
	
	$report_item = fetch_tpl($PARS, $report_item_tpl);
	
	return 	$report_item;
}
// Кол-во новых отчетов для задач
function get_project_task_new_reports_count($project_data, $what)
{
	global $site_db, $current_user_id;
	
	### Выбираем задачи проекта
	if($project_data)
	{
		$sql = "SELECT i.* FROM ".PROJECT_TASKS_TB." i
				LEFT JOIN ".PROJECTS_TB." j ON i.project_id=j.project_id 
				WHERE j.deleted=0 AND i.project_id='".$project_data['project_id']."' AND j.project_closed='0'"; 
	}
	// Выбор проектов, которые создавал пользователь
	else if(!$project_data && $what=='user_projects')
	{ 
		$sql = "SELECT i.* FROM ".PROJECT_TASKS_TB." i
				LEFT JOIN ".PROJECTS_TB." j ON i.project_id=j.project_id 
				WHERE j.user_id='$current_user_id' AND (i.user_id='$current_user_id' || i.added_by_user_id='$current_user_id') AND j.deleted=0 AND j.project_closed='0'"; 
	}
	// Выбор проектов, в которых пользователь участвует
	else if(!$project_data && $what=='user_part_projects')
	{ 
		$sql = "SELECT i.* FROM ".PROJECT_TASKS_TB." i
				LEFT JOIN ".PROJECTS_TB." j ON i.project_id=j.project_id 
				WHERE j.user_id!='$current_user_id' AND (i.user_id='$current_user_id' || i.added_by_user_id='$current_user_id') AND j.deleted=0 AND j.project_closed='0'";
	}
	 
	 
	 
	 
	$res = $site_db->query($sql);
		  
	while($row=$site_db->fetch_array($res))
	{
		// Формируем массив задач, которые имеют отношение к пользователю текущему
		if($row['user_id']==$current_user_id || $row['added_by_user_id']==$current_user_id)
		{
			$to_user_tasks[] = $row['task_id'];
		}
	}
	
	 
	
	if($to_user_tasks)
	{
		$to_user_tasks_ids = implode(', ', $to_user_tasks);
		
		// Выбираем кол-во новых отчетов
		$sql = "SELECT COUNT(*) as count FROM ".PROJECTS_TASKS_REPORTS_TB." WHERE task_id IN($to_user_tasks_ids) AND user_id!='$current_user_id' AND report_confirm=0 AND deleted=0";
		 
		$row = $site_db->query_firstrow($sql);
		
		return $row['count'];
	}
	else return 0;
}

function get_new_project_task_reports_count($task_id, $for_user)
{
	global $site_db, $current_user_id;
	
	$sql = "SELECT COUNT(*) as count FROM ".PROJECTS_TASKS_REPORTS_TB." WHERE task_id='$task_id' AND user_id!='$current_user_id' AND report_confirm=0 AND deleted=0";
	 
	$row = $site_db->query_firstrow($sql);
	 
	return $row['count'];
}

function project_send_notice_by_email($project_id, $notice_type, $parameters)
{
	global $site_db, $current_user_id, $user_obj;

	 
	// новый проект
	if($notice_type==1)
	{
		$email_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/email/new_project.tpl');
		$subject = 'Новый проект';
	}
	// проект отредактирован
	else if($notice_type==2)
	{
		$email_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/email/edit_project.tpl');
		$subject = 'Проект отредактирован';
	}
	// К проекту добавлен комментарий
	else if($notice_type==3)
	{
		$email_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/email/new_report_project.tpl');
		$subject = 'Комментарий к проекту';
	}
	// К задаче проекта добавлен комментарий
	else if($notice_type==4)
	{
		$email_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/email/new_report_task_project.tpl');
		$subject = 'Комментарий к задаче проекта';
	}
	
	
	// данные проекта
	$project_data = get_project_data($project_id);
	
	$PARS['{PROJECT_ID}'] = $project_id;
	$PARS['{PROJECT_NAME}'] = $project_data['project_name'];
	$PARS['{PROJECT_REPORT_TEXT}'] = $parameters['project_report_text'];
	$PARS['{PROJECT_TASK_TITLE}'] = $parameters['task_title'];
	$PARS['{HOST}'] = HOST;
		
	$email_text = fetch_tpl($PARS, $email_tpl);
	
	// пользователи для уведомления
	$users_arr = get_project_users_for_notice($project_id, $current_user_id);
	//print_r($users_arr);
	foreach($users_arr as $user_id)
	{ 
		if(!$user_obj->get_user_notice_par($user_id, 'projects'))
		{
			continue;
		}
		
		$user_obj->fill_user_data($user_id);
				
		$user_data = $user_obj->get_user_data();
		 
		// Отправка email
		send_email_msg($user_data['user_email'], $subject, $email_text);
	}
}

// Полученеи списка пользователей для уведомления
function get_project_users_for_notice($project_id, $not_user_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$users_arr = array();
	
	$project_data = get_project_data($project_id);
	
	if($not_user_id!=$project_data['user_id'])
	{
		$users_arr[$project_data['user_id']] = $project_data['user_id'];
	}
	
	if($not_user_id!=$project_data['project_head'])
	{
		$users_arr[$project_data['project_head']] = $project_data['project_head'];
	}
	
	// выбор ролей задачи
	$sql = "SELECT * FROM tasks_projects_tasks WHERE project_id='$project_id' AND user_id<>'$not_user_id'";
	
	$res = $site_db->query($sql);
		
	while($row=$site_db->fetch_array($res))
	{
		$users_arr[$row['user_id']] = $row['user_id'];
	}
	
	return $users_arr;
}
?>