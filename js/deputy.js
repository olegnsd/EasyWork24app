function deputy_get_dept_users()
{
	var dept_id = $('#deputy_dept').val();
	
	$('#deputy_users_wrap').hide();
	
	$.post('/ajax/ajaxDeputy.php', 
	{   
		mode : 'deputy_get_dept_users',
		dept_id : dept_id
	},
	function(data){ 
		
		if(data)
		{
			$('#deputy_users_wrap').show();
			$('#deputy_users_bl').html(data);
		}
		
	});
}

function add_deputy()
{
	var deputy_user_id, workers_arr = {};
	
	$('#result').html('');
	
	var deputy_user_id = $('#deputy_user_id').val();
	
	var dept_id = $('#deputy_dept').val();
	
	loading_btn('add_deputy_btn');
	
	$.post('/ajax/ajaxDeputy.php', 
	{   
		mode : 'add_deputy',
		dept_id : dept_id,
		deputy_user_id : deputy_user_id
	},
	function(data){ 
		
		loading_btn('add_deputy_btn', 1);
		
		var error_txt = '';
		
		if(data['error'])
		{
			$(data['error']).each(function(i,j) {
				
				if(j['dept_id']==1)
				{
					error_txt += '<div>Не выбрано подразделение</div>';
				}
				if(j['deputy_user_id']==1)
				{
					error_txt += '<div>Не выбран заместитель</div>';
				}
				
			})
			
			if(error_txt)
			{
				$('#result').html('<div class="error_box" style="display:block">'+error_txt+'</div>');
			}
		}
		if(data['success']==1)
		{
			 clear_deputy_form(1);
			 $('#deputy_workers').hide();
			 get_deputy_item(data['deputy_id']);
		}
		
	}, 'json');
}

function get_deputy_item(deputy_id)
{
	$.post('/ajax/ajaxDeputy.php', 
	{   
		mode : 'get_deputy_item',
		deputy_id : deputy_id
	},
	function(data){ 
		
		$('.no_contents').remove();
		$('#deputy_list').prepend(data);
		
	});
}

function deputy_onchange_deputy_user()
{
	var deputy_user_id;
	
	deputy_user_id = $('#deputy_user_id').val();
	
	if(deputy_user_id=='0')
	{
		$('#deputy_workers').hide();
		clear_deputy_form(0);
	}
	else
	{
		$('#deputy_workers').show();
		
		$('#workers_list .access_block_item').show();
		$('#workers_list .access_block_item a[user_id='+deputy_user_id+']').parent().hide();
		$('#workers_list .access_block_item a[user_id='+deputy_user_id+']').removeClass('access_active');
		$('#workers_list .access_block_item a[user_id='+deputy_user_id+']').attr('is_select', 0);
	}
}

function clear_deputy_form(deputy)
{
	$('#workers_list .access_block_item a').removeClass('access_active');
	$('#workers_list .access_block_item a').attr('is_select', 0);
	$('#workers_list .access_block_item').show();
	
	if(deputy)
	{
		$('#deputy_user_id').val(0);
	}
}

function workers_select_for_deputy(user_id)
{
	if($('#user_'+user_id).attr('is_select')!=1)
	{
		$('#user_'+user_id).addClass('access_active');
		$('#user_'+user_id).attr('is_select', 1);
	}
	else
	{
		$('#user_'+user_id).removeClass('access_active');
		$('#user_'+user_id).attr('is_select', 0);
	}
}

function deputy_confirm(deputy_id)
{
	
	loading_btn('confirm_deputy_btn_'+deputy_id);
	
	$.post('/ajax/ajaxDeputy.php', 
	{   
		mode : 'deputy_confirm',
		deputy_id : deputy_id,
		user_id : current_user_id
	},
	function(data){ 
		
		loading_btn('confirm_deputy_btn_'+deputy_id, 1);
		
		if(data['success']==1)
		{
			 $('#deputy_confirm_btn_bl_'+deputy_id).remove();
			 $('#deputy_'+deputy_id).removeClass('not_confirm');
			 
			if(data['new_deputy_count']>=1)
			{
				$('#new_deputy_count_top_menu').html('(+ '+data['new_deputy_count']+')');
				$('#new_deputy_count').html('(+ '+data['new_deputy_count']+')');
			}
			else
			{
				$('#new_deputy_count_top_menu').html('');
				$('#new_deputy_count').html('');
			}
		}
		
	}, 'json');
}

 

function delete_deputy(deputy_id)
{
	if(!confirm('Убрать сотрудника из заместителей?')) return false;
	
	loading_btn('delete_deputy_btn_'+deputy_id);
	
	$.post('/ajax/ajaxDeputy.php', 
	{   
		mode : 'delete_deputy',
		deputy_id : deputy_id
	},
	function(data){ 
		
		loading_btn('delete_deputy_btn_'+deputy_id, 1);
		
		// Удаление успешно
		if(data==1)
		{	 
			$('#deputy_'+deputy_id).remove();
		}
	
	
	});
}

function restore_deputy(deputy_id)
{
	$.post('/ajax/ajaxDeputy.php', 
	{   
		mode : 'restore_deputy',
		deputy_id : deputy_id,
		user_id : current_user_id
	},
	function(data){ 
		
		// Удаление успешно
		if(data==1)
		{
			 
			$('.cont_hide_after_act_'+deputy_id).show();
			// Вывод уведомления
			$('#action_notice_'+deputy_id).html('');
		}
	
	
	});
}