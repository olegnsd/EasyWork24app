var Tasks = {
	
	// Выставить качество проделанной работы
	edit_task_rating : function(task_id, rating)
	{
		 
		// Картинка процесса
		$('#task_rating_'+task_id).html('<img src="/img/ajax-loader.gif">');
		
		$.post('/ajax/ajaxTasks1.php', 
		{   
			mode : 'edit_task_rating',
			task_id : task_id,
			rating : rating
		},
		function(data){ 
			
			loading('task_rating_'+task_id, 1); 
			
			if(data)
			{
				// Картинка процесса
				$('#task_notice_'+task_id).html('');
			}
		});
	},
	actual_page : 1,
	
	get_more : function(complete_load_eval_str, empty_load_eval_str)
	{
		var page, search_word;
	
		page = Tasks.actual_page + 1;
		
		$.post('/ajax/ajaxTasks1.php', 
		{   
			mode : 'get_more',
			page : page,
			list : list,
			list_status : list_status,
			key : key,
			filter_user_id : filter_user_id
		},
		function(data){ 
			
			if(data)
			{
				$('#tasks_list_body').append(data);
			}
			else
			{
				eval(empty_load_eval_str);
				$('#more_btn').hide();
			}
			 
			
			// Возможный переход к след итерации
			if(complete_load_eval_str)
			{
				eval(complete_load_eval_str);
			}
			 
			
		 	Tasks.actual_page++;
			 
		});
	},
	tasks_filter : function()
	{
		var pars = [];
		
		if(list)
		{
			pars.push('l='+list);
		}
		
		var status = $('#filter_status').val();
		if(status) pars.push('s='+status);
		
		var key = $('#filter_key').val();
		if(key) pars.push('k='+key);
		
		var uid  = $('#filter_user').val();
		if(uid) pars.push('uid='+uid);
		 
		
		var g_pars = pars.join('&');
		
		document.location.href = '/tasks?'+g_pars;
		
	},
	delegate_task : function(task_id)
	{
		var user_id = $('#task_delegate_performer').val();
		
		loading_btn('delegate_btn');
		
		$.post('/ajax/ajaxTasks1.php', 
		{   
			mode : 'delegate_task',
			task_id : task_id,
			user_id : user_id
		},
		function(data){ 
			
			loading_btn('delegate_btn', 1);
			
			if(data==1)
			{
				alert('Задача успешно делегирована!');
				document.location.reload();
			}
			
		});
	},
	get_task_delegate_from : function(task_id)
	{
		$.post('/ajax/ajaxTasks1.php',
		{   
			mode : 'get_task_delegate_from',
			task_id : task_id
		},
		function(data){ 
			
			if(data)
			{
				create_popup_block('delegate_form', 500, data, '', 1);
			}
			
		});
	},
	task_delete : function(task_id)
	{
		if(!confirm('Удалить задачу?')) return false;
		
		$.post('/ajax/ajaxTasks1.php', 
		{   
			mode : 'task_delete',
			task_id : task_id
		},
		function(data){ 
			
			if(data==1)
			{
				document.location.href = '/tasks';
			}
			
		});
	},
	save_task_report : function(report_id, task_id)
	{
		var report_text = $('#report_text_'+report_id).val();
		 
		loading_btn('edit_report_btn_'+report_id);
		
		// Прикрепленные файлы
		files_arr = Disk.get_upload_content_files('task_report_'+report_id+'_'+task_id);
		files_content_type = Disk.get_upload_content_files_content_type('task_report_'+report_id+'_'+task_id);
		files_deleted = Disk.get_content_deleted_files();
	
		$.post('/ajax/ajaxTasks1.php', 
		{   
			mode : 'save_task_report',
			report_id : report_id,
			report_text : report_text,
			files_arr : $.toJSON(files_arr),
			files_content_type : files_content_type,
			files_deleted : $.toJSON(files_deleted)
		},
		function(data){ 
			
			loading_btn('edit_report_btn_'+report_id, 1);
			
			if(data['error'])
			{  
				if(data['error']['report_text']=='1')
				{   
					$('#report_text_'+report_id).focus();
				}
			}
			if(data['success']==1)
			{
				  Tasks.get_report_item(report_id, 0, 1)
			}
			
		}, 'json');
	},
	get_report_item : function(report_id, form, replace_item)
	{
		$.post('/ajax/ajaxTasks1.php', 
		{   
			mode : 'get_report_item',
			task_id : task_id,
			report_id : report_id,
			form : form
		},
		function(data){ 
			
			if(data && replace_item)
			{
				$('#task_report_'+report_id).replaceWith(data);
			}
			
		});
	},
	delete_task_report : function(report_id)
	{
		if(!confirm('Удалить отчет?')) return false;
		
		$.post('/ajax/ajaxTasks1.php', 
		{   
			mode : 'delete_task_report',
			task_id : task_id,
			report_id : report_id
		},
		function(data){ 
			
			if(data==1)
			{
				$('#task_report_'+report_id).remove();
			}
			
		});
	},
	
	history_is_show : 0,
	
	init_task_report_list : function(task_id)
	{
		$.post('/ajax/ajaxTasks1.php', 
		{   
			mode : 'get_task_reports_count',
			task_id : task_id
		},
		function(data){ 
			
			var count = data['count'];
			 
			$("#pagination").pagination(Number(count), {
					current_page: current_page, // select number(page) 5 (4+1)
					num_display_entries: 5, // count of paging numbers
					next_text: "Вперед", // next button text
					next_show_always: true,
					prev_text: "Назад",
					prev_show_always: true,
					callback: Tasks.get_task_report_list,                        
					items_per_page: reports_per_page,
					link_to : '#reports'
				});
			  
			history_is_show = 1;	 
			
		}, 'json');
	},
	get_task_report_list : function(current_page)
	{
		var page;
		 
		page = current_page
		
		$.post('/ajax/ajaxTasks1.php', 
		{   
			mode : 'get_task_report_list',
			task_id : task_id,
			page : page
		},
		function(data){ 
			
			if(data)
			{
				$('#task_reports_list').html(data);
			}
		});
	},

	task_report_btn : 0,
	
	add_task_report : function(task_id)
	{
		var report_text, by_sms;
		
		if(Tasks.task_report_btn==1)
		{
			return;
		}
		
		report_text = $('#report_text').val();
		
		Tasks.task_report_btn = 1;
		
		// Прикрепленные файлы
		files_arr = Disk.get_upload_content_files('task_report_'+task_id);
		files_content_type = Disk.get_upload_content_files_content_type('task_report_'+task_id);
		
		loading_btn('add_report_btn');
		
		$.post('/ajax/ajaxTasks1.php', 
		{   
			mode : 'add_task_report',
			task_id : task_id,
			report_text : report_text,
			by_sms : by_sms,
			files_arr : $.toJSON(files_arr),
			files_content_type : files_content_type
		},
		function(data){ 
			
			Tasks.task_report_btn = 0;
			
			loading_btn('add_report_btn', 1);
			
			task_report_btn = 0;
			
			if(data['error'])
			{  
				if(data['error']['report_text']=='1')
				{   
					$('#report_text').focus();
				}
			}
			if(data['success']==1)
			{
				Tasks.init_task_report_list(task_id);
				
				Disk.cancel_file_upload_aueue('task_report_'+task_id, 1);
				
				$('#report_text').val('');
				
				get_task_report_list(task_id);
				
				 
			}
			
		}, 'json');
		
	},
	show_task_tab : function(id)
	{  
		$('.task_tabs a').removeClass('active');
		$('#task_tab_'+id).addClass('active');
		
		$('.tab_content_wrap .tab').hide();
		$('.tab_content_wrap #tab_'+id).show();
		
	},
	get_task_status_bar : function(task_id)
	{
		$.post('/ajax/ajaxTasks1.php', 
		{   
			mode : 'get_task_status_bar',
			task_id : task_id
		},
		function(data){ 
			
			if(data['btns'])
			{
				$('#task_btns').html(data['btns']);
				$('#task_status_bar').html(data['status_bar']);
				
			}
			else
			{
				error();
			}
			
		}, 'json')
	},
	// Пользователь прочитал задание
	task_status : function(task_id, status, is_boss)
	{
		$.post('/ajax/ajaxTasks1.php', 
		{   
			mode : 'task_status',
			task_id : task_id,
			status : status
		},
		function(data){ 
			
			if(data=='-1') {document.location.reload();}
			else Tasks.get_task_status_bar(task_id); 
			 
		
			 
			
		})
	},

	edit_task : function(task_id)
	{
		$.post('/ajax/ajaxTasks1.php', 
		{   
			mode : 'edit_task',
			task_id : task_id
			
		},
		function(data){ 
			
			if(data)
			{
				create_popup_block('add_form', 700, data, '', 1);
			}
			
		});
	},
	
	 
	
	save_task : function(task_id)
	{
		Tasks.hide_task_result_form(0);
		
		var task_theme = $('#task_theme').val();
		
		var task_text = $('#task_text').val();
		
		var task_max_date = $('#task_max_date').val();
		
		var task_max_date_hours = $('#task_max_date_hours').val();
		
		var task_max_date_minuts = $('#task_max_date_minuts').val();
	 
		var task_priority = $('#task_priority').val();
	
		var task_difficulty = $('#task_difficulty').val();
		
		var task_from_user = $('#task_from_user').val();
		
		var task_from_user = $('#task_from_user').val(); 
		
		var task_user_performer_main = $('#task_performer_main_user').val();
		
		var task_users_performers = {};
		var task_users_copies = {};
		
		$('#task_users_performers .task_user_performers').each(function(){
			 
			var user_id = $(this).val();
			task_users_performers[user_id] = user_id;
		})
		
		$('#task_users_copies .task_user_copy').each(function(){
			var user_id = $(this).val();
			task_users_copies[user_id] = user_id;
		})
		
		// Прикрепленные файлы
		files_arr = Disk.get_upload_content_files('task_'+task_id);
		files_content_type = Disk.get_upload_content_files_content_type('task_'+task_id);
		files_deleted = Disk.get_content_deleted_files();
		
		Tasks.add_task_btn = 1;
		
		loading_btn('add_task_btn_0');
		
		$.post('/ajax/ajaxTasks1.php', 
		{   
			mode : 'save_task',
			task_id : task_id,
			task_theme : task_theme,
			task_text : task_text,
			task_max_date : task_max_date,
			task_max_date_hours : task_max_date_hours,
			task_max_date_minuts : task_max_date_minuts,
			task_priority : task_priority,
			task_difficulty : task_difficulty,
			task_from_user : task_from_user,
			task_user_performer_main : task_user_performer_main,
			task_users_performers : $.toJSON(task_users_performers),
			task_users_copies : $.toJSON(task_users_copies),
			files_arr : $.toJSON(files_arr),
			files_content_type : files_content_type,
			files_deleted : $.toJSON(files_deleted)
			
		},
		function(data){ 
			
			 
			loading_btn('add_task_btn_0', 1);
			
			if(data['error'])
			{
				Tasks.error_task_form(data['error']);
			}
			else if(data['success']==1)
			{
				document.location.reload();
			}
			
		}, 'json');
	},
	
	add_task_btn : 0,
	add_task : function()
	{
		if(Tasks.add_task_btn==1)
		{
			return false;
		}
		
		Tasks.hide_task_result_form(0);
		
		var task_theme = $('#task_theme').val();
		
		var task_text = $('#task_text').val();
		
		var task_max_date = $('#task_max_date').val();
	 	
		var task_max_date_hours = $('#task_max_date_hours').val();
		
		var task_max_date_minuts = $('#task_max_date_minuts').val();
		
		var task_priority = $('#task_priority').val();
	
		var task_difficulty = $('#task_difficulty').val();
		
		var task_from_user = $('#task_from_user').val();
		
		var task_from_user = $('#task_from_user').val(); 
		
		var task_user_performer_main = $('#task_performer_main_user').val();
		
		var task_users_performers = {};
		var task_users_copies = {};
		
		$('#task_users_performers .task_user_performers').each(function(){
			 
			var user_id = $(this).val();
			task_users_performers[user_id] = user_id;
		})
		
		$('#task_users_copies .task_user_copy').each(function(){
			var user_id = $(this).val();
			task_users_copies[user_id] = user_id;
		})
		
		// Прикрепленные файлы
		files_arr = Disk.get_upload_content_files('new_task');
		files_content_type = Disk.get_upload_content_files_content_type('new_task');
		
		Tasks.add_task_btn = 1;
		
		loading_btn('add_task_btn_0');
		
		$.post('/ajax/ajaxTasks1.php', 
		{   
			mode : 'add_task',
			task_theme : task_theme,
			task_text : task_text,
			task_max_date : task_max_date,
			task_max_date_hours : task_max_date_hours,
			task_max_date_minuts : task_max_date_minuts,
			task_priority : task_priority,
			task_difficulty : task_difficulty,
			task_from_user : task_from_user,
			task_user_performer_main : task_user_performer_main,
			task_users_performers : $.toJSON(task_users_performers),
			task_users_copies : $.toJSON(task_users_copies),
			files_arr : $.toJSON(files_arr),
			files_content_type : files_content_type
			
		},
		function(data){ 
			
			Tasks.add_task_btn = 0;
			loading_btn('add_task_btn_0', 1);
			
			if(data['error'])
			{
				Tasks.error_task_form(data['error']);
			}
			else if(data['success']==1)
			{
				document.location.reload();
			}
			
		}, 'json');
		
	},	
	error_task_form : function(data)
	{
		var error_txt = '';
		
		$(data).each(function(i,j) {
			
			if(j['task_text']==1)
			{
				error_txt += '<div>Не указан текст задания</div>';
			}
			if(j['task_max_date']==1)
			{
				error_txt += '<div>Некорректно указан крайний срок</div>';
			}
			if(j['task_from_user']==1)
			{
				error_txt += '<div>Не выбран постановщик задачи</div>';
			}
			if(j['task_user_performer_main']==1)
			{
				error_txt += '<div>Не выбран исполнитель задачи</div>';
			}
			if(j['task_max_date_hours']==1)
			{
				error_txt += '<div>Некорректно указаны часы</div>';
			}
			if(j['task_max_date_minuts']==1)
			{
				error_txt += '<div>Некорректно указаны минуты</div>';
			}
			 
		})
		
		if(error_txt)
		{
			$('#task_form_result_0').html('<div class="error_box" style="display:block">'+error_txt+'</div>');
		}
	},
	hide_task_result_form : function(id)
	{
		$('#task_form_result_'+id).html('');
	},
	add_task_user_row : function(what)
	{
		var id = Math.round( Math.random()*1000);
		
		if(what=='performer')
		{
			var elem_id = 'task_performer_'+id;
			$('#task_users_performers').append('<select id="'+elem_id+'" class="task_user_performers"></select><br>');
			var btn_text = 'Выбрать соисполнителя';
		}
		else if(what=='copy')
		{
			var elem_id = 'task_copy_'+id;
			$('#task_users_copies').append('<select id="'+elem_id+'" class="task_user_copy"></select><br>');
			var btn_text = 'Выбрать сотрудника';
		}
		
		Tasks.init_task_user_select(elem_id, btn_text); 
		 
	},
	init_task_user_select : function(elem_id, btn_text)
	{
		$('#'+elem_id).easycomplete(
		{
			str_word_select : btn_text,
			width:396,
			url:'/ajax/ajaxGetUsers.php?current_user=1&by=name&who=all'
		});	 
	},
	
	show_add_task_extend_form : function()
	{
		$('#add_task_ext_pars').fadeIn(200);
		$('#add_form_block_show').hide();
	},
	
	get_add_form : function()
	{
		$.post('/ajax/ajaxTasks1.php', 
		{   
			mode : 'get_add_form'			
		},
		function(data){ 
			
			if(data)
			{
				create_popup_block('add_form', 700, data, '', 1);
			}
			
		});
	}
	
}
