// Авторизация
function auth()
{ 
	var login, password, remember, sms_code, is_sms_code;
	
	$('#auth_error').html('');
	   
	login = $('#login').val();
	  
	password = $('#password').val();
	
	$.post('/client/ajax/ajaxAuth.php', 
	{   
		login : login,
		password : password
		 
	},
	function(data){ 
		if(data['success'])
		{
			document.location = '/client/msgs'
		}
		else
		{
			if(data['auth_by_sms'] || data['auth_activate_by_sms'] || data['auth_protect_by_sms'])
			{
				if(data['auth_by_sms'])
				{
					$('#auth_by_sms_notice').html('Введите верный пароль')
				}
				
				$('.auth_block').css('padding-top', "35px");
				$('#password').val('');
				 
			}
			// Вывод ошибок при авторизации
			$('#auth_error').html(data['error']) ;
		}
	},'json');
}

function hide_auth_by_sms()
{
	$('.auth_block').css('padding-top', "85px");
	$('#auth_by_sms').hide();
	$('#sms_code').val('');
	$('#is_sms_code').val(0)
}
