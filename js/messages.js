function show_msgs_add_file_form(toclose)
{
	if($('#message_file_form').is(':visible') || toclose)
	{
		$('#message_file_form').hide();
	}
	else
	{
		$('#message_file_form').show();
	}
	 
}
function show_msgs_add_group_user_form(toclose)
{
	if($('#message_group_user').is(':visible') || toclose)
	{
		$('#message_group_user').hide();
	}
	else
	{
		$('#message_group_user').show();
	}
	 
}

function msg_add_user_to_group_msgs()
{
	var id = Math.round( Math.random()*1000);
	var elem_id = 'task_copy_'+id;
	$('#messages_users_group').append('<select id="'+elem_id+'" class="user_group_item"></select><br>');
	
	$('#'+elem_id).easycomplete(
	{
		str_word_select : 'Выбрать пользователя',
		width:396,
		url:'/ajax/ajaxGetUsers.php?by=name&who=all&result_name=1'
	});	 
}

function messages_list_scroll(f)
{
	var offset = $('#msgs_list .msg_list_item:first').offset();
	
	if(searched_list)
	{
		return false;
	}
	 
	var msgs_list_height = Number($('#msgs_list').height() - 350);

	if(offset.top>-100 && !messages_more_proc && Number(pages_count) > Number(current_prev_msg_page)) 
	{ 
		messages_more_proc = 1
		get_more_msgs('prev');
		  
	}
	else if(Number(msgs_list_height-Math.abs(offset.top))<200 && !messages_more_proc && current_next_msg_page>1)
	{ 
		messages_more_proc = 1
		get_more_msgs('next');
	}
}

function msg_bl_scroll(set)
{
	if(set=='top')
	{
		$('#messages_container').scrollTop(0);

	}
	if(set=='down')
	{
		var sc_h = document.getElementById("messages_container").scrollHeight
		$('#messages_container').scrollTop(sc_h);
	}
}
messages_more_proc = 0
stope = 0;

function new_msgs_bl_proc(show)
{
	if(show==1)
	{
		$('#msgs_list_new').show();
	}
	else if(show==0)
	{
		$('#msgs_list_new').hide();
	}
}

function dialog_search_toggle(hide, refresh_msg_list)
{  
	if(hide)
	{
		$('#mdsl_sh').show();
		$('#mdsl_sh_tb').hide();
		$('#msg_search_text').val('');
		if(refresh_msg_list && searched_list)
		{
			sh_dialog_messages();
		}
	}
	else
	{
		msg_date_block_op('delete')
 		$('#mdsl_sh').hide();
		$('#mdsl_sh_tb').fadeIn(300);
		$('#msg_search_text').focus();
	}
}
function hide_message_act_bls(hide_delete_bl, hide_last_msg_block)
{
	if(hide_delete_bl)
	$('#delete_selected_msgs_block').hide();
}
function go_to_end_dialog()
{
	sh_dialog_messages();
}

searched_list = 0;
function search_messages()
{
	var search_words;
	
	search_words = $('#msg_search_text').val();
	
	var date_from = $('#from_date').val();
	var date_to = $('#to_date').val();
	
	loading_btn('search_msgs_btn');
	
	$.post('/ajax/ajaxMessages.php', 
	{   
		mode : 'search_messages',
		search_words : search_words,
		to_user_id : to_user_id,
		date_from : date_from,
		date_to : date_to
	},
	function(data){ 
		
		loading_btn('search_msgs_btn', 1);
		
		hide_message_act_bls(1);
		show_dialog_end_lnk(1);
		$('#messages_body').hide();
		$('#messages_search_body').show();
		$('#message_add_form').hide();
		$('#msg_search_text').addClass('search_msg_input_proc');
		searched_list = 1;
		current_prev_dialog_msg_page = 1;
		
		if(data['messages_list'])
		{
			$('#messages_searched_list').html(data['messages_list']);
			$('#prev_messages_searched_btn').html('<a href="javascript:;" class="link_11" onclick="get_search_dialog_messages_more()">предыдущие сообщения.. <img src="/img/ajax-loader.gif" /></a>');
			
			 $('#messages_container').scroll(dialog_messages_search_list_scroll);
			get_search_dialog_messages_more();  
			
			msg_bl_scroll('down');
		}
		else if(!data['messages_list'])
		{
			$('#prev_messages_searched_btn').html('')
			$('#messages_searched_list').html('<div class="content_no stand_margin_left">Ничего не найдено.</div>');
		}
	}, 'json');
}

current_prev_dialog_msg_page = 1;

