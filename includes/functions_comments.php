<?php
// Страница отзывов о сотруднике
function fill_user_comments($user_id)
{
	global $site_db, $current_user_id;
	
	$main_tpl = file_get_contents('templates/comments/user_comments.tpl');
	
	$comment_add_form_tpl = file_get_contents('templates/comments/comment_add_form.tpl');
	
	// Строка навигации
	$nav = fill_nav('user_comments');
	
	$PARS['{NAV}'] = $nav;
	
	// Если пользователь является начальником и еще не оставлял отзыв о сотруднике
	if(!is_comment_for_user_by_user($current_user_id, $user_id) && is_user_subordinate($current_user_id, $user_id))
	{
		$PARS1['{USER_ID}'] = $user_id;
		
		$comment_add_panel = fetch_tpl($PARS1, $comment_add_form_tpl);
	}
	
	$comments_arr = fill_user_comments_list($user_id);
	
	$PARS['{COMMENTS_LIST}'] = $comments_arr['visible'].$comments_arr['hidden'];
	
	$PARS['{COMMENT_ADD_FORM}'] = $comment_add_panel;
	
	return fetch_tpl($PARS, $main_tpl);
}

// Список отзывов 
function fill_user_comments_list($user_id, $only_read = 0)
{
	global $site_db, $current_user_id;
	
	$comment_item_tpl = file_get_contents('templates/comments/comment_item.tpl');
	
	$sql = "SELECT * FROM ".COMMENTS_TB." WHERE comment_to_user_id='$user_id' AND comment_deleted<> 1 ORDER by comment_date DESC";
	
	$res = $site_db->query($sql);
		
	while($row=$site_db->fetch_array($res))
	{
		$comments_list[] = fill_comment_item($row, $only_read);
	}
	
	// Разбиваем две части - которую выводим и которую прячем
	if(count($comments_list) > COMMENTS_ON_MAIN_PER_PAGE)
	{
		$comments_list_visible = array_slice($comments_list,0,COMMENTS_ON_MAIN_PER_PAGE);
		$comments_list_hidden = array_slice($comments_list, COMMENTS_ON_MAIN_PER_PAGE);
		$comments_list_visible = implode('', $comments_list_visible);
		$comments_list_hidden = implode('', $comments_list_hidden);
	}
	else
	{
		$comments_list_visible = implode('', $comments_list);
	}
	
	if(!$comments_list_visible)
	{
		$comments_list_visible = file_get_contents('templates/comments/no_comments.tpl');
	}
	
	return 	array('visible' => $comments_list_visible, 'hidden' => $comments_list_hidden);
}

// заполнение отзыва
function fill_comment_item($comment_data, $only_read=0)
{
	global $site_db, $current_user_id, $user_obj;
	
	$comment_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/comments/comment_item.tpl');
	
	$comment_edit_tools_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/comments/comment_edit_tools.tpl');
	
	// Заполянем объект пользователя
	$user_obj->fill_user_data($comment_data['comment_from_user_id']);
	
	// Превью аватарки пользователя
	$user_avatar_src = get_user_preview_avatar_src($comment_data['comment_from_user_id'], $user_obj->get_user_image());
		
	$PARS['{AVATAR_SRC}'] = $user_avatar_src;
	
	$PARS['{USER_ID}'] = $comment_data['comment_from_user_id'];
	
	$PARS['{SURNAME}'] = $user_obj->get_user_surname();
		
	$PARS['{NAME}'] = $user_obj->get_user_name();
		
	$PARS['{MIDDLENAME}'] = $user_obj->get_user_middlename();
		
	$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
		
	$PARS['{COMMENT_TEXT}'] = stripslashes(nl2br($comment_data['comment_text']));
	
	$PARS['{DATE}'] = datetime($comment_data['comment_date'], '%j %M в %H:%i');
	
	// Автору отзыва добавляем панель редактирования
	if($current_user_id == $comment_data['comment_from_user_id'])
	{
		$PARS1['{COMMENT_ID}'] = $comment_data['comment_id'];
		
		$comment_edit_tools = fetch_tpl($PARS1, $comment_edit_tools_tpl); 
	}
	
	$PARS['{COMMENT_ID}'] = $comment_data['comment_id'];
	
	$PARS['{EDIT_TOOLS}'] = $comment_edit_tools;
	
	$comment_item = fetch_tpl($PARS, $comment_item_tpl);
		
	return $comment_item;
}


// заполнение отзыва, форма редактирвоания
function fill_comment_item_edit($comment_data)
{
	global $site_db, $current_user_id, $user_obj;
	
	$comment_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/comments/comment_item_edit.tpl');
	
	// Заполянем объект пользователя
	$user_obj->fill_user_data($comment_data['comment_from_user_id']);

	$PARS['{SURNAME}'] = $user_obj->get_user_surname();
		
	$PARS['{NAME}'] = $user_obj->get_user_name();
		
	$PARS['{MIDDLENAME}'] = $user_obj->get_user_middlename();
		
	$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
	
	$PARS['{COMMENT_TEXT}'] = $comment_data['comment_text'];
	
	$PARS['{COMMENT_ID}'] = $comment_data['comment_id'];
	
	$comment_item = fetch_tpl($PARS, $comment_item_tpl);
		
	return $comment_item;
}

// Проверка, оставлял ли комментарий пользователь для пользователя
function is_comment_for_user_by_user($from_user_id, $to_user_id)
{
	global $site_db, $current_user_id;
	
	// Проверка, есть ли отзыв от пользователя пользователю
	$sql = "SELECT comment_id FROM ".COMMENTS_TB." WHERE comment_from_user_id='$from_user_id' AND comment_to_user_id='$to_user_id' AND comment_deleted <> 1";
	 
	$row = $site_db->query_firstrow($sql);
	
	// Если есть отзыв
	if($row['comment_id'])
	{
		return true;
	}
	else
	{
		return false;
	}
}

// Является ли пользователь автором комментария
function is_author_of_comments($comment_id, $user_id)
{
	global $site_db, $current_user_id;
	
	$sql = "SELECT comment_id FROM ".COMMENTS_TB." WHERE comment_id='$comment_id' AND comment_from_user_id='$user_id'";
	
	$row = $site_db->query_firstrow($sql);
	 
	if($row['comment_id'])
	{
		return true;
	}
	else
	{
		return false;
	}
}
?>