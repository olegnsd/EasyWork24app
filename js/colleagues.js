function show_add_colleague_form()
{
	$('#add_in_colleagues_list_form').fadeIn(200);
	$('#add_colleague_link').hide();
}

function hide_add_colleague_form()
{
	$('#add_in_colleagues_list_form').hide();
	$('#add_colleague_link').show();
	$('#add_colleague_result').html('');
	$('#colleague_error').html('')
}	

function add_in_colleague_list()
{
	var user_id, comment;
	
	$('#work_error').html('');
	
	user_id = $('#colleague_select').val();
	
	comment = $('#colleguae_comment').val();
	
	if(user_id<=0)
	{
		$('#add_colleague_error').show();
		$('#add_colleague_error').html('Коллега не найден');
			
		 
		return false;
	}
	
	$('#add_colleague_error').hide();
	$('#add_colleague_error').html('');
			
	loading_btn('add_colleague_btn');
	
	$.post('/ajax/ajaxColleagues.php', 
	{   
		mode : 'add_in_colleague_list',
		user_id : user_id,
		comment : comment
	},
	function(data){ 
		
		loading_btn('add_colleague_btn', 1);
		
		var error_text = '';
		
		if(data['error']==1)
		{
			 error_text += '<div>Пользователь уже находится в списке "Мои коллеги"</div>';
		}
		else if(data['error']==2)
		{
			// error_text += '<div>Добавить в список "Мои коллеги" невозможно, так как выбранный пользователь является Вашим руководителем"</div>';
			error_text += '<div>Вы не можете добавить данного сотрудника в список "Мои коллеги", так как выбранный сотрудник является Вашим руководителем</div>';
		}
		else if(data['error']==3)
		{
			// error_text += '<div>Добавить в список "Мои коллеги" невозможно, так как выбранный пользователь является Вашим руководителем"</div>';
			error_text += '<div>Вы не можете добавить данного сотрудника в список "Мои коллеги", так как выбранный сотрудник является Вашим сотрудником</div>';
		}
		else if(data['success']==1)
		{
			clear_add_colleague_form();
			 
			 $('#add_colleague_result').html('<div class="success">Пользователь будет уведомлен о вашем запросе на добавление | <a href="javascript:;" onclick="hide_add_colleague_form()">Закрыть</a></div>')
			 
			// Обновляем список сотрудников	
			get_colleagues_list(current_user_id)
		}
		
		if(error_text)
		{
			$('#add_colleague_error').show();
			$('#add_colleague_error').html(error_text);
		}
		
	}, 'json');
	
}


function get_colleagues_list(user_id)
{
	$.post('/ajax/ajaxColleagues.php', 
	{   
		mode : 'get_colleagues_list',
		user_id : user_id
	},
	function(data){ 
		 
		$('#colleagues_list_container').html(data);
		
	});
}
// Очистка формы
function clear_add_colleague_form()
{
	$('#worker_comment').val('');
	$('.maininput').val('');
	$('.closebutton').trigger('click');
	$('#work_error').html('')
}

// Подтвердить, что пользователь является коллегой
function colleague_confirm(invite_user_id, invited_user_id)
{
	
	
	$.post('/ajax/ajaxColleagues.php', 
	{   
		mode : 'colleague_confirm',
		invited_user_id : invited_user_id,
		invite_user_id : invite_user_id
	},
	function(data){ 
		
		
		if(data['success']==1)
		{ 
			// Правим счетчик в левом меню
			if(parseInt(data['new_colleagues_count'])>=1)
			{
				$('#new_colleague_count').html('(+ '+data['new_colleagues_count']+')');
			}
			else
			{
				$('#new_colleague_count').html('');
			}
			
			get_colleague_item(invite_user_id, invited_user_id);
		}
		else if(data['remove']==1)
		{
			//$('#colleague_'+invite_user_id+'_'+invited_user_id).remove();
			$('.content_hiiden_block_'+invite_user_id+'_'+invited_user_id).html('<div class="success success_not_icon">Вы не можете добавить пользователя в список коллег  | <a href="javascript:;" onclick="$(\'#colleague_'+invite_user_id+'_'+invited_user_id+'\').remove()" class="link">скрыть</a></div>')
		}
		 
		
		
	}, 'json');

}


// Отказать в добавлении в список коллег
function colleague_cancel_confirm(invite_user_id, invited_user_id)
{
	
	$.post('/ajax/ajaxColleagues.php', 
	{   
		mode : 'colleague_cancel_confirm',
		invited_user_id : invited_user_id,
		invite_user_id : invite_user_id
	},
	function(data){ 
		 
		// Правим счетчик в левом меню
		if(parseInt(data['new_colleagues_count'])>=1)
		{
			$('#new_colleague_count').html('(+ '+data['new_colleagues_count']+')');
		}
		else
		{
			$('#new_colleague_count').html('');
		}
		
		$('#colleague_'+invite_user_id+'_'+invited_user_id).remove();
		
	}, 'json');

}

// Скрыть отмененное приглашение на добавление в список коллег
function hide_colleague_rejected_notice(invite_user_id, invited_user_id)
{
	$.post('/ajax/ajaxColleagues.php', 
	{   
		mode : 'hide_colleague_rejected_notice',
		invited_user_id : invited_user_id,
		invite_user_id : invite_user_id
	},
	function(data){ 
		 
		$('#colleague_'+invite_user_id+'_'+invited_user_id).remove();
		
	});

}

// Получает блок коллеги
function get_colleague_item(invite_user_id, invited_user_id)
{
	$.post('/ajax/ajaxColleagues.php', 
	{   
		mode : 'get_colleague_item',
		invited_user_id : invited_user_id,
		invite_user_id : invite_user_id
	},
	function(data){ 
		 
		$('#colleague_'+invite_user_id+'_'+invited_user_id).replaceWith(data);
		
	});
}

// Удалить из коллег
function delete_from_colleagues(invite_user_id, invited_user_id)
{
	$.post('/ajax/ajaxColleagues.php', 
	{   
		mode : 'delete_from_colleagues',
		invited_user_id : invited_user_id,
		invite_user_id : invite_user_id
	},
	function(data){ 
		 
		if(data==1)
		{
		 
			$('.content_hiiden_block_'+invite_user_id+'_'+invited_user_id).hide();
			$('#colleague_result_'+invite_user_id+'_'+invited_user_id).html('<div class="success">Пользователь удален из списка коллег | <a href="javascript:;" onclick="restore_deleted_colleague('+invite_user_id+', '+invited_user_id+')" class="link">Восстановить</a> | <a href="javascript:;" onclick="$(\'#colleague_'+invite_user_id+'_'+invited_user_id+'\').remove()" class="link">Скрыть</a></div>')
		}
		
	});
}

// Восстановить удаленных коллег
function restore_deleted_colleague(invite_user_id, invited_user_id)
{
	$.post('/ajax/ajaxColleagues.php', 
	{   
		mode : 'restore_deleted_colleague',
		invited_user_id : invited_user_id,
		invite_user_id : invite_user_id
	},
	function(data){ 
		 
		if(data==1)
		{
			$('.content_hiiden_block_'+invite_user_id+'_'+invited_user_id).show();
			$('#colleague_result_'+invite_user_id+'_'+invited_user_id).html('')
		}
		
	});
}