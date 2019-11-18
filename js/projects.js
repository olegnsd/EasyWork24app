function get_project_edit_form(project_id)
{
	$.post('/ajax/ajaxProjects.php', 
	{   
		mode : 'get_project_edit_form',
		project_id : project_id
	},
	function(data){ 
		
		if(data)
		{
			create_popup_block('edit_project_form', 700, data, '', 1);
		}
		
	});
}
function add_project()
{
	var project_name, project_desc;
	
	$('.td_error').html('');
	
	project_name = $('#project_name').val();
	 
	project_desc = tinyMCE.editors['project_desc'].getContent();
	
	$('#error_box').hide();
	$('#project_tasks_tb .date_inp').removeClass('light_error_input');
	
	var project_head = $('#project_head').val();
	
	project_tasks_arr = get_project_form_tasks();
	
	// Прикрепленные файлы
	files_arr = Disk.get_upload_content_files(0);
	files_content_type = Disk.get_upload_content_files_content_type(0);
	 
	loading_btn('add_project_btn');
	 
	$.post('/ajax/ajaxProjects.php', 
	{   
		mode : 'add_project',
		project_name : project_name,
		project_desc : project_desc,
		project_tasks_arr : $.toJSON(project_tasks_arr),
		files_arr : $.toJSON(files_arr),
		files_content_type : files_content_type,
		project_head : project_head
	},
	function(data){ 
		
		loading_btn('add_project_btn', 1);
		 
		var error_text='';
		 
		if($(data['error']).size())
		{
			project_errors(data)
		}
		else if(data['success'])
		{
			get_project_item(data['project_id'], 1);
			$('#success').html('<div class="success">Проект успешно добавлен.</div>');
			document.location = '/projects?id='+data['project_id'];
			
			//clear_block_by_settime('success');
			//clear_add_project_form();
		}
		
		
	}, 'json');
}

function clear_add_project_form()
{
	$('#project_name').val('');
	$('#project_desc').val('');
	$('#projects_tasks').html('');
	add_project_task();
	scheme_pre_init();
}

function get_project_item(project_id, prepend)
{
	$.post('/ajax/ajaxProjects.php', 
	{   
		mode : 'get_project_item',
		project_id : project_id
	},
	function(data){ 
		
		if(data)
		{
			if(prepend)
			{
				$('#no_projects').remove();
				$('#project_list').prepend(data);
			}
			else
			{
				$('#project_'+project_id).replaceWith(data);
			}
		}
		
	});
}

function get_project_form_tasks()
{
	var project_tasks_arr = {}
	var num = 1;
	$('.task').each(function(){
		
		var obj = {};
		var task_id = $(this).attr('task_id') 
		var date_start = $('#task_date_start_'+task_id).val() || $('#task_date_start_'+task_id).text();
		var date_finish =  $('#task_date_finish_'+task_id).val() || $('#task_date_finish_'+task_id).text();;
		var user_id = $('#select_task_user_'+task_id).val() == undefined ? '' : $('#select_task_user_'+task_id).val();
		var task_desc = $('#task_desc_'+task_id).val() == undefined ? '' : $('#task_desc_'+task_id).val();
		var after_task_id = $(this).attr('after_task_id') == undefined ? '' : $(this).attr('after_task_id');
		 
		date_start = date_start == undefined ? '' : date_start;
		date_finish = date_finish == undefined ? '' : date_finish;
		
		obj['date_start'] = date_start;
		obj['date_finish'] = date_finish;
		obj['user_id'] = user_id;
		obj['task_desc'] = task_desc;
		obj['after_task_id'] = after_task_id;
		obj['num'] = num;
		
		var tmp_task_id = $(this).attr('id').split('_');
		obj['task_id'] =  tmp_task_id[1];
		
		project_tasks_arr[tmp_task_id[1]] = obj;
		
		num++;
	})
	
	return project_tasks_arr;
}

function project_errors(data)
{
	var error_text = '';
	
	if(data['error']['project_name'])
	{
		error_text += '<div>Название проекта не может быть пустым.</div>';
		 
	}
	if(data['error']['date'])
	{
		$.each(data['error']['date'], function(i,j){
			
			var this_task_num = $('#task_'+j['task_id']).attr('num');
			
			if(j['date_start']==1)
			{
				$('#task_date_start_'+j['task_id']).addClass('light_error_input');
				error_text += '<div>Дата старта задачи #<b>'+this_task_num+'</b> не указана.</div>';
			}
			if(j['date_finish']==1)
			{
				$('#task_date_finish_'+j['task_id']).addClass('light_error_input');
				error_text += '<div>Дата завершения задачи #<b>'+this_task_num+'</b> не указана.</div>';
			}
			
			if(j['valid']==1)
			{ 
				$('#task_date_start_'+j['task_id']).addClass('light_error_input');
				$('#task_date_finish_'+j['task_id']).addClass('light_error_input');
				error_text += '<div>Дата старта задачи #<b>'+this_task_num+'</b> не может быть меньше даты завершения.</div>';
			}
			 
		})
	}
	
	if(data['error']['pre_date'])
	{
		$.each(data['error']['pre_date'], function(i){
			
			var this_task_num = $('#task_'+i).attr('num');
			var this_task_after_task = $('#task_'+i).attr('after_task_id');
			var pre_task_num = $('#task_'+this_task_after_task).attr('num');
			
			$('#task_date_start_'+i).addClass('light_error_input');
			$('#task_date_finish_'+i).addClass('light_error_input');
			
			error_text += '<div>Дата старта задачи #<b>'+this_task_num+'</b> должна быть больше даты завершения задачи #<b>'+pre_task_num+'</b>.</div>';
			 
		})
	}
	
	if(error_text)
	{
		$('#error_box').html(error_text);
		$('#error_box').show();
	}
}
function save_project_heads(project_id)
{
	var project_name = $('#project_name').val();
	
	var project_desc = tinyMCE.editors['project_desc'].getContent();
	var project_head = $('#project_head').val();
	 
	loading_btn('save_project_btn');
	 
	$.post('/ajax/ajaxProjects.php', 
	{   
		mode : 'save_project_heads',
		project_id : project_id,
		project_desc : project_desc,
		project_name : project_name,
		project_head : project_head
		
	},
	function(data){ 
		
		loading_btn('save_project_btn', 1);
		 
		var error_text='';
		 
		if($(data['error']).size())
		{
			if(data['error']['project_name'])
			{
				$('#project_name').focus();
			}
		}
		else if(data['success']=='1')
		{
			document.location.reload();
		}
		
	}, 'json');
}

