// Добавление нового имущества
add_good_btn = 0;
function add_planning()
{
	var num, boss_id, planning_type, dates_one_arr = {}, dates_period_arr = {}, error;
	
	$('#date_error').html('')
	num = 1;
	
	$('.date_input').removeClass('light_error_input');
	
	$('#planning_date_one_list .planning_data_item').each(function(){
		
		var date = $.trim($(this).children('.date_input').val());
		var date_hours = $.trim($(this).children('.date_input_hours').val());
		var date_minutes = $.trim($(this).children('.date_input_minutes').val());
		
		dates_one_arr[num] =  date+'%'+date_hours+'%'+date_minutes;
		num++;
		 
	})
	
	num = 1;
	$('#planning_date_period_list .planning_data_item').each(function(){
		
		var date_from = $.trim($(this).children('.date_input_from').val());
		var date_from_hours = $.trim($(this).children('.date_input_from_hour').val());
		var date_from_minutes = $.trim($(this).children('.date_input_from_minutes').val());
		
		var date_to = $.trim($(this).children('.date_input_to').val());
		var date_to_hours = $.trim($(this).children('.date_input_to_hour').val());
		var date_to_minutes = $.trim($(this).children('.date_input_to_minutes').val());
		
		
		if(date_from && !date_to)
		{
			$(this).children('.date_input_to').addClass('light_error_input');
			error = 1;
		}
		if(!date_from && date_to)
		{
			$(this).children('.date_input_from').addClass('light_error_input');
			error = 1;
		}
		
		dates_period_arr[num] = date_from+'%'+date_from_hours+'%'+date_from_minutes+'|'+date_to+'%'+date_to_hours+'%'+date_to_minutes;
		num++;
		 
	})
	
	planning_type_id = $('#planning_type_id').val();
	planning_boss_id = $('#planning_boss_id').val();
	
	var planning_for = $('input[name="notice_to"]:checked').val();
	var depts = $('#planning_depts').val();
	var planning_type_str = $('#planning_type_str').val();
	 
	if(error)
	{
		return false
	}
	
	loading_btn('add_planning_btn');
	
	$.post('/ajax/ajaxPlanning.php', 
	{   
		mode : 'add_planning',
		user_id : current_user_id,
		boss_id : planning_boss_id,
		type_id:planning_type_id,
		dates_one_arr : $.toJSON(dates_one_arr),
		dates_period_arr : $.toJSON(dates_period_arr),
		planning_for : planning_for,
		depts : depts,
		planning_type_str : planning_type_str
	},
	function(data){ 
		
		loading_btn('add_planning_btn', 1)
		
		if(data['error'])
		{
			if(data['error']['date']=='1')
			{ 
				$('#date_error').html('Укажите правильно дату')
			}
			if(data['error']['depts']=='1')
			{ 
				$('#date_error').html('Выберите подразделения')
			}
			if(data['error']['boss']=='1')
			{ 
				$('#date_error').html('Выберите руководителя')
			}
		}
		else if(data['success']==1)
		{
			document.location.reload();
			
			//$('.date_input').val('')
			//get_planning_item(data['planning_id']);
			//$('.no_contents').remove()
		}
		else
		{
			_error()
		}
	
		
	}, 'json');
}


function planning_more_date(list)
{
	var num;
	
	if(list==1)
	{
		num = $('#planning_date_one_list .planning_data_item').size();
	}
	if(list==2)
	{
		num = $('#planning_date_period_list .planning_data_item').size();
	}
	
	num++;
	
	var date_one = '<div class="planning_data_item"><input type="text" id="date_one_'+num+'" class="input_text date_input" /> <input class="input_text time_inp date_input_hours" maxlength="2" id="date_one_hour_'+num+'" placeholder="чч"/>:<input class="input_text time_inp date_input_minutes" maxlength="2" id="date_one_minutes_'+num+'" placeholder="мм"/></div>';
	
	var date_period = '<div class="planning_data_item">c <input type="text" id="date_period_from_'+num+'" class="input_text date_input date_input_from" /> <input class="input_text time_inp date_input_from_hour" maxlength="2" id="date_period_from_hour_'+num+'" placeholder="чч"/>:<input class="input_text time_inp date_input_from_minutes" maxlength="2" id="date_period_from_minutes_'+num+'" placeholder="мм"/> по <input type="text" id="date_period_to_'+num+'" class="input_text date_input date_input_to"/> <input class="input_text time_inp date_input_to_hour" maxlength="2" id="date_period_to_hour_'+num+'" placeholder="чч"/>:<input class="input_text time_inp date_input_to_minutes" maxlength="2" id="date_period_to_minutes_'+num+'" placeholder="мм"/></div>';
	
	 
	
	if(list==1)
	{
		$('#planning_date_one_list').append(date_one);
		planning_date_init(1, num);
	}
	if(list==2)
	{
		$('#planning_date_period_list').append(date_period);
		planning_date_init(2, num);
	}
}

