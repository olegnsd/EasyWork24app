<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_video_instructions.php';
//  ласс авторизации
$auth = new CAuth($site_db);

$mode = $_POST['mode'];

$current_user_id = $auth->get_current_user_id();

if(!$current_user_id)
{
	exit();
}

switch($mode)
{
	case 'get_video_instruction':
		
		$site_page = value_proc($_POST['site_page']);
		
		$method = value_proc($_POST['method']);
		
		$video_block_result = fill_video_instructions_block($site_page, $method);
		
		echo json_encode($video_block_result);
	
	break;

	case 'user_show_video_ins':
		
		$video_href_id = value_proc($_POST['video_href_id']);
		
		$site_page = value_proc($_POST['site_page']);
		
		if(!$site_page) exit();
		// ≈сли автоматически пользователю не выводилс€ блок видио
		if(!is_user_view_this_video_ins($site_page))
		{
			// делаем отметку, чтобы его не выводить в дальнейшем автоматически
			set_video_ins_view_by_user($site_page);
		}
		
	break;

}

?>