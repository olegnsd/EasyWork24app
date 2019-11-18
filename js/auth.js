// Авторизация
auth_method_proc = '';
auth_login_proc = '';
function auth()
{ 
	var login, password, remember, sms_code, is_sms_code, restore_by_sms_code;
	
	$('#auth_error').html('');
	   
	login = $('#login').val();
	  
	password = $('#password').val();
	
	sms_code = $('#sms_code').val();
	
	restore_by_sms_code = $('#restore_by_sms_code').val();
	
	//remember = $('#remember').attr('checked') == 'checked' ? 1 : 0;
	
	is_sms_code = $('#is_sms_code').val();
	
	auth_login_proc = login;
	
	$.post('/ajax/ajaxAuth.php', 
	{   
		login : login,
		password : password,
		sms_code : sms_code,
		is_sms_code : is_sms_code,
		auth_method_proc : auth_method_proc,
		restore_by_sms_code : restore_by_sms_code
		 
	},
	function(data){
		
		if(auth_method_proc!='by_pass_and_sms')
		{
			$('.au_form').removeClass('au_form_marg');
		}
			
		if(data['error'])
		{
			// Вывод ошибок при авторизации
			$('#auth_error').html(data['error']) ;
		}
		else if(data['success'])
		{
			if(auth_method_proc=='restore_by_sms_code')
			{
				document.location = '/settings?chp=1'
			}
			else
			{
				document.location = '/'
			} 
		}
		else if(data['auth_method']=='by_pass')
		{
			$('#pass_method').show();
			auth_method_proc = 'by_pass';
			$('#password').focus();
		}
		else if(data['auth_method']=='restore_by_sms_code')
		{
			$('#auth_by_sms_notice').html('Введите код, полученный по СМС') 
			 
			$('#auth_by_sms').show();
			auth_method_proc = 'restore_by_sms_code';
			$('#auth_restore_btn').hide();
			$('#auth_restore_proc_btn').show();
			$('#sms_code').focus();
		}
		else if(data['auth_method']=='by_sms')
		{
			$('#auth_by_sms_notice').html('В настройках вы указали защитить свой аккаунт. Введите код, полученный по СМС') 
			 
			$('#auth_by_sms').show();
			auth_method_proc = 'by_sms';
			$('#sms_code').focus();
		}
		else if(data['auth_method']=='activation')
		{
			$('#auth_by_sms_notice').html('Для активации аккаунта, введите код, полученный по СМС');
			$('#auth_by_sms').show();
			auth_method_proc = 'activation';
			$('#sms_code').focus();
		}
		else if(data['auth_method']=='by_pass_and_sms')
		{
			$('#pass_method').show();
			$('#auth_by_sms_notice').html('В настройках вы указали защитить свой аккаунт. Введите пароль и смс-код');
			$('.au_form').addClass('au_form_marg');
			$('#auth_by_sms').show();
			auth_method_proc = 'by_pass_and_sms';
			$('#password').focus();
		}
	},'json');
}

function show_restore_auth_form()
{
	$('#auth_restore_btn').show();
	$('#auth_df_btn').hide();
	$('#restore_by_sms_code').val(1);
	$('#pass_method').hide();
	$('#password').val('');
	auth_method_proc = '';
	$('#auth_error').html('');
	$('#auth_restore_title').show();
	$('.au_form').removeClass('au_form_marg');
	$('#auth_by_sms').hide();
}
function cancel_restore_auth_form()
{
	$('#auth_restore_btn').hide();
	$('#auth_df_btn').show();
	$('#restore_by_sms_code').val(0);
	$('#auth_restore_proc_btn').hide();
	$('#sms_code').val('');
	$('#auth_by_sms').hide();
	auth_method_proc = '';
	$('#auth_error').html('');
	$('#auth_restore_title').hide();
}

function hide_auth_by_sms()
{  
	if(auth_login_proc!=$('#login').val())
	{
		$('.auth_block').css('padding-top', "85px");
		$('#auth_by_sms').hide();
		$('#pass_method').hide();
		$('#password').val('');
		$('#sms_code').val('');
		$('#is_sms_code').val(0);
		auth_method_proc = '';
	}
}
function auth_ev_init()
{
	$('#login').focus();
	
	$('#login').keydown(function(e)
	{
		if(e.which==13)
		{
			auth()
		}
	})
	$('#password').keydown(function(e)
	{
		if(e.which==13)
		{
			auth()
		}
	})
	
	$('#sms_code').keydown(function(e)
	{
		if(e.which==13)
		{
			auth()
		}
	})
}