save_project_btn = 0;
function save_project()
{
	var project_name, project_desc;
	
	if(save_project_btn)
	{
		return '';
	}
	
	//save_project_btn = 1;
	
	$('.td_error').html('');

	$('#error_box').hide();
	$('#success').html('');
	$('#project_tasks_tb .date_inp').removeClass('light_error_input');
	
	project_tasks_arr = get_project_form_tasks();
	
	// Прикрепленные файлы
	files_arr = Disk.get_upload_content_files(project_id);
	files_content_type = Disk.get_upload_content_files_content_type(project_id);
	files_deleted = Disk.get_content_deleted_files();
	
	 
	 
	loading_btn('add_project_btn');
	 
	$.post('/ajax/ajaxProjects.php', 
	{   
		mode : 'save_project',
		project_id : project_id,
		project_tasks_arr : $.toJSON(project_tasks_arr),
		deleted_project_tasks : $.toJSON(deleted_project_tasks),
		files_arr : $.toJSON(files_arr),
		files_content_type : files_content_type,
		files_deleted : $.toJSON(files_deleted)
	},
	function(data){ 
		
		loading_btn('add_project_btn', 1);
		 
		var error_text='';
		 
		if($(data['error']).size())
		{
			project_errors(data);
			save_project_btn = 0;
		}
		else if(data['success']=='1')
		{
			document.location.reload();
			/*
			$('#success').html('<div class="success">Данные успешно сохранены.</div>');
			clear_block_by_settime('success');
			
			get_project_tasks_list(project_id);
			deleted_project_tasks = {};
			
			project_close(project_id,'open');
			
			show_gr_edited_notice(1);*/
		}
		
	}, 'json');
}

function get_project_tasks_list(project_id)
{
	$.post('/ajax/ajaxProjects.php', 
	{   
		mode : 'get_project_tasks_list',
		project_id : project_id 
	},
	function(data){ 
		
		if(data)
		{
			$('#projects_tasks').html(data);
			projects_tasks_init();
			save_project_btn = 0;
			
			after_tasks_select_init();
		}
		else
		{
			$('#project_scheme').hide();
			$('#project_period_date').hide();
		}
		
	});
}

function add_project_task(after_task_id)
{
	var num;
	var task_id;
	var date_start = '';
	
	num = $('.task').size() + 1;
	task_id = 'rand'+Number(Math.round(Math.random()*1000000));
	
	// Если есть предшествующая задача
	if(after_task_id)
	{
		var after_task_date_finish = $('#task_'+after_task_id+' .date_finish').val();
		
		if(after_task_date_finish)
		{
			date_start = date_plus_days(to_norm_date(after_task_date_finish), 1);
			
			// Дата старта новой задачи
			date_start = time_to_rus_date(date_start);
		}
	}
	else
	{
		after_task_id = '';
	}
	
	$('#add_project_task_btn').hide();
	$('#add_more_project_task_btn').show();
	$('#project_tasks_tb').show();
	 
	$('#projects_tasks').append('<tr class="task" num="'+num+'" after_task_id="'+after_task_id+'" id="task_'+task_id+'" task_id="'+task_id+'" completed="0"><td class="task_num cont_process"></td><td class="task_user_c" ><select id="select_task_user_'+task_id+'" class="user"></select><div class="project_task_desc_bl"><textarea id="task_desc_'+task_id+'" class="task_desc input_text"></textarea></div></td><td class="prt_after_sel_bl"><select id="after_task_'+task_id+'" class="input_text after_task_s"></select></td><td class="prt_dates_bl"><div class="prt_date_t">По плану</div><input type="text" id="task_date_start_'+task_id+'" class="date_inp input_text date_start" task_id="'+task_id+'" value="'+date_start+'"/>&nbsp;&nbsp;&nbsp;<input type="text" id="task_date_finish_'+task_id+'" class="date_inp input_text date_finish" task_id="'+task_id+'"/></td><td class="prt_delete_bl"><a href="javascript:;" class="delete" onclick="delete_project_task(\''+task_id+'\')"></a></td></tr>');
	
	$('#project_tasks_tb').show();
	
	//draw_background_list_item('task', 'zebra1');
	renumber_project_tasks();
	
	$("#task_date_start_"+task_id+'[not_picker!=1]').datepicker({
      showOn: "button",
      buttonImage: "/img/calendar.gif",
      buttonImageOnly: true,
	  changeMonth: true,
      changeYear: true
    });
	$("#task_date_finish_"+task_id+'[not_picker!=1]').datepicker({
      showOn: "button",
      buttonImage: "/img/calendar.gif",
      buttonImageOnly: true,
	  changeMonth: true,
      changeYear: true
    });
	
	$('#select_task_user_'+task_id+'[not_select!=1]').easycomplete(
	{
		str_word_select : 'Выбрать сотрудника',
		url:'/ajax/ajaxGetUsers.php?who=all&by=name&current_user=1&result_name=2',
		width:350,
		trigger : 1
	});	
	
	after_tasks_select_init();
	
	//$('.task .date_inp').bind('change',  function(e) {alert($(this).attr('id'))});
}

deleted_project_tasks = {};
function delete_project_task(task_id)
{
	$('#task_'+task_id).remove();
	
	$('.task[after_task_id="'+task_id+'"]').attr('after_task_id', 0);
	
	deleted_project_tasks[task_id] = task_id;
	renumber_project_tasks();
	
	after_tasks_select_init() 
	
	show_gr_edited_notice();
	
	 
	
	//scheme_pre_init();
}

function show_gr_edited_notice(toclose)
{
	if(toclose==1)
		$('#gr_edited_notice').hide();
	else
		$('#gr_edited_notice').show();
}





function get_today_timedate()
{
	var tdaytmp = new Date();
	 
	var tday = new Date(parseDate(tdaytmp.getFullYear()+'-'+Number(tdaytmp.getMonth()+1)+'-'+tdaytmp.getDate()));
	
	return tday.getTime();

}



function delete_project(project_id)
{
	loading_btn('delete_project_btn_'+project_id);
	
	$.post('/ajax/ajaxProjects.php', 
	{   
		mode : 'delete_project',
		project_id : project_id
	},
	function(data){ 
		
		if(data==1)
		{
			$('#project_'+project_id).replaceWith('<tr class="tb_data_1_row" id="project_'+project_id+'"><td colspan="5"><div class="success">Проект успешно удален | <a href="javascript:;" onclick="restore_project('+project_id+');">Восстановить</a> | <a href="javascript:;" onclick="$(\'#project_'+project_id+'\').remove();">Скрыть</a></div></td></tr>');
		}
		
	});
}

function restore_project(project_id)
{
	$.post('/ajax/ajaxProjects.php', 
	{   
		mode : 'restore_project',
		project_id : project_id
	},
	function(data){ 
		
		if(data==1)
		{
			get_project_item(project_id);
		}
		
	});
}

