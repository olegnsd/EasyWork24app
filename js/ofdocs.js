function add_ofdoc()
{
	var boss_id, ofdocs_type, ofdocs_text;
	
	$('.td_error').html('');
	
	to_user_id = $('#ofdocs_to_user_id').val();
	
	ofdocs_type = $('#ofdocs_type').val();
	
	ofdocs_text = $('#ofdocs_text').val();
	
	loading_btn('add_ofdoc_btn');
	
	$.post('/ajax/ajaxOfDocs.php', 
	{   
		mode : 'add_ofdoc',
		to_user_id : to_user_id,
		ofdocs_type : ofdocs_type,
		ofdocs_text : ofdocs_text
	},
	function(data){ 
		
		loading_btn('add_ofdoc_btn', 1);
		if(data['error'])
		{
			if(data['error']['text'])
			{
				$('#ofdocs_text').next().html('Введите текст документа')
			}
		}
		if(data['success']==1)
		{
			$('#ofdocs_text').val('');
			get_ofdoc_item(data['ofdoc_id'],0,1);
		}
		
	}, 'json');
}

function save_ofdoc(ofdoc_id)
{
	var boss_id, ofdocs_type, ofdocs_text;
	
	$('#ofdoc_'+ofdoc_id+' .td_error').html('');
	
	//boss_id = $('#ofdocs_boss_id_'+ofdoc_id).val();
	
	ofdocs_type = $('#ofdocs_type_'+ofdoc_id).val();
	
	ofdocs_text = $('#ofdocs_text_'+ofdoc_id).val();
	
	loading_btn('save_ofdoc_'+ofdoc_id);
	
	$.post('/ajax/ajaxOfDocs.php', 
	{   
		mode : 'save_ofdoc',
		ofdoc_id : ofdoc_id,
		ofdocs_type : ofdocs_type,
		ofdocs_text : ofdocs_text,
		user_id : current_user_id
	},
	function(data){ 
		
		//loading_btn('save_ofdoc_'+ofdoc_id, 1);
		
		if(data['error'])
		{
			if(data['error']['text'])
			{
				$('#ofdocs_text_'+ofdoc_id).next().html('Введите текст документа')
			}
		}
		if(data['success']==1)
		{
			 get_ofdoc_item(ofdoc_id, 0, 0);
		}
		
	}, 'json');
}

function cancel_save_ofdoc(ofdoc_id)
{
	loading_btn('cancel_save_btn_'+ofdoc_id);
	
	get_ofdoc_item(ofdoc_id, 0, 0);
}

actual_page = 1;

// Выводит больше имущест
function get_more_ofdocs()
{
	var page;
	
	page = actual_page + 1;

	$.post('/ajax/ajaxOfDocs.php', 
	{   
		mode : 'get_more_ofdocs',
		user_id : user_id,
		page : page,
		is_wks : is_wks
	},
	function(data){ 
		
		$('#ofdocs_list').append(data);
		
		// Актаульная страница
		actual_page++;
		
		if(actual_page>=pages_count)
		{
			$('#more_docs_btn').hide();
		}
	});
}

function delete_ofdoc(ofdoc_id)
{
	$.post('/ajax/ajaxOfDocs.php', 
	{   
		mode : 'delete_ofdoc',
		ofdoc_id : ofdoc_id,
		user_id : current_user_id
	},
	function(data){ 
		
		// Удаление успешно
		if(data==1)
		{
			 
			$('.cont_hide_after_act_'+ofdoc_id).hide();
			// Вывод уведомления
			$('#action_notice_'+ofdoc_id).html('<div class="success">Документ удален | <a href="javascript:;" onclick="restore_ofdoc('+ofdoc_id+')">Восстановить</a> | <a href="javascript:;" onclick="$(\'#ofdoc_'+ofdoc_id+'\').remove();">Скрыть</a></div>');
		}
	
	
	});
}

function restore_ofdoc(ofdoc_id)
{
	$.post('/ajax/ajaxOfDocs.php', 
	{   
		mode : 'restore_ofdoc',
		ofdoc_id : ofdoc_id,
		user_id : current_user_id
	},
	function(data){ 
		
		// Удаление успешно
		if(data==1)
		{
			 
			$('.cont_hide_after_act_'+ofdoc_id).show();
			// Вывод уведомления
			$('#action_notice_'+ofdoc_id).html('');
		}
	
	
	});
}


// Получает форму для редактирования клиента
function get_ofdoc_item(ofdoc_id, form, prepend)
{
	$.post('/ajax/ajaxOfDocs.php', 
	{   
		mode : 'get_ofdoc_item',
		ofdoc_id : ofdoc_id,
		form : form,
		user_id : current_user_id
	},
	function(data){ 
		
		if(prepend)
		{
			$('#ofdocs_list').prepend(data);
		}
		else
		{  
			$('#ofdoc_'+ofdoc_id).replaceWith(data);
		}
		
		$('.no_contents').remove(); 
		 
	});
}

// Показать блок настройки доступа к файлам и папкам
function show_access_ofdoc_block(ofdoc_id)
{
	close_access_blocks();
	
	$('#owner_block_'+ofdoc_id).show();
}

function close_access_blocks()
{
	$('.file_hide_block').hide();
}