// Получает больше сообщений
function get_search_dialog_messages_more()
{   
	current_prev_dialog_msg_page++;
	
	page = current_prev_dialog_msg_page;
	
	var search_words = $('#msg_search_text').val();
	var date_from = $('#from_date').val();
	var date_to = $('#to_date').val();
	  
	$.post('/ajax/ajaxMessages.php', 
	{   
		mode : 'get_search_dialog_messages_more',
		page : page,
		to_user_id : to_user_id,
		search_words : search_words,
		date_from : date_from,
		date_to : date_to
	},
	function(data){ 
		
		if(data)
		{  
				var  msgs_list_height_1 = parseInt($('#messages_searched_list').css('height'));
				
				$('#messages_searched_list').prepend(data);
				
			    var messages_container_scroll_top =  $('#messages_container').scrollTop();
				var msgs_list_height_2 = parseInt($('#messages_searched_list').css('height'));
				var res_scroll_top = msgs_list_height_2 - msgs_list_height_1+messages_container_scroll_top;
				$('#messages_container').scrollTop(res_scroll_top);
			
			
			    dialog_messages_search_more_proc = 0;
		}
		else
		{
			$('#prev_messages_searched_btn').html('');
		}
		 
	});
}

dialog_messages_search_more_proc = 0
function dialog_messages_search_list_scroll()
{
	 
	var offset = $('#messages_searched_list .msg_list_item:first').offset();
	
	var msgs_list_height = Number($('#messages_searched_list').height() - 350);

	if(offset.top>-100 && !dialog_messages_search_more_proc && searched_list==1) 
	{ 
		dialog_messages_search_more_proc = 1
		get_search_dialog_messages_more();  
	}

}

function sh_dialog_messages(message_id)
{
	gl_ms = message_id
	$.post('/ajax/ajaxMessages.php', 
	{   
		mode : 'dialog_messages',
		message_id : message_id,
		to_user_id : to_user_id
	},
	function(data){ 
		
		 searched_list = 0;
		 if(data)
		 { 
		    
		 	 hide_message_act_bls(1);
		 	 $('#messages_body').show();
			 $('#messages_search_body').hide();
			 $('#prev_messages_searched_btn').html('');
			 $('#messages_body').html(data);
			 $('#message_add_form').show();
			 show_dialog_end_lnk();
			 dialog_search_toggle(1);
			 
			 if(message_id)
			 {
				 msg_bl_scroll('top')
				 center_message_item(message_id);
				 messages_list_scroll(0);
			 }
			 else
			 { 
				 msg_bl_scroll('down');
			 }
		}
		 
	});
}

function center_message_item(message_id)
{
	var message_offset = $('#message_'+message_id).offset();
	$('#messages_container').scrollTop(message_offset.top-200);
	$('#message_'+message_id).addClass('not_confirm');
	setTimeout(function(){$('#message_'+message_id).removeClass('not_confirm');},1000);
	
}

function show_dialog_end_lnk(hide)
{ 
	 if(hide || current_next_msg_page<=1)
	 {
		  $('#to_end_of_dialog').html('');
		  new_msgs_bl_proc(1); 
	 }
	 else if(current_next_msg_page>1)
	 { 
		 $('#to_end_of_dialog').html('<a href="javascript:;" onclick="go_to_end_dialog();" class="to_end_of_dialog_lnk">Перейти в конец диалога</a>');
		 new_msgs_bl_proc(0);
		 
	 }
}


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
function add_new_msg(to_user_id, to_sms)
{
	var msg_text;
	
	if(add_msg_btn)
	{
		return;
	}
	$('#add_new_msg_error').html('');
	
	msg_text = $('#msg_add_text').val();
	
	msg_theme = $('#msg_add_theme').val();
	
	add_msg_btn = 1;
	
	if(to_sms==1)
	{
		loading_btn('add_msg_sms_btn');
	}
	else
	{
		loading_btn('add_msg_btn');
	}
	
	// Прикрепленные файлы
	files_arr = Disk.get_upload_content_files(to_user_id);
	files_content_type = Disk.get_upload_content_files_content_type(to_user_id);
	
	users_group = {};
	
	if($('#message_group_user').is(':visible'))
	{
		$('#message_group_user .user_group_item').each(function(){
			
			var user_id = $(this).val();
			
			users_group[user_id] = user_id;
		})
	}
	
	$.post('/ajax/ajaxMessages.php', 
	{   
		mode : 'add_new_msg',
		to_user_id : to_user_id,
		default_text : default_add_msg_text,
		msg_text : msg_text,
		msg_theme : msg_theme,
		to_sms : to_sms,
		files_arr : $.toJSON(files_arr),
		files_content_type : files_content_type,
		users_group : $.toJSON(users_group)
	},
	function(data){ 
		
		if(to_sms==1)
		{
			loading_btn('add_msg_sms_btn', 1);
		}
		else
		{
			loading_btn('add_msg_btn', 1);
		}
	
		add_msg_btn = 0;
		
		if(data['error'])
		{
			if(data['error']['msg_text']==1)
			{  
				 $('#msg_add_text').val('');
				 $('#msg_add_text').focus()
			}
			if(data['error']['access_denied']==1)
			{  
				$('#msgs_list').html('<div style="color:red; padding-left:5px">Вы не можете отправить сообщение, так как у вас нет доступа</div>');
			}
		}
		if(data['success']==1)
		{
			 Disk.cancel_file_upload_aueue(to_user_id, 1);
			 show_msgs_add_file_form(1);
			
			 $('#msg_add_text').focus();
			 $('#msg_add_text').val('');
			 $('#msg_add_theme').val('');
			 $('#messages_users_group').html('');
			 show_msgs_add_group_user_form(1);
			 msg_add_user_to_group_msgs();
			 
			 
			 if(searched_list && current_next_msg_page>1)
			 {
				sh_dialog_messages();
			 }
			 else
			 {
			 	// Выводим добавленное сообщение
				 get_msg(data['message_id']);
			 }
			 
			 // Обновляем кол-во новых сообщений
			 refresh_msgs_count(current_user_id);
			 
			 
			 //msg_read_light(0, 1);
		}
		 
		
	}, 'json');
}