function planning_date_init(list, num)
{
	if(list==1)
	{
		$("#date_one_"+num).datepicker({
		  showOn: "button",
		  buttonImage: "/img/calendar.gif",
		  buttonImageOnly: true,
		   changeMonth: true,
		  changeYear: true
		});
	}
	
	if(list==2)
	{
		$("#date_period_from_"+num).datepicker({
		  showOn: "button",
		  buttonImage: "/img/calendar.gif",
		  buttonImageOnly: true,
		   changeMonth: true,
		  changeYear: true
		});
		
		$("#date_period_to_"+num).datepicker({
		  showOn: "button",
		  buttonImage: "/img/calendar.gif",
		  buttonImageOnly: true,
		   changeMonth: true,
		  changeYear: true
		});
	}
}


planning_actual_page = 1;

// Выводит больше имущест
function get_more_planning()
{
	var page;
	
	page = planning_actual_page + 1;

	$.post('/ajax/ajaxPlanning.php', 
	{   
		mode : 'get_more_planning',
		user_id : user_id,
		page : page,
		others : others
	},
	function(data){ 
		
		$('#planning_list').append(data);
		
		// Актаульная страница
		planning_actual_page++;
		
		if(planning_actual_page>=pages_count)
		{
			$('#more_planning_btn').hide();
		}
	});
}

function get_planning_item(planning_id)
{
	$.post('/ajax/ajaxPlanning.php', 
	{   
		mode : 'get_planning_item',
		planning_id : planning_id
	},
	function(data){ 
		
		$('#planning_list').prepend(data);
	
	});
}

function delete_planning(planning_id)
{
	$.post('/ajax/ajaxPlanning.php', 
	{   
		mode : 'delete_planning',
		planning_id : planning_id,
		user_id : current_user_id
	},
	function(data){ 
		
		// Удаление успешно
		if(data==1)
		{
			 
			$('.cont_hide_after_act_'+planning_id).hide();
			// Вывод уведомления
			$('#planning_notice_'+planning_id).html('<div class="success">Планирование удалено | <a href="javascript:;" onclick="restore_planning('+planning_id+')">Восстановить</a> | <a href="javascript:;" onclick="$(\'#planning_'+planning_id+'\').remove();">Скрыть</a></div>');
		}
	
	});
}

function restore_planning(planning_id)
{
	$.post('/ajax/ajaxPlanning.php', 
	{   
		mode : 'restore_planning',
		planning_id : planning_id,
		user_id : current_user_id
	},
	function(data){ 
		
		// Удаление успешно
		if(data==1)
		{
			 
			$('.cont_hide_after_act_'+planning_id).show();
			// Вывод уведомления
			$('#planning_notice_'+planning_id).html('');
		}
	
	});
}

function confirm_planning(planning_id, planning_result)
{  
	if(planning_result==1)
	{
		loading_btn('confirm_planning_btn_'+planning_id);
	}
	else if(planning_result==2)
	{
		loading_btn('cancel_confirm_planning_btn_'+planning_id)
	}
	
	$.post('/ajax/ajaxPlanning.php', 
	{   
		mode : 'confirm_planning',
		planning_id : planning_id,
		planning_result : planning_result
	},
	function(data){ 
		
		if(data['success']==1)
		{
			if(planning_result==1)
			{
				loading_btn('confirm_planning_btn_'+planning_id, 1);
			}
			else if(planning_result==2)
			{
				loading_btn('cancel_confirm_planning_btn_'+planning_id, 2)
			}
			 
			$('#planning_'+planning_id).removeClass('not_confirm');
			
			$('#planning_confirm_btn_bl_'+planning_id).remove();
			
			$('#planning_result_'+planning_id).html(data['result']);
			
			if(data['new_planning_count_all']>=1)
			{
				$('#planning_new_count').html('(+ '+data['new_planning_count_all']+')');
				$('#planning_new_count_in_top').html('(+ '+data['new_planning_count_all']+')');
			}
			else
			{
				$('#planning_new_count').html('');
				$('#planning_new_count_in_top').html('');
			}
			
			/*if(data['new_planning_count_for_boss']>=1)
			{
				$('#new_count_for_boss').html('(+ '+data['new_planning_count_for_boss']+')');
			}
			else
			{
				$('#new_count_for_boss').html('');
			}*/
			
		}
		else
		{
			_error();
		}
	
	}, 'json');
}