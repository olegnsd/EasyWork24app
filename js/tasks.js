// Показывает доску заданий для пользователя

task_active = 0;

// Показ последних заданий для сотрудника
function show_user_tasks(current_user_id, user_id)
{ 
	var top;
	$('#task_error').html('')
	$('#user_task_block').show();
	$('#task_success').html('');
	$('#new_task_text').addClass('add_new_task_default');
	$('#new_task_text').val('Добавить задание...');
	$('#tasks_list').html('<img src="/img/loading5.gif" />');
	
	if(task_active==user_id)
	{
		$('.worker_list_block').removeClass('worker_list_block_active');
		$('#user_task_block').hide();
		 
		task_active = 0;
		return
	}
	
	task_active = user_id;
	
	$('.worker_list_block').removeClass('worker_list_block_active')
	
	$('#task_'+user_id).addClass('worker_list_block_active')
	
	top = Math.ceil($('#task_'+user_id).offset().top)
	
	$('#user_task_block').css('margin-top', Number(top-140)+'px');
	
	
}

// Получает список заданий
function get_tasks_list(to_user_id, after_add)
{
	search_word = $('#search_text').val();
	
	if(search_word == default_search_text)
	{
		search_word = '';
	}
	
	if(search_word.length<4)
	{
		if(search_word.length>=1 && !after_add)
		{
			return false;
		}
		
		search_word = '';
	}
	 
	$.post('/ajax/ajaxTasks.php', 
	{   
		mode : 'get_tasks_list',
		to_user_id : to_user_id,
		search_word : search_word,
		date : date,
		is_tasks_to_users : is_tasks_to_users
	},
	function(data){ 
		
		$('#tasks_list').html(data);
		//draw_background_list_item('task_list_item');
		
	});
}

// Показать блок настройки доступа к файлам и папкам
function show_task_copy_block(task_id)
{
	close_task_copy_blocks();
	
	$('#owner_block_'+task_id).show();
}

function close_task_copy_blocks()
{
	$('.file_hide_block').hide();
}

function copy_task(task_id)
{
	clear_add_task_form();
	
	$('#get_copy_task_proc').html('<img src="/img/ajax-loader.gif">');
	
	$.post('/ajax/ajaxTasks.php', 
	{   
		mode : 'get_task_json_data',
		task_id : task_id
	},
	function(data){ 
		
		$('#get_copy_task_proc').html('');
		
		 if(data['task_id']==task_id)
		 {
			  $('#task_theme').val(data['task_theme']);
			  $('#new_task_text').val(data['task_text']);
			  $('#task_priority').val(data['task_priority']);
			  $('#task_difficulty').val(data['task_difficulty']);
			  show_add_task_extend_form();
		 }
		 
	}, 'json');
}

task_send = 0;