// Получает сообщение по его ID
function get_msg(message_id)
{  
	$.post('/ajax/ajaxMessages.php', 
	{   
		mode : 'get_msg',
		message_id : message_id,
		to_user_id : to_user_id,
		from_user_id : current_user_id
	},
	function(data){ 
		
		if(data['msgs'])
		{
			// Убираем предупреждение, что нет новых сообщений
			remove_no_msgs_notice();
			
			$('#msgs_list_new').append(data['msgs']);
			
			scroll_message_container();
		}
		 
		
	}, 'json');
}

 
// Получает больше сообщений
function get_more_msgs(show)
{   
	if(show=='next')
	{
		current_next_msg_page--;
		page = current_next_msg_page;
	}
	else if(show=='prev')
	{
		current_prev_msg_page++;
		page = current_prev_msg_page;
	}
	else return '';
	 
	 
	$.post('/ajax/ajaxMessages.php', 
	{   
		mode : 'get_more_msgs',
		show : 'next',
		to_user_id : to_user_id,
		page : page
	},
	function(data){ 
		
		if(data['msgs'])
		{
			
			if(show=='next')
			{
				 
				$('#msgs_list').append(data['msgs']);
				show_dialog_end_lnk();
			}
			if(show=='prev')
			{
				var  msgs_list_height_1 = parseInt($('#msgs_list').css('height'));
				
				$('#msgs_list').prepend(data['msgs']);
				
			    var messages_container_scroll_top =  $('#messages_container').scrollTop();
				var msgs_list_height_2 = parseInt($('#msgs_list').css('height'));
				var res_scroll_top = msgs_list_height_2 - msgs_list_height_1+messages_container_scroll_top;
				$('#messages_container').scrollTop(res_scroll_top);
			}
			
			//$('#messages_container').scrollTop($('#messages_container').scrollTop()+1); 
			
			messages_more_proc = 0;
		}
		if(show=='prev' && current_prev_msg_page>=pages_count)
		{
			$('#prev_msgs_link_block').hide()
		}
		if(show=='next' && current_next_msg_page<=1)
		{
			$('#next_msgs_link_block').hide()
		}
		 
		
	}, 'json');
}

function scroll_message_container()
{
	$('#messages_container').scrollTop(1000000);
}

// Сделать сообщение прочитанным
function msg_read(message_id, read_all)
{
	var messages = {};
	
	if(!read_all)
	{
		messages[message_id] = message_id
	}
	else
	{
		messages = dialog_new_messages;
	}
	 
	if(!Object.keys(messages).length)
	{
		return false
	}
	 
	if($('#message_'+message_id).attr('read_status')==1)
	{
		return 
	}
	
	$('#message_'+message_id).attr('read_status', 1);
	
	$.post('/ajax/ajaxMessages.php', 
	{   
		mode : 'msg_read',
		messages : messages
	},
	function(data){ 
		
		if(data['success']==1)
		{
			msg_read_light(messages);
			$.each(messages,function(i, j){
			 	delete dialog_new_messages[j]
			})
			 
			
			refresh_msgs_count(to_user_id);
			
		}
		 
		 
		
	}, 'json');
}
// Убираем подсвет нового сообщения
function msg_read_light(messages, remove_from)
{ 
	if(remove_from)
	{
		$('.msg_list_item').removeClass('msg_not_read');
	}
	else
	{
		$.each(messages,function(i, j){
			 
			$('#message_'+j).removeClass('msg_not_read');
		})
	}
			
	 
}

// Кол-во новых сообщений
function refresh_msgs_count(user_id)
{
	$.post('/ajax/ajaxMessages.php', 
	{   
		mode : 'get_new_msgs_count',
		user_id : user_id
	},
	function(data){ 
		
		if(data>=1)
			{
				$('#new_msgs_count').html('(+ '+data+')');
			}
			else
			{
				$('#new_msgs_count').html('');
			} 
	});

}


// Выделить сообщение
function select_msg_item(message_id)
{ 
	  
	if($('#message_'+message_id).attr('select')==0 && $('#message_'+message_id).attr('deleted')!=1)
	{  
		$('#message_'+message_id).addClass('msg_select');
		
		$('#message_'+message_id).attr('select', 1);
	}
	else if($('#message_'+message_id).attr('select')==1)
	{
		$('#message_'+message_id).removeClass('msg_select');
		
		$('#message_'+message_id).attr('select', 0);
	}
	
	var selected_items = 0;
	
	// Проверяем, есть ли выделенные сообщения
	$('.msg_list_item').each(function(){
		
		if($(this).attr('select')==1)
		{
			selected_items = 1;
			
			$('#delete_selected_msgs_block').show();
		
		}
	})
	
	if(selected_items==0)
	{
		$('#delete_selected_msgs_block').hide();
	}

}

