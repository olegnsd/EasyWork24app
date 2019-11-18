add_finance_btn = 0;
// Создание расчетного счета
function add_finance()
{
	var summa, currency, finance_name;
	
	if(add_finance_btn)
	{
		return;
	}
	
	$('.td_error').html('');
	
	$('.input_text').removeClass('light_error_input')
	
	finance_summa = $('#finance_summa').val();
	
	finance_currency = $('#finance_currency').val();
	
	finance_name =  $('#finance_name').val();
	
	add_finance_btn = 1;
	
	loading_btn('add_finance_btn')
	 
	$.post('/ajax/ajaxFinances.php', 
	{   
		mode : 'add_finance',
		finance_summa : finance_summa,
		finance_currency : finance_currency,
		finance_name : finance_name
	},
	function(data){ 
		
		loading_btn('add_finance_btn', 1)
		add_finance_btn = 0;
		
		if(data['error'])
		{
			$('#finance_proc').html('');
			
			$.each(data['error'], function(i, j){
				
				error_text = '';
						 
				if(i=='finance_summa' && j==1)
				{ 
					error_text = 'Сумма на счету должна быть положительной';
					light_error_input('finance_summa');
				}
				if(i=='finance_name' && j==1)
				{ 
					error_text = 'Не указано название счета';
					light_error_input('finance_name');
				}
								
				$("#"+i).next().html(error_text);
			
			})
		}
		else if(data['success']==1)
		{
			$('#finance_summa').val('0.00');
			
			$('#finance_currency').val('1');
			
			 $('#finance_name').val('');
			 
			 
			 
			get_finance_item(data['finance_id']);
			
		}
		
	},'json');
}

// Получает форму для редактирования клиента
function get_finance_item(finance_id)
{
	$('#finance_proc').html('<img src="/img/loading5.gif">');
	  
	$.post('/ajax/ajaxFinances.php', 
	{   
		mode : 'get_finance_item',
		finance_id : finance_id
	},
	function(data){ 
		
		if(data)
		{	
			$('#finance_proc').html('');
			$('#finances_list').prepend(data);
			
			$('.finance_no').remove();
		}
	});
}


add_finance_operation_btn = 0;

function add_finance_operation()
{
	var operation_type, operation_client, operation_summa, operation_date, operation_time, operation_comment
	
	if(add_finance_operation_btn)
	{
		return
	}
	
	$('.td_error').html('');
	
	$('.input_text').removeClass('light_error_input')
	
	operation_type = $('#operation_type').val();
	
	operation_client = $('#operation_client').val();
	
	operation_summa = $('#operation_summa').val();
	
	operation_date = $('#operation_date').val();
	
	operation_time = $('#operation_time').val();
	
	operation_comment = $('#operation_comment').val();
	
	add_finance_operation_btn = 1;
	
	// Прикрепленные файлы
	files_arr = Disk.get_upload_content_files(finance_id);
	files_content_type = Disk.get_upload_content_files_content_type(finance_id);
	
	loading_btn('add_finance_operation_btn')
	
	$.post('/ajax/ajaxFinances.php', 
	{   
		mode : 'add_finance_operation',
		finance_id : finance_id,
		operation_type : operation_type,
		operation_client : operation_client,
		operation_summa : operation_summa,
		operation_date : operation_date,
		operation_comment : operation_comment,
		operation_time : operation_time,
		files_arr : $.toJSON(files_arr),
		files_content_type : files_content_type
	},
	function(data){
		
		loading_btn('add_finance_operation_btn', 1)
		 
		add_finance_operation_btn = 0;
		
		if(data['error'])
		{
			$('#add_operation_proc').html('');
			$.each(data['error'], function(i, j){
				
				error_text = '';
						 
				if(i=='operation_client' && j==1)
				{ 
					error_text = 'Не выбран клиент';
					//light_error_input('operation_client');
					$("#operation_client_error").html(error_text);
				}
				
				if(i=='operation_summa' && j==1)
				{ 
					error_text = 'Не введена сумма';
					light_error_input('operation_summa');
					$("#"+i).next().html(error_text);
				}
				if(i=='operation_summa' && j==2)
				{ 
					error_text = 'Некорректно введена сумма';
					light_error_input('operation_summa');
					$("#"+i).next().html(error_text);
				}
				if(i=='operation_date' && j==1)
				{ 
					error_text = 'Некорректно введена дата операции';
					light_error_input('operation_date');
					light_error_input('operation_time');
					$("#"+i).next().html(error_text);
				}
				if(i=='operation_type')
				{ 
					error_text = 'Не указан тип операции';
					$("#"+i).next().html(error_text);
				}
				
				 
			
			})
		}
		else if(data['success']==1)
		{
			document.location.reload();
			 
			/*$('#operation_summa').val('0.00');
			
			$('#operation_currency').val('1');
			
			$('#operation_comment').val('');
			
			$('#operation_type').val(0);
			
		 
			
			$('#finance_summa').html(data['result_sum'])
			
			set_operation_time_default();
			
			$('#operations_no').remove();
			get_finance_operation_item(data['operation_id']);
			finance_clear_all_active_select_clients();*/
			
		}
		
	}, 'json');
}

