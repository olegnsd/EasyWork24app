function notes_search()
{
	var search_word;
	 
	search_word = $('#search_text').val();
	
	$.post('/ajax/ajaxNotes.php', 
	{   
		mode : 'notes_search',
		search_word : search_word,
		is_av : is_av
	},
	function(data){ 
		
		$('#notes_content').html('');
		$('#notes_content').html(data['content']);
		 
		actual_page = 1;
		
	}, 'json');
}

function add_note()
{
	var note_text, note_theme;
	
	note_text = $('#note_text').val();
	
	note_theme = $('#note_theme').val();
	
	loading_btn('add_note_btn');
	
	$.post('/ajax/ajaxNotes.php', 
	{   
		mode : 'add_note',
		note_text : note_text,
		note_theme : note_theme
	},
	function(data){ 
		
		loading_btn('add_note_btn', 1);
		
		if(data['error'])
		{
			if(data['error']['text'])
			{
				$('#note_text').focus();
			}
		}
		if(data['success']==1)
		{
			$('#note_text').val('');
			$('#note_theme').val('');
			$('#note_text').focus();
			get_note_item(data['note_id'],0,1);
		}
		
	}, 'json');
}

actual_page = 1;

// Выводит больше имущест
function get_more_notes()
{
	var page;
	
	var search_word = $('#search_text').val();
	
	page = actual_page + 1;

	$.post('/ajax/ajaxNotes.php', 
	{   
		mode : 'get_more_notes',
		page : page,
		is_av : is_av,
		search_word : search_word
	},
	function(data){ 
		
		$('#notes_list').append(data);
		
		// Актаульная страница
		actual_page++;
		
		if(actual_page>=pages_count)
		{
			$('#more_notes_btn').hide();
		}
	});
}




function delete_note(note_id)
{
	$.post('/ajax/ajaxNotes.php', 
	{   
		mode : 'delete_note',
		note_id : note_id,
		user_id : current_user_id
	},
	function(data){ 
		
		// Удаление успешно
		if(data==1)
		{
			$('#note_cont_'+note_id).hide();
			// Вывод уведомления
			$('#note_cont_action_notice_'+note_id).html('<div class="success">Заметка удалена | <a href="javascript:;" onclick="restore_note('+note_id+')">Восстановить</a> | <a href="javascript:;" onclick="$(\'#note_'+note_id+'\').remove();">Скрыть</a></div>');
		}
	});
}

function restore_note(note_id)
{
	$.post('/ajax/ajaxNotes.php', 
	{   
		mode : 'restore_note',
		note_id : note_id,
		user_id : current_user_id
	},
	function(data){ 
		
		if(data==1)
		{
			$('#note_cont_'+note_id).show();
			// Вывод уведомления
			$('#note_cont_action_notice_'+note_id).html('');
		}
	});
}

function delete_note_version(version_id, note_id)
{
	$.post('/ajax/ajaxNotes.php', 
	{   
		mode : 'delete_note_version',
		version_id : version_id,
		user_id : current_user_id
	},
	function(data){ 
		
		// Удаление успешно
		if(data==1)
		{  
			$('#note_cont_'+note_id).hide();
			// Вывод уведомления
			$('#note_cont_action_notice_'+note_id).html('<div class="success">Версия заметки удалена | <a href="javascript:;" onclick="restore_note_version('+version_id+', '+note_id+')">Восстановить</a> | <a href="javascript:;" onclick="get_note_item('+note_id+', 0,0)">Скрыть</a></div>');
		}
	});
}

function restore_note_version(version_id, note_id)
{
	$.post('/ajax/ajaxNotes.php', 
	{   
		mode : 'restore_note_version',
		version_id : version_id,
		user_id : current_user_id
	},
	function(data){ 
		
		if(data==1)
		{
			$('#note_cont_'+note_id).show();
			// Вывод уведомления
			$('#note_cont_action_notice_'+note_id).html('');
		}
	});
}
  

function get_note_item(note_id, form, prepend)
{
	$.post('/ajax/ajaxNotes.php', 
	{   
		mode : 'get_note_item',
		note_id : note_id,
		user_id : current_user_id
	},
	function(data){ 
		
		if(prepend)
		{
			$('#notes_list').prepend(data);
		}
		else
		{  
			$('#note_'+note_id).replaceWith(data);
		}
		
		$('.no_contents').remove(); 
		 
	});
}
function get_note_version_item(version_id, form, prepend, is_show, note_id)
{
	$.post('/ajax/ajaxNotes.php', 
	{   
		mode : 'get_note_version_item',
		version_id : version_id,
		form : form,
		is_show : is_show
	},
	function(data){ 
		
		if(note_id)
		{
			$('#note_version_'+note_id).html(data);
			 
			note_versions_bl_css_init(note_id)
		}
		else if(prepend)
		{
			$('#notes_list').prepend(data);
		}
		else
		{   
			$('#version_'+version_id).replaceWith(data);
			
			if(is_show)
			{ 
				$('#version_'+version_id+' .show_note_version_cont_btn').trigger('click')
			}
		}
		
		if(form==1)
		{
			$('textarea').autoResize();
			//$('#note_text_'+version_id).val($('#note_text_'+version_id).val()+$('#note_text_'+version_id).val());
			$('#note_text_'+version_id).trigger('keydown');
			 
		}
	});
}