// Удаляет выделенные сообщения
function delete_messages()
{
	var messages_arr = {};
	
	// Проверяем, есть ли выделенные сообщения
	$('.msg_list_item').each(function(){
		
		if($(this).attr('select')==1)
		{
			messages_arr[$(this).attr('message_id')] = $(this).attr('message_id');
		
		}
	})
	
	if(!messages_arr)
	{
		return
	}
	
	loading_btn('delete_msgs_btn');
	
	$.post('/ajax/ajaxMessages.php', 
	{   
		mode : 'delete_messages',
		messages_arr : $.toJSON(messages_arr)
	},
	function(data){ 
		
		loading_btn('delete_msgs_btn', 1)
		
		if(data==1)
		{
			$.each(messages_arr, function(message_id)
			{
				 $('#message_'+message_id).attr('deleted', 1);
				 
				 $('#message_'+message_id).addClass('reject');
				 
				 $('#msgs_content_'+message_id).hide();
				 
				 $('#message_result_'+message_id).html("Сообщение успешно удалено | <a class='link' href='javascript:;' onclick='restore_msg("+message_id+")'>Восстановить</a>");
				 
				 select_msg_item(message_id)
			})
		}
		 
		 
		
	});
}

// Восстановление сообщения
function restore_msg(message_id)
{
	$.post('/ajax/ajaxMessages.php', 
	{   
		mode : 'restore_msg',
		message_id : message_id
	},
	function(data){ 
		
		if(data==1)
		{
			$('#message_'+message_id).attr('deleted', 0);
			
			$('#msgs_content_'+message_id).show();
				 
			$('#message_result_'+message_id).html("");
			
			 $('#message_'+message_id).removeClass('reject');
		}
		 
	});
}

// Выводит новые сообщения в диалоге
function refresh_new_messages(user_id)
{
	$.ajax({
		type: "POST",
		url: "/ajax/ajaxMessages.php",
		data : {mode : 'refresh_new_messages', user_id : user_id},
		dataType: "json",
		success : function(data, textStatus)
		{
			if(data['msgs'])
			{
				
				// Убираем предупреждение, что нет новых сообщений
				remove_no_msgs_notice();
				
				var mcbstr_offset = $('#mcbstr').offset();
				
				var messages_container_offset = $('#messages_container').offset();
				
				$('#msgs_list_new').append(data['msgs']);
				
				if(Number($('#messages_container').height()+messages_container_offset.top+30)>mcbstr_offset.top)
				{  
					var sc_h = document.getElementById("messages_container").scrollHeight
					var scroll_top = $('#messages_container').scrollTop();
					$('#messages_container').scrollTop(sc_h);
				}
				 
				$('.msg_list_item[msg_is_my="1"]').removeClass('msg_not_read');
				
				$.each(data['dialog_new_messages_ids_arr'],function(i, j){
					dialog_new_messages[j] = j
				})
				
			}
			
			setTimeout(function(){ refresh_new_messages(user_id)},1000)
		},
		error : function()
		{
			refresh_new_messages(user_id)
		}
	});
		
		
	/*$.post('/ajax/ajaxMessages.php', 
	{   
		mode : 'refresh_new_messages',
		user_id : user_id
	},
	function(data){ 
		
		if(data['msgs'])
		{
			
			// Убираем предупреждение, что нет новых сообщений
			remove_no_msgs_notice();
			
			var mcbstr_offset = $('#mcbstr').offset();
			
			var messages_container_offset = $('#messages_container').offset();
			
			$('#msgs_list_new').append(data['msgs']);
			
			if(Number($('#messages_container').height()+messages_container_offset.top+30)>mcbstr_offset.top)
			{  
				var sc_h = document.getElementById("messages_container").scrollHeight
			    var scroll_top = $('#messages_container').scrollTop();
			    $('#messages_container').scrollTop(sc_h);
			}
			 
			$('.msg_list_item[msg_is_my="1"]').removeClass('msg_not_read');
			
			$.each(data['dialog_new_messages_ids_arr'],function(i, j){
			 	dialog_new_messages[j] = j
			})
			
		}
		 
		setTimeout(function(){ refresh_new_messages(user_id)},3000)
	}, 'json');*/
}

delete_over_btn = 0;
// Удалить полностью диалог с пользователем
function delete_dialog(dialog_user_id)
{
	if(!confirm('Вы уверены, что хотите удалить диалог?'))
	{
		return
	}
	 
	$.post('/ajax/ajaxMessages.php', 
	{   
		mode : 'delete_dialog',
		dialog_user_id : dialog_user_id
		
	},
	function(data){ 
		 
		if(data==1)
		{
			$('#dialog_'+dialog_user_id).remove();
			
			// Проверяем, есть ли диалоги
			if(!$('.msg_main_list_item')[0])
			{
				 $('#dialog_list').html(no_dialog_msg_tpl)
			}
			 
			// Обновляем кол-во новых сообщений
			refresh_msgs_count(current_user_id);
		}
	}, 'json');
}
// Убирает блок "сообщений нет"
function remove_no_msgs_notice()
{
	$('#no_messages').remove();
}

