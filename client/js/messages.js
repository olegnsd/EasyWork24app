default_add_msg_text = 'Введите сообщение..';
function check_add_msg_textarea(mode)
{
	if(mode==1)
	{
		if($('#msg_add_text').val()==default_add_msg_text)
		{
			$('#msg_add_text').val('');
			$('#msg_add_text').removeClass('input_empty')
		}
	}
	 
	if(mode==2)
	{
		if($('#msg_add_text').val()=='')
		{
			$('#msg_add_text').val(default_add_msg_text);
			$('#msg_add_text').addClass('input_empty')
		}
	}
 
}


add_msg_btn = 0;

// Отправка сообщения
function add_new_msg(to_client_id)
{
	var msg_text;
	
	if(add_msg_btn)
	{
		return;
	}
	$('#add_new_msg_error').html('');
	
	msg_text = $('#msg_add_text').val();
	
	add_msg_btn = 1;
	
	loading_btn('add_msg_btn');
	$.post('/client/ajax/ajaxMessages.php', 
	{   
		mode : 'add_new_msg',
		from_user_id : from_user_id,
		from_client_id : from_client_id,
		client_id : client_id,
		default_text : default_add_msg_text,
		msg_text : msg_text
	},
	function(data){ 
		
		add_msg_btn = 0;
		loading_btn('add_msg_btn', 1);
		if(data['error'])
		{
			if(data['error']['msg_text']==1)
			{  
				 $('#msg_add_text').val(default_add_msg_text);
				 $('#msg_add_text').addClass('input_empty');
				  $('#msg_add_text').focus()
			}
		}
		if(data['success']==1)
		{
			 $('#msg_add_text').focus();
			 $('#msg_add_text').val('');
			 
			 // Выводим добавленное сообщение
			 get_msg(data['message_id'], from_user_id, from_client_id);
			 
			 // Обновляем кол-во новых сообщений
			 refresh_clients_msgs_count(to_client_id);
			 
			 msg_read_light(0, 1);
		}
		 
		
	}, 'json');
}

// Получает сообщение по его ID
function get_msg(message_id, from_user_id, from_client_id)
{
	$('#msgs_proc').html('<img src="/img/loading5.gif">');
	
	$.post('/client/ajax/ajaxMessages.php', 
	{   
		mode : 'get_msg',
		message_id : message_id,
		client_id : client_id,
		from_user_id : from_user_id,
		from_client_id : from_client_id
	},
	function(data){ 
		
		if(data['msgs'])
		{
			// Убираем предупреждение, что нет новых сообщений
			remove_no_msgs_notice();
			
			$('#msgs_list').append(data['msgs']);
			
			$('#msgs_proc').html('');
			
			scroll_message_container();
		}
		 
		
	}, 'json');
}

current_msg_page = 1;
// Получает больше сообщений
function get_more_client_msgs()
{
	current_msg_page++;
	 
	$.post('/client/ajax/ajaxMessages.php', 
	{   
		mode : 'get_more_msgs',
		client_id : client_id,
		page : current_msg_page
	},
	function(data){ 
		
		if(data['msgs'])
		{
			$('#msgs_list').prepend(data['msgs']);
		}
		if(data['not_any_more']==1)
		{
			$('#more_msgs_link_block').html('')
		}
		 
		
	}, 'json');
}

function scroll_message_container()
{
	$('#messages_container').scrollTop(1000000);
	 
}


// Выводит новые сообщения в диалоге
function refresh_new_client_messages(client_id)
{
	$.post('/client/ajax/ajaxMessages.php', 
	{   
		mode : 'refresh_new_client_messages',
		client_id : client_id,
		from_user_id : from_user_id,
		from_client_id : from_client_id
	},
	function(data){ 
		
		if(data['msgs'])
		{
			// Убираем предупреждение, что нет новых сообщений
			remove_no_msgs_notice();
			
			$('#msgs_list').append(data['msgs']);
			
			$('.msg_list_item[msg_is_my="1"]').removeClass('msg_not_read');
			
			scroll_message_container();
		}
		 
		setTimeout(function(){ refresh_new_client_messages(client_id)},3000)
	}, 'json');
}

// Убирает блок "сообщений нет"
function remove_no_msgs_notice()
{
	$('#no_messages').remove();
}



// Сделать сообщение прочитанным
function client_msg_read(message_id, client_id)
{
	if($('#message_'+message_id).attr('read_status')==1)
	{
		return false
	}

	
	$('#message_'+message_id).attr('read_status', 1);
	
	$.post('/client/ajax/ajaxMessages.php', 
	{   
		mode : 'client_msg_read',
		client_id : client_id,
		message_id : message_id
	},
	function(data){ 
		
		if(data['success']==1)
		{  
			refresh_clients_msgs_count(client_id)
			
			var tmp_obj = {};
			tmp_obj[message_id] = message_id;
			msg_read_light(tmp_obj, 0);
		}
		
	}, 'json');
}


// Кол-во новых сообщений
function refresh_clients_msgs_count(client_id)
{
	$.post('/client/ajax/ajaxMessages.php', 
	{   
		mode : 'get_new_client_msgs_count',
		client_id : client_id
	},
	function(data){ 
		
		if(data>=1)
			{
				$('#new_client_msgs_count').html('(+ '+data+')');
			}
			else
			{
				$('#new_client_msgs_count').html('');
			} 
		
	});

}

function client_msg_s_init()
{
	$('#msg_add_text').focus();
	$('#msg_add_text').bind('keydown', function(e)
	{ 
		if(e.which==13 && e.ctrlKey)
		{ 
			add_new_msg(client_id, 0)
		}
	})
}
