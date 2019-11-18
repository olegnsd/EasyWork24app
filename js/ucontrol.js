Ucontrol = {
	show_stat : function()
	{
		var date = $('#for_date').val();
		
		to_chart = {};
		
		to_chart['inbox'] = $('#to_chart_inbox').attr('checked') == 'checked' ? 1 :0 ;
		to_chart['outbox'] = $('#to_chart_outbox').attr('checked') == 'checked' ? 1 :0 ;
		to_chart['sipuni'] = $('#to_chart_sipuni').attr('checked') == 'checked' ? 1 :0 ;
		to_chart['task_reports'] = $('#to_chart_task_reports').attr('checked') == 'checked' ? 1 :0 ;
		to_chart['edit_deals'] = $('#to_chart_edit_deals').attr('checked') == 'checked' ? 1 :0 ;
		to_chart['add_deals'] = $('#to_chart_add_deals').attr('checked') == 'checked' ? 1 :0 ;
		to_chart['work_reports'] = $('#to_chart_work_reports').attr('checked') == 'checked' ? 1 :0 ;
		 
		$('#stat_result').html('');
		
		loading_btn('show_stat_btn');
		
		$.post('/ajax/ajaxUcontrol.php', 
		{   
			mode : 'show_stat',
			user_id : selected_user_id,
			to_chart : $.toJSON(to_chart),
			date : date
		},
		function(data){ 
			
			loading_btn('show_stat_btn', 1);
			
			var error_str = '';
			
			if(data['error'])
			{
				var error_txt = '';
		
				$(data['error']).each(function(i,j) {
					
					if(j['date']==1)
					{
						error_txt += '<div>Не выбрана дата</div>';
					}
				})
				
				if(error_txt)
				{
					$('#stat_result').html('<div class="error_box" style="display:block">'+error_txt+'</div>');
				}
			
			}
			
			$('#stat_msgs_block').html(data['content']) 
			
		}, 'json');
	},
	get_sipuni_stat : function()
	{
		loading_btn('sipuni_btn');
		
		$('#sipuni_stat_result').html('');
		
		var date_from = $('#sipuni_from_date').val();
		var date_to = $('#sipuni_to_date').val();
		
		$.post('/ajax/ajaxUcontrol.php', 
		{   
			mode : 'get_sipuni_stat',
			user_id : selected_user_id,
			date_from : date_from,
			date_to : date_to
		},
		function(data){ 
			
			loading_btn('sipuni_btn', 1);
			
			var error_str = '';
			
			if(data['error'])
			{
				var error_txt = '';
		
				$(data['error']).each(function(i,j) {
					
					if(j['sipuni_phone']==1)
					{
						error_txt += '<div>Не указан номер телефона sipuni</div>';
					}
					
					if(j['date_from']==1)
					{
						error_txt += '<div>Некорректно указана дата "с"</div>';
					}
					
					if(j['date_to']==1)
					{
						error_txt += '<div>Некорректно указана дата "по"</div>';
					}
					
					if(j['date']==1)
					{
						error_txt += '<div>Не указана дата</div>';
					}
				})
				
				if(error_txt)
				{
					$('#sipuni_stat_result').html('<div class="error_box" style="display:block">'+error_txt+'</div>');
				}
			
			}
			
			$('#sipuni_stat_wrap').html(data['content']) ;
			
		}, 'json');
	},
	
	select_user : function()
	{
		var user_id = $('#select_user').val();
		
		if(parseInt(user_id) > 0)
		{
			document.location.href = "/ucontrol?id="+user_id;
		}
		else
		{ 
			$('#select_result').html('<div class="error_box display">Пользователь не выбран</div>');
		}
	},
	show_user_options : function()
	{
		$('#ucontrol_options_block').toggle();
	},
	save_user_options : function()
	{
		loading_btn('save_user_options_btn');
		
		$('#sipuni_stat_result').html('');
		
		var sipuni_phone = $('#sipuni_phone').val();
		
		$.post('/ajax/ajaxUcontrol.php', 
		{   
			mode : 'save_user_options',
			sipuni_phone : sipuni_phone,
			user_id : selected_user_id,

		},
		function(data){ 
			
			loading_btn('save_user_options_btn', 1);
			
			var error_str = '';
			
			if(data['error'])
			{
				var error_txt = '';
		
				$(data['error']).each(function(i,j) {
					
					
					
				})
				
				if(error_txt)
				{
					$('#sipuni_stat_result').html('<div class="error_box" style="display:block">'+error_txt+'</div>');
				}
			
			}
			else if(data['success'])
			{
				document.location.reload();
			}
			
			
		}, 'json');
	},
	save_ucontrol_settings : function()
	{
		var option_sipuni_id = $('#option_sipuni_id').val();
		var option_secret_key = $('#option_secret_key').val();
		
		
		loading_btn('save_ucontrol_settings_btn');
		
		$.post('/ajax/ajaxUcontrol.php', 
		{   
			mode : 'save_ucontrol_settings',
			option_sipuni_id : option_sipuni_id,
			option_secret_key : option_secret_key

		},
		function(data){ 
			
			loading_btn('save_ucontrol_settings_btn', 1);
			
			var error_str = '';
			
			if(data['error'])
			{
				
			
			}
			else if(data['success'])
			{
				document.location.reload();
			}
			
			
		}, 'json');
	}
}