mnst = 0;
lmid = 0;
is_animate = 0;
// Обновляем кол-во новых сообщений и подсвечиваем TITLE на всех страницах просмотра, кроме сообщений
function check_new_msgs(user_id, o)
{
	// Не обновлять кол-во новых сообщений
	if(not_check_new_msgs=='1')
	{
		return false;
	}
	 
	$.post('/ajax/ajaxMessages.php', 
	{   
		mode : 'check_new_msgs',
		user_id : user_id
	},
	function(data){ 
		
		responce_new_messages_count = parseInt(data['new_msgs_count']);
		
		if(data['last_msg_id'] > lmid && responce_new_messages_count!=0  && mnst)
		{ 
			nmsg_sound();
			
			if(!is_animate)
			{
				animate_title_stop = 0;
				new_msgs_title_animate();
				is_animate = 1;
			}
		}
		
		if(responce_new_messages_count==0)
		{ 
			animate_title_stop = 1;
			set_title_default();
			is_animate  = 0;
		}
		 
		lmid = data['last_msg_id'];
		 
		mnst = 1;
		 
		// Обновляем в левом меню
		if(responce_new_messages_count>0)
		{
			$('#new_msgs_count').html('(+ '+responce_new_messages_count+')');
		}
		else
		{
			$('#new_msgs_count').html('');
		} 
		
		
		setTimeout(function(){check_new_msgs(user_id, o)},5000)
		
	}, 'json');
	
	
}

actual_page = 1;
function get_more_dialogs()
{
	var page;
	
	page = actual_page + 1;
	
	$.post('/ajax/ajaxMessages.php', 
	{   
		mode : 'get_more_dialogs',
		page : page
	},
	function(data){ 
		
		$('#dialog_list').append(data);
		dialog_init();
		// Актаульная страница
		actual_page++;
		
		if(actual_page>=pages_count)
		{
			$('#more_dialogs').hide();
		}
		
	});
}

function add_user_to_msgs_group(group_id)
{
	var user_id = $('#select_user').val();
	
	$('#add_user_error').hide();
	
	$('#add_user_error').html('');
	
	loading_btn('add_user_btn');
	
	$.post('/ajax/ajaxMessages.php', 
	{   
		mode : 'add_user_to_msgs_group',
		user_id : user_id,
		group_id : group_id
		
	},
	function(data){ 
		
		loading_btn('add_user_btn', 1);
		
		var error_text= '';
		
		if(data['error'])
		{
			if(data['error']=='1')
			{
				error_text = 'Пользователь не выбран';
			}
			if(data['error']=='2')
			{
				error_text = 'Пользователь уже состоит в диалоге';
			}
			
			if(error_text)
			{ 
				$('#add_user_error').html(error_text);
				$('#add_user_error').show();
			}
		}
		else if(data['success']==1)
		{
			document.location.reload();
		}
		 
		
	}, 'json');
}

add_btn = 0;
function add_msgs_group(user_id)
{
	var users_arr = {}, msgs_group_desc;
	
	if(add_btn)
	{
		return false;
	}
	
	$('#error_users').html('')
	
	msgs_group_desc = $('#msgs_group_desc').val();
	
	$('.access_block_item a').each(function(){
		if($(this).attr('active')==1)
		{
			users_arr[$(this).attr('user_id')] = $(this).attr('user_id');
		}
	})
	
	loading_btn('add_msgs_group');
	
	add_btn = 1;
	
	$.post('/ajax/ajaxMessages.php', 
	{   
		mode : 'add_msgs_group',
		user_id : user_id,
		users_arr : $.toJSON(users_arr),
		group_desc : msgs_group_desc
		
	},
	function(data){ 
		 
		loading_btn('add_msgs_group', 1);
		 
		add_btn = 0;
		 
		if(data['error'])
		{
			if(data['error']['users']==1)
			{
				$('#error_users').html('Не выбраны сотрудники');
			}
		}
		if(data['success']==1)
		{ 
			$('#add_msg_group').replaceWith('<div class="success">Планерка успешно добавлена. Ваши сотрудники будут уведомлены по СМС | <a href="/msgs?group_id='+data['msgs_group_id']+'">Открыть планерку</a></div>');
			tp_notice_bar_init('ps');
		}
		
	}, 'json');
}

function users_to_msgs_list_select(user_id, elem)
{ 
	if($(elem).attr('active')==1)
	{ 
		$(elem).removeClass('access_active');
		$(elem).attr('active', 0);
	}
	else
	{
		$(elem).addClass('access_active');
		$(elem).attr('active', 1);
	}
}



add_msg_group_btn = 0;

