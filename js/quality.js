add_comment_btn = 0;
// Добавить отзыв
function add_comment(user_id)
{
	var comment_text;
	
	comment_text = $('#comment_text').val();
	
	if(add_comment_btn==1)
	{
		return;	
	}
	
	add_comment_btn = 1;
	
	$('#comment_proc').html('<img src="/img/loading5.gif">');
	
	$.post('/ajax/ajaxComment.php', 
	{   
		mode : 'add_comment',
		to_user_id : user_id,
		comment_text : comment_text
	},
	function(data){ 

		add_comment_btn = 0;
		
		$('#add_comment_error').html('');
		
		if(data['error'])
		{
			 if(data['error']['comment_text']==1)
			 {
			 	$('#add_comment_error').html('Отзыв не может быть пустым');
			 }
		}
		if(data['success']==1)
		{
			$('#no_comemnts').remove();
			
			// Выводим добавленный отзыва
			get_comment_item(data['comment_id'], 1);
			
			$('#comment_add_form').html('');
		}
		
	}, 'json');
 
}
// Возвращает блок отзыва
function get_comment_item(comment_id, mode)
{
	$('#comemnt_list_proc').html('<img src="/img/loading5.gif">');
	
	$.post('/ajax/ajaxComment.php', 
	{   
		mode : 'get_comment_item',
		comment_id : comment_id
	},
	function(data){ 
		
		if(data)
		{
			if(mode=='replace')
			{
				$('#comment_'+comment_id).replaceWith(data);
			}
			else
			{
				$('#comments_list').append(data);
			}
			$('#comemnt_list_proc').html('');
			 
		}
		
	});
}

// Удаление отзыва
function delete_comment(comment_id)
{
	$('#comment_notice_'+comment_id).html('<img src="/img/loading5.gif">');
	$.post('/ajax/ajaxComment.php', 
	{   
		mode : 'delete_comment',
		comment_id : comment_id
	},
	function(data){ 
		
		if(data)
		{
			$('#comment_content_'+comment_id).hide();
			$('#comment_notice_'+comment_id).html('Отзыв успешно удален | <a class="link" href="javascript:;" onclick="restore_comment('+comment_id+')">Восстановить</a>');
		}
		
	});
}

// Восстановление отзыва
function restore_comment(comment_id)
{
	$.post('/ajax/ajaxComment.php', 
	{   
		mode : 'restore_comment',
		comment_id : comment_id
	},
	function(data){ 
		
		if(data)
		{
			$('#comment_content_'+comment_id).show();
			$('#comment_notice_'+comment_id).html('');
		}
		
	});
}

// Форма для редактирования отзыва
function get_comment_edit_form(comment_id)
{
	$('#comment_notice_'+comment_id).html('<img src="/img/loading5.gif">');
	
	$.post('/ajax/ajaxComment.php', 
	{   
		mode : 'get_comment_edit_form',
		comment_id : comment_id
	},
	function(data){ 
		
		if(data)
		{	
			$('#comment_notice_'+comment_id).html('');
			$('#comment_'+comment_id).hide();
			$('#comment_'+comment_id).after(data);
		}
		
	});
}

// Отменить редактирование отзыва
function cancel_edit_comment(comment_id)
{
	$('#comment_edit_'+comment_id).remove();
	$('#comment_'+comment_id).show();
}

// Сохранить изменения отзыва
function save_edit_comment(comment_id)
{
	var comment_text;
	
	comment_text = $('#comment_text_'+comment_id).val();
	
	$('#comment_edit_error_'+comment_id).html('');
	
	$.post('/ajax/ajaxComment.php', 
	{   
		mode : 'save_edit_comment',
		comment_id : comment_id,
		comment_text : comment_text
	},
	function(data){ 
		
		if(data['error'])
		{
			 if(data['error']['comment_text']==1)
			 {
			 	$('#comment_edit_error_'+comment_id).html('Отзыв не может быть пустым');
			 }
		}
		if(data['success']==1)
		{
			// Подгружаем отредактированный блок
			get_comment_item(comment_id, 'replace');
			
			$('#comment_edit_'+comment_id).remove();
			
		}
		
	}, 'json');
}