// Добавить задание
function add_task(to_user_id)
{
	var task_text, task_date, task_priority, task_desired_days, task_max_date, task_theme, task_difficulty, task_sms_notice_to_boss, task_link_deal;
	
	// Защита повторного нажатия на кнопку
	if(task_send==1)
	{
		return
	}
	
	// Убираем сообщение об ошибках
	$('#task_error').html('')
	$('#task_error').hide();
	$('#add_task_result').hide();
	
	task_text = $('#new_task_text').val();
	
	// Убираем сообщение о прошлых успешных добавленных заданиях
	$('#task_success').html('');
	
	if(task_text=='Добавить задание...')
	{
		task_text = '';
	}
	
	// Дата задания
	task_date = $('#task_date').val();
	
	task_desired_days = $('#task_desired_days').val();
	
	task_max_date = $('#task_max_date').val();
	 
	task_priority = $('#task_priority').val();
	
	task_theme = $('#task_theme').val();
	
	task_difficulty = $('#task_difficulty').val();
	
	task_sms_notice_to_boss = $('#task_sms_notice_to_boss').attr('checked')=='checked' ? 1 : 0;
	
	task_link_deal = $('#task_link_deal').val();
	
	task_send = 1;
	
	// Прикрепленные файлы
	files_arr = Disk.get_upload_content_files(to_user_id);
	files_content_type = Disk.get_upload_content_files_content_type(to_user_id);
	 
	loading_btn('add_task_btn');
	 
	$.post('/ajax/ajaxTasks.php', 
	{   
		mode : 'add_new_task',
		to_user_id : to_user_id,
		task_text : task_text,
		task_date : task_date,
		task_max_date : task_max_date,
		task_desired_days : task_desired_days,
		task_priority : task_priority,
		task_theme : task_theme,
		task_difficulty : task_difficulty,
		task_sms_notice_to_boss : task_sms_notice_to_boss,
		task_link_deal : task_link_deal,
		files_arr : $.toJSON(files_arr),
		files_content_type : files_content_type
	},
	function(data){ 
		
		loading_btn('add_task_btn', 1);
		task_send = 0;
		
		if(data['error'])
		{  
			var error_notice;
			
			if(data['error']['text']==1)
			{
				//$('#task_error').html('<div>Задание не может быть пустым.</div>');
				//$('#new_task_text').val('Добавить задание...');
				$('#new_task_text').focus();
				//$('#new_task_text').addClass('add_new_task_default')
			}
			if(data['error']['date']==1)
			{
				//$('#task_error').append('<div>Не выбрана дата.</div>');
				
			}
			if(data['error']['date_start']==1)
			{
				error_notice = 1;
				$('#task_error').append('<div>Выбранная дата меньше текущей.</div>');
				
			}
			if(data['error']['date']==3)
			{	
				error_notice = 1;
				$('#task_error').append('<div>Максимальное время выполнения введено некорректно.</div>');
			}
			if(error_notice)
			{
				$('#add_task_result').show();
				$('#task_error').show();
			}
		}
		 
		if(data['success']=='1')
		{
			
			$('#add_task_result').show();
			 
			$('#task_success').html('<div class="success">Задание успешно добавлено.</div>');
			
			clear_add_task_form(to_user_id);
			
			//$('#new_task_text').addClass('add_new_task_default');
			
			setTimeout(function(){$('#task_success').html('')},2000)
			
			// Список заданий
			get_tasks_list(to_user_id, 1);
			
			// Обновляем данные календаря
			//get_tasks_dates()
	
			 
		}
		
	}, 'json');
}

function clear_add_task_form(to_user_id)
{
	$('#new_task_text').val('');
			
	$('#task_date').val('');
	
	$('#task_theme').val('');
	
	$('#task_max_date').val('');
	
	$('#task_desired_days').val(0);
	
	$('#task_date').hide();
	
	$('#task_max_date').hide();
	
	$('#task_priority').val(2);
	
	$('#task_difficulty').val(2);
	 
	$('#clear_max_task_date').hide();
	
	$('#clear_task_date').hide();
	
	Disk.cancel_file_upload_aueue(to_user_id, 1);
}

// Удалить задание
function delete_task(task_id)
{
	// Картинка процесса
	//$('#task_notice_'+task_id).html('<img src="/img/loading5.gif">');
	
	// Картинка процесса
	//loading('task_notice_'+task_id);
	 
	$.post('/ajax/ajaxTasks.php', 
	{   
		mode : 'delete_task',
		task_id : task_id
	},
	function(data){ 
		
		// Удаление успешно
		if(data==1)
		{
			// Прячем текст задачи
			$('.task_cont_to_hide_'+task_id).hide();
	
			// Вывод уведомления
			$('#task_notice_'+task_id).html('Задание удалено | <a href="javascript:;" class="link" onclick="restore_task('+task_id+')">Восстановить</a> | <a href="javascript:;" class="link" onclick="remove_deleted_task('+task_id+');">Скрыть</a>');
		}
		
		
	});
}

function remove_deleted_task(task_id)
{
	var group_date = $('#task_'+task_id).parent('.group_list').attr('date');
	
	$('#task_'+task_id).remove();
	
	if(!$('#group_date_list_'+group_date).children('.task_item').size())
	{ 
		$('#date_group_'+group_date).remove();
	}
	
	 
	apacity_task_item(task_id, 1)
}
// Восстановить задание
function restore_task(task_id)
{
	// Картинка процесса
	loading('task_notice_'+task_id);
	
	$.post('/ajax/ajaxTasks.php', 
	{   
		mode : 'restore_task',
		task_id : task_id
	},
	function(data){ 
		
		// Восстановление успешно
		if(data==1)
		{
			$('#task_notice_'+task_id).html('');
			
			$('.task_cont_to_hide_'+task_id).show();
			
		}
		
		
	});
}