// Отправка сообщения
function add_new_msg_to_msgs_group(group_id)
{
	var msg_text;
	
	if(add_msg_group_btn)
	{
		return;
	}
	$('#add_new_msg_error').html('');
	
	msg_text = $('#msg_add_text').val();
	
	add_msg_group_btn = 1;
	
	loading_btn('add_msg_btn');
	
	// Прикрепленные файлы
	files_arr = Disk.get_upload_content_files(group_id);
	files_content_type = Disk.get_upload_content_files_content_type(group_id);
	
	$.post('/ajax/ajaxMessages.php', 
	{   
		mode : 'add_new_msg_to_msgs_group',
		group_id : group_id,
		default_text : default_add_msg_text,
		msg_text : msg_text,
		files_arr : $.toJSON(files_arr),
		files_content_type : files_content_type
	},
	function(data){ 
		
		add_msg_group_btn = 0;
		
		 loading_btn('add_msg_btn', 1);
		
		if(data['error'])
		{
			if(data['error']['msg_text']==1)
			{  
				 $('#msg_add_text').val(default_add_msg_text);
				 $('#msg_add_text').addClass('input_empty');
				 $('#msg_add_text').focus()
			}
			if(data['error']['offline']==1)
			{
				session_planning_closed_notice();
				clear_add_msgs_area();
			}
		}
		if(data['success']==1)
		{
			clear_add_msgs_area(); 
			 
			refresh_new_messages_group(group_id, 1);
			
			Disk.cancel_file_upload_aueue(group_id, 1);
			 show_msgs_add_file_form(1);
			 // Выводим добавленное сообщение
			// get_msg_group(data['message_id']);
		}
		 
		
	}, 'json');
}

function session_planning_closed_notice()
{
	if(!$('.session_planning_offline').is('.session_planning_offline'))
	{
		$('#msgs_list').append('<div class="session_planning_offline">Планерка завершена</div>');
		scroll_message_container();
	}
}

function clear_add_msgs_area()
{
	$('#msg_add_text').focus();
			 $('#msg_add_text').val('');
}

// Получает сообщение по его ID
function get_msg_group(message_id)
{
	$.post('/ajax/ajaxMessages.php', 
	{   
		mode : 'get_msg_group',
		message_id : message_id,
		group_id : group_id
	},
	function(data){ 
		
		if(data['msgs'])
		{
			// Убираем предупреждение, что нет новых сообщений
			remove_no_msgs_notice();
			
			$('#msgs_list').append(data['msgs']);
			scroll_message_container();
		}
		 
		
	}, 'json');
}


current_msg_group_page = 1;
// Получает больше сообщений
function get_more_msgs_group()
{
	var page;
	
	page = current_msg_group_page + 1;
	
	$.post('/ajax/ajaxMessages.php', 
	{   
		mode : 'get_more_msgs_group',
		group_id : group_id,
		page : page
	},
	function(data){ 
		
		if(data['msgs'])
		{
			$('#msgs_list').prepend(data['msgs']);
		}
		
		// Актаульная страница
		current_msg_group_page++;
		
		if(current_msg_group_page>=pages_count)
		{
			$('#more_msgs_link_block').html('')
		}
		 
		
	}, 'json');
}


// Выводит новые сообщения в диалоге
function refresh_new_messages_group(group_id, not_once)
{
	$.post('/ajax/ajaxMessages.php', 
	{   
		mode : 'refresh_new_messages_group',
		group_id : group_id
	},
	function(data){ 
		
		if(data['msgs'])
		{
			// Убираем предупреждение, что нет новых сообщений
			remove_no_msgs_notice();
			
			$('#msgs_list').append(data['msgs']);
			
			scroll_message_container();
		}
		
		if(not_once)
		{
			setTimeout(function(){ refresh_new_messages_group(group_id, 1)},3000);
		}
		 
	}, 'json');	 
}

function close_planning_session()
{
	if(!confirm('Завершить планерку?')) return false;
	
	loading_btn('close_planning_session_btn');
	 
	$.post('/ajax/ajaxMessages.php', 
	{   
		mode : 'close_planning_session',
		group_id : group_id
	},
	function(data){ 
		
		loading_btn('close_planning_session_btn', 1);
		
		if(data==1)
		{
			$('#planning_session_close').remove();
			session_planning_closed_notice();
			tp_notice_bar_init('ps');
			
			//$('#planning_session').replaceWith('<div class="success">Планерка завершена.</div>');
		}
		else
		{
			_error();
		}
		
		 
	}, 'json');	 
}

function show_session_planning_notice()
{
	if($('#show_planning_session_list').is(':visible'))
	{
		$('#show_planning_session_list').hide();
	}
	else
	{
		$('#show_planning_session_list').show();
	}
}
function msg_s_init()
{
	$('#msg_add_text').focus();
	$('#msg_add_text').bind('keydown', function(e)
	{ 
		if(e.which==13 && e.ctrlKey)
		{ 
			add_new_msg(to_user_id, 0)
		}
	})
	$('#msg_search_text').bind('keydown', function(e)
	{ 
		if(e.which==13)
		{ 
			search_messages()
		}
	})
}

function group_msg_s_init()
{
	$('#msg_add_text').focus();
	$('#msg_add_text').bind('keydown', function(e)
	{ 
		if(e.which==13 && e.ctrlKey)
		{ 
			add_new_msg_to_msgs_group(group_id)
		}
	})
}

