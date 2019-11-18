// Добавить в мои сотрудники
function add_in_my_workers_list()
{ 
	var worker_id, comment;
	
	$('#work_error').html('');
	
	$('#work_error').hide();
	
	worker_id = $('#worker_select').val();
	
	comment = $('#worker_comment').val();
	
	 
	 
	if(worker_id<=0)
	{
		$('#work_error').show()
		$('#work_error').html('Сотрудник не найден');
		return false;
	} 
	$.post('/ajax/ajaxWorkers.php', 
	{   
		mode : 'add_in_my_workers',
		worker_id : worker_id,
		comment : comment
	},
	function(data){ 
		 
		if(data['error'])
		{
			$('#work_error').show()
		}
		else
		{
			$('#work_error').hide()
		}
		
		if(data['error']==1)
		{
			 $('#work_error').html('Сотрудник уже находится в списке "Мои сотрудники"');
		}
		else if(data['error']==2)
		{
			$('#work_error').html('Добавить в список "Мои сотрудники" невозможно, так как выбранный сотрудник является Вашим руководителем');
		}
		else if(data['error']==3)
		{
			$('#work_error').html('Вы не можете добавить сами себя в список моих сотрудников');
		}
		else if(data['error']==4)
		{
			$('#work_error').html('Вы не можете добавить сотрудника, так как он является руководителем');
		}
		else if(data['success']==1)
		{
			//$('#add_in_my_workers_list_form').fadeOut(100);
			clear_add_form();
			
			// Очистка поиска 
			$('#ea_selected__worker_select').trigger('click')
			
			setTimeout(function(){
				$('#add_my_workers_result').html('<div class="success" style="margin-top:10px">Сотрудник будет уведомлен о вашем запросе | <a href="javascript:;" onclick="open_add_my_workers_form()">Закрыть</a></div>')}, 100);
				
			// Обновляем список сотрудников	
			//get_my_workers_list(global_current_user_id)
		}
		
	}, 'json');
	
	
}

// Очистка формы
function clear_add_form()
{
	$('#worker_comment').val('');
	$('.maininput').val('');
	$('.closebutton').trigger('click');
	$('#work_error').html('');
	$('#work_error').hide('')
}
is_open_form = 0
// Открытие формы на добавление сотрулника
function open_add_my_workers_form()
{
	// Убираем сообщение об успешности проведенной ранее операции
	$('#add_my_workers_result').html('');
	
	 
	if(is_open_form==1)
	{
		$('#add_in_my_workers_list_form').hide();
		clear_add_form();
		is_open_form = 0;
	}
	else
	{
		$('#add_in_my_workers_list_form').show(1)
		is_open_form = 1;
		
	}
	 
}

// Список сотрудников
function get_my_workers_list(user_id)
{
	
	$.post('/ajax/ajaxWorkers.php', 
	{   
		mode : 'get_workers_list',
		user_id : user_id
	},
	function(data){ 
		
		$('#user_list_container').html(data)
		
	});
}


// Скрыть отклоненную заявку
function hide_rejected_notice(invite_user_id, invited_user_id)
{
	$.post('/ajax/ajaxWorkers.php', 
	{   
		mode : 'hide_rejected_notice',
		invite_user_id : invite_user_id,
		invited_user_id : invited_user_id
	},
	function(data){ 
		
		if(data==1)
		{
			$('#task_'+invited_user_id).remove()
		}
		
	});
}

// Удалить сотрудника
function remove_user_from_worker(user_id, mode)
{
	$.post('/ajax/ajaxWorkers.php', 
	{   
		mode : 'remove_user_from_worker',
		user_id : user_id
	},
	function(data){ 
		
		if(data==1)
		{
			if(mode==1)
			{
				$('#remove_from_workers_block').remove();
				$('#delete_user_from_workers_result').html('<div class="success">Пользователь больше не является сотрудником</div>')
			}
			else if(mode==2)
			{
				$('#worker_'+user_id).remove();
			}
		}
		
	});
}