// Форма редактирования задания
function get_edit_task_form(task_id)
{
	// Прячем текстзадачи
	$('#task_'+task_id).html('<img src="/img/loading5.gif">');
	
	
	$.post('/ajax/ajaxTasks.php', 
	{   
		mode : 'get_edit_task_form',
		task_id : task_id
	},
	function(data){ 
		
		// Удаление успешно
		if(data)
		{
			$('#task_'+task_id).replaceWith(data);
			apacity_task_item(task_id);
			
		}
	});
}

// Отменить редактирование задачи
function task_cancel_edit(task_id)
{
	loading_btn('cancel_save_task_btn_'+task_id);
	
	// Получаем задачу
	get_task_list_item(task_id);
	
}

// Получает задачу по id и вставляет ее в список задач
function get_task_list_item(task_id)
{
	$.post('/ajax/ajaxTasks.php', 
	{   
		mode : 'get_task_list_item',
		task_id : task_id
	},
	function(data){ 
		
		// Удаление успешно
		if(data)
		{
			$('#task_'+task_id).replaceWith(data);
			 
			apacity_task_item(task_id, 1); 
		}
	});
}

// Сохранить задачу
function save_edit_task(task_id)
{
	var task_text, task_priority, task_theme, task_difficulty, task_max_date, task_sms_notice_to_boss, task_link_deal;
	
	$('#task_error_'+task_id).hide();
	$('#task_error_'+task_id).html('');
	
	task_text = $('#task_text_'+task_id).val();
	
	task_priority = $('#task_priority_'+task_id).val();
	
	task_theme = $('#task_theme_'+task_id).val();
	
	task_difficulty = $('#task_difficulty_'+task_id).val();
	
	task_max_date = $('#task_max_date_'+task_id).val();
	
	task_sms_notice_to_boss = $('#task_sms_notice_to_boss_'+task_id).attr('checked')=='checked' ? 1 : 0;
	
	task_link_deal = $('#task_link_deal_'+task_id).val();
	
	// Прикрепленные файлы
	files_arr = Disk.get_upload_content_files('task_'+task_id);
	files_content_type = Disk.get_upload_content_files_content_type('task_'+task_id);
	files_deleted = Disk.get_content_deleted_files();
	
	loading_btn('save_task_btn_'+task_id);
	 
	$.post('/ajax/ajaxTasks.php', 
	{   
		mode : 'save_edit_task',
		task_id : task_id,
		task_text : task_text,
		task_priority : task_priority,
		task_theme : task_theme,
		task_difficulty : task_difficulty,
		task_max_date : task_max_date,
		task_sms_notice_to_boss : task_sms_notice_to_boss,
		task_link_deal : task_link_deal,
		files_arr : $.toJSON(files_arr),
		files_content_type : files_content_type,
		files_deleted : $.toJSON(files_deleted),
	},
	function(data){ 
		
		// Сохранение успешно
		if(data['success']==1)
		{
			get_task_list_item(task_id);
		}
		else if(data['error'])
		{
			loading_btn('save_task_btn_'+task_id, 1);
			
			if(data['error']['text'])
			{
				$('#task_text_'+task_id).focus();
			}
			if(data['error']['date']==3)
			{  
				$('#task_error_'+task_id).append('<div >Максимальное время выполнения введено некорректно.</div>');
				error_notice = 1;
			}
			if(error_notice)
			{
				$('#add_task_result').show();
				$('#task_error_'+task_id).show();
			}
		}
		
	}, 'json');
}

// Получить список задач и обновить календарь
function get_tasks_dates(task_id)
{
	$('#task_notice_'+task_id).html('<img src="/img/loading5.gif" />');
	
	$.post('/ajax/ajaxTasks.php', 
	{   
		mode : 'get_tasks_dates',
		to_user_id : to_user_id
	},
	function(data){ 
		
		// Массив дат задач
		datesArr = new Array();
	  	
		var date_arr_tmp = data.split(',');
		
		$.each(date_arr_tmp, function(i, j) {
			datesArr.push(j)
		})

		
		// Инициализация календаря событий
		render_event_calendar(calendareStartDate);
			


	});
}