function dialogs_search()
{
	search_word = $('#search_text').val();
	
	if(search_word=='')
	{
		$('#dialogs_wrap').show();
		$('#dialog_search_res').hide();
		$('#msg_search_text').html('');
		$('#search_in_messages_btn').hide(); 
		search_word = '';
		return '';
	}
	
	init_iterface_dialogs('dialog_search');
	$('#msg_search_text').html('"'+$.trim(search_word)+'"');
	 
	$.post('/ajax/ajaxMessages.php', 
	{   
		mode : 'dialogs_search',
		search_word : search_word
	},
	function(data){
		
		 
		
		if(data)
		{
			$('#dialog_search_res').html(data);
			 
			dialog_init();
		}
		else
		{
			$('#dialog_search_res').html('');
		}
		
		//$('#more_dialogs').remove();
	});	
}


function dialog_init()
{
	$(".msg_main_list_item[init=0]").click(function(event){

		document.location = $(this).attr('href');
		
    });
	
 	$(".msg_main_list_item[init=0] .delete_dialog_link").click(function(event){
	
		event.stopPropagation();
	
		delete_dialog($(this).attr('user_dialog_id'))
    });
	
	$(".msg_main_list_item").attr('init', 1);
}

function dialog_list_refresh()
{
	setTimeout(function()
	{
		$.ajax({
		  type: "POST",
		  url: "/ajax/ajaxMessages.php",
		  data : {mode : 'dialog_list_refresh'},
		  dataType:"json",
		  success : function(data, textStatus)
		  {
			  $.each(data, function(i,j){
				   
				  if( $('#dialog_'+i).attr('id')=='dialog_'+i)
				  {
					  $('#dialog_'+i).replaceWith(j)
				  }
				  else
				  {
					  $('#dialog_list').prepend(j)
				  }
				  
			  })
			  
			  dialog_init();
			  dialog_list_refresh();
		  },
		  error : function()
		  {
			  dialog_list_refresh();
		  }
		});
		
	}, 2000);	
}
function search_in_messages(act_btn)
{
	var search_words = $('#search_text').val();
	var date_from = $('#from_date').val();
	var date_to = $('#to_date').val();
	
	
	init_iterface_dialogs('message_search');
	
	if(act_btn)
	{
		loading_btn('search_mesg_btn')
	}
	
	$.post('/ajax/ajaxMessages.php', 
	{   
		mode : 'search_in_messages',
		search_words : search_words,
		date_from : date_from,
		date_to : date_to
	},
	function(data){ 
		
		if(act_btn)
		{
			loading_btn('search_mesg_btn', 1)
		}
	
		 
		
		if(data)
		{
			current_prev_search_msg_page = 1;
			messages_search_more_proc = 0;
			
			 
			$('#messages_search_wrap').html(data);
			
			$('#messages_container').scroll(messages_search_list_scroll);
			msg_bl_scroll('down')
			get_search_messages_more();
		}
	});
}

function close_search_msgs()
{
	init_iterface_dialogs('show_dialogs')
}

function init_iterface_dialogs(what)
{
	if(what=='message_search')
	{
		$('#search_in_messages_btn').hide();
		$('#dialogs_wrap').hide();
		$('#dialog_search_res').hide();
		
		// изменение поисковой  панели
		$('#search_text').addClass('input_for_msg_search');
		$('#search_in_msgs_tool').show();
		$('#search_text').unbind('keyup', dialogs_search);
		
		$('#nav_custom_n').html('» Поиск по всем сообщениям');
		
	}
	else if(what == 'show_dialogs')
	{
		$('#nav_custom_n').html('');
		
		// изменение поисковой  панели
		$('#search_text').val('');
		$('#search_text').removeClass('input_for_msg_search');
		$('#search_in_msgs_tool').hide();
		$('#search_text').bind('keyup', dialogs_search);
		 
		
		$('#messages_search_wrap').html('');
		
		
		$('#search_in_messages_btn').hide();
		$('#dialogs_wrap').show();
		$('#dialog_search_res').hide();
	}
	else if(what=='dialog_search')
	{
		msg_date_block_op('delete');
		$('#dialog_search_res').show();
		$('#dialogs_wrap').hide();
		$('#search_in_messages_btn').show();
	}
}

messages_search_more_proc = 0;

function messages_search_list_scroll()
{
	 
	var offset = $('#msgs_list .msg_list_item:first').offset();
	
	var msgs_list_height = Number($('#msgs_list').height() - 350);

	if(offset.top>-100 && !messages_search_more_proc) 
	{ 
		messages_search_more_proc = 1
		get_search_messages_more();  
	}

}

// Получает больше сообщений
function get_search_messages_more()
{   
	current_prev_search_msg_page++;
	
	page = current_prev_search_msg_page;
	
	var search_words = $('#search_text').val(); 
	var date_from = $('#from_date').val();
	var date_to = $('#to_date').val();
	  
	$.post('/ajax/ajaxMessages.php', 
	{   
		mode : 'get_search_messages_more',
		page : page,
		search_words : search_words,
		date_from : date_from,
		date_to : date_to
	},
	function(data){ 
		
		if(data)
		{
				var  msgs_list_height_1 = parseInt($('#msgs_list').css('height'));
				
				$('#msgs_list').prepend(data);
				
			    var messages_container_scroll_top =  $('#messages_container').scrollTop();
				var msgs_list_height_2 = parseInt($('#msgs_list').css('height'));
				var res_scroll_top = msgs_list_height_2 - msgs_list_height_1+messages_container_scroll_top;
				$('#messages_container').scrollTop(res_scroll_top);
			
			
			    messages_search_more_proc = 0;
		}
		else
		{
			$('#prev_msgs_link_block').hide();	
		}
		 
	});
}