project_report_btn = 0;
function add_project_report(project_id)
{
	var report_text;
	 
	if(project_report_btn==1)
	{
		return;
	}
	report_text = tinyMCE.editors['report_text'].getContent();
	
	project_report_btn = 1;
	
	loading_btn('add_report_btn_'+project_id);
	
	$.post('/ajax/ajaxProjects.php', 
	{   
		mode : 'add_project_report',
		project_id : project_id,
		report_text : report_text
	},
	function(data){ 
		
		loading_btn('add_report_btn_'+project_id, 1);
		
		project_report_btn = 0;
		
		if(data['error'])
		{  
			if(data['error']['report_text']=='1')
			{   
				$('#report_text').focus();
			}
		}
		if(data['success']==1)
		{
			tinyMCE.editors['report_text'].setContent('');  
			$('#report_text').focus();
			
			get_project_report_item(data['report_id']);
			confirm_project_report(0,1);
		}
		
	}, 'json');
	
}

actual_report_page = 1;
// Выводит больше выговоров
function get_more_project_reports()
{
	var page;
	
	page = actual_report_page + 1;

	$.post('/ajax/ajaxProjects.php', 
	{   
		mode : 'get_more_project_reports',
		project_id : project_id,
		page : page
	},
	function(data){ 
		
		$('#reports_list').append(data);
		
		// Актаульная страница
		actual_report_page++;
		
		if(actual_report_page>=pages_count)
		{
			$('#more_reports_btn').hide();
		}
	});
}


function get_project_report_item(report_id, with_replace)
{
	$.post('/ajax/ajaxProjects.php', 
	{   
		mode : 'get_project_report_item',
		report_id : report_id,
		project_id : project_id
	},
	function(data){ 
		
		if(!with_replace)	 
		{
			$('#reports_list').prepend(data);
			$('#no_reports').remove();
		}
		
	});
}


// Принять отчет о круге обязанностей 
function confirm_project_report(report_id, confirm_all)
{
	loading_btn('confirm_report_btn_'+report_id);
	 
	$.post('/ajax/ajaxProjects.php', 
	{   
		mode : 'confirm_project_report',
		report_id : report_id,
		project_id : project_id,
		confirm_all : confirm_all
		
	},
	function(data){ 
		
		if(data==1)
		{
			if(report_id)
			{
				$('#confirm_report_'+report_id).remove();
				$('#report_'+report_id).removeClass('not_confirm');
			}
			else if(confirm_all)
			{
				$('.confirm_btn_for_report_'+project_id).remove();
				$('.report_item_'+project_id).removeClass('not_confirm');
			}
			
			recount_project_notice();
		}
		 
	});
}

function delete_project_report(report_id, project_id)
{
	$.post('/ajax/ajaxProjects.php', 
	{   
		mode : 'delete_project_report',
		report_id : report_id,
		project_id : project_id
		
	},
	function(data){ 
		
		if(data==1)
		{ 
			$('.cont_hide_'+report_id+'_'+project_id).hide();
			$('#cont_report_result_'+report_id+'_'+project_id).html('<div class="">Комментарий успешно удален | <a href="javascript:;" onclick="restore_project_report(\''+report_id+'\',\'' +project_id+'\')" class="link">Восстановить</a> | <a href="javascript:;" onclick="$(\'#report_'+report_id+'\').remove()" class="link">Скрыть</a> </div>');
			
			recount_project_notice();
		}
		 
	});
}

function restore_project_report(report_id, project_id)
{
	$.post('/ajax/ajaxProjects.php', 
	{   
		mode : 'restore_project_report',
		report_id : report_id,
		project_id : project_id
		
	},
	function(data){ 
		
		if(data==1)
		{
			$('.cont_hide_'+report_id+'_'+project_id).show();
			$('#cont_report_result_'+report_id+'_'+project_id).html('');
			
			recount_project_notice();
		}
		 
	});
}

function recount_project_notice()
{
	$.post('/ajax/ajaxProjects.php', 
	{   
		mode : 'recount_project_notice'
	},
	function(data){ 
		 
		// Правим счетчик в левом меню
		if(parseInt(data['count'])>=1)
		{ 
			$('#new_projects_count').html('(+ '+data['count']+')');
		}
		else
		{
			$('#new_projects_count').html('');
		}
		 // Правим счетчик в левом меню
		if(parseInt(data['new_projects_count'])>=1)
		{
			$('#new_count_in_part_projects').html('(+ '+data['count']+')');
		}
		else
		{
			$('#new_count_in_part_projects').html('');
		}
		 
	}, 'json');
}
function project_task_complete(task_id, completed)
{
	if(completed=='-1')
		loading_btn('not_complete_project_task_btn_'+task_id);
	else
		loading_btn('complete_project_task_btn_'+task_id);
	
	$.post('/ajax/ajaxProjects.php', 
	{   
		mode : 'project_task_complete',
		project_id : project_id,
		task_id : task_id,
		completed : completed
	},
	function(data){ 
		
		var task_completed_class = $('#task_'+task_id).attr('completed_class');
		
		if(data['success']==1 && completed=='-1')
		{
			$('#task_'+task_id+' .task_num').removeClass(task_completed_class);
			$('#task_'+task_id+' .task_num').addClass('cont_process');
			$('#project_task_completed_bl_'+task_id).html('');
			recount_project_notice();
		}
		// Правим счетчик в левом меню
		else if(data['success']==1 && (completed==1 || completed==2))
		{	
			 $('#task_'+task_id+' .task_num').removeClass(task_completed_class);
			 $('#task_'+task_id+' .task_num').addClass('cont_completed');
			 $('#project_task_completed_bl_'+task_id).html(data['complete_btn']);
			 recount_project_notice();
		}
		else if(data['success']==1 && completed==0)
		{
			 $('#task_'+task_id+' .task_num').removeClass('cont_completed');
			 $('#task_'+task_id+' .task_num').addClass(task_completed_class);
			 $('#project_task_completed_bl_'+task_id).html(data['complete_btn']); 
		}
		 
	}, 'json');
}
function project_confirm(project_id)
{
	loading_btn('project_confirm_btn_'+project_id); 
	
	$.post('/ajax/ajaxProjects.php', 
	{   
		mode : 'project_confirm',
		project_id : project_id
	},
	function(data){ 
		
		if(data==1)
		{
			$('#project_'+project_id).removeClass('not_confirm_row');
			$('#project_confirm_btn_'+project_id).remove();
			recount_project_notice();
		}
		 
	}, 'json');
}


projects_list_actual_page = 1;

// Выводит больше выговоров
function get_more_projects()
{
	var page;
	
	page = projects_list_actual_page + 1;

	$.post('/ajax/ajaxProjects.php', 
	{   
		mode : 'get_more_projects',
		page : page,
		is_part : is_part,
		closed : show_closed
	},
	function(data){ 
		
		$('#project_list').append(data);
		
		//draw_background_list_item('project_item', 'zebra1');
		
		// Актаульная страница
		projects_list_actual_page++;
		
		if(projects_list_actual_page>=pages_count)
		{
			$('#more_projects_btn').hide();
		}
	});
}

