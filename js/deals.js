add_deal_btn = 0;
import_deal_btn = 0;

// Добавление нового клиента
function add_new_deal()
{
	var deal_name, deal_type, deal_client, deal_status, deal_report, deal_private_edit, deal_private_show, deal_other_info,deal_contact_person,
	deal_email, deal_address, deal_phone, deal_price, group_id;
	 
	if(add_deal_btn==1)
	{
		return;
	}
	
	$('.td_error').html('');
	
	$('.input_text').removeClass('light_error_input')
	
	deal_name = $('#deal_name').val();
	deal_type = $('input[name=deal_type]:checked').val();
	deal_client = $('#deal_client').val();
	deal_status = $('#deal_status').val();
	deal_report = $('#deal_report').val();
	deal_other_info = $('#deal_other_info').val();

	deal_private_edit = $('#deal_private_edit').attr('checked')=='checked' ? 1 : 0;
	//deal_private_show = $('#deal_private_show').attr('checked')=='checked' ? 1 : 0;

	deal_price = $('#deal_price').val();
	deal_contact_person = $('#deal_contact_person').val();
	deal_email = $('#deal_email').val();
	deal_address = $('#deal_address').val();
	deal_phone = $('#deal_phone').val();
	
	group_id = $("#deal_group").val();
	
	loading_btn('add_deal_btn');
	
	add_deal_btn = 1;
	
	// Прикрепленные файлы
	files_arr = Disk.get_upload_content_files(0);
	files_content_type = Disk.get_upload_content_files_content_type(0);
	
	$.post('/ajax/ajaxDeals.php', 
	{   
		mode : 'add_new_deal',
		deal_name : deal_name,
		deal_type : deal_type,
		deal_client : deal_client,
		deal_status : deal_status,
		deal_report : deal_report,
		deal_private_edit : deal_private_edit,
		//deal_private_show : deal_private_show,
		deal_other_info : deal_other_info,
		deal_contact_person : deal_contact_person,
		deal_price : deal_price,
		deal_email : deal_email,
		deal_address : deal_address,
		deal_phone : deal_phone,
		group_id : group_id,
		files_arr : $.toJSON(files_arr),
		files_content_type : files_content_type
	},
	function(data){ 
		
		add_deal_btn = 0;
		
		loading_btn('add_deal_btn', 1);
		
		if(data['error'])
		{
			$.each(data['error'], function(i, j){
				error_text = '';
						 
				if(i=='deal_client')
				{ 
					error_text = 'Не выбран клиент';
					
					
					$("#client_name_error").html(error_text);
					//light_error_input('deal_client');
				}
				
				//$("#"+i).next().html(error_text);
			
			})
		}
		else if(data['success']==1)
		{ 
			document.location = '/deals/edit/'+data['deal_id']
		}
		
	}, 'json');
}

