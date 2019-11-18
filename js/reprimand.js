function add_reprimand()
{
	var worker_id, reprimand_text;
	
	$('.td_error').html('');
	
	worker_id = $('#worker_id').val();
	
	reprimand_text = $('#reprimand_text').val();
	
	var type = $('#type').val();
	
	loading_btn('add_reprimand_btn');
	
	$.post('/ajax/ajaxReprimand.php', 
	{   
		mode : 'add_reprimand',
		worker_id : worker_id,
		reprimand_text : reprimand_text,
		type : type
	},
	function(data){ 
		
		loading_btn('add_reprimand_btn', 1);
		
		if(data['error'])
		{
			if(data['error']['text'])
			{
				$('#reprimand_text').next().html('Введите текст')
			}
		}
		if(data['success']==1)
		{
			$('#reprimand_text').val('');
			get_reprimand_item(data['reprimand_id'],0,1);
		}
		
	}, 'json');
}

actual_page = 1;

// Выводит больше выговоров
function get_more_reprimands()
{
	var page;
	
	page = actual_page + 1;

	$.post('/ajax/ajaxReprimand.php', 
	{   
		mode : 'get_more_reprimands',
		user_id : user_id,
		page : page,
		is_wks : is_wks,
		type : type
	},
	function(data){ 
		
		$('#reprimand_list').append(data);
		
		// Актаульная страница
		actual_page++;
		
		if(actual_page>=pages_count)
		{
			$('#more_reprimands_btn').hide();
		}
	});
}




function delete_reprimand(reprimand_id)
{
	$.post('/ajax/ajaxReprimand.php', 
	{   
		mode : 'delete_reprimand',
		reprimand_id : reprimand_id,
		user_id : current_user_id
	},
	function(data){ 
		
		// Удаление успешно
		if(data==1)
		{
			 
			$('.cont_hide_after_act_'+reprimand_id).hide();
			// Вывод уведомления
			$('#action_notice_'+reprimand_id).html('<div class="success">Выговор удален | <a href="javascript:;" onclick="restore_reprimand('+reprimand_id+')">Восстановить</a> | <a href="javascript:;" onclick="$(\'#reprimand_'+reprimand_id+'\').remove();">Скрыть</a></div>');
		}
	
	
	});
}

function restore_reprimand(reprimand_id)
{
	$.post('/ajax/ajaxReprimand.php', 
	{   
		mode : 'restore_reprimand',
		reprimand_id : reprimand_id,
		user_id : current_user_id
	},
	function(data){ 
		
		// Удаление успешно
		if(data==1)
		{
			 
			$('.cont_hide_after_act_'+reprimand_id).show();
			// Вывод уведомления
			$('#action_notice_'+reprimand_id).html('');
		}
	
	
	});
}
 

function get_reprimand_item(reprimand_id, form, prepend)
{
	$.post('/ajax/ajaxReprimand.php', 
	{   
		mode : 'get_reprimand_item',
		reprimand_id : reprimand_id,
		user_id : current_user_id
	},
	function(data){ 
		
		if(prepend)
		{
			$('#reprimand_list').prepend(data);
		}
		else
		{  
			$('#reprimand_'+reprimand_id).replaceWith(data);
		}
		
		$('.no_contents').remove(); 
		 
	});
}

function reprimand_confirm(reprimand_id)
{
	$.post('/ajax/ajaxReprimand.php', 
	{   
		mode : 'reprimand_confirm',
		reprimand_id : reprimand_id,
		user_id : current_user_id
	},
	function(data){ 
		
		if(data['success']==1)
		{  
			$('#reprimand_'+reprimand_id).removeClass('not_confirm');
			$('#reprimand_confirm_btn_bl_'+reprimand_id).remove();
			
			if(data['new_reprimand_count_type_1']>=1)
			{
				$('#new_reprimand_count_type_1').html('(+ '+data['new_reprimand_count_type_1']+')');
			}
			else
			{
				$('#new_reprimand_count_type_1').html('');
			}
			
			if(data['new_reprimand_count_type_2']>=1)
			{
				$('#new_reprimand_count_type_2').html('(+ '+data['new_reprimand_count_type_2']+')');
			}
			else
			{
				$('#new_reprimand_count_type_2').html('');
			}
			
			if(data['new_reprimand_count']>=1)
			{
				$('#new_reprimand_count').html('(+ '+data['new_reprimand_count']+')');
			}
			else
			{
				$('#new_reprimand_count').html('');
			}
		}
		
		 
	}, 'json');
}