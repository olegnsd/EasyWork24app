// Добавляет постоянную работу
is_add_work_btn = 0;
function add_user_work(to_user_id)
{
	var work_text, periodicity, work_sms_notice_to_boss;
	
	if(is_add_work_btn==1)
	{
		return
	}
	
	$('#add_work_error').html('');
	 
	$('#add_work_success').html('');
	
	periodicity = $('#periodicity').val();
	
	work_text = $('#work_text').val();
	
	work_sms_notice_to_boss = $('#work_sms_notice_to_boss').attr('checked')=='checked' ? 1 : 0;
	
	is_add_work_btn = 1;
	
	loading_btn('add_user_work_btn')
	
	$.post('/ajax/ajaxWork.php', 
	{   
		mode : 'add_user_work',
		to_user_id : to_user_id,
		periodicity : periodicity,
		work_text : work_text,
		work_sms_notice_to_boss : work_sms_notice_to_boss
	},
	function(data){ 
		
		loading_btn('add_user_work_btn', 1)
		
		is_add_work_btn = 0;
	 
		if(data['error'])
		{
			 if(data['error']['work_text']==1)
			 {
			 	$('#work_text').focus();
			 }
		}
		if(data['success']==1)
		{
			$('#work_text').val('');
			 
			$('#add_work_success').html('<div class="success success_marg">Обязанность успешно изменена</div>');
			
			setTimeout(function(){$('#add_work_success').html('')},2000)
			
			get_user_actual_work(data['work_id']);
			
		}
		
	}, 'json');
}

// Получает актуальную постоянную работу и отчет к ней
function get_user_actual_work(work_id)
{
	$.post('/ajax/ajaxWork.php', 
	{   
		mode : 'get_user_work',
		work_id : work_id
	},
	function(data){ 
		
		$('#work_now_text').html(data['actual_work']);
		
		$('#work_report_block').remove(); 
		
	}, 'json');
}

// Получает актуальную постоянную работу и отчет к ней
function get_actual_work_reports(work_id)
{
	$.post('/ajax/ajaxWork.php', 
	{   
		mode : 'get_actual_work_reports',
		work_id : work_id
	},
	function(data){ 
		
		$('#work_now_report').html(data['work_report']);
		
	}, 'json');
}

// Отмека "принял новую постоянную работу"
function actual_work_noticed(work_id)
{
	loading_btn('confirm_work_btn_'+work_id);
	 
	$.post('/ajax/ajaxWork.php', 
	{   
		mode : 'actual_work_noticed',
		work_id : work_id
	},
	function(data){ 
		 
		if(data==1)
		{
			hide_notice_for_new_work(work_id)
		}
		
	});
}

// Убирает уведомления о новой работе
function hide_notice_for_new_work(work_id)
{
	$('#work_'+work_id).removeClass('not_confirm');
			
	$('#confirm_work_btn_'+work_id).remove();
			
	$('#new_actual_work_count').html('');
}

// Добавить отчет для работы

is_add_work_report_btn = 0;
function add_report_for_work(work_id)
{
	var report_date;
	
	$('#add_work_report_error').html('');
	
	$('#add_work_report_success').html('');
	
	if(is_add_work_report_btn==1)
	{
		return;
	}
	
	report_text = $('#work_report_text').val();
	
	is_add_work_report_btn = 1;
	
	loading_btn('add_work_report_btn')
	
	// Прикрепленные файлы
	files_arr = Disk.get_upload_content_files(work_id);
	files_content_type = Disk.get_upload_content_files_content_type(work_id);
	
	$.post('/ajax/ajaxWork.php', 
	{   
		mode : 'add_report_for_work',
		work_id : work_id,
		report_text : report_text,
		files_arr : $.toJSON(files_arr),
		files_content_type : files_content_type
	},
	function(data){ 
		
		loading_btn('add_work_report_btn', 1);
		
		is_add_work_report_btn = 0;
		 
		$('#add_work_report_proc').html('');
		  
		if(data['error'])
		{ 
			if(data['error']['report_text']==1)
			{  
				$('#work_report_text').focus();
			}
			if(data['error']['work_empty']==1)
			{  
				$('#add_work_report_error').html('Вам еще не назначена постоянная работа.');
			}
		}
		if(data['success']==1)
		{
			$('#work_report_text').val('');
			 
			//$('#add_work_report_success').html('<div class="success">Отчет добавлен | <a href="javascript:;" onclick="$(this).parent().remove()">Закрыть</a></div>');
			
			hide_notice_for_new_work(work_id);
			
			get_work_report_item(data['report_id'])
			//get_actual_work_reports(work_id);
			
			Disk.cancel_file_upload_aueue(work_id, 1);
			
			//clear_uploaded_files();
		}
		
	}, 'json');
}

function get_work_report_item(report_id, with_replace)
{
	$.post('/ajax/ajaxWork.php', 
	{   
		mode : 'get_work_report_item',
		report_id : report_id
	},
	function(data){ 
		
		if(!with_replace)	 
		{
			$('#work_report').prepend(data)
		}
		
	});
}

