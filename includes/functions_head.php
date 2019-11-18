<?php
// Заполняет шаблон переменными
function fill_head($o)
{
	global $db, $current_user_id;
	
	$head_tpl = file_get_contents('templates/carcass/head.tpl');
	
	$title_main= 'EasyWork24 - cистема управления заданиями';
	
	// Флаг о возможности проверки на новые сообщения
	$not_check_new_msgs = 0;
	
	if($o=='msgs' && $_GET['id'])
	{
		$not_check_new_msgs = 1;
	}
	
	// Кол-во новых сообщений
//	$new_msgs_count = get_count_user_new_messages($current_user_id);

	$PARS['{SCRIPTS}'] = get_scripts_list($o); 
	
	$PARS['{CSS}'] = get_css_list($o); 
	
	$PARS['{TITLE}'] = $title_main; 
	
	//$PARS['{NEW_MSGS_COUNT}'] = $new_msgs_count; 
	
	$PARS['{CURRENT_USER_ID}'] = $current_user_id; 
	
	$PARS['{NOT_CHECK_NEW_MSGS}'] = $not_check_new_msgs; 
	
	$PARS['{O}'] = $o; 
	
	return fetch_tpl($PARS, $head_tpl);
}


// Формирует список подключаемых стилей
function get_css_list($o)
{ 
	switch($o)
	{
		case 'personal':
			$css_list = return_css_list_by_num(array(100,101));
		break;
		case 'projects':
			$css_list = return_css_list_by_num(array(102));
		break;
		case 'evcal':
			$css_list = return_css_list_by_num(array(103));
		break;
		 
	}
	return $css_list;
}
// Возвращает список переданных номеров стилей
function return_css_list_by_num($num_array)
{  
	$css_arr = array(
		100 => '<link rel="stylesheet" type="text/css" href="/js/fancybox/jquery.fancybox-1.3.4.css" media="screen" />',
		101 => '<link rel="stylesheet" type="text/css" href="/css/jcrop/jquery.Jcrop.css" media="screen" />',
		102 => '<link rel="stylesheet" type="text/css" href="/css/tooltips/poshy/tip-green/tip-green.css" media="screen" />',
		103 => '<link rel="stylesheet" type="text/css" href="/css/evcal/style.css?v=1" media="screen" />'
	);
	
	foreach($num_array as $i)
	{
		 
		$css_list .= chr(10).$css_arr[$i];
	}
	
	return $css_list;
}