function project_close(project_id, status)
{
	if($('#project_close_btn').attr('closed')==0 && status=='open')
	{
		return false;	
	}
	
	loading_btn('project_close_btn');
			
	$.post('/ajax/ajaxProjects.php', 
	{   
		mode : 'project_close',
		project_id : project_id,
		status : status
	},
	function(data){ 
		
		loading_btn('project_close_btn', 1);
		
		if(data)
		{
			if(status=='close')
			{
				$('#report_add_form').hide();
			}
			else if(status=='open')
			{
				$('#report_add_form').show();
			}
			$('#project_close_btn').replaceWith(data['btn']);
			$('.project_closed_title').html(data['str_status']);
			
			if(status=='close')
			{
				$('#success_close').html('<div class="success">Проект успешно закрыт.</div>');
			}
			else
			{
				$('#success_close').html('<div class="success">Проект успешно открыт.</div>');
			}
			
			clear_block_by_settime('success_close');
			
		}
		
	},'json');
}

function show_projects_content()
{
	//var show_closed;
	
	//show_closed = $('#show_closed').is(':checked') ? 1 : 0;
	
	$.post('/ajax/ajaxProjects.php', 
	{   
		mode : 'show_projects_content',
		show_closed : show_closed,
		is_part : is_part
	},
	function(data){ 
		
		loading_btn('project_close_btn', 1);
		
		if(data)
		{
			projects_list_actual_page = 1;
			$('#projects_list_content').html(data);
		}
		
	});
}


function projects_tasks_init(static_task_obj)
{	
	// Если объкт задач для диаграммы был сформирован на стороне сервера
	if(!static_task_obj)
	{
		renumber_project_tasks();
		
		$('.task').each(function(){
			
			var task_id = $(this).attr('task_id')
			 
			$("#task_date_start_"+task_id+'[not_picker!=1]').datepicker({
			  showOn: "button",
			  buttonImage: "/img/calendar.gif",
			  buttonImageOnly: true,
			  changeMonth: true,
			  changeYear: true
			});
			$("#task_date_finish_"+task_id+'[not_picker!=1]').datepicker({
			  showOn: "button",
			  buttonImage: "/img/calendar.gif",
			  buttonImageOnly: true,
			  changeMonth: true,
			  changeYear: true
			});
			
			$('#select_task_user_'+task_id+'[not_select!=1]').easycomplete(
			{
				str_word_select : 'Выбрать сотрудника',
				url:'/ajax/ajaxGetUsers.php?who=all&by=name&current_user=1&result_name=2',
				width:350,
				trigger : 1
			});
				
		})
	}

	scheme_pre_init(static_task_obj);	
}

function scheme_pre_init(static_task_obj, e)
{
	if(static_task_obj!=1)
	{
		project_tasks_data_to_obj();
	}
	
	//project_tasks_data_to_obj(); 
	project_scheme_init(e, static_task_obj);
	
}

tasks_obj = {};

function project_tasks_data_to_obj(e)
{
	num = 1;
	tasks_obj = {};
	// Выбираем время старта и завершения
	$('.task').each(function(){
		
		var this_task_obj = {};
		
		var start_val = $(this).children('td').children('.date_start').val() || $(this).children('td').children('.date_start').text();
		var finish_val = $(this).children('td').children('.date_finish').val() || $(this).children('td').children('.date_finish').text();
		var task_id = $(this).attr('task_id');
		var completed = $(this).attr('completed');
		var task_desc = $('#task_desc_'+task_id).text();
		var after_task_id = $(this).attr('after_task_id');
		var date_finished = $(this).attr('date_finished').substr(0,10);
		
	 

		this_task_obj['start'] = to_norm_date(start_val);
		this_task_obj['finish'] = to_norm_date(finish_val);
	 	this_task_obj['task_id'] = task_id;
		this_task_obj['completed'] = completed;
		this_task_obj['task_desc'] = task_desc;
		this_task_obj['after_task_id'] = after_task_id;
		this_task_obj['date_finished'] = date_finished;
		
		tasks_obj[task_id] = this_task_obj
		num++;
		 
	})	
}

function check_pr_task_for_visible_in_user_part_list(task_data)
{
	if(task_data['this_user_task']!=1 || task_data['completed']!=0)
	{
		return false;
	}
	else
	{
		return true;
	}
}

