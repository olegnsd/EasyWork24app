add_client_btn = 0;

// Добавление нового клиента
function add_new_client()
{
	var client_name, client_inn, client_address_actual, client_address_legal, client_phone, client_fax, client_email, client_bank_name, client_bik, client_bank_account, client_desc, client_private_edit, client_private_show, client_contact_person, client_organization_type;
	 
	if(add_client_btn==1)
	{
		return;
	}
	
	$('.td_error').html('');
	
	$('.input_text').removeClass('light_error_input')
	
	client_name = $('#client_name').val();
	client_inn = $('#client_inn').val();
	client_address_actual = $('#client_address_actual').val();
	client_address_legal = $('#client_address_legal').val();
	client_phone = $('#client_phone').val();
	client_fax = $('#client_fax').val();
	client_email = $('#client_email').val();
	client_bank_name = $('#client_bank_name').val();
	client_bik = $('#client_bik').val();
	client_bank_account = $('#client_bank_account').val();
	client_desc = $('#client_desc').val();
	client_private_edit = $('#client_private_edit').attr('checked')=='checked' ? 1 : 0;
	//client_private_show = $('#client_private_show').attr('checked')=='checked' ? 1 : 0;
	client_contact_person = $('#client_contact_person').val();
	client_organization_type  = $('#client_organization_type').val();
	
	 
	add_client_btn = 1;
	
	loading_btn('add_client_btn');
	
	$.post('/ajax/ajaxClients.php', 
	{   
		mode : 'add_new_client',
		client_name : client_name,
		client_inn : client_inn,
		client_address_actual : client_address_actual,
		client_address_legal : client_address_legal,
		client_phone : client_phone,
		client_fax : client_fax,
		client_email : client_email,
		client_bank_name : client_bank_name,
		client_bik : client_bik,
		client_bank_account : client_bank_account,
		client_desc : client_desc,
		client_private_edit : client_private_edit,
		//client_private_show : client_private_show,
		client_contact_person : client_contact_person,
		client_organization_type : client_organization_type
	
	},
	function(data){ 
		
		add_client_btn = 0;
		
		loading_btn('add_client_btn', 1);
		
		if(data['error'])
		{
			$.each(data['error'], function(i, j){
				error_text = '';
						 
				if(i=='client_name' && j==1)
				{ 
					error_text = 'Название не может быть пустым';
					light_error_input('client_name');
					//$('#client_name_error').html(error_text)
				}
				if(i=='client_name' && j==2)
				{ 
					error_text = 'Клиент с таким названием уже существует';
					light_error_input('client_name');
				}
				$("#"+i).next().html(error_text);
			
			})
		}
		else if(data['success']==1)
		{ 
			$('.clients_no').remove();
			
			$('#client_name').val('');
			$('#client_inn').val('');
			$('#client_address_actual').val('');
			$('#client_address_legal').val('');
			$('#client_phone').val('');
			$('#client_fax').val('');
			$('#client_email').val('');
			$('#client_bank_name').val('');
			$('#client_bik').val('');
			$('#client_bank_account').val('');
			$('#client_desc').val('');
			$('#client_private_edit').removeAttr('checked');
			//$('#client_private_show').removeAttr('checked');
			$('#client_organization_type').val(0);
			
			get_client_form(data['client_inserted_id'], 0, 1);
		}
		
	}, 'json');
}

// Добавление нового клиента
function save_client(client_id)
{
	var client_name, client_inn, client_address_actual, client_address_legal, client_phone, client_fax, client_email, client_bank_name, client_bik, client_bank_account, client_desc, client_private_edit, client_private_show, client_contact_person, client_organization_type;
	
	$('.td_error').html('');
	
	$('.input_text').removeClass('light_error_input')
	
	client_name = $('#client_name_'+client_id).val();
	client_inn = $('#client_inn_'+client_id).val();
	client_address_actual = $('#client_address_actual_'+client_id).val();
	client_address_legal = $('#client_address_legal_'+client_id).val();
	client_phone = $('#client_phone_'+client_id).val();
	client_fax = $('#client_fax_'+client_id).val();
	client_email = $('#client_email_'+client_id).val();
	client_bank_name = $('#client_bank_name_'+client_id).val();
	client_bik = $('#client_bik_'+client_id).val();
	client_bank_account = $('#client_bank_account_'+client_id).val();
	client_desc = $('#client_desc_'+client_id).val();
	client_private_edit = $('#client_private_edit_'+client_id).attr('checked')=='checked' ? 1 : 0;
	//client_private_show = $('#client_private_show_'+client_id).attr('checked')=='checked' ? 1 : 0;
	client_contact_person = $('#client_contact_person_'+client_id).val();
	client_organization_type = $('#client_organization_type_'+client_id).val();
	
	loading_btn('save_client_btn_'+client_id);
	
	$.post('/ajax/ajaxClients.php', 
	{   
		mode : 'save_client',
		client_id : client_id,
		client_name : client_name,
		client_inn : client_inn,
		client_address_actual : client_address_actual,
		client_address_legal : client_address_legal,
		client_phone : client_phone,
		client_fax : client_fax,
		client_email : client_email,
		client_bank_name : client_bank_name,
		client_bik : client_bik,
		client_bank_account : client_bank_account,
		client_desc : client_desc,
		client_private_edit : client_private_edit,
		//client_private_show : client_private_show,
		client_contact_person : client_contact_person,
		client_organization_type : client_organization_type
	
	},
	function(data){ 
		
		add_client_btn = 0;
		
		loading_btn('save_client_btn_'+client_id, 1);
		 
		if(data['success']==1)
		{  
			 get_client_form(client_id, 0, 0);
		} 
		
		if(data['error'])
		{
			$.each(data['error'], function(i, j){
				error_text = '';
						 
				if(i=='client_name' && j==1)
				{ 
					error_text = 'Название не может быть пустым';
					light_error_input('client_name_'+client_id);
				}
				if(i=='client_name' && j==2)
				{ 
					error_text = 'Клиент с таким названием уже существует';
					light_error_input('client_name_'+client_id);
				}
				$("#"+i+"_"+client_id).next().html(error_text);
			
			})
		}
		
		
	}, 'json');
}