function go_to_searched_message(dialog_user_id, message_id)
{
	document.location.href = "/msgs?id="+dialog_user_id+'&mid='+message_id;
}

function msg_date_option_show(value)
{
	if(value==3)
	{
		$('#to_date_wrap').show();
		$('#from_date').attr('placeholder', 'С..')
	}
	else
	{
		$('#to_date_wrap').hide();
		$('#from_date').attr('placeholder', 'дд.мм.гггг')
	}

	 
}

function msg_date_block_op(what)
{
	if(what=='delete')
	{
		$('#to_date').val('');
		$('#from_date').val(''); 
		$('#show').hide();
		$('#search_date_block').hide();
		$('#date_selected').html('');
		$('#date_cancel_btn_wrap').show(); 
		$('#date_delete_btn_wrap').hide(); 
	}
	else if(what=='open')
	{
		$('#search_date_block').toggle();
	}
	else if(what=='cancel')
	{
		$('#search_date_block').hide();
	}
	else if(what=='select')
	{
		var date_type = $.trim($('#date_s_type').val());
		
		var date_from = $.trim($('#from_date').val());
		var date_to = $.trim($('#to_date').val());
		
		if(!valid_date_rus(date_from))
		{
			date_from = '';
			$('#from_date').val('')
		}
		if(!valid_date_rus(date_to))
		{
			date_to = '';
			$('#from_to').val('')
		}
		
		if(date_from > date_to && date_from && date_to)
		{
			$('#from_date').val(date_to);
			$('#to_date').val(date_from);
			
			var date_to_tmp = date_to;
			date_to = date_from;
			date_from = date_to_tmp;
			
		}
		
		var date_str = '';
		
		if(date_from && date_to && date_from==date_to)
		{
			date_str = date_from;
		}
		else if(date_from && date_to)
		{
			date_str = 'с '+date_from+' по '+date_to;
		}
		else if(date_from && !date_to)
		{
			date_str = 'с '+date_from;
		}
		else if(!date_from && date_to)
		{
			date_str = 'по '+date_to;
		}
		
		if(date_from || date_to)
		{
			$('#date_delete_btn_wrap').show();
			$('#date_cancel_btn_wrap').hide(); 
		}
		
		 
		
		$('#date_selected').html(date_str);
		$('#search_date_block').hide();
	}
}

function dialog_window_size_init()
{
	winheight = parseInt((window.innerHeight));

	dialog_window_height = winheight / 2;
	
	if(dialog_window_height < 300)
	{
		return false;
	}
	
	if(dialog_window_height > 0)
	{
		$('#messages_container').css('height', dialog_window_height+'px');
	}
	 
}


function show_planning_sessions()
{
	if(!toggle_top_popups('ps'))
	{
		return false;
	}
	
	$.post('/ajax/ajaxMessages.php', 
	{   
		mode : 'show_session_planning'
	},
	function(data){ 
		
		if(data)
		{
			//$('#ps_wrap').html(data);
			open_top_popup(data, 'ps')
			bind_hide_tp_popup_form();
		}
		
	});
}


ps_current_page = 0;
function history_ps_init()
{
	$.post('/ajax/ajaxMessages.php', 
	{   
		mode : 'get_ps_history_count'
	},
	function(data){ 
		
		var count = data;
		 
		$("#ps_pagination").pagination(Number(count), {
				current_page: ps_current_page, // select number(page) 5 (4+1)
				num_display_entries: 5, // count of paging numbers
				next_text: "Вперед", // next button text
				next_show_always: true,
				prev_text: "Назад",
				prev_show_always: true,
				callback: history_ps_list,                        
				items_per_page: ps_per_page,
				link_to : '#reports'
			});
		  
		history_is_show = 1;	 
		
	}, 'json');
}
 
function history_ps_list(page)
{
	var page;
	  
	//page = current_page
	 
	$.post('/ajax/ajaxMessages.php', 
	{   
		mode : 'get_ps_history_list',
		page : page
	},
	function(data){ 
		
		$('#ps_preload').remove();
		
		if(data)
		{
			if($('#ps_actual_wrap').attr('id')=='ps_actual_wrap')
			{
				$('#ps_sep').html("<div class='ps_sep'></div>");
			}
			 
			$('#hps_wrap').show();
			$('#ps_history_list').html(data);
		}
		else if(!data && $('#ps_actual_wrap').attr('id')!='ps_actual_wrap')
		{
			$('#ps_preload').remove();
			$('#ps_no').html('<div class="" style="padding-left:10px">Нет текущих планерок</div>')
		}
		
	});
}