// импорт нового клиента(сделок)
function import_new_deal()
{
	var deal_name, deal_type, deal_client, deal_status, deal_report, deal_private_edit, deal_private_show, deal_other_info,deal_contact_person,
	deal_email, deal_address, deal_phone, deal_price, group_id;
	 
	if(import_deal_btn==1)
	{
		return;
	}
	
	$('.td_error').html('');
	
	$('.input_text').removeClass('light_error_input')
	
	deal_name = $('#import_deal_name').val();
	deal_type = $('#import_deal_type input[name=deal_type]:checked').val();
	deal_client = $('#import_deal_client').val();
	deal_status = $('#import_deal_status').val();
	deal_report = $('#import_deal_report').val();
	deal_other_info = $('#import_deal_other_info').val();

	deal_private_edit = $('#import_deal_private_edit').attr('checked')=='checked' ? 1 : 0;
	//deal_private_show = $('#deal_private_show').attr('checked')=='checked' ? 1 : 0;

	deal_price = $('#import_deal_price').val();
	deal_contact_person = $('#import_deal_contact_person').val();
	deal_email = $('#import_deal_email').val();
	deal_address = $('#import_deal_address').val();
	deal_phone = $('#import_deal_phone').val();
	
	group_id = $("#import_deal_group").val();
	
	loading_btn('import_deal_btn');
	
	import_deal_btn = 1;
	
	// Прикрепленные файлы
//	files_arr = Disk.get_upload_content_files(0);
//	files_content_type = Disk.get_upload_content_files_content_type(0);
	
	var files = []; // переменная. будет содержать данные файлов
	// заполняем переменную данными файлов, при изменении значения file поля
	$('#file_form_import').each(function(){
		files.push(this.files[0]);//obj.age = obj.age.toString();
//                    alert( JSON.stringify(this.files[0].toString()) );
	});
	
	// создадим данные файлов в подходящем для отправки формате
	var data = new FormData();

	data.append( 'import_data', files[0] );
	data.append( 'deal_name', deal_name);
	data.append( 'deal_type', deal_type);
	data.append( 'deal_client', deal_client);
	data.append( 'deal_status', deal_status);
	data.append( 'deal_report', deal_report);
	data.append( 'deal_private_edit', deal_private_edit);
	data.append( 'group_id', group_id);
	data.append( 'mode', 'import_new_deal');
	
	// AJAX запрос
	$.ajax({
		url         : '/ajax/ajaxDeals.php',
		type        : 'POST',
		data        : data,
		cache       : false,
//                        dataType    : 'json',
		// отключаем обработку передаваемых данных, пусть передаются как есть
		processData : false,
		// отключаем установку заголовка типа запроса. Так jQuery скажет серверу что это строковой запрос
		contentType : false
	}).done(function(data1){ 
		data1 = jQuery.parseJSON(data1);
		import_deal_btn = 0;
		loading_btn('import_deal_btn', 1);
		if(data1['error'])
		{
			$.each(data1['error'], function(i, j){
				error_text = '';		 
				if(i=='deal_client')
				{ 
					error_text = 'Не выбран клиент';
					$("#client_name_error").html(error_text);
					//light_error_input('deal_client');
				}
				//$("#"+i).next().html(error_text);
			})
		}
		else if(data1['success']==1)
		{ 
			document.location = '/deals/'+data1['user_id'];
		}
		
	});
}

// Добавление нового клиента
function save_deal(deal_id)
{
	var deal_name, deal_type, deal_client, deal_status, deal_report, deal_private_edit, deal_private_show, deal_price, deal_contact_person, deal_other_info, deal_email, deal_address, deal_phone, group_id;
	
	$('.td_error').html('');
	
	$('.input_text').removeClass('light_error_input')
	
	deal_name = $('#deal_name').val();
	deal_type = $('input[name=deal_type]:checked').val();
	deal_client = $('#deal_client').val();
	deal_status = $('#deal_status').val();
	deal_report = $('#deal_report').val();
	deal_price = $('#deal_price').val();
	deal_private_edit = $('#deal_private_edit').attr('checked')=='checked' ? 1 : 0;
	//deal_private_show = $('#deal_private_show').attr('checked')=='checked' ? 1 : 0;
	deal_contact_person = $('#deal_contact_person').val();
	deal_other_info = $('#deal_other_info').val();
	
	deal_email = $('#deal_email').val();
	deal_address = $('#deal_address').val();
	deal_phone = $('#deal_phone').val();
	
	group_id = $("#deal_group").val();
	
	// Прикрепленные файлы
	files_arr = Disk.get_upload_content_files(deal_id);
	files_content_type = Disk.get_upload_content_files_content_type(deal_id);
	files_deleted = Disk.get_content_deleted_files();
	
	loading_btn('save_deal_btn');
	
	$.post('/ajax/ajaxDeals.php', 
	{   
		mode : 'save_deal',
		deal_id : deal_id,
		deal_name : deal_name,
		deal_type : deal_type,
		deal_client : deal_client,
		deal_status : deal_status,
		deal_report : deal_report,
		deal_price : deal_price,
		deal_private_edit : deal_private_edit,
		//deal_private_show : deal_private_show,
		deal_contact_person : deal_contact_person,
		deal_other_info : deal_other_info,
		deal_email : deal_email,
		deal_address : deal_address,
		deal_phone : deal_phone,
		group_id : group_id,
		files_arr : $.toJSON(files_arr),
		files_content_type : files_content_type,
		files_deleted : $.toJSON(files_deleted)
	
	},
	function(data){ 
		
		add_client_btn = 0;
		
		loading_btn('save_deal_btn', 1);
		
		if(data['error'])
		{
			$.each(data['error'], function(i, j){
				error_text = '';
						 
				if(i=='deal_client')
				{ 
					error_text = 'Не выбран клиент';
					//light_error_input('deal_client');
					$('#client_name_error').html(error_text)
				}

				//$("#"+i).parent().next().html(error_text);
			
			})
		}
		if(data['success'])
		{
			document.location.reload();
			
			if(parseInt(deal_status) > 0)
			{  
				$('#deal_status').children('option[value=0]').remove();
			}
			$('#edit_deal_success').html('<div class="success success_marg">Изменения успешно сохранены</div>');
			//$('#deal_report').val('');
			//$('#deal_status').val(0);
			setTimeout(function(){$('#edit_deal_success').html('')},2000);
			
			$('#deal_status_history_block').html(data['deal_history_status_block']);
			
			$('#deal_id_block_'+deal_id).removeClass();
			$('#deal_id_block_'+deal_id).addClass(data['deal_status_class']);
		}
		
		
	}, 'json');
}