// Пользователь прочитал задание
function task_status(task_id, status, is_boss)
{
	
	show_task_proc_img(task_id);
	
	if(status==5)
	{
		if(!confirm('Сотрудник не справился с заданием?')) return false;
	}
	
	if(is_boss)
	{
		
		if(status==5)
		{
			loading_btn('finished_fail_'+task_id);
		}
		if(status=='-3')
		{
			loading_btn('not_confirm_finished_task_btn_'+task_id);
		}
	}
	else if(!is_boss)
	{
		if(status=='1')
		{
			loading_btn('task_confirm_btn_'+task_id);
		}
		else if(status=='2')
		{
			loading_btn('task_proc_btn_'+task_id);
		}
		else if(status=='-2')
		{
			loading_btn('task_not_proc_btn_'+task_id);
		}
		else if(status=='3')
		{
			loading_btn('task_complete_btn_'+task_id);
		}
		else if(status=='-3')
		{
			loading_btn('task_not_complete_btn_'+task_id);
		}
		else if(status=='4')
		{
			loading_btn('task_cant_btn_'+task_id);
		}
	}
	 
	 
	 
	 
	$.post('/ajax/ajaxTasks.php', 
	{   
		mode : 'task_status',
		task_id : task_id,
		status : status,
		is_boss : is_boss
	},
	function(data){ 
		
		if(status==5)
		{
			loading_btn('finished_fail_btn_'+task_id), 1;
		}
	
		if(data['success']==1)
		{
			switch(status)
			{
				case 1:
				
					$('#task_'+task_id).removeClass('not_confirm');
					// Обновляем бар со статусными кнопками для задачи
					refresh_task_status_bar(task_id);
					
					recount_new_notice_active_tasks();
					
				break;
				
				case 2:
					
					$('#task_'+task_id).removeClass('cont_fail');
					
					// Обновляем бар со статусными кнопками для задачи
					refresh_task_status_bar(task_id);
					$('#task_report_block_'+task_id).show();
					$('#task_add_report_form_'+task_id).show();
				break;
				case '-2':
					refresh_task_status_bar(task_id);
					//$('#task_report_block_'+task_id).hide();
					//$('#task_add_report_form_'+task_id).hide();
				break;
				case 3:
					
					$('#task_'+task_id).addClass('cont_completed');
					// Обновляем бар со статусными кнопками для задачи
					refresh_task_status_bar(task_id);
					//$('#task_report_block_'+task_id).show();
				break;
				case '-3':
					
					$('#task_'+task_id).removeClass('cont_completed');
					if(is_boss)
					{
						// Картинка процесса
						$('.task_hide_group_btn_'+task_id).remove('');
						
					}
					else
					{		
												
						refresh_task_status_bar(task_id);
						
					}
			
				break;
				case 4:
					 
					$('#task_'+task_id).removeClass('cont_completed'); 
					$('#task_'+task_id).addClass('cont_fail'); 
					// Обновляем бар со статусными кнопками для задачи
					refresh_task_status_bar(task_id);
					//$('#task_report_block_'+task_id).hide();
					$('#task_add_report_form_'+task_id).show();
					$('#task_report_block_'+task_id).show();
				break;
				case 5:
					
					$('#task_'+task_id).removeClass('cont_completed');
					$('#task_'+task_id).addClass('cont_fail'); 
					
					$('#confirm_finished_'+task_id).remove('');
					
					get_new_task_reports_count(task_id);
					$('.report_for_task_'+task_id).removeClass('not_confirm');
					$('.confirm_btn_for_report_'+task_id).remove();
					$('#task_add_report_form_'+task_id).show();
					$('#task_report_block_'+task_id).show();
					get_new_count_tasks_to_act();
					
				break;
			}
		}
		else
		{
			error();
		}
		
	}, 'json');
}