function save_note_version(version_id, note_id)
{
	var note_text, note_theme;
	
	note_text = $('#note_text_'+version_id).val();
	
	note_theme = $('#note_theme_'+version_id).val();
	
	loading_btn('save_note_version_btn_'+version_id);
	
	$.post('/ajax/ajaxNotes.php', 
	{   
		mode : 'save_note_version',
		version_id : version_id,
		note_text : note_text,
		note_id : note_id,
		note_theme : note_theme
	},
	function(data){ 
		
		if(data['success']==1)
		{
			get_note_version_for_user(note_id);
			get_note_versions_list(note_id);
			 
		}
		else
		{
			loading_btn('save_note_version_btn_'+version_id, 1);
			
			if(data['error'])
			{
				if(data['error']['text'])
				{
					$('#note_text_'+version_id).focus();
				}
			}
		}
		 
	}, 'json');
}

function get_note_version_for_user(note_id)
{
	$.post('/ajax/ajaxNotes.php', 
	{   
		mode : 'get_note_version_for_user',
		note_id : note_id
	},
	function(data){ 
		
		if(data)
		{
			$('#note_version_'+note_id).html(data);
			note_versions_bl_css_init(note_id)
		}
	});
}

function get_note_versions_list(note_id)
{
	$.post('/ajax/ajaxNotes.php', 
	{   
		mode : 'get_note_versions_list',
		note_id : note_id
	},
	function(data){ 
		
		if(data)
		{
			$('#note_versions_list_'+note_id).html(data)
		}
	});
}

function get_note_title(note_id)
{
	$.post('/ajax/ajaxNotes.php', 
	{   
		mode : 'get_note_title',
		note_id : note_id
	},
	function(data){ 
		
		if(data)
		{
			$('#note_title_'+note_id).html(data)
		}
		 
	});
}

// Показать блок настройки доступа к файлам и папкам
function show_access_note_block(note_id)
{
	close_note_access_blocks();
	
	$('#owner_block_'+note_id).show();
}

function close_note_access_blocks()
{
	$('.file_hide_block').hide();
}

function give_access_to_note(note_id, user_id)
{
	$('#access_proc_'+note_id).html('<img src="/img/ajax-loader.gif">');
	
	$.post('/ajax/ajaxNotes.php', 
	{   
		mode : 'give_access_to_note',
		note_id : note_id,
		user_id : user_id
	},
	function(data){ 
		
		$('#access_proc_'+note_id).html('');
		
		if(data==1)
		{
			$('#user_'+note_id+'_'+user_id).removeClass('access_active')
		}
		if(data==2)
		{
			$('#user_'+note_id+'_'+user_id).addClass('access_active')
		}
		
		$('.no_contents').remove(); 
		 
	});
}

function show_note_version_text(version_id, note_id)
{
	$('#note_version_title_'+version_id).hide()
	$('#note_version_text_'+version_id).slideDown(200);
	$('#hide_note_version_btn_'+version_id).show();
	setTimeout(function() {note_versions_bl_css_init(note_id)}, 200); 
}
function hide_note_version_text(version_id)
{
	$('#note_version_title_'+version_id).show()
	$('#note_version_text_'+version_id).hide(0);
	$('#hide_note_version_btn_'+version_id).hide();
	 note_versions_bl_css_init();
}

function note_versions_bl_css_init(note_id)
{ 
	$('.note_version_text').each(function() {
		 
		height = $(this).css('height');
		height = parseInt(height);
		par_note_id = $(this).attr('note_id');
		
		if(!$(this).is(':visible'))
		{ 
			height = 55
		}
		if(height<55)
			{  
			  	height = 55;
			//  scroll_y = 'none';
			}
			else
			{
			 // scroll_y = 'scroll';
			}
			height = height - 0;
			$('#note_versions_list_'+par_note_id).css('height', height+'px');
			//$('#note_versions_list_'+par_note_id).css('overflow-y', scroll_y);
			})
}

function hide_accessed_note(note_id)
{
	$.post('/ajax/ajaxNotes.php', 
	{   
		mode : 'hide_accessed_note',
		note_id : note_id
	},
	function(data){ 
		
		if(data==1)
		{
			$('#note_'+note_id).remove();
		}
		 
	});
}

function add_user_to_access_note(note_id)
{
	var id = Math.round( Math.random()*1000);
	var elem_id = 'noto_user_access_'+id;
	$('#access_users_list_'+note_id).append('<select id="'+elem_id+'" class="access_user_item"></select><br>');
	
	$('#'+elem_id).easycomplete(
	{
		str_word_select : 'Выбрать пользователя',
		width:396,
		url:'/ajax/ajaxGetUsers.php?by=name&who=all_tree&result_name=2'
	});	 
}
function save_note_user_access(note_id)
{
	var access_users = {};
	
	loading_btn('save_access_btn_'+note_id);
	
	$('#access_users_list_'+note_id+' .access_user_item').each(function(){
			  
		var user_id = $(this).val();
		
		access_users[user_id] = user_id;
	})
		
	$.post('/ajax/ajaxNotes.php', 
	{   
		mode : 'save_note_user_access',
		note_id : note_id,
		access_users : $.toJSON(access_users)
	},
	function(data){ 
		
		loading_btn('save_access_btn_'+note_id, 1);
		
		if(data==1)
		{
			$('#access_result_'+note_id).html('<div class="success stand_margin">Успешно сохранено</div>');
			clear_block_by_settime('access_result_'+note_id);
		}
		 
	});
}

function get_note_access_block(note_id)
{
	$.post('/ajax/ajaxNotes.php', 
	{   
		mode : 'get_note_access_block',
		note_id : note_id
	},
	function(data){ 
		
		if(data)
		{
			$('.note_access_block').html('');
			$('#owner_block_'+note_id).html(data)
		}
	});
}