// Удаляет контакт
function delete_deal(deal_id)
{
	$('#deal_proc_'+deal_id).html('<img src="/img/loading5.gif">');
	
	$.post('/ajax/ajaxDeals.php', 
	{   
		mode : 'delete_deal',
		deal_id : deal_id
	},
	function(data){ 
		
		//$('#deal_proc_'+deal_id).html('');
		if(data==1)
		{
			//$('#deal_content_'+deal_id).hide();
			
			$('#deal_'+deal_id).hide();
			$('#deal_'+deal_id).after("<tr id='deleted_deal_"+deal_id+"'><td colspan='12'><div class='success'>Сделка успешно удалена | <a href='javascript:;' onclick='restore_deal("+deal_id+")'>Восстановить</a> | <a href='javascript:;' onclick='$(\"#deleted_deal_"+deal_id+"\").remove(); $(\"#deal_"+deal_id+"\").remove();draw_background_list_item(\"deals_item\");'>Закрыть</a></div></td></tr>");
			
			//$('#deal_result_'+deal_id).html("<div class='success'>Сделка успешно удалена | <a href='javascript:;' onclick='restore_deal("+deal_id+")'>Восстановить</a> | <a href='javascript:;' onclick='$(\"#deal_"+deal_id+"\").remove(); draw_background_list_item(\"deals_item\");'>Закрыть</a></div>");
		}
	});
}

// Восстановить контакт
function restore_deal(deal_id)
{
	$('#deal_proc_'+deal_id).html('<img src="/img/loading5.gif">');
	
	$.post('/ajax/ajaxDeals.php', 
	{   
		mode : 'restore_deal',
		deal_id : deal_id
	},
	function(data){ 
		
		$('#deal_proc_'+deal_id).html('');
		if(data==1)
		{
			$('#deal_'+deal_id).show();
			$('#deleted_deal_'+deal_id).remove();
			
			//$('#deal_content_'+deal_id).show();
			//$('#deal_result_'+deal_id).html('');
		}
	});
}


deals_actual_page = 1;
default_search_text = '';
// Выводит больше контактов
function get_more_deals()
{
	var page, search_word;
	
	page = deals_actual_page + 1;

	search_word = $('#search_text').val();
	
	if(search_word == default_search_text)
	{
		search_word = '';
	}
	 
	$.post('/ajax/ajaxDeals.php', 
	{   
		mode : 'get_more_deals',
		user_id : user_id,
		page : page,
		search_word : search_word,
		deal_list_type : deal_list_type
		
	},
	function(data){ 
		
		$('#deals_list').append(data);
		
		draw_background_list_item("deals_item");
		// Актаульная страница
		deals_actual_page++;
		
		draw_background_list_item('deals_item');
		if(deals_actual_page>=pages_count)
		{
			$('#more_deals_btn').hide();
		}
	});
}

