// Изменить название компьютера
function save_computer_name(computer_id)
{
	var computer_name, computer_authed;
	
	computer_name = $('#computer_name').val();
	
	computer_authed = $('#computer_authed').attr('checked')=='checked' ? 1 : 0;
	 
	loading_btn('save_computer_btn');
	
	$.post('/ajax/ajaxWorktime.php', 
	{   
		mode : 'save_computer_name',
		computer_name : computer_name,
		computer_id : computer_id,
		computer_authed : computer_authed
	},
	function(data){ 
		
		loading_btn('save_computer_btn', 1);
		
		if(data['error'])
		{
			$.each(data['error'], function(i, j){
				error_text = '';
						 
				if(i=='computer_name')
				{ 
					 $('#computer_name').focus();
				}
				
				$("#"+i).parent().next().html(error_text);
			
			})
			
			$('#user_computer_input_'+activity_id).css('border-color', 'red');
			setTimeout(function(){$('#user_computer_input_'+activity_id).css('border-color', 'green');},300);
		}
		else if(data['success']==1)
		{  
			$('#computer_result').html('<div class="success">Изменения успешно сохранены</div>');
			setTimeout(function(){$('#computer_result').html('')}, 1000);
		}
		
	}, 'json');
}
function change_computer_name_proc(activity_id)
{
	// Актуализируем название в инпуте 
	$('#user_computer_input_'+activity_id).val($('#user_computer_a_'+activity_id).html())
	$('#computer_edit_c_'+activity_id).show();
	$('#computer_name_c_'+activity_id).hide();
}

function cancel_save_computer_name(activity_id, from_top_panel)
{
	$('#computer_edit_c_'+activity_id).hide();
	$('#computer_name_c_'+activity_id).show();
}

// Нажатие на статусную кнопку
function change_user_activity_work_status(status_id, from_top_panel)
{
	date = get_user_agent_date();
	
	if(!from_top_panel)
	{
		loading_btn('change_user_activity_btn');
	}
	else
	{
		simple_loading_btn('change_us_activity_tpp');
	}
	
	$.post('/ajax/ajaxWorktime.php', 
	{   
		mode : 'change_user_activity_work_status',
		status_id : status_id,
		date : date
	},
	function(data){ 
		
		$('#activity_work_buttons_proc').html('');
		
		if(data['success']==1)
		{
			wk_top();
			$('#activity_work_buttons').html(data['status_btn']);
		}
		
		
	}, 'json');
}

// Вывести активность за другую дату
function show_user_activity_by_date()
{
	var date;
	
	date = $('#activity_date').val();
	
	document.location = '/wktime/'+user_id+'?date='+date
}

function set_u_act()
{
	var date, screen_width, screen_height;
	
	date = get_user_agent_date();
	screen_width = screen.width;
	screen_height = screen.height;

	 
	$.post('/ajax/ajaxWorktime.php', 
	{   
		mode : 'set_u_act',
		date : date,
		screen_width : screen_width,
		screen_height : screen_height
	},
	function(data){ 
		
	});
}

function get_user_agent_date()
{
	var date = new Date();
	return date.getFullYear()+'-'+Number(date.getMonth()+1)+'-'+date.getDate()+' '+date.getHours()+':'+date.getMinutes()+':'+date.getSeconds();
	
}
function show_statsu_list()
{
	if(!$('#wk_statuses_list').is(':visible'))
	{
		$('#wk_statuses_list').slideDown(200);
	}
	else
	{
		$('#wk_statuses_list').slideUp(200);
	}
}

defaults_status_data = {};
status_btn = 0
function change_user_status()
{
	var status_id;
	
	if(status_btn)
	{
		return false
	}
	status_id = $(this).attr('status_id');
	

	
	var elem = $('#wk_statuses_list a[status_id='+status_id+']');
	var width = $(elem).css('width');
	var height = $(elem).css('height');
	
	defaults_status_data[status_id] = $(elem).text();
	
	$(this).css('width', width);
	$(this).css('height', height);
	$(this).html('');
	$(this).html('<div class="btn_loading"></div>');
		
	status_btn = 1;
	
	$.post('/ajax/ajaxWorktime.php', 
	{   
		mode : 'change_user_status',
		status_id : status_id,
		user_id : user_id
	},
	function(data){ 
		
		status_btn = 0;
		
		$(elem).html(defaults_status_data[status_id]);
		
		if(data==1)
		{
			$('#user_status_active').html($(elem).text());
			show_statsu_list();
		}
	}); 
}