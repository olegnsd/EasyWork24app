// Регистрация сотрудника
function select_tab_on_main(elem)
{
	$('#user_tabs a').removeClass('active');
	
	var tab = $(elem).attr('tab');
	
	$(elem).addClass('active');
	
	$('#tab_conts .tab_cont_item').hide();
	$('#tab_conts .tab_cont_item[tab="'+tab+'"]').show();
}

function send_reg_data_to_user(user_id)
{
	$('#send_reg_data_to_user_btn').html('<img src="/img/ajax-loader.gif">');
	
	$.post('/ajax/ajaxUser.php', 
	{   
		mode : 'send_reg_data_to_user',
		user_id : user_id
		 
	},
	function(data){ 
		
		 
		if(data==1)
		{
			alert('Регистрационные данные отправлены по смс!');
		}
		else
		{
		}
		$('#send_reg_data_to_user_btn').remove();
		 
	});
}

is_reg_btn = 0;
function registration()
{ 
	var name, surname, middlename, login, pass, pass1, phone, position, registration_privilege, reg_user_to_list
	
	if(is_reg_btn)
	{
		return;
	}
	$('.td_error').html('');
	$('.input_text').removeClass('light_error_input')
	surname = $('#surname').val();
	name = $('#name').val();
	middlename = $('#middlename').val();
	login = $('#login').val();
	pass = $('#pass').val();
	pass1 = $('#pass1').val();
	phone = $('#phone').val();
	position = $('#position').val();
	user_dept = $('#user_dept').val();
	user_is_admin = $('#user_is_admin').attr('checked')=='checked' ? 1 : 0;
	var user_limitation = $('input[name="user_limitation"]:checked').val();
	//reg_user_to_list = $('input[name=reg_user_to_list]:checked').val();
	var user_is_full_access = $('#user_is_full_access').attr('checked')=='checked' ? 1 : 0;
 	//registration_privilege = $('#registration_privilege').attr('checked') == 'checked' ? 1 : 0
	var email = $('#email').val();
	 
	loading_btn('reg_btn')
	
	is_reg_btn = 1;
	
	$.post('/ajax/ajaxRegistration.php', 
	{   
		surname : surname,
		name : name,
		middlename : middlename,
		login : login,
		pass : pass,
		pass1 : pass1,
		phone : phone,
		position : position,
		user_dept : user_dept,
		user_is_admin : user_is_admin,
		user_limitation : user_limitation,
		user_is_full_access : user_is_full_access,
		email : email
		 
	},
	function(data){ 
		
		var error_text;
		
		loading_btn('reg_btn', 1)
		
		if(data['success']==1)
		{
			$('#reg_form').remove();
			$('#reg_result').html('<div class="success">'+data['success_msg']+'</div>')
		}
		else
		{
			is_reg_btn = 0;
			$.each(data['error'], function(i, j){
				
				error_text = '';
				
				if(i=='surname')
				{
					error_text = 'Не указана фамилия';
					light_error_input('surname');
				}
				if(i=='phone' && j ==1)
				{
					error_text = 'Такой номер телефона уже используется';
					light_error_input('phone');
				}
				if(i=='phone' && j ==2)
				{
					error_text = 'Не указан номер телефона';
					light_error_input('phone');
				}
				if(i=='name')
				{
					error_text = 'Не указано имя';
					light_error_input('name');
				}
				if(i=='middlename')
				{
					error_text = 'Не указано отчество';
					light_error_input('middlename');
				}
				if(i=='position')
				{
					error_text = 'Не указана должность';
					light_error_input('position');
				}
				if(i=='login' && j==1)
				{
					error_text = 'Логин не может быть пустым';
					light_error_input('login');
				}
				if(i=='login' && j==2)
				{
					error_text = 'Пользователь с таким логином уже существует';
					light_error_input('login');
				}
				if(i=='pass' && j==1)
				{
					error_text = 'Пароль не может быть пустым';
					light_error_input('pass');
				}
				if(i=='pass1' && j==1)
				{
					error_text = 'Подтверждение пароля не может быть пустым';
					light_error_input('pass1');
				}
				if(i=='pass1' && j==2)
				{
					error_text = 'Введенные пароли не совпадают';
					light_error_input('pass1');
				}
				if(i=='pass' && j==2)
				{
					error_text = 'Введенные пароли не совпадают';
					light_error_input('pass');
				}
				if(i=='email' && j==1)
				{
					error_text = 'Некорректно введен e-mail адрес';
					light_error_input('email');
				}
				
				if(i=='email' && j==2)
				{
					error_text = 'Такой e-mail адрес уже используется';
					light_error_input('email');
				}
				$("#"+i).parent().next().html(error_text)
				
			})
		}
	}, 'json');
}