// Обновляем статусный блок задачи
function refresh_task_status_bar(task_id)
{
	$.post('/ajax/ajaxTasks.php', 
	{   
		mode : 'get_task_status_bar',
		task_id : task_id
	},
	function(data){ 
		
		$('#task_status_btn_block_'+task_id).html(data)
		//hide_task_proc_img(task_id);
		
	});
}

task_report_btn = 0;
function add_task_report(task_id)
{
	var report_text, by_sms;
	
	if(task_report_btn==1)
	{
		return;
	}
	report_text = $('#task_report_'+task_id).val();
	
	by_sms = $('#task_report_by_sms_'+task_id).attr('checked')=='checked' ? 1 : 0; 
	 
	task_report_btn = 1;
	
	$('#task_report_error_'+task_id).html('');
	
	// Прикрепленные файлы
	files_arr = Disk.get_upload_content_files('task_report_'+task_id);
	files_content_type = Disk.get_upload_content_files_content_type('task_report_'+task_id);
	
	loading_btn('add_report_btn_'+task_id);
	
	$.post('/ajax/ajaxTasks.php', 
	{   
		mode : 'add_task_report',
		task_id : task_id,
		report_text : report_text,
		by_sms : by_sms,
		files_arr : $.toJSON(files_arr),
		files_content_type : files_content_type
	},
	function(data){ 
		
		loading_btn('add_report_btn_'+task_id, 1);
		
		task_report_btn = 0;
		
		//hide_task_proc_img(task_id);
		
		$('#task_reports_proc_'+task_id).html('')
		 
		if(data['error'])
		{  
			if(data['error']['report_text']=='1')
			{   
				$('#task_report_'+task_id).focus();
				//$('#task_report_error_'+task_id).html('<div>Отчет не может быть пустым.</div>');
			}
		}
		if(data['success']==1)
		{
			Disk.cancel_file_upload_aueue('task_report_'+task_id, 1);
			
			$('#task_report_'+task_id).val('');
			
			get_task_report_list(task_id);
			
			$('#task_report_by_sms_'+task_id).removeAttr('checked');
			
				//$('#task_report_success_'+task_id).html('<div class="success">Отчет</div>');
		}
		
	}, 'json');
	
}


// Получает список отчета
function get_task_report_list(task_id)
{
	//$('#task_reports_proc_'+task_id).html('<img src="/img/loading5.gif" />')
	 
	$.post('/ajax/ajaxTasks.php', 
	{   
		mode : 'get_task_report_list',
		task_id : task_id
	},
	function(data){ 
		
		if(data)
		{
			//$('#task_reports_proc_'+task_id).html('');
			
			$('#task_reports_list_'+task_id).html(data)
		}
	});
}



// Убирает картинку обработки
function show_task_proc_img(task_id)
{
	$('#task_item_proc_'+task_id).html('<img src="/img/loading5.gif" />')
}

// Убирает картинку обработки
function hide_task_proc_img(task_id)
{
	$('#task_item_proc_'+task_id).html('')
}

// Выставить качество проделанной работы
function edit_task_quality(task_id, quality)
{
	 
	// Картинка процесса
	$('#task_quality_'+task_id).html('<img src="/img/loading5.gif">');
	
	loading('task_quality_'+task_id); 
	$.post('/ajax/ajaxTasks.php', 
	{   
		mode : 'edit_task_quality',
		task_id : task_id,
		quality : quality
	},
	function(data){ 
		
		loading('task_quality_'+task_id, 1); 
		if(data)
		{
			// Картинка процесса
			$('#task_notice_'+task_id).html('');
		}
	});
}

// Подтвердить выполнение задания
function confirm_finished_task(task_id)
{
	// Картинка процесса
	//$('#task_notice_'+task_id).html('<img src="/img/loading5.gif">');
	
	loading_btn('confirm_finished_task_btn_'+task_id);
	 
	$.post('/ajax/ajaxTasks.php', 
	{   
		mode : 'confirm_finished_task',
		task_id : task_id
	},
	function(data){ 
		
		$('#task_notice_'+task_id).html('');
		
		if(data==1)
		{
			$('#confirm_finished_'+task_id).remove('');
			$('#finished_fail_'+task_id).remove('');
			$('#task_qualitiy_'+task_id).show(); 
			$('#task_add_report_form_'+task_id).hide();
			get_new_task_reports_count(task_id);
			$('.report_for_task_'+task_id).removeClass('not_confirm');
			$('.confirm_btn_for_report_'+task_id).remove();
			get_new_count_tasks_to_act();
		}
	});
}