// Добавляет статус к фин. операции
function add_finance_operation_status(operation_id)
{
	var status_id, status_comment
	
	status_id = $('#operation_status_'+operation_id).val();
	
	status_comment = $('#operation_comment_'+operation_id).val();
	
	loading_btn('add_finance_operation_status_'+operation_id);
	
	$.post('/ajax/ajaxFinances.php', 
	{   
		mode : 'add_finance_operation_status',
		finance_id : finance_id,
		status_id : status_id,
		status_comment : status_comment,
		operation_id : operation_id
		
	},
	function(data){ 
		
		loading_btn('add_finance_operation_status_'+operation_id, 1);
		
		if(data['success'])
		{
			$('#operation_status_'+operation_id).val('');
	
			$('#operation_comment_'+operation_id).val('');
			
			get_finance_operation_status_item(operation_id);
		}
		 
		
	}, 'json');
}

function get_finance_operation_status_item(operation_id)
{
	$.post('/ajax/ajaxFinances.php', 
	{   
		mode : 'get_finance_operation_status_item',
		finance_id : finance_id,
		operation_id : operation_id
		
	},
	function(data){ 
		
		if(data['item'])
		{
			$('#operation_statuses_list_'+operation_id).prepend(data['item']);
			
			$('#finance_operation_type_'+operation_id).removeClass();
			
			$('#finance_operation_type_'+operation_id).addClass(data['status_back_color'])
		}
		
	}, 'json');
}

function finance_clear_all_active_select_clients()
{
	// Очистка поиска 
	$('#ea_selected__operation_client').trigger('click')
	//$('#operation_client_block').html('')
	//$('#operation_client_block').html('<select id="operation_client"></select>');
	//finance_clients_init()
}
// Очистка поля с выбором клиентов
function finance_clients_init()
{
	// Инициализация селекта клиентов
	 $("#operation_clien1t").fcbkcomplete({
                    json_url: "/ajax/ajaxGetClientsForFinances.php",
                    addontab: false,                   
                    maxitems: 1,
                    input_min_size: 0,
                    height: 10,
					width: 415,
                    cache: false,
                    newel: true,
					complete_text : 'Введите клиента',
					onselect : finance_check_select_client_in_add_form,
					onremove : finance_clear_client_notice

    });
	
	$('#operation_client').easycomplete(
			{
				str_word_select : 'Найти клиента',
				width:415,
				url:'/ajax/ajaxGetClientsForFinances.php'
			});
					    
}

function get_finance_operation_item(operation_id, replace_item)
{
	
	$.post('/ajax/ajaxFinances.php', 
	{   
		mode : 'get_finance_operation_item',
		finance_id : finance_id,
		operation_id : operation_id
	},
	function(data){ 
		
		
		if(data)
		{
			if(replace_item)
			{
				$('#finance_operation_'+operation_id).replaceWith(data);
			}
			else
			{
				$('#finance_operations_list').prepend(data);
			}
		}
		
		
	});
}

function set_operation_time_default()
{
	var time, minutes, hours;
	
	time_obj = new Date();
	
	hours = time_obj.getHours().toString().length==1 ? '0'+time_obj.getHours() : time_obj.getHours();
	
	minutes = time_obj.getMinutes().toString().length==1 ? '0'+time_obj.getMinutes() : time_obj.getMinutes();
 
	time = hours+':'+minutes;
	
	$('#operation_time').val(time)
}

function finance_clear_client_notice()
{
	$('#client_notice').hide()
}

// Проверка на существующего постоянного клиента
function finance_check_select_client_in_add_form()
{
	var tmp_value; 
	tmp_value = $('#operation_client').val();
	  
	if(tmp_value[0].substr(0,3)!='-s-')
	{
		$('#client_notice').show()
	}
	else
	{
		$('#client_notice').hide()
	}
}