function project_scheme_init(e, static_task_obj)
{  
	if(e)
 	var task_id = $(e.target).attr('task_id');
	 
	// Начальная дата гарфиков
	var min_date_start = null;
	// Финальная дата графиков
	var max_date_finish = null;
	
	// Начальная дата гарфиков по плану
	var min_plan_date_start = null;
	// Финальная дата графиков 
	var max_plan_date_finish = null;
	
	
	var tday_time = get_today_timedate();
	 
	$('#project_tasks_tb .date_inp').removeClass('light_error_input');
	 
	
	// Устанавливаем сначала дату финальных графиков как дату текущего дня
	max_date_finish = new Date().getTime(); 
	
	var tasks_count = 0; 
 
	// Проход по задачам и определяем гарницы графиков
	$.each(tasks_obj, function(i, task_data){
		 
		//var start_val = $(this).children('td').children('.date_start').val() || $(this).children('td').children('.date_start').text();
		//var finish_val = $(this).children('td').children('.date_finish').val() || $(this).children('td').children('.date_finish').text();
		
		// Статически задан объект задач
		if(static_task_obj==1 && !check_pr_task_for_visible_in_user_part_list(task_data))
		{
			return true;
		}
		 
		// Даты старта и завершения задач с учетом сдвигов		
		var dates_arr = get_task_dates(task_data);
		// По факту		 
		var fact_date_start = dates_arr['start'];
		var fact_date_finish = dates_arr['finish'];
			 
		// По плану
		var plan_date_start = task_data['start'];
		var plan_date_finish = task_data['finish'];
		
		 
		if(fact_date_start!='' && fact_date_finish!='')
		{
			var fact_date_start_obj = new Date(parseDate(fact_date_start));
			var fact_date_finish_obj = new Date(parseDate(fact_date_finish));
			 
			if(min_plan_date_start==null || fact_date_start_obj.getTime() < min_date_start)
			{
				min_date_start = fact_date_start_obj.getTime();
			}
			if(max_date_finish==null || fact_date_finish_obj.getTime() > max_date_finish)
			{
				max_date_finish = fact_date_finish_obj.getTime();
			}
			
		}
		
		if(plan_date_start!='' && plan_date_finish!='')
		{
			var plan_date_start_obj = new Date(parseDate(plan_date_start));
			var plan_date_finish_obj = new Date(parseDate(plan_date_finish));
			
			if(min_plan_date_start==null || plan_date_start_obj.getTime() < min_plan_date_start)
			{
				min_plan_date_start = plan_date_start_obj.getTime();
			}
			if(max_plan_date_finish==null || plan_date_finish_obj.getTime() > max_plan_date_finish)
			{
				max_plan_date_finish = plan_date_finish_obj.getTime();
			}
			
		}
		
		tasks_count++;
	})
	 
	if(!Object.keys(tasks_obj).length)
	{
		return '';
	}
	
	 
	if(static_task_obj!=1)
	{
		// Определяет границы временного интервала выполнения задачи
		show_project_period_date(min_date_start, max_date_finish, min_plan_date_start, max_plan_date_finish);
	}
	
	// alert(min_date_start+' '+max_date_finish)
	if(!min_date_start || !max_date_finish)
	{  
		$('#project_scheme').hide();
		$('#project_period_date').hide();
		return false;
	}
	 
	// Разбираем заданную дату
	date_start = new Date(min_date_start);
	 
	date_start_day=date_start.getDay()+1;
	date_start_month=date_start.getMonth();
	date_start_date=date_start.getDate();
	date_start_year=date_start.getFullYear();
	date_start_timeseconds = Math.round(date_start.getTime()/1000);
	 
	
	date_finish = new Date(max_date_finish);
	 
	date_finish_day=date_finish.getDay()+1;
	date_finish_month=date_finish.getMonth();
	date_finish_date=date_finish.getDate();
	date_finish_year=date_finish.getFullYear();
	 
	date_finish_timeseconds = Math.round(date_finish.getTime()/1000);
	date_finish_timeseconds += 60*60*24*14;
	
	var stop_date = 0;
	var date_proc = date_start_timeseconds - 60*60*24*4;
	var dates_days = '';
	var dates_months = {};
	var current_month_in_row;
	var month_colspan = 1;
	var days_count = 0;
	var days_rows = '';
	
	dates_days = '<td>№</td>';
	
	while(!stop_date)
	{
		var tmp_date = new Date(date_proc*1000);
		var date = tmp_date.getDate();
		var day = tmp_date.getDay();
		var month = tmp_date.getMonth();
		var year = tmp_date.getFullYear();
		var month_rus_name;
		var dayweek = get_dayweek_by_day(day);
		var dayweek_class;
		
		dayweek_class='';
		if(day==0 || day==6)
		{
			dayweek_class = 'weekend';
		}
		
		tday_class = '';
		if(tday_time==date_proc*1000)
		{
			tday_class = 'actual_date';
		}
		
		// Дни в заголовке
		dates_days += '<td class="'+tday_class+'"><div class='+dayweek_class+'>'+dayweek+'</div>'+date+'</td>';
		
		month_rus_name = get_month_rus_name_by_month(month,1);
		
		// Месяц в заголовке
		dates_months[year+'_'+month] = '<th colspan="'+month_colspan+'">'+month_rus_name+', <span class="year">'+year+'</span></th>';
		
		// Подсчет, сколько дней будет отображено в месяце на схеме
		if(current_month_in_row!=month)
		{
			month_colspan = 1;
			current_month_in_row = month;
		}
		month_colspan++;	
		
		if(date_proc>date_finish_timeseconds || days_count > 3000)
		{
			stop_date = 1;
		}
		
		date_proc += 60 * 60 * 24;
		days_count ++;
	}
	 
 
	// Формируем ячейки для графика
	var date_proc = date_start_timeseconds - 60*60*24*4;
	 
	
	days_rows_result = '';
	stop_date = 0;
	for(i=1;i<tasks_count+1;i++)
	{
		var tmptask_id = $('.task_num[num='+i+']').parent('.task').attr('task_id');
		var user_id = $('#select_task_user_'+tmptask_id).val(); 
		var tdback_class = '';
		 
		tdback_class = '';
		if(user_id==current_user_id)
		{ 
			tdback_class = 'curuser';
		}
		 
		while(!stop_date)
		{
			tday_class = '';
			if(tday_time==date_proc*1000)
			{
				tday_class = 'actual_date';
			}
			 
			days_rows += '<td class="'+date_proc*1000+' '+tday_class+'  "><div class="line_cont"></div></td>';
			
			if(date_proc>date_finish_timeseconds)
			{ 
				stop_date = 1;
			}
			date_proc += 60 * 60 * 24;
		}
		
		days_rows_result += '<tr id="user_row_'+i+'" class="'+tdback_class+'"><td style="text-align:center;vertical-align:middle">'+i+'.</td>'+days_rows+'</tr>';
	}
	
	var dates_months_result = '';
	$.each(dates_months, function(i,j){
		dates_months_result += j
	})
	dates_months_result = '<th></th>'+dates_months_result;
	 
	$('#project_scheme_month_th_tr').html(dates_months_result);
	$('#project_scheme_days_th_tr').html(dates_days);
	$('#project_scheme_days_rows').html(days_rows_result);
	$('#project_scheme').show();
	
	render_project_scheme_line(task_id, static_task_obj);
	//alert(parseDate(dateStr))
}

function light_project_task(date_start, date_finish, this_true_finish_date, task_data)
{
	 
	var actual_mkdate = to_mktime(date('Y-m-d'));
	 
	// alert(actual_mkdate+' '+date_finish+' '+task_data['finish']+' '+this_true_finish_date)
	if(task_data['completed']>0)
	{
		$('#task_'+task_data['task_id']+' .task_num').addClass('cont_completed');
		$('#task_'+task_data['task_id']).attr('completed_class', 'cont_completed');
	}
	else if(to_mktime(date_finish) > to_mktime(task_data['finish']) && actual_mkdate >= to_mktime(this_true_finish_date))
	{
		$('#task_'+task_data['task_id']+' .task_num').addClass('cont_fail');
		$('#task_'+task_data['task_id']).attr('completed_class', 'cont_fail');
	}
	else
	{
		$('#task_'+task_data['task_id']+' .task_num').addClass('cont_process');
		$('#task_'+task_data['task_id']).attr('completed_class', 'cont_process');
	}
}