// Поиск контактов
function deals_search()
{
	var search_word;
	 
	search_word = $('#search_text').val();
	
	if(search_word == default_search_text)
	{
		search_word = '';
	}
	 
	$.post('/ajax/ajaxDeals.php', 
	{   
		mode : 'deals_search',
		search_word : search_word,
		user_id : user_id,
		deal_list_type : deal_list_type
	},
	function(data){ 
		
		$('#deals_list').children('tbody').html('');
		$('#deals_list').children('tbody').html(data['deals_list']);
		
		draw_background_list_item("deals_item");
		
		if(data['pages_count'] > 1)
		{
			$('#more_deals_btn').show();
		}
		else
		{
			$('#more_deals_btn').hide();
		}
		pages_count = data['pages_count'];
		
		deals_actual_page = 1;
		
	}, 'json');
}

// Проверка на существующего постоянного клиента
function check_select_client_in_add_form()
{
	var tmp_value; 
	tmp_value = $('#deal_client').val();
	  
	if(tmp_value[0].substr(0,3)!='-s-')
	{
		$('#client_notice').show()
	}
	else
	{
		$('#client_notice').hide()
	}
}

function clear_client_notice()
{
	$('#client_notice').hide()
}

function show_deal_by_date(clear)
{
	var pars, date_from, date_to, group_id, deal_status;
	
	date_from = $('#date_from').val();
	date_to = $('#date_to').val();
	group_id = $("#search_deals_by_group_id").val();
	deal_status = $("#search_deals_by_status").val();
	deal_call = $("#search_deals_by_call").val();
	
	pars = 'date_from='+date_from+"&date_to="+date_to+"&group_id="+group_id+"&status="+deal_status+"&call="+deal_call;
	
	if(clear)
	{
		pars = '';
	}
	switch(deal_list_type)
	{
		case 'my':
			document.location = '/deals/'+user_id+"?"+pars;
		break;
		case 'wks':
			document.location = '/deals?list=wks&'+pars;
		break;
		case 'all':
			document.location = '/deals?list=all&'+pars;
		break;
		case 'av':
			document.location = '/deals?list=av&'+pars;
		break;
	}
	 
	
	
}

function show_sales_funnel(user_id, show)
{
	if(show)
	{
		$('#sales_funnel_'+user_id).show();
	}
	else
	{
		$('#sales_funnel_'+user_id).hide();
	}
}


// Показать блок настройки доступа к файлам и папкам
function show_access_deal_block(deal_id)
{
	close_deal_access_blocks();
	
	$('#owner_block_'+deal_id).show();
}

function give_access_to_deal(deal_id, user_id)
{
	$('#access_proc_'+deal_id).html('<img src="/img/ajax-loader.gif">');
	
	$.post('/ajax/ajaxDeals.php', 
	{   
		mode : 'give_access_to_deal',
		deal_id : deal_id,
		user_id : user_id
	},
	function(data){ 
		
		$('#access_proc_'+deal_id).html('');
		
		if(data==1)
		{
			$('#user_'+deal_id+'_'+user_id).removeClass('access_active')
		}
		if(data==2)
		{
			$('#user_'+deal_id+'_'+user_id).addClass('access_active')
		}
		
		$('.no_contents').remove(); 
		 
	});
}

function close_deal_access_blocks()
{
	$('.file_hide_block').hide();
}


user_deals_actual_page = {};
// Выводит больше сделок
function get_user_more_deals(user_id)
{
	var page;
	
	page = deals_actual_page + 1;
	
	
	if(!user_deals_actual_page[user_id])
	{
		user_deals_actual_page[user_id] = 1; 
	}
	
	page =  Number(user_deals_actual_page[user_id]) + 1;
	 
	 
	$.post('/ajax/ajaxDeals.php', 
	{   
		mode : 'get_user_more_deals',
		user_id : user_id,
		page : page
		
	},
	function(data){ 
		
		if(data['deals_list'])
		{
			$('#user_deals_list_'+user_id).append(data['deals_list']);
			
			user_deals_actual_page[user_id]++;
			
			// Убираем кнопку - показать больше
			if(data['hide_more_pages_btn'])
			{
				$('#user_more_deals_tr_'+user_id).remove();
			}
		}
		
		
	}, 'json');
}