finances_actual_page = 1;

function get_more_finances()
{
	var page;
	
	page = finances_actual_page + 1;
	
	
	$.post('/ajax/ajaxFinances.php', 
	{   
		mode : 'get_more_finances',
		user_id : user_id,
		page : page,
		finance_av : finance_av
	},
	function(data){ 
		
		$('#finances_list').append(data);
		
		
		// Актаульная страница
		finances_actual_page++;
		
		if(finances_actual_page>=pages_count)
		{
			$('#more_finance_btn').hide();
		}
	});
}

// Отменить операцию
function finance_operation_return(operation_id)
{
	$('#finance_operation_return_proc_'+operation_id).html('<img src="/img/loading5.gif">');
	
	$.post('/ajax/ajaxFinances.php', 
	{   
		mode : 'finance_operation_return',
		operation_id : operation_id,
		finance_id : finance_id
	},
	function(data){ 
		
		if(data['success']==1)
		{
			get_finance_operation_item(operation_id, 1);
			
			$('#finance_summa').html(data['result_sum'])
		}
		
	}, 'json');
}

// Показать блок настройки доступа к файлам и папкам
function show_finance_owner_block(finance_id)
{
	$('.file_users_access_block_list').hide();
 
	$('#finance_owner_block_'+finance_id).show();
		 
}


// Дать доступ к файлу
function give_access_to_finance(finance_id, user_id)
{
	$('#access_proc_'+finance_id).html('<img src="/img/ajax-loader.gif">');
	 
	$.post('/ajax/ajaxFinances.php', 
	{   
		mode : 'give_access_to_finance',
		finance_id : finance_id,
		user_id : user_id
	},
	function(data){ 
		
		$('#access_proc_'+finance_id).html('');
		if(data==1)
		{
			$('#user_'+finance_id+'_'+user_id).removeClass('access_active')
		}
		if(data==2)
		{
			$('#user_'+finance_id+'_'+user_id).addClass('access_active')
		}
		
	});
}

searched_finance_id_to = 0;
function get_finance_for_transfer()
{
	var search_finance_id;
	
	search_finance_id = $('#search_finance_id').val();
	
	$('#transfer_error').html('');
	
	searched_finance_id_to = 0;
	
	if(search_finance_id=='')
	{
		$('#transfer_form').html('')
		return 
	}
	$('#finance_search_acc_proc').html('<img src="/img/ajax-loader.gif">');
	
	$.post('/ajax/ajaxFinances.php', 
	{   
		mode : 'get_finance_for_transfer',
		finance_id : finance_id,
		search_finance_id : search_finance_id
	},
	function(data){ 
		
		$('#finance_search_acc_proc').html('');
		
		$('#transfer_form').html('');
		
		if(data['success']==1)
		{  
			$('#transfer_form').html(data['transfer_form']);
			
			searched_finance_id_to = search_finance_id;
		}
		else if(data['error']==1)
		{
			$('#transfer_error').html('Счет не найден');
		}
		
	}, 'json');
}

function to_transfer_finance()
{
	var summa_from, summa_to, comment;
	
	summa_from = $('#summa_from').val();
	
	summa_to = $('#summa_to').val();
	
	comment = $('#transfer_comment').val();
	
	loading_btn('to_transfer_finance_btn');
	
	$('#transfer_error').html('');
	
	
	$.post('/ajax/ajaxFinances.php', 
	{   
		mode : 'to_transfer_finance',
		finance_id_from : finance_id,
		finance_id_to : searched_finance_id_to,
		summa_from : summa_from,
		summa_to : summa_to,
		comment : comment
	},
	function(data){ 
		
		loading_btn('to_transfer_finance_btn', 1);
		
		if(data['error'])
		{
			if(data['error']['summa'])
			{
				$('#transfer_error').html('Некорректно указаны суммы перевода');
			}
			
		}
		else if(data['success'])
		{
			$('#finance_summa').html(data['finance_summa_from']);
			$('#finance_summa_to').html(data['finance_summa_to']);
			$('#transfer_sub_form').html('<div class="success">Перевод выполнен успешно | <a href="javascript:;" class="link" onclick="close_transfer_form()">Закрыть</a></div>')
		}
		
	}, 'json');
}

function open_transfer_form()
{  
	$('#finance_transfer_form_block').fadeIn(100);
}
function close_transfer_form()
{
	$('#search_finance_id').val('');
	$('#finance_transfer_form_block').hide();
	$('#transfer_form').html('');
}