// Не подтверждать выполнение задания
function not_confirm_finished_task(task_id)
{
	// Картинка процесса
	$('#task_notice_'+task_id).html('<img src="/img/loading5.gif">');
	 
	$.post('/ajax/ajaxTasks.php', 
	{   
		mode : 'not_confirm_finished_task',
		task_id : task_id
	},
	function(data){ 
		
		$('#task_notice_'+task_id).html('');
		if(data==1)
		{
			// Картинка процесса
			$('#confirm_finished_'+task_id).remove('');
			
		}
	});
}
// Показывает тело задачи
function show_task_content(task_id)
{
	$('#show_task_cont_bl_'+task_id).addClass('task_item_hide_icon');
	$('#show_task_cont_bl_'+task_id).removeClass('task_item_show_icon');
	$('#show_task_cont_bl_'+task_id).unbind('click');
	$('#show_task_cont_bl_'+task_id).bind('click', function(){ hide_task_content(task_id)});
	
	apacity_task_item(task_id);
	
	$('#task_hidden_content_'+task_id).slideDown(400);
	$('#hide_task_cont_'+task_id).show();
	$('#show_task_cont_'+task_id).hide();
	$('#preview_task_'+task_id).hide();
	$('#task_status_ext_'+task_id).slideDown(200);
}

function apacity_task_item(task_id, edit)
{
	var show_attr, opacity_hide;
	
	opacity_hide = 0.3;
	
	show_attr = $('#task_'+task_id).attr('show');
	
	if(edit==1)
	{  
		 if(!$('.task_item[show=1]').size())
		{
			 $('.task_item[show!=1]').css('opacity', '1');
		}
		else
		{
			$('#task_'+task_id).css('opacity', opacity_hide);
		}
	}
	else if(show_attr==0)
	{
		if($('#task_'+task_id).attr('not_opacity')==1)
		{
			return false;
		}
		$('#task_'+task_id).attr('show', 1);
		$('#task_'+task_id).css('opacity', 1);
		$('.task_item[show!=1]').css('opacity', opacity_hide);
		
	}
	else if(show_attr==1)
	{
		$('#task_'+task_id).attr('show', 0);
		
		if(!$('.task_item[show=1]').size())
		{
			 $('.task_item[show!=1]').css('opacity', '1');
		}
		else
		{
			$('#task_'+task_id).css('opacity', opacity_hide);
		}
		 
	}
	
}
// Скрывает тело задачи
function hide_task_content(task_id)
{ 
	$('#show_task_cont_bl_'+task_id).addClass('task_item_show_icon');
	$('#show_task_cont_bl_'+task_id).removeClass('task_item_hide_icon');
	$('#show_task_cont_bl_'+task_id).unbind('click');
	$('#show_task_cont_bl_'+task_id).bind('click', function(){ show_task_content(task_id)});
	//$('.task_item').css('opacity', '1')
	
	 
	apacity_task_item(task_id);
	
	$('#task_hidden_content_'+task_id).slideUp(200);
	
	setTimeout(function(){
	$('#hide_task_cont_'+task_id).hide();
	$('#show_task_cont_'+task_id).show();
	$('#preview_task_'+task_id).show();
	$('#task_status_ext_'+task_id).hide(100);
	},198)
	
	//c = $('#task_'+task_id).css('top');
	
	//document.location = '#task_'+task_id
}
function show_task_status_bar(task_id)
{
	$('#task_status_ext_'+task_id).toggle()
}

function error()
{
	alert('Произошла неизвестная ошибка');
}

// Показывает тело задачи
function show_add_task_extend_form(task_id)
{
	$('#add_task_ext_pars').fadeIn(200);
	$('#add_form_block_show').hide();
	//$('#add_form_block_hide').show();
}
// Скрывает тело задачи
function hide_add_task_extend_form(task_id)
{
	$('#add_task_ext_pars').hide();
	$('#add_form_block_show').show();
	//$('#add_form_block_hide').hide();

}