// Формирует список подключаемых скриптов
function get_scripts_list($o)
{
	switch($o)
	{
		case 'comments':
			$scripts_list = return_scripts_list_by_num(array(1));
		break;
		case 'files':
			$scripts_list = return_scripts_list_by_num(array(2,100));
		break;
		case 'personal':
			$scripts_list = return_scripts_list_by_num(array(3,4,21,100,101,102, 103,105, 107));
		break;
		case 'contacts':
			$scripts_list = return_scripts_list_by_num(array(5, 100));
		break;
		case 'tree':
			$scripts_list = return_scripts_list_by_num(array(6));
		break;
		case 'clients':
			$scripts_list = return_scripts_list_by_num(array(7, 14, 15,100, 104, 34));
		break;
		case 'deals':
			$scripts_list = return_scripts_list_by_num(array(8, 105, 34, 35, 110));
		break;
		case 'efficiency':
			$scripts_list = return_scripts_list_by_num(array(105));
		break;
		case 'goods':
			$scripts_list = return_scripts_list_by_num(array(9,100));
		break;
		case 'money':
			$scripts_list = return_scripts_list_by_num(array(10));
		break;
		case 'wktime':
			$scripts_list = return_scripts_list_by_num(array(11, 105, 107));
		break;
		case 'cam':
			$scripts_list = return_scripts_list_by_num(array(12));
		break;
		case 'auto':
			$scripts_list = return_scripts_list_by_num(array(13));
		break;
		case 'colleagues':
			$scripts_list = return_scripts_list_by_num(array(16));
		break;
		case 'registration':
			$scripts_list = return_scripts_list_by_num(array(17,104));
		break;
		case 'settings':
			$scripts_list = return_scripts_list_by_num(array(17,104));
		break;
		case 'finances':
			$scripts_list = return_scripts_list_by_num(array(18, 19, 105, 107, 35, 110));
		break;
		case 'planning':
			$scripts_list = return_scripts_list_by_num(array(20));
		break;
		/*case 'task_to_users':
		case 'tasks':
			$scripts_list = return_scripts_list_by_num(array(21,35, 110));
		break;*/
		case 'work':
			$scripts_list = return_scripts_list_by_num(array(23, 100, 35, 110));
		break;
		case 'ofdocs':
			$scripts_list = return_scripts_list_by_num(array(22));
		break;
		case 'reprimand':
			$scripts_list = return_scripts_list_by_num(array(24));
		break;
		case 'deputy':
			$scripts_list = return_scripts_list_by_num(array(25));
		break;
		case 'boss':
			$scripts_list = return_scripts_list_by_num(array(26));
		break;
		case 'external':
			$scripts_list = return_scripts_list_by_num(array(27));
		break;
		case 'notes':
			$scripts_list = return_scripts_list_by_num(array(28));
		break;
		case 'projects':
			$scripts_list = return_scripts_list_by_num(array(29, 109, 35, 110));
		break;
		case 'grhy':
			$scripts_list = return_scripts_list_by_num(array(30, 108));
		break;
		case 'cnews':
			$scripts_list = return_scripts_list_by_num(array(31));
		break;
		case 'evcal':
			$scripts_list = return_scripts_list_by_num(array(33));
		break;
		case 'posttr':
			$scripts_list = return_scripts_list_by_num(array(34));
		break;
		case 'disk':
			$scripts_list = return_scripts_list_by_num(array(35, 110));
		break;
		case 'msgs':
			$scripts_list = return_scripts_list_by_num(array(35, 110));
		break;
		case 'c_structure':
		case 'org':
			$scripts_list = return_scripts_list_by_num(array(36, 111));
		break;
		case 'tasks':
			$scripts_list = return_scripts_list_by_num(array(37, 35, 110, 111));
		break;
		 
	}
	return $scripts_list;
}
// Возвращает список переданных номеров скриптов
function return_scripts_list_by_num($num_array)
{
	$version = '?v=7';
	
	$scripts_arr = array(
	1 => '<script src="/js/quality.js'.$version.'"></script>',
	2 => '<script src="/js/files.js'.$version.'"></script>',
	3 => '<script src="/js/personal.js'.$version.'"></script>',
	4 => '<script src="/js/workers.js'.$version.'"></script>',
	5 => '<script src="/js/contacts.js'.$version.'"></script>',
	6 => '<script src="/js/tree.js'.$version.'"></script>',
	7 => '<script src="/js/clients.js'.$version.'"></script>',
	8 => '<script src="/js/deals.js'.$version.'"></script>',
	9 => '<script src="/js/goods.js'.$version.'"></script>',
	10 => '<script src="/js/money.js'.$version.'"></script>',
	11 => '<script src="/js/worktime.js'.$version.'"></script>',
	12 => '<script src="/js/camera.js'.$version.'"></script>',
	13 => '<script src="/js/auto.js'.$version.'"></script>',
	14 => '<script src="/client/js/messages.js'.$version.'"></script>',
	15 => '<script src="/client/js/files.js'.$version.'"></script>',
	16 => '<script src="/js/colleagues.js'.$version.'"></script>',
	17 => '<script src="/js/user.js'.$version.'"></script>',
	18 => '<script src="/js/finances.js'.$version.'"></script>',
	19 => '<script src="/js/functions.js'.$version.'"></script>',
	20 => '<script src="/js/planning.js'.$version.'"></script>',
	21 => '<script src="/js/tasks.js'.$version.'"></script>',
	22 => '<script src="/js/ofdocs.js'.$version.'"></script>',
	23 => '<script src="/js/work.js'.$version.'"></script>',
	24 => '<script src="/js/reprimand.js'.$version.'"></script>',
	25 => '<script src="/js/deputy.js'.$version.'"></script>',
	26 => '<script src="/js/boss.js'.$version.'"></script>',
	27 => '<script src="/js/external.js'.$version.'"></script>',
	28 => '<script src="/js/notes.js'.$version.'"></script>',
	29 => '<script src="/js/projects.js'.$version.'"></script>',
	30 => '<script src="/js/grhy.js'.$version.'"></script>',
	31 => '<script src="/js/cnews.js'.$version.'"></script>',
	32 => '<script src="/js/video_instructions.js'.$version.'"></script>',
	33 => '<script src="/js/evcal.js'.$version.'"></script>',
	34 => '<script src="/js/post_tracking.js'.$version.'"></script>',
	35 => '<script src="/js/disk.js'.$version.'"></script>',
	36 => '<script src="/js/org.js'.$version.'"></script>',
	37 => '<script src="/js/tasks1.js'.$version.'"></script>',
  
	100 => '<script src="/js/ajaxupload.3.5.js"></script>',
	101 => '<script src="/js/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>',
	102 => '<script src="/js/fancybox/jquery.fancybox-1.3.4.pack.js"></script>',
	103 => '<script src="/js/jquery.Jcrop.min.js"></script>',
	104 => '<script src="/js/jquery.maskedinput-1.2.2.js"></script>',
	105 => '<script src="/js/highcharts-3.0.2/js/highcharts.js"></script>',
	106 => '<script src="/js/highcharts-3.0.2/js/highcharts-more.js"></script>',
	107 => '<script src="/js/highcharts_options.js"></script>',
	108 => '<script src="/js/jquery-ui-1.10.4.custom.min.js"></script>',
	109 => '<script src="/js/jquery.poshytip.min.js"></script>',
	110 => '<script src="/js/upload/jquery.uploadifive.min.js"></script>',
	111 => '<script src="/js/auto_load.js"></script>'
	 
	  
	);
	
	foreach($num_array as $i)
	{
		$scripts_list .= chr(10).$scripts_arr[$i];
	}
	
	return $scripts_list;
}
?>