function give_access_to_ofdoc(ofdoc_id, user_id)
{
	$('#access_proc_'+ofdoc_id).html('<img src="/img/ajax-loader.gif">');
	
	$.post('/ajax/ajaxOfDocs.php', 
	{   
		mode : 'give_access_to_ofdoc',
		ofdoc_id : ofdoc_id,
		user_id : user_id
	},
	function(data){ 
		
		$('#access_proc_'+ofdoc_id).html('');
		
		if(data==1)
		{
			$('#user_'+ofdoc_id+'_'+user_id).removeClass('access_active')
		}
		if(data==2)
		{
			$('#user_'+ofdoc_id+'_'+user_id).addClass('access_active')
		}
		
		$('.no_contents').remove(); 
		 
	});
}

add_status_btn = 0
// Добавление статуса к документу
function add_ofdoc_status(ofdoc_id, status_id)
{
	var status_text;
	
	if(add_status_btn)
	{
		return false;
	}
	status_text = $('#status_text_'+ofdoc_id).val();
	
	//$('#file_status_proc_'+file_id).html('<img src="/img/loading5.gif">');
	
	add_status_btn = 1;
	
	loading_btn('ofdoc_status_btn_'+ofdoc_id+'_'+status_id);
	
	$.post('/ajax/ajaxOfDocs.php', 
	{   
		mode : 'add_ofdoc_status',
		ofdoc_id : ofdoc_id,
		status_id : status_id,
		status_text : status_text
	},
	function(data){ 
		
		add_status_btn = 0;
		
		loading_btn('ofdoc_status_btn_'+ofdoc_id+'_'+status_id, 1);
		
		if(data['error'])
		{
			if(data['error']['status_text'])
			{
				$('#status_text_'+ofdoc_id).focus();
			}
		}
		if(data['success']==1)
		{
			$('#status_text_'+ofdoc_id).val('');
			
			$('#ofdoc_statuses_list_'+ofdoc_id).html(data['statuses_list'])
			
		}
		 
	}, 'json');
}
function ofdoc_show_statuses_list(ofdoc_id)
{
	$('#status_block_'+ofdoc_id).show()
	
	$.post('/ajax/ajaxOfDocs.php', 
	{   
		mode : 'ofdoc_show_statuses_list',
		ofdoc_id : ofdoc_id
	},
	function(data){ 
		
		$('#ofdoc_statuses_list_'+ofdoc_id).html(data);
		
		$('#status_new_count_'+ofdoc_id).html('');
		
		ofdoc_recount_new_notices();
		 
	});
}

function ofdoc_recount_new_notices()
{
	$.post('/ajax/ajaxOfDocs.php', 
	{   
		mode : 'ofdoc_recount_new_notices'
	},
	function(data){ 
		
		// Правим счетчик в левом меню
		if(parseInt(data['all_count'])>=1)
		{ 
			$('#new_ofdocs_count').html('(+ '+data['all_count']+')');
		}
		else
		{
			$('#new_ofdocs_count').html('');
		}
		
		if(parseInt(data['new_accessed_count'])>=1)
		{ 
			$('#new_count_for_boss').html('(+ '+data['new_accessed_count']+')');
		}
		else
		{
			$('#new_count_for_boss').html('');
		}
		
		if(parseInt(data['new_own_count'])>=1)
		{ 
			$('#ofdoc_own_new_count').html('(+ '+data['new_own_count']+')');
		}
		else
		{
			$('#ofdoc_own_new_count').html('');
		}
		 
	}, 'json');
}

function get_ofdoc_access_block(ofdoc_id)
{
	$.post('/ajax/ajaxOfDocs.php', 
	{   
		mode : 'get_ofdoc_access_block',
		ofdoc_id : ofdoc_id
	},
	function(data){ 
		
		if(data)
		{
			 
			$('.item_access_block').html('');
			$('#access_block_'+ofdoc_id).html(data)
		}
	});
}

function add_user_to_access_ofdoc(ofdoc_id)
{
	var id = Math.round( Math.random()*1000);
	var elem_id = 'noto_user_access_'+id;
	$('#access_users_list_'+ofdoc_id).append('<select id="'+elem_id+'" class="access_user_item"></select><br>');
	
	$('#'+elem_id).easycomplete(
	{
		str_word_select : 'Выбрать пользователя',
		width:396,
		url:'/ajax/ajaxGetUsers.php?by=name&who=all_tree&result_name=2'
	});	 
}

function save_ofdoc_user_access(ofdoc_id)
{
	var access_users = {};
	
	loading_btn('save_access_btn_'+ofdoc_id);
	
	$('#access_users_list_'+ofdoc_id+' .access_user_item').each(function(){
			  
		var user_id = $(this).val();
		
		access_users[user_id] = user_id;
	})
		
	$.post('/ajax/ajaxOfDocs.php', 
	{   
		mode : 'save_ofdoc_user_access',
		ofdoc_id : ofdoc_id,
		access_users : $.toJSON(access_users)
	},
	function(data){ 
		
		loading_btn('save_access_btn_'+ofdoc_id, 1);
		
		if(data==1)
		{
			$('#access_result_'+ofdoc_id).html('<div class="success stand_margin">Успешно сохранено</div>');
			clear_block_by_settime('access_result_'+ofdoc_id);
		}
		 
	});
}