function render_project_scheme_line(task_id, static_task_obj)
{
	var num_row = 1;
	var tday_time = get_today_timedate();
	
	$('#project_scheme .line').remove();
	 
	//$('#projects_tasks .task').each(function(){
	$.each(tasks_obj, function(i, task_data){

		if(static_task_obj==1 && !check_pr_task_for_visible_in_user_part_list(task_data))
		{
			return true;
		}
		
		// Даты старта и завершения задач		
		var dates_arr = get_task_dates(task_data);
		
		var this_date_start = dates_arr['start'];
		var this_date_finish = dates_arr['finish'];
		var this_true_finish_date = dates_arr['true_finish_date'];
		 
		var this_date_finished = task_data['date_finished'];
		var this_task_id = task_data['task_id'];
		var after_task_id = task_data['after_task_id'];
		
		light_project_task(this_date_start, this_date_finish, this_true_finish_date, task_data);
		
		$('#fact_date_start_'+this_task_id).html(time_to_rus_date(this_date_start));
		$('#fact_date_finish_'+this_task_id).html(time_to_rus_date(this_date_finish));
		
		// Кол-во дней, на которое было просрочено задание 
		var task_dates_later = dates_arr['task_dates_later'];
		
	 	//alert(task_dates_later)
		 
		var task_completed =  task_data['completed']; 
		var task_desc = task_data['task_desc']; 
		var to_project_link = task_data['to_project_link']; 
		
		this_date_start_obj = new Date(parseDate(this_date_start));
		this_date_finish_obj = new Date(parseDate(this_date_finish));
	 
		if(this_date_start!='' && this_date_finish!='' && this_date_start_obj.getTime() <= this_date_finish_obj.getTime())
		{
			var line_width, line_class='';
			
			of_start = $('#user_row_'+num_row+' .'+this_date_start_obj.getTime()+' .line_cont').offset(); 
			of_finish = $('#user_row_'+num_row+' .'+this_date_finish_obj.getTime()).next().children('.line_cont').offset();
			line_width = Number(of_finish.left - of_start.left);
			  
			line_desc  = '';
			
			/*if(tday_time > this_date_finish_obj.getTime() && task_completed==0)
			{
				line_class = 'line_fail';
				line_desc = task_desc;
			}*/
			
			if(task_completed==1 || task_completed==2)
			{
				line_class = 'line_finished';
				line_desc = task_desc;
				 
			}
			else if(task_completed==0)
			{ 
				line_class = 'line_process';
				line_desc = task_desc;
			} 
			 
			//line_class = 'line_process';
			var desc_attr = 0;
			var style_cursor;
			
			if(task_desc!='')
			{
				desc_attr = 1;
				style_cursor = "cursor:pointer;";
			}
			
			line_desc = line_desc.substr(0,250);
			
			var line_href;
			
			if(to_project_link==1)
			{
				line_href = 'href="/projects?referer=part&id='+task_data['project_id']+'"';
			}
			else
			{
				line_href = 'href="javascript:;"';
			}
			
			var later_line_days= '';
			
			if(to_mktime(this_date_finish) > to_mktime(this_true_finish_date))
			{
				this_true_finish_date_obj = new Date(to_mktime(this_true_finish_date));
				
				of_this_true_finish_date = $('#user_row_'+num_row+' .'+this_true_finish_date_obj.getTime()).next().children('.line_cont').offset(); 
				line_width1 = Number(of_finish.left - of_this_true_finish_date.left);
			
				later_w  = line_width1
				later_line_days = '<div class="line_later" style="width:'+later_w+'px"></div>';
			}
			 
			
			 
			// Анимируем линию
			if(task_id == this_task_id)
			{  
				line = '<a '+line_href+' class="line '+line_class+'" style="width:0px; '+style_cursor+'" is_desc="'+desc_attr+'" after_task_id="'+after_task_id+'" task_id="'+this_task_id+'"><div id="pr_line_desc_'+this_task_id+'" class="d_none prline_desc_c">'+line_desc+'</div>'+later_line_days+'</a>';
				$('#user_row_'+num_row+' .'+this_date_start_obj.getTime()+' .line_cont').html(line);
				$('#user_row_'+num_row+' .'+this_date_start_obj.getTime()+' .line_cont .line').animate({width:line_width}, 700);
			}
			else
			{ 
				line = '<a '+line_href+' class="line '+line_class+'" style="width:'+line_width+'px; '+style_cursor+'" is_desc="'+desc_attr+'"after_task_id="'+after_task_id+'" task_id="'+this_task_id+'"><div id="pr_line_desc_'+this_task_id+'" class="d_none prline_desc_c">'+line_desc+'</div>'+later_line_days+'</a>';
				 
				$('#user_row_'+num_row+' .'+this_date_start_obj.getTime()+' .line_cont').html(line);
				 
			}
		}
		num_row++;
	}) 
	 
	$('.line[is_desc=1]').poshytip({
		content: function(){ return $(this).children('.prline_desc_c').html()},
		className: 'tip-green',
		offsetX: -5 ,
		liveEvents: true ,
		showTimeout : 50
	});
	
	 
		draw_line_dependence();
	 
}

function draw_line_dependence()
{
	$('.line').each(function(){
		
		var after_task_id = $(this).attr('after_task_id');
		var task_id = $(this).attr('task_id');
		
		if(after_task_id<=0)
		{
			return true;
		}
		
		var this_task_line_offset = $(this).offset();
		
		if($('.line[task_id="'+after_task_id+'"]').attr('task_id')!=after_task_id)
		{
			return true;
		}
		
		// Находим график предшеств. задачи 
		var pre_task_line_obj = $('.line[task_id="'+after_task_id+'"]');
		var pre_task_line_offset = pre_task_line_obj.offset();
		var pre_task_line_obj_width = $(pre_task_line_obj).width();
		
		var line_dep_height__height = 0;
		var line_dep_height__bottom = 0;
		var line_dep_right__width = 0;
		// 
		if(Number(this_task_line_offset.top - pre_task_line_offset.top) > 0)
		{
			 line_dep_height__height = Number(this_task_line_offset.top - pre_task_line_offset.top) - 10;
			 line_dep_height__bottom = line_dep_height__height;
			 line_dep_right__width = Number(this_task_line_offset.left - pre_task_line_offset.left - pre_task_line_obj_width) + 2;
			 
			 var line_dep = '<div class="dep_line_to_down"><div class="dep_line" style="height:'+line_dep_height__height+'px; bottom:-'+line_dep_height__bottom+'px"><div class="dep_line" style="bottom:0px; width:'+line_dep_right__width+'px"></div></div></div>';
		}
		else
		{
			 line_dep_height__height = Number(pre_task_line_offset.top - this_task_line_offset.top) - 10;
			 line_dep_height__bottom = line_dep_height__height;
			 line_dep_right__width = Number(this_task_line_offset.left - pre_task_line_offset.left - pre_task_line_obj_width) + 2;
			
			 var line_dep = '<div class="dep_line_to_up"><div class="dep_line" style="height:'+line_dep_height__height+'px; top:-'+line_dep_height__bottom+'px"><div class="dep_line" style="top:0px; width:'+line_dep_right__width+'px"></div></div></div>';
		}
		 
		var line_dep_str = '<div class="dep_line_str"></div>';
		 
		$(pre_task_line_obj).append(line_dep);
		
		$(this).append(line_dep_str);
	})
}