// Принять отчет для задачи
function confirm_task_report(report_id, task_id)
{
	loading_btn('confirm_report_btn_'+report_id);
	 
	$.post('/ajax/ajaxTasks.php', 
	{   
		mode : 'confirm_task_report',
		report_id : report_id,
		task_id : task_id
		
	},
	function(data){ 
		
		loading_btn('confirm_report_btn_'+report_id, 1);
		
		if(data==1)
		{
			$('#confirm_report_'+report_id).remove();
			$('#report_'+report_id).removeClass('not_confirm');
			get_new_task_reports_count(task_id);
			get_new_count_tasks_to_act();
			recount_new_notice_active_tasks(); 
		}
	
		 
	});
}

// Обновить кол-во новых отчетов о задачах начальника
function get_new_task_reports_count(task_id)
{
	$.post('/ajax/ajaxTasks.php', 
	{   
		mode : 'get_new_task_reports_count',
		user_id : current_user_id,
		task_id : task_id
		
	},
	function(data){ 
		
		// Правим счетчик в левом меню
		if(parseInt(data['all_report_count'])>=1)
		{
			$('#my_tasks_reports_new_count').html('(+ '+data['all_report_count']+')');
		}
		else
		{
			$('#my_tasks_reports_new_count').html('');
		}
		
		// Правим счетчик в блоке задания
		if(parseInt(data['task_report_count'])>=1)
		{
			$('#new_task_reports_count_'+task_id).html(data['task_report_count']);
		}
		else
		{
			$('#new_task_reports_'+task_id).remove();
		}
	}, 'json');
}

// Обновить кол-во новых отчетов о задачах начальника
function get_new_task_reports_count(task_id)
{
	$.post('/ajax/ajaxTasks.php', 
	{   
		mode : 'get_new_task_reports_count',
		user_id : current_user_id,
		task_id : task_id
		
	},
	function(data){ 
		
		
		// Правим счетчик в блоке задания
		if(parseInt(data['task_report_count'])>=1)
		{
			$('#new_task_reports_count_'+task_id).html(data['task_report_count']);
		}
		else
		{
			$('#new_task_reports_'+task_id).remove();
		}
	}, 'json');
}

// Обновить кол-во новых отчетов о задачах начальника
function get_new_count_tasks_to_act()
{
	$.post('/ajax/ajaxTasks.php', 
	{   
		mode : 'get_new_count_tasks_to_act'
		
	},
	function(data){ 
		
		// Правим счетчик в левом меню
		if(parseInt(data['new_count_tasks_to_act'])>=1)
		{
			$('#new_count_tasks_to_act').html('(+ '+data['new_count_tasks_to_act']+')');
			$('#my_tasks_reports_new_count').html('(+ '+data['new_count_tasks_to_act']+')');
		}
		else
		{
			$('#new_count_tasks_to_act').html('');
			$('#my_tasks_reports_new_count').html('');
		}
	
		
	}, 'json');
}

function recount_new_notice_active_tasks()
{
	$.post('/ajax/ajaxTasks.php', 
	{   
		mode : 'recount_new_notice_active_tasks'
		
	},
	function(data){ 
		
		// Правим счетчик в левом меню
		if(parseInt(data['new_count'])>=1)
		{
			$('#new_tasks_count').html('(+ '+data['new_count']+')');
		}
		else
		{
			$('#new_tasks_count').html('');
		}
	
		
	}, 'json');
}

function change_task_sms_notice(task_id)
{
	var sms_notice;
	
	sms_notice = $('#sms_notice_'+task_id).attr('checked')=='checked' ? 1 : 0;
	
	$('#sms_notice_res_'+task_id).html('<img src="/img/ajax-loader.gif" />');
	$.post('/ajax/ajaxTasks.php', 
	{   
		mode : 'change_task_sms_notice',
		task_id : task_id,
		sms_notice : sms_notice
		
	},
	function(data){ 
		
		if(data==1)
		{
			$('#sms_notice_res_'+task_id).html('');
			//setTimeout(function(){$('#sms_notice_res').html('Да');},1000)
		}
		 
	});
}