// Сохранить данные пользователя
function save_profile_settings(user_id)
{
	var name, surname, middlename, login, pass, pass1, phone, position, phone_country_code, registration_privilege, auth_method, bdate;
	
	$('.td_error').html('');
	$('.input_text').removeClass('light_error_input')
	 
	surname = $('#surname').val();
	name = $('#name').val();
	middlename = $('#middlename').val();
	login = $('#login').val();
	pass = $('#pass').val();
	pass1 = $('#pass1').val();
	phone = $('#phone').val();
	position = $('#position').val();
	phone_country_code = $('#phone_country_code').val();
	registration_privilege = $('#registration_privilege').attr('checked') == 'checked' ? 1 : 0
	auth_method = $('input[name="auth_method"]:checked').val();
	bdate_day = $('#bdate_day').val();
	bdate_month = $('#bdate_month').val();
	bdate_year = $('#bdate_year').val();
	user_dept = $('#user_dept').val();
	var user_is_admin = $('#user_is_admin').attr('checked')=='checked' ? 1 : 0;
	var user_is_fired = $('#user_is_fired').attr('checked')=='checked' ? 1 : 0;
	var user_limitation = $('input[name="user_limitation"]:checked').val();
	var user_is_full_access = $('#user_is_full_access').attr('checked')=='checked' ? 1 : 0;
	var email = $('#email').val();
	
	loading_btn('save_profile_settings_btn');
	  
	$.post('/ajax/ajaxUser.php', 
	{   
		mode : 'save_profile_settings',
		user_id : user_id,
		surname : surname,
		name : name,
		middlename : middlename,
		login : login,
		pass : pass,
		pass1 : pass1,
		phone : phone,
		position : position,
		phone_country_code : phone_country_code,
		//registration_privilege : registration_privilege,
		auth_method : auth_method,
		bdate_day : bdate_day,
		bdate_month : bdate_month,
		bdate_year : bdate_year,
		user_dept : user_dept,
		user_is_admin : user_is_admin,
		user_is_fired : user_is_fired,
		user_limitation : user_limitation,
		user_is_full_access : user_is_full_access,
		email : email
	},
	function(data){ 
		
		loading_btn('save_profile_settings_btn', 1);
		
		$('#tasks_list').html(data);
		
		if(data['success']==1)
		{
			$('#settings_result').html('<div class="success">Изменения успешно сохранены</div>')
		}
		else
		{
			$.each(data['error'], function(i, j){
				
				error_text = '';
				
				if(i=='bdate')
				{
					 
					error_text = 'Некорректно указана дата рождения';
					light_error_input('bdate');
				}
				if(i=='surname')
				{
					error_text = 'Не указана фамилия';
					light_error_input('surname');
				}
				if(i=='phone' && j ==1)
				{
					error_text = 'Такой номер телефона уже используется';
					light_error_input('phone');
				}
				if(i=='phone' && j ==2)
				{
					error_text = 'Не указан номер телефона';
					light_error_input('phone');
				}
				if(i=='phone' && j ==3)
				{
					error_text = 'Некорректно указан номер телефона';
					light_error_input('phone');
				}
				if(i=='name')
				{
					error_text = 'Не указано имя';
					light_error_input('name');
				}
				if(i=='middlename')
				{
					error_text = 'Не указано отчество';
					light_error_input('middlename');
				}
				if(i=='position')
				{
					error_text = 'Не указана должность';
					light_error_input('position');
				}
				if(i=='login' && j==1)
				{
					error_text = 'Логин не может быть пустым';
					light_error_input('login');
				}
				if(i=='login' && j==2)
				{
					error_text = 'Таким логин уже существует';
					light_error_input('login');
				}
				if(i=='pass' && j==1)
				{
					error_text = 'Пароль не может быть пустым';
					light_error_input('pass');
				}
				if(i=='pass1' && j==1)
				{
					error_text = 'Подтверждение пароля не может быть пустым';
					light_error_input('pass1');
				}
				if(i=='pass1' && j==2)
				{
					error_text = 'Пароли не совпадают';
					light_error_input('pass1');
				}
				if(i=='pass' && j==2)
				{
					error_text = 'Пароли не совпадают';
					light_error_input('pass');
				}
				if(i=='email')
				{
					error_text = 'Некорректно введен e-mail адрес';
					light_error_input('email');
				}
				
				if(i=='email' && j==2)
				{
					error_text = 'Такой e-mail адрес уже используется';
					light_error_input('email');
				}
				
				$("#"+i).parent().next().html(error_text)
				
			})
		}
	}, 'json');
}