// Возвращает даты старта и конца задач
function get_task_dates(this_task_data)
{
	var result = {};
	var start_date = this_task_data['start'];
	var finish_date = this_task_data['finish'];
	var task_true_finish_date;
	
	var task_offset_days = 0;
	
	result['start'] = start_date;
	result['finish'] = finish_date;
	
	// Предыдущие задачи для данной задачи
	var prepends_tasks;

	 
	// Предыдущие задачи перед задачей
	var prepends_tasks = get_prepend_tasks_arr(this_task_data);
 
	// Если у задачи есть предшествующие задачи
	if(Object.keys(prepends_tasks).length)
	{ 
		// Кол-во дней, на которые смещается задача исходят из предыдущих
		task_offset_days = get_task_offset_days(prepends_tasks, this_task_data);
		
		// Обозначаем дату cтарта и завершения с учетом смещения
		start_date = date_plus_days(this_task_data['start'], task_offset_days);
		finish_date = date_plus_days(this_task_data['finish'], task_offset_days);		
	}
	
	
	// Дата предполагаемого завершения задачи с учетом всех сдвигов(если есть)
	// Дата завершения является предполагаемой с учотом сдвига и не отображает дату завершения фактическую
	task_true_finish_date = finish_date;
	
	
	var actual_mkdate = to_mktime(date('Y-m-d'));

	
	// Если задание еще не выполнено и дата выполнения уже просрочена
	// Делаем дату завершения ФАКТИЧЕСКОЕ, т.е. актуального дня 
	if(actual_mkdate > to_mktime(finish_date) && !isset_date(this_task_data['date_finished']))
	{
		finish_date = date('Y-m-d');
	} 
	// Если дата ФАКТИЧЕСКОГО завершения больше дату ПЛАНИРУЕМОГО завершения
	else if(isset_date(this_task_data['date_finished']) && to_mktime(this_task_data['date_finished']) > to_mktime(finish_date))
	{
		finish_date = this_task_data['date_finished'];
	}
	
	result['start'] = start_date;
	result['finish'] = finish_date;
	result['true_finish_date'] = task_true_finish_date;
	
	 
	return result;
}
// Получает кол-во дней, на которое задача смещается, если нужно из-за предыдущих задач
function get_task_offset_days(prepends_tasks, this_task_data)
{
	var task_count_later_days = 0;
	
	var prepend_task_later_days = 0;
	
	var actual_mkdate;
	
	var task_later_days = 0;
	
	actual_mkdate = to_mktime(date('Y-m-d'));
	
	var later_days = 0;
	
	var pre_iter_task_date_start;
	var pre_iter_task_date_finish;
	var pre_iter_task_date_finished;
		 
 	//alert(prepends_tasks)
	// Проходим по всем предыдущим задачам
	$.each(prepends_tasks, function(i,j){
		 
		var task_id = tasks_obj[j]['task_id']; 
		var task_date_start = tasks_obj[j]['start'];
		var task_date_finish = tasks_obj[j]['finish'];
		var task_date_finished = tasks_obj[j]['date_finished'];
		
		 
		if(pre_iter_task_date_finish)
		{
			
			diff = difference_in_days_between_dates(task_date_start, pre_iter_task_date_finish);
			 
			if(diff>0)
			{ 
				task_later_days = 0;
			}
			if(diff==0)
			{ 
				task_later_days = 1;
			}
			if(diff<0)
			{
				task_later_days = Math.abs(diff) + 1;
			}
			 
			 
			task_count_later_days =  task_later_days;
			
			// Если есть дни смещения от предыдущих задач
			task_date_start = date_plus_days(task_date_start, task_count_later_days);
			task_date_finish = date_plus_days(task_date_finish, task_count_later_days);
			
		}
		
		/* Выявляем, на сколько дней просрочено задание*/		
		// Если задача еще не выполнена и уже просрочена
		if(actual_mkdate > to_mktime(task_date_finish) && !isset_date(task_date_finished))
		{    
			task_date_finish = date('Y-m-d');
		}
		// Если дата ФАКТИЧЕСКОГО завершения больше даты ПЛАНИРУЕМОГО завершения
		else if(isset_date(task_date_finished) && to_mktime(task_date_finished) > to_mktime(task_date_finish))
		{ 
			task_date_finish = task_date_finished;
		}
		 
		pre_iter_task_date_start = task_date_start;
		pre_iter_task_date_finish = task_date_finish;
		pre_iter_task_date_finished = task_date_finished;
	 
	})
	
	diff = difference_in_days_between_dates(this_task_data['start'], pre_iter_task_date_finish);
	 
	if(diff>0)
	{ 
		task_later_days = 0;
	}
	if(diff==0)
	{ 
		task_later_days = 1;
	}
	if(diff<0)
	{
		task_later_days = Math.abs(diff) + 1;
	}
	 
	 
	task_count_later_days =  task_later_days
	
	return task_count_later_days;
}
 


// Получает массив предыдущих заданий
function get_prepend_tasks_arr(this_task_data)
{
	var st = 0;
	var n = 0;
	
	// Предыдущие задачи для данной задачи
	var prepends_tasks = [];
	
	if(this_task_data['after_task_id']!=0 && this_task_data['task_id'])
	{
		var search_task_id = this_task_data['after_task_id']; 
		 
		// Находим все задачи за которыми слудует текущая задача
		while(!st && n < 100)
		{  
			// Проходим по всем задачам
			$.each(tasks_obj, function(i, task_data1){
			 
			 	// Находим задачу, которая является предыдущей для задачи
			 	if(task_data1['task_id']==search_task_id)
				{
					// Добавляем в массив предыдущих задач
					prepends_tasks.push(search_task_id);
					
					// Если задача найдена и у нее есть так же предыдущая задача
					if(task_data1['after_task_id']!=0)
					{  
						search_task_id = task_data1['after_task_id'];
					}
					else
					{
						search_task_id = 0;
					}
					return false;
				}
			})
				
			if(search_task_id==0) st = 1;
			n++;
		}
	}
	 
	prepends_tasks = prepends_tasks.reverse();
	 
	return prepends_tasks;
}


function renumber_project_tasks()
{
	var num = 1;
	$('.task').each(function(){
		$(this).children('.task_num').html(num+'.');
		$(this).children('.task_num').attr('num', num);
		num++;
	})
	
	if($('.task').size()==0)
	{
		$('#project_tasks_tb').hide();
		$('#add_project_task_btn').show();
		$('#add_more_project_task_btn').hide();
		$('#project_tasks_tb').hide();
	}
	else
	{
		$('#add_project_task_btn').hide();
	}
}

function show_project_line_desc(task_id)
{
	 
	if($('#pr_line_desc_'+task_id).is(':visible'))
	{
		$('#pr_line_desc_'+task_id).hide();
	}
	else
	{ 
		$('#pr_line_desc_'+task_id).fadeIn(100);
	}
}
function show_project_period_date(min_date_start, max_date_finish, min_plan_date_start, max_plan_date_finish)
{ 
	if($.trim(min_date_start)=='' || $.trim(max_date_finish)=='')
	{
		$('#project_period_date').hide();	
	}
	else
	{ 
		$('#project_date_start').html(time_to_rus_date(min_date_start));
		$('#project_date_finish').html(time_to_rus_date(max_date_finish));
		
		$('#project_date_start_plan').html(time_to_rus_date(min_plan_date_start));
		$('#project_date_finish_plan').html(time_to_rus_date(max_plan_date_finish));
		
		//alert(date('Y-m-d', min_date_start));
		
		var days = difference_in_days_between_dates(date('Y-m-d', max_date_finish), date('Y-m-d', max_plan_date_finish));
		
		var days_str =  numToword(days, new Array('день', 'дня', 'дней'));
		
		//difference_in_days_between_dates();
		
		$('#behind_schedule').html(days+' '+days_str);
		
		$('#project_period_date').show();	
	}
	 
}