// Получает форму для редактирования клиента
function get_client_form(client_id, form, prepend)
{
	$.post('/ajax/ajaxClients.php', 
	{   
		mode : 'get_client_form',
		client_id : client_id,
		form : form,
		client_show : client_show
	},
	function(data){ 
		
		if(prepend)
		{
			$('#clients_list').prepend(data);
			
		}
		else
		{  
			$('#client_'+client_id).replaceWith(data);
			 
		}
		 
		 
	});
}

// отмена сохранения контакт
function cancel_save_client(client_id)
{
	loading_btn('cancel_client_btn_'+client_id)
	get_client_form(client_id,0,0);
}



// Удаляет контакт
function delete_client(client_id)
{
	$('#client_proc_'+client_id).html('<img src="/img/loading5.gif">');
	
	$.post('/ajax/ajaxClients.php', 
	{   
		mode : 'delete_client',
		client_id : client_id
	},
	function(data){ 
		
		$('#client_proc_'+client_id).html('');
		if(data==1)
		{
			$('#client_content_'+client_id).hide();
			$('#client_'+client_id).replaceWith('<tr id="client_'+client_id+'"><td colspan="12"><div class="success">Клиент успешно удален | <a href="javascript:;" onclick="restore_client('+client_id+')">Восстановить</a> | <a href="javascript:;" onclick="$(\'#client_'+client_id+'\').remove(); draw_background_list_item(\'client_item\')">Закрыть</a></div></td></tr>');
		}
	});
}

// Восстановить контакт
function restore_client(client_id)
{
	//$('#client_proc_'+client_id).html('<img src="/img/loading5.gif">');
	
	$.post('/ajax/ajaxClients.php', 
	{   
		mode : 'restore_client',
		client_id : client_id
	},
	function(data){ 
		
		//$('#client_proc_'+client_id).html('');
		if(data==1)
		{
			//$('#client_content_'+client_id).show();
			
			get_client_form(client_id, 0 , 0);
			
			//$('#client_result_'+client_id).html('');
		}
	});
}


clients_actual_page = 1;
default_search_text = '';
// Выводит больше контактов
function get_more_clients()
{
	var page, search_word;
	
	page = clients_actual_page + 1;

	search_word = $('#search_text').val();
	
	if(search_word == default_search_text)
	{
		search_word = '';
	}
	 
	$.post('/ajax/ajaxClients.php', 
	{   
		mode : 'get_more_clients',
		user_id : user_id,
		page : page,
		search_word : search_word,
		client_list_type : client_list_type
		
	},
	function(data){ 
		
		$('#clients_list').append(data);
		
		draw_background_list_item("client_item");
		// Актаульная страница
		clients_actual_page++;
		
		if(clients_actual_page>=pages_count)
		{
			$('#more_clients_btn').hide();
		}
	});
}



default_search_text = '';
// Поиск контактов
function clients_search()
{
	var search_word;
	 
	search_word = $('#search_text').val();
	
	if(search_word == default_search_text)
	{
		search_word = '';
	}
	
	$.post('/ajax/ajaxClients.php', 
	{   
		mode : 'clients_search',
		search_word : search_word,
		client_list_type : client_list_type
	},
	function(data){ 
		
		$('#clients_list').html(data['clients_list']);
		
		draw_background_list_item("client_item");
		
		if(data['pages_count'] > 1)
		{
			$('#more_clients_btn').show();
		}
		else
		{
			$('#more_clients_btn').hide();
		}
		pages_count = data['pages_count'];
		clients_actual_page = 1;
		
	}, 'json');
}