function save_settings_notices(user_id)
{
	var pars = {};
	
	pars['tasks'] = $('#tasks_notice').attr('checked')=='checked' ? 1 : 0;
	pars['projects'] = $('#project_notice').attr('checked')=='checked' ? 1 : 0;
	
	loading_btn('save_notice_btn');
	
	$.post('/ajax/ajaxUser.php', 
	{   
		mode : 'save_settings_notices',
		user_id : user_id,
		pars : pars
	},
	function(data){ 
		
		loading_btn('save_notice_btn', 1);
		
		if(data['success']==1)
		{
			$('#settings_result').html('<div class="success">Изменения успешно сохранены</div>')
		}
		
	}, 'json');
}

function init_phone_mask()
{
	var code_tmp;
	
	if($.trim($('#phone').val())=='')
	{
		$("#phone").mask("+7 (999) 999-99-99");
	}
	else
	{
		 
		var re = /\+38/;
		if(/\+38/.test($('#phone').val()))
		{
			$('#phone_country_code').val('UK');
			$("#phone").mask("+38 (999) 999-99-99");
		}
		else if(/\+375/.test($('#phone').val()))
		{
			$('#phone_country_code').val('BY');
			$("#phone").mask("+375 (99) 999-99-99");
		}
		else
		{
			$('#phone_country_code').val('RU');
			$("#phone").mask("+7 (999) 999-99-99");
		}

		 
	//	alert(code_tmp)
	}
	 
	
	 
}

function change_phone_mask()
{
	$('#phone').val('')
	clone = $("#phone").clone();
	
	$("#phone").replaceWith(clone);
	switch($('#phone_country_code').val())
	{
		case 'BY':
			$("#phone").mask("+375 (99) 999-99-99");
			 
		break;
		case 'UK':
			$("#phone").mask("+38 (999) 999-99-99");
			 
		break;
		case 'RU':
		default:
			$("#phone").mask("+7 (999) 999-99-99");
			 
		break;
	}
	
}

function user_remove_from_work(user_id)
{
	$.post('/ajax/ajaxUser.php', 
	{   
		mode : 'user_remove_from_work',
		user_id : user_id
		 
	},
	function(data){ 
		
		var error_text;
		
		loading_btn('reg_btn', 1)
		
		if(data['success']==1)
		{
			$('#remove_from_work').replaceWith(data['remove_from_work_btn']);
		}
		
	}, 'json');
}

function hide_notice_user_bday(cookie_val)
{
	expires_date = date_plus_days('', 2).replace('-','/', 'gi');
	
	var date = new Date( new Date().getTime() + 3600*24*2*1000 );

	document.cookie = cookie_val+"="+cookie_val+";logged_in=no;expires = "+date.toUTCString();
;
	$('.bday_notice_block[notice='+cookie_val+']').remove();
	 
}