function after_tasks_select_init()
{
	 
	var tasks_nums = {};
	var num  = 1;
	var options_list='<option value="0">-</option>';
	
	$('.task').each(function(){
	 
		var task_id = $(this).attr('task_id');
		tasks_nums[task_id] = num;		
		options_list += '<option value="'+task_id+'">'+num+'</option>';
		num++;
	})
	
	// Проход по задачам
	$('.task').each(function(){
		 
		var task_id = $(this).attr('task_id');
		var after_task_id = $(this).attr('after_task_id');
		var is_participation = $(this).attr('is_participation');
		
		$('#after_task_'+task_id).html(options_list);
		
		if(after_task_id!='')
		{
			if(is_participation == 1)
			{
				if(tasks_nums[after_task_id])
				{
					var task_num = tasks_nums[after_task_id]
				}
				else
				{
					var task_num =  "-";
				}
				//var task_num = after_task_id > 0 ? tasks_nums[after_task_id] : "-";
				//alert(task_num)
				$('#after_task_'+task_id).replaceWith("<div>"+task_num+"</div>");
			}
			else
			{
				$('#after_task_'+task_id).val(after_task_id);
			} 
		}
		
		$('#after_task_'+task_id+' option[value="'+task_id+'"]').remove();
		 
	})
	
	 
}

function pr_task_after_task_change(e)
{
	var task_id, after_task_id;
	
	task_id = $(this).parent().parent().attr('task_id');
	
	after_task_id = $(this).val();
	
	$('#task_'+task_id).attr('after_task_id', after_task_id);
	
	var date_finish = $('#task_date_finish_'+after_task_id).val() || $('#task_date_finish_'+after_task_id).text();
	
	if(isset_date(date_finish))
	{
		date_finish = date_plus_days(to_norm_date(date_finish), 1);
		 
		$('#task_date_start_'+task_id).val(time_to_rus_date(date_finish));
		 
		
	}
}

function show_project_tasks_comments(task_id, toclose)
{
	// если окно с комментарием уже открыто
	if($('#task_comments_bl_'+task_id).attr('id')=='task_comments_bl_'+task_id || toclose)
	{
		tinyMCE.editors['task_report_text_'+task_id].destroy()
		$('#task_comments_bl_'+task_id).remove();
		return;
	}
	
	var task_elem = $('#task_'+task_id);
	
	var elem_offset = $(task_elem).offset();
	
	$('.project_task_comments_bl').html('');
	
	$.post('/ajax/ajaxProjects.php', 
	{   
		mode : 'get_project_task_comments_block',
		task_id : task_id
	},
	function(data){ 
		
		if(data)
		{
			$('#comments_'+task_id).html(data)
		}
		
	});
	 
}

project_task_report_btn = 0;
function add_project_task_report(task_id)
{
	var report_text;
	 
	if(project_task_report_btn==1)
	{
		return;
	}
	
 	report_text = tinyMCE.editors['task_report_text_'+task_id].getContent();
	
	project_task_report_btn = 1;
	
	loading_btn('add_task_report_btn_'+task_id);
	
	$.post('/ajax/ajaxProjects.php', 
	{   
		mode : 'add_project_task_report',
		task_id : task_id,
		report_text : report_text
	},
	function(data){ 
		
		loading_btn('add_task_report_btn_'+task_id, 1);
		
		project_task_report_btn = 0;
		
		if(data['error'])
		{  
			if(data['error']['report_text']=='1')
			{   
				$('#task_report_text_'+task_id).focus();
			}
		}
		if(data['success']==1)
		{
			tinyMCE.editors['task_report_text_'+task_id].setContent('');  
			$('#task_report_text_'+task_id).focus();
			
			 get_project_task_report_item(data['report_id'], task_id);
			//confirm_project_report(0,1);
		}
		
	}, 'json');
	
}

function get_project_task_report_item(report_id, task_id, with_replace)
{
	$.post('/ajax/ajaxProjects.php', 
	{   
		mode : 'get_project_task_report_item',
		report_id : report_id,
		task_id : task_id
	},
	function(data){ 
		
		if(!with_replace)	 
		{
			$('#task_reports_list_'+task_id).prepend(data);
			$('#task_reports_list_'+task_id+' .no_contents').remove();
		}
		
	});
}

function delete_project_task_report(report_id, task_id)
{
	$.post('/ajax/ajaxProjects.php', 
	{   
		mode : 'delete_project_task_report',
		report_id : report_id,
		task_id : task_id
		
	},
	function(data){ 
		
		if(data==1)
		{ 
			$('.task_report_cont_hide_'+report_id+'_'+task_id).hide();
			$('#cont_task_report_result_'+report_id+'_'+task_id).html('<div class="">Комментарий успешно удален | <a href="javascript:;" onclick="restore_project_task_report(\''+report_id+'\',\'' +task_id+'\')" class="link">Восстановить</a> | <a href="javascript:;" onclick="$(\'#task_report_'+report_id+'\').remove()" class="link">Скрыть</a> </div>');
			
			recount_project_notice();
		}
		 
	});
}

function restore_project_task_report(report_id, task_id)
{
	$.post('/ajax/ajaxProjects.php', 
	{   
		mode : 'restore_project_task_report',
		report_id : report_id,
		task_id : task_id
		
	},
	function(data){ 
		
		if(data==1)
		{
			$('.task_report_cont_hide_'+report_id+'_'+task_id).show();
			$('#cont_task_report_result_'+report_id+'_'+task_id).html('');
			
			//recount_project_notice();
		}
		 
	});
}

// Принять отчет о круге обязанностей 
function confirm_project_task_report(report_id, task_id, confirm_all)
{
	loading_btn('confirm_report_task_btn_'+report_id);
	 
	$.post('/ajax/ajaxProjects.php', 
	{   
		mode : 'confirm_project_task_report',
		report_id : report_id,
		task_id : task_id,
		confirm_all : confirm_all
		
	},
	function(data){ 
		
		if(data==1)
		{
			if(report_id)
			{
				$('#confirm_report_'+report_id).remove();
				$('#task_report_'+report_id).removeClass('not_confirm');
				get_project_task_new_reports_count(task_id);
			}
			else if(confirm_all)
			{
				$('.confirm_btn_for_task_report_'+project_id).remove();
				$('.task_report_item_'+task_id).removeClass('not_confirm');
			}
			
			recount_project_notice();
		}
		 
	});
}

function get_project_task_new_reports_count(task_id)
{
	$.post('/ajax/ajaxProjects.php', 
	{   
		mode : 'get_project_task_new_reports_count',
		task_id : task_id
		
	},
	function(data){ 
		
		if(parseInt(data)>=1)
		{ 
			$('#new_task_report_count_'+task_id).html('(+ '+data+')');
		}
		else
		{
			$('#new_task_report_count_'+task_id).html('');
		}
		 
	});
}