function show_add_client_access(client_id)
{
	$('.take_client_access_block').hide();
	$('#take_client_access_block_'+client_id).fadeIn(200);
}

function hide_add_client_access(client_id)
{
	$('#take_client_access_block_'+client_id).hide();
	$('#access_to_client_cont_'+client_id).show();
	$('#access_to_client_result_'+client_id).html('');
	
}

// Передать пароль для пользования клиентским сервисом
function take_access_to_client(client_id)
{
	var phone;
	
	phone = $('#phone_to_client_access_'+client_id).val();
	
	$('.access_to_client_error').html('')
	
	
	loading_btn('take_access_to_client_btn_'+client_id);
	
	$.post('/ajax/ajaxClients.php', 
	{   
		mode : 'take_access_to_client',
		client_id : client_id,
		phone : phone
	},
	function(data){ 
		
		loading_btn('take_access_to_client_btn_'+client_id, 1);
		
		$('#access_to_client_proc_'+client_id).html('');
		
		if(data['error'])
		{
			if(data['error']['phone']==1)
			{
				$('#phone_to_client_access_'+client_id).focus();
			//	$('#phone_to_client_access_error_'+client_id).html('Некорректно указан номер телефона');
			}
		}
		
		if(data['success']==1)
		{
			$('#access_to_client_cont_'+client_id).hide();
			$('#access_to_client_result_'+client_id).html("<div class='success'>Данные для входа в систему EasyWork.Clients успешно переданы | <a href='javascript:;' onclick='hide_add_client_access("+client_id+")'>Скрыть</a></div>");
			$('#phone_to_client_access_'+client_id).val('');
		}
	}, 'json');
}


// Показать блок настройки доступа к файлам и папкам
function show_access_client_block(client_id)
{
	close_client_access_blocks();
	
	$('#owner_block_'+client_id).show();
}

function give_access_to_client(client_id, user_id)
{
	$('#access_proc_'+client_id).html('<img src="/img/ajax-loader.gif">');
	
	$.post('/ajax/ajaxClients.php', 
	{   
		mode : 'give_access_to_client',
		client_id : client_id,
		user_id : user_id
	},
	function(data){ 
		
		$('#access_proc_'+client_id).html('');
		
		if(data==1)
		{
			$('#user_'+client_id+'_'+user_id).removeClass('access_active')
		}
		if(data==2)
		{
			$('#user_'+client_id+'_'+user_id).addClass('access_active')
		}
		
		$('.no_contents').remove(); 
		 
	});
}

function close_client_access_blocks()
{
	$('.file_hide_block').hide();
}

function client_import_save(import_file)
{
	var client_private_edit, client_private_show;
	
	client_private_edit = $('#client_private_edit').attr('checked')=='checked' ? 1 : 0;
	//client_private_show = $('#client_private_show').attr('checked')=='checked' ? 1 : 0;
	
	loading_btn('client_import_save_btn');
	
	$.post('/ajax/ajaxClients.php', 
	{   
		mode : 'client_import_save',
		import_file : import_file,
		//client_private_show : client_private_show,
		client_private_edit : client_private_edit
	},
	function(data){ 
		
		if(data==1)
		{
			$('#import_preview').html('<div class="success">Контрагенты успешно импортированы | <a href="/clients/'+current_user_id+'" class="link">Перейти к списку контрагентов</a></div>');
		}
		 
	});
}

function add_user_to_access_client(client_id)
{
	var id = Math.round( Math.random()*1000);
	var elem_id = 'noto_user_access_'+id;
	$('#access_users_list_'+client_id).append('<select id="'+elem_id+'" class="access_user_item"></select><br>');
	
	$('#'+elem_id).easycomplete(
	{
		str_word_select : 'Выбрать пользователя',
		width:396,
		url:'/ajax/ajaxGetUsers.php?by=name&who=all_tree&result_name=2'
	});	 
}
function save_client_user_access(client_id)
{
	var access_users = {};
	
	loading_btn('save_access_btn_'+client_id);
	
	$('#access_users_list_'+client_id+' .access_user_item').each(function(){
			  
		var user_id = $(this).val();
		
		access_users[user_id] = user_id;
	})
		
	$.post('/ajax/ajaxClients.php', 
	{   
		mode : 'save_client_user_access',
		client_id : client_id,
		access_users : $.toJSON(access_users)
	},
	function(data){ 
		
		loading_btn('save_access_btn_'+client_id, 1);
		
		if(data==1)
		{
			$('#access_result_'+client_id).html('<div class="success stand_margin">Успешно сохранено</div>');
			clear_block_by_settime('access_result_'+client_id);
		}
		 
	});
}

function get_client_access_block(client_id)
{
	$.post('/ajax/ajaxClients.php', 
	{   
		mode : 'get_client_access_block',
		client_id : client_id
	},
	function(data){ 
		
		if(data)
		{
			 
			$('.item_access_block').html('');
			$('#access_block_'+client_id).html(data)
		}
	});
}