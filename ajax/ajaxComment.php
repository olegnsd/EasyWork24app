<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_comments.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_deputy.php';

// Класс авторизации
$auth = new CAuth($site_db);

$mode = $_POST['mode'];

$current_user_id = $auth->get_current_user_id();

if(!$current_user_id)
{
	exit();
}

switch($mode)
{
	// Список заданий для сотрудника
	case 'add_comment':
	
		// Проверка авторизации
		if(!$auth->check_auth())
		{
			exit();
		}
		
		$user_id = $_POST['to_user_id'];
		
		$comment_text = value_proc($_POST['comment_text']);
		
		if(!$user_id)
		{
			exit();
		}
		
		
		// Если уже оставлял отзыв данному сотурднику
		if(is_comment_for_user_by_user($current_user_id, $user_id) || !check_user_access_to_user_content($user_id, array(0,1,0,0,1)))
		{
			exit();
		}
		
		if($comment_text=='')
		{
			$error['comment_text'] = 1;
		}
		if(!$error)
		{
			// Добавляем отзыв
			$sql = "INSERT INTO ".COMMENTS_TB." SET comment_from_user_id='$current_user_id', comment_to_user_id='$user_id', comment_date=NOW(), comment_text='$comment_text'";
			
			$site_db->query($sql);
			
			$sql = "SELECT comment_id FROM ".COMMENTS_TB." WHERE comment_to_user_id='$user_id' ORDER by comment_id DESC LIMIT 1";
			
			$row = $site_db->query_firstrow($sql);
			
			$inserted_comment_id = $row['comment_id'];
			
			$success = 1;
		}
		echo json_encode(array('success' => $success, 'error' => $error, 'comment_id' => $inserted_comment_id));
	
	break;
	
	// Возвращает блок комментария
	case 'get_comment_item':
		
		$comment_id = $_POST['comment_id'];
		
		$sql = "SELECT * FROM ".COMMENTS_TB." WHERE comment_id='$comment_id'";
		
		//echo $sql;
		$comment_data = $site_db->query_firstrow($sql);
		
		$comment_item = fill_comment_item($comment_data);
		
		echo $comment_item;
	break;
	
	// Удалить отзыв
	case "delete_comment":
		
		$comment_id = $_POST['comment_id'];
		
		// Если не является автором
		if(!is_author_of_comments($comment_id, $current_user_id))
		{
			exit();
		}
		
		$sql = "UPDATE ".COMMENTS_TB." SET comment_deleted = 1 WHERE comment_id='$comment_id'";
		
		$site_db->query($sql);
		
		echo 1;
	break;
	
		// Восстановить отзыв
	case "restore_comment":
		
		$comment_id = $_POST['comment_id'];
		
		// Если не является автором
		if(!is_author_of_comments($comment_id, $current_user_id))
		{
			exit();
		}
		
		$sql = "UPDATE ".COMMENTS_TB." SET comment_deleted = 0 WHERE comment_id='$comment_id'";
		
		$site_db->query($sql);
		
		echo 1;
	break;
	
	// Получает форму редактирования комментария
	case 'get_comment_edit_form':
		
		$comment_id = $_POST['comment_id'];
		
		// Если не является автором
		if(!is_author_of_comments($comment_id, $current_user_id))
		{
			exit();
		}
		
		$sql = "SELECT * FROM ".COMMENTS_TB." WHERE comment_id='$comment_id'";
		
		
		$comment_data = $site_db->query_firstrow($sql);
		
		// Получаем форму для редактирования коммен
		$comment_item_edit = fill_comment_item_edit($comment_data);
		
		echo $comment_item_edit;
	break;
	
	// Список заданий для сотрудника
	case 'save_edit_comment':
		
		$comment_text = value_proc($_POST['comment_text']);
		
		$comment_id = value_proc($_POST['comment_id']);

		// Если не является автором
		if(!is_author_of_comments($comment_id, $current_user_id))
		{
			exit();
		}
		
		if($comment_text=='')
		{
			$error['comment_text'] = 1;
		}
		if(!$error)
		{
			// Добавляем отзыв
			$sql = "UPDATE ".COMMENTS_TB." SET comment_text='$comment_text', comment_date=NOW() WHERE comment_id='$comment_id'";
			
			$site_db->query($sql);
		
			$success = 1;
		}
		echo json_encode(array('success' => $success, 'error' => $error));
	
	break;
}

?>