history_is_show = 0;
// Получает историю постоянной работы
function show_history_work_list(user_id)
{
	if(history_is_show==1)
	{
		$('#history_work_list').html('');
		$('#Pagination').html('');
		current_page = 0;
		history_is_show = 0;
		return;
	}
	$('#show_history_btn').hide();
	
	$('#history_work_list').html('<img src="/img/loading5.gif">');
	
	$('#history_title').show();
	 
	$.post('/ajax/ajaxWork.php', 
	{   
		mode : 'get_history_work_count',
		user_id : user_id
	},
	function(data){ 
		
		history_count = data['history_count'];
		 
		$("#Pagination").pagination(history_count, {
                current_page: current_page, // select number(page) 5 (4+1)
                num_display_entries: 5, // count of paging numbers
                next_text: "Вперед", // next button text
                next_show_always: true,
                prev_text: "Назад",
                prev_show_always: true,
                
                callback: get_history_work_list,                        
                items_per_page:history_per_page,
				link_to : '#history_work_list'
            });
		  
		history_is_show = 1;	 
		
	}, 'json');
}

// Получает список историй кругов обязанностей
function get_history_work_list(current_page)
{
	var p;
	 
	p = current_page
	
	$.post('/ajax/ajaxWork.php', 
	{   
		mode : 'get_history_work_list',
		page : p,
		user_id : user_id
		
	},
	function(data){ 
		
		$('#history_work_list').html(data);
		
	});
}

// Принять отчет о круге обязанностей 
function confirm_work_report(report_id, work_id)
{
	loading_btn('confirm_report_'+report_id);
	 
	$.post('/ajax/ajaxWork.php', 
	{   
		mode : 'confirm_work_report',
		report_id : report_id,
		work_id : work_id
		
	},
	function(data){ 
		
		if(data==1)
		{
			$('#confirm_report_'+report_id).remove();
			$('#report_'+report_id).removeClass('not_confirm');
		}
		 
	});
}


uploaded_start = {};
files_arr = {};
function work_file_upload_init(num)
{
	 new AjaxUpload($('#file_upload_'+num), {  
		  		    action: '/ajax/ajaxUploadContentFile.php?mode=2',  
		  		    name: 'uploadfile',  
		  		    onSubmit: function(file, ext){
						
						if (!(ext && /^(jpg|png|jpeg|gif)$/i.test(ext))){  
							// check for valid file extension  
						//	alert('Ошибка. Допускаются только файлы форматов .jpg .gif и .png.')
						//	return false;  
						} 
						
						$('#file_upload_proc_'+num).html('<img src="/img/ajax-loader.gif">');
						
						$('#file_uploaded_name_'+num).html('');
						 
					 	// Блок кнопки, пока не загрузится изображение
						if(uploaded_start[num])
						{
							return false
						}
						
						uploaded_start[num] = 1;
						
						
		  		    },     
		  		    onComplete: function(file, response_data){  
						
						// Разбираем на массив ответ
						response_data_arr = response_data.split('|');
						
						// Стутас загрузки
						response = response_data_arr[0];
						
						// Делаем возможным снова загрузки фотографии
					    uploaded_start[num] = 0;
							
						$('#file_upload_proc_'+num).html('');
						
						if(response=='ok')
						{
							files_arr[response_data_arr[2]] = response_data_arr[1];
							 
							$('#upload_files').append('<div class="uploaded_file_name"><b>'+response_data_arr[1]+' </b> <a href="javascript:;" class="link" onclick="delete_preupload_file(\''+response_data_arr[2]+'\', this)">Удалить</a></div>');
							
							$('#upload_file_str').html('Загрузить еще файл')
								
						}
						else if(response=='2')
						{
							alert('Максимальный размер загружаемого файла 100 Мб');
						}
					}
			}); 
}

function delete_preupload_file(file_code_name, elem)
{ 
	files_arr[file_code_name] = '';
	$(elem).parent().remove();
}

function clear_uploaded_files()
{
	files_arr = {};
	$('#upload_files').html('')
}

// Кнопка, добавляет ссылки для загрузки фотографии
function more_upload_work_files()
{
	var lenght = parseInt($('.upload_file_bl').length) + 1
	
	var num = Math.round(Math.random() * 10000);
	 
	
	$('#upload_files').append('<div  class="upload_file_bl" default="1"><a href="javascript:;" class="link" id="file_upload_'+num+'">Загрузить файл</a> <div class="uploaded_file_name"><span id="file_uploaded_name_'+num+'"></span> <span id="file_uploaded_delete_'+num+'"></span></div><div id="file_upload_proc_'+num+'"></div></div>');
	
	work_file_upload_init(num);
	
}

function change_new_work_reports_notice(work_id)
{
	var sms_notice;
	
	sms_notice = $('#sms_notice').attr('checked')=='checked' ? 1 : 0;
	
	$('#sms_notice_res').html('<img src="/img/ajax-loader.gif" />');
	
	$.post('/ajax/ajaxWork.php', 
	{   
		mode : 'change_new_work_reports_notice',
		work_id : work_id,
		sms_notice : sms_notice
		
	},
	function(data){ 
		
		if(data==1)
		{
			$('#sms_notice_res').html('')
		}
		 
	});
}


actual_page = 1;
// Выводит больше выговоров
function get_more_work_reports()
{
	var page;
	
	page = actual_page + 1;

	$.post('/ajax/ajaxWork.php', 
	{   
		mode : 'get_more_work_reports',
		work_id : work_id,
		page : page
	},
	function(data){ 
		
		$('#work_report').append(data);
		
		// Актаульная страница
		actual_page++;
		
		if(actual_page>=pages_count)
		{
			$('#more_work_reports_btn').hide();
		}
	});
}