function show_deal_reminder_date()
{  
	hide_deal_reminder_error();
	if($('#deal_reminder_block').is(':visible'))
	{
		$('#deal_reminder_block').hide();
	}
	else
	{
		$('#deal_reminder_block').show();
	}
}
function hide_deal_reminder_error()
{
	$('#deal_reminder_error_box').hide();
}

function set_deal_reminder_date(deal_id)
{
	var date;
	
	reminder_date = $('#deal_reminder_date').val();
	reminder_date_hour = $('#deal_reminder_date_hour').val();
	reminder_date_minute = $('#deal_reminder_date_minute').val();
		
	hide_deal_reminder_error();
	
	loading_btn('deal_reminder_date_btn');
	
	$.post('/ajax/ajaxDeals.php', 
	{   
		mode : 'set_deal_notice_date',
		deal_id : deal_id,
		reminder_date : reminder_date	,
		reminder_date_hour : reminder_date_hour,
		reminder_date_minute : reminder_date_minute
	},
	function(data){ 
		
		loading_btn('deal_reminder_date_btn', 1);
		
		if(data['error'])
		{
			$.each(data['error'], function(i, j){
				
				error_text = '';
						 
				if(i=='reminder_date' && j==1)
				{ 
					error_text += 'Некорректно указана дата';
				}
			})
			
			if(error_text)
			{ 
				$('#deal_reminder_error_box').html(error_text);
				$('#deal_reminder_error_box').show();
			}
		}
		else if(data['success']==1)
		{ 
			get_deal_reminder_block(deal_id);
			$('#deal_reminder_date').val('');
			show_deal_reminder_date();
		}
	}, 'json');
}
function delete_deal_reminder(reminder_id)
{
	$.post('/ajax/ajaxDeals.php', 
	{   
		mode : 'delete_deal_reminder',
		reminder_id : reminder_id
	},
	function(data){ 
		
		if(data==1)
		{
			$('#deal_reminder_wrap').html('');
		}
		
	});
}
function get_deal_reminder_block(deal_id)
{
	$.post('/ajax/ajaxDeals.php', 
	{   
		mode : 'get_deal_reminder_block',
		deal_id : deal_id
	},
	function(data){ 
		
		if(data)
		{
			$('#deal_reminder_wrap').html(data);
		}
		
	});
}
function add_user_to_access_deal(deal_id)
{
	var id = Math.round( Math.random()*1000);
	var elem_id = 'noto_user_access_'+id;
	$('#access_users_list_'+deal_id).append('<select id="'+elem_id+'" class="access_user_item"></select><br>');
	
	$('#'+elem_id).easycomplete(
	{
		str_word_select : 'Выбрать пользователя',
		width:396,
		url:'/ajax/ajaxGetUsers.php?by=name&who=all_tree&result_name=2'
	});	 
}
function save_deal_user_access(deal_id)
{
	var access_users = {};
	
	loading_btn('save_access_btn_'+deal_id);
	
	$('#access_users_list_'+deal_id+' .access_user_item').each(function(){
			  
		var user_id = $(this).val();
		
		access_users[user_id] = user_id;
	})
		
	$.post('/ajax/ajaxDeals.php', 
	{   
		mode : 'save_deal_user_access',
		deal_id : deal_id,
		access_users : $.toJSON(access_users)
	},
	function(data){ 
		
		loading_btn('save_access_btn_'+deal_id, 1);
		
		if(data==1)
		{
			$('#access_result_'+deal_id).html('<div class="success stand_margin">Успешно сохранено</div>');
			clear_block_by_settime('access_result_'+deal_id);
		}
		 
	});
}

function get_deal_access_block(deal_id)
{
	$.post('/ajax/ajaxDeals.php', 
	{   
		mode : 'get_deal_access_block',
		deal_id : deal_id
	},
	function(data){ 
		
		if(data)
		{
			 
			$('.item_access_block').html('');
			$('#access_block_'+deal_id).html(data)
		}
	});
}
