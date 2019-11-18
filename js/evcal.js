add_evcal_btn = 0;
var Evcal= {
	
	evcal_recount_notices : function()
	{
		$.post('/ajax/ajaxEvcal.php', 
		{   
			mode : 'get_new_notices_count'
		},
		function(data){ 
			
			// Правим счетчик в левом меню
			if(parseInt(data)>=1)
			{ 
				$('#new_evcal_count').html('(+ '+data+')');
			}
			else
			{
				$('#offers_block').remove();
				$('#new_evcal_count').html('');
			}
			
		});
	},
	to_add_offer_event : function(offer_id)
	{
		$.post('/ajax/ajaxEvcal.php', 
		{   
			mode : 'to_add_offer_event',
			offer_id : offer_id
		},
		function(data){ 
			
			if(data==1)
			{
				Evcal.evcal_recount_notices();
				Evcal.get_month_events();
				$('#event_offer_'+offer_id).remove();
			}
			
		});
	},
	to_delete_offer_event : function(offer_id)
	{
		if(!confirm('Удалить предложенное событие?'))
		{
			return false;
		}
		$.post('/ajax/ajaxEvcal.php', 
		{   
			mode : 'to_delete_offer_event',
			offer_id : offer_id
		},
		function(data){ 
			
			if(data==1)
			{
				Evcal.evcal_recount_notices();
				$('#event_offer_'+offer_id).remove();
			}
			
		});
	},
	add_event_offer : function(event_id)
	{
		var users = {};
		
		$('#event_offer_result').html('');
		
		$('#offer_rows_'+event_id+' select').each(function(){
			
			var user_id = $(this).val();
			
			users[user_id] = user_id;
		})
		
		loading_btn('add_offer_btn');
		
		$.post('/ajax/ajaxEvcal.php', 
		{   
			mode : 'add_event_offer',
			event_id : event_id,
			users : $.toJSON(users)
		},
		function(data){ 
		
			loading_btn('add_offer_btn', 1);
			
			 
			
			if(data['error'])
			{
				$('#event_offer_result').html('<div class="error_box" style="display:block">Не выбраны сотрудники.</div>');
			}
			else if(data['success']==1)
			{
				$('#event_offer_form').remove();
				$('#event_offer_result').html('<div class="success stand_margin" style="display:block">Пользователи успешно уведомлены | <a href="javascript:;" onclick="close_popup(\'offer_form\')">Закрыть</a></div>');
			}
			
		}, 'json');
	},
	event_offer_form : function(event_id)
	{
		$.post('/ajax/ajaxEvcal.php', 
		{   
			mode : 'event_offer_form',
			event_id : event_id
		},
		function(data){ 
		
			if(data)
			{
				create_popup_block('offer_form', 500, data, '', 1);
			}
			
		});
	},
	add_offer_user_row : function(event_id)
	{
		var num = Math.round(Math.random() * 1000000);
		 
		$('#offer_rows_'+event_id).append('<tr class="access_row" id="user_access_'+event_id+'_'+num+'_row" num="'+num+'"> \
				<td> \
			    <select id="user_access_'+event_id+'_'+num+'"></select> \
				</td> \
			</tr>');
			
		
		$('#user_access_'+event_id+'_'+num).easycomplete(
		{
			str_word_select : 'Выбрать сотрудника',
			url:'/ajax/ajaxGetUsers.php?by=name&who=all&result_name=2',
			width:350,
			trigger : 0
		});
	},
	save_evcal_cat : function(category_id)
	{
		var name = $('#ev_cat_name_'+category_id).val();
		var color = $('#ev_cat_color_'+category_id).val();
		
		$.post('/ajax/ajaxEvcal.php', 
		{   
			mode : 'save_evcal_cat',
			category_id : category_id,
			name : name,
			color : color
		},
		function(data){ 
		
			if(data['error'])
			{
				if(data['error']['name']==1)
				{
					$('#ev_cat_name').focus();
				}
			}
			else if(data['success']==1)
			{
				document.location.reload();
			}
			
		}, 'json');
	},
	delete_evcal_category : function(category_id)
	{
		if(!confirm('Действительно удалить категорию?'))
		{
			return false;
		}
		
		$.post('/ajax/ajaxEvcal.php', 
		{   
			mode : 'delete_evcal_category',
			category_id : category_id
		},
		function(data){ 
		
			if(data==1)
			{
				 document.location.reload();
			}
			
		});
	},
	get_evcal_category_add_form : function(category_id)
	{
		$.post('/ajax/ajaxEvcal.php', 
		{   
			mode : 'get_evcal_category_add_form',
			category_id : category_id
		},
		function(data){ 
		
			if(data)
			{
				$('#evcal_types_form_wrap').html(data);
			}
			
		});
	},
	edit_evcal_category : function(category_id)
	{
		$.post('/ajax/ajaxEvcal.php', 
		{   
			mode : 'edit_evcal_category',
			category_id : category_id
		},
		function(data){ 
		
			if(data)
			{
				$('#evcal_types_form_wrap').html(data);
			}
			
		});
	},
	add_evcal_cat: function()
	{
		var name = $('#ev_cat_name').val();
		var color = $('#ev_cat_color').val();
		
		$.post('/ajax/ajaxEvcal.php', 
		{   
			mode : 'add_evcal_cat',
			name : name,
			color : color
		},
		function(data){ 
		
			if(data['error'])
			{
				if(data['error']['name']==1)
				{
					$('#ev_cat_name').focus();
				}
			}
			else if(data['success']==1)
			{
				document.location.reload();
			}
			
		}, 'json');
	},
	show_evcal : function(elem)
	{
		var what = $(elem).val();
		
		if(what>0) 
			document.location = '/evcal?id='+what;
		else
			document.location = '/evcal';	
	},
	public_evcal : function()
	{
		is_public = $('#public_evcal').attr('checked')=='checked' ? 1 : 0;
		
		$.post('/ajax/ajaxEvcal.php', 
		{   
			mode : 'public_evcal',
			is_public : is_public
		},
		function(data){ 
		});
	},
	
	save_event : function(event_id)
	{
		var event_name, event_desc, event_start, event_finish, event_start_hour, event_start_minute, event_finish_minute, event_finish_hour;
		
		$('#error_box_'+event_id).hide();
		$('#event_'+event_id+' .evcal_add_tb .input_text').removeClass('light_error_input');
		 
		event_name = $('#event_name_'+event_id).val();
		event_desc = $('#event_desc_'+event_id).val();
		event_start = $('#event_start_'+event_id).val();
		event_finish = $('#event_finish_'+event_id).val();
		
		event_start_hour = $('#event_start_hour_'+event_id).val();
		event_start_minute = $('#event_start_minute_'+event_id).val();
		
		event_finish_hour = $('#event_finish_hour_'+event_id).val();
		event_finish_minute = $('#event_finish_minute_'+event_id).val();
		
		var event_reminder = $('#event_reminder_'+event_id).val();
		
		category_id = $('#event_category_'+event_id).val();
		
		loading_btn('save_event_btn_'+event_id);
		
		$.post('/ajax/ajaxEvcal.php', 
		{   
			mode : 'save_event',
			event_id : event_id,
			event_name : event_name,
			event_desc : event_desc,
			event_start : event_start,
			event_finish : event_finish,
			event_start_hour : event_start_hour,
			event_start_minute : event_start_minute,
			event_finish_hour : event_finish_hour,
			event_finish_minute : event_finish_minute,
			category_id : category_id,
			event_reminder : event_reminder
		},
		function(data){ 
			
			if(data['error'])
			{
				loading_btn('save_event_btn_'+event_id, 1);
				
				Evcal.event_form_error(data, event_id);
			}
			else if(data['success']==1)
			{
				Evcal.get_event_item(event_id, 0, 'replace');
				// Обновляем точки на календаре
				Evcal.get_month_events();
			}
			
		}, 'json');	
	},
	get_event_item : function(event_id, form, method)
	{
		$.post('/ajax/ajaxEvcal.php', 
		{   
			mode : 'get_event_item',
			event_id : event_id,
			form : form,
			date : show_list_date
		},
		function(data){ 
			
			if(data)
			{
				if(method=='replace')
				{ 
					$('#event_'+event_id).replaceWith(data);
					
					if(form==1)
					{
						$('#event_desc_'+event_id).autoResize();
					}
				}
			}
		});
	},
	delete_event : function(event_id)
	{
		$.post('/ajax/ajaxEvcal.php', 
		{   
			mode : 'delete_event',
			event_id : event_id
		},
		function(data){ 
			
			if(data==1)
			{
				$('#event_'+event_id).replaceWith('<tr id="event_'+event_id+'"><td colspan="5"><div class="success">Событие успешно удалено | <a href="javascript:;" class="link" onclick="Evcal.restore_event('+event_id+')">Восстановить</a> | <a href="javascript:;" class="link" onclick="Evcal.hide_deleted_event('+event_id+')">Скрыть</a></div></td><tr>');
				
				Evcal.get_month_events();
			}
			
		});
	},
	restore_event : function(event_id)
	{
		$.post('/ajax/ajaxEvcal.php', 
		{   
			mode : 'restore_event',
			event_id : event_id
		},
		function(data){ 
			
			if(data==1)
			{
				Evcal.get_event_item(event_id, 0, 'replace');
				Evcal.get_month_events();
			}
			
		});
	},
	hide_deleted_event : function(event_id)
	{
		$('#event_'+event_id).remove();
		// Обновляем точки на календаре
		Evcal.get_month_events();
	},
	get_next_date : function(act)
	{
		var evcal_showed_date =  $('#evcal_showed_date').val();
		var evcal_showed_date_obj = new Date((evcal_showed_date));
		
	 	var current_month = evcal_showed_date_obj.getMonth()+1;
		var current_year = evcal_showed_date_obj.getFullYear();
 
		if(act=='prev')
		{
			current_month = current_month - 1;
	
			if(current_month==0)
			{
				current_month=12;
	
				current_year--;
			}
		}
		if(act=='next')
		{
			current_month = current_month + 1;
	
			if(current_month==13)
			{
				current_month=1;
	
				current_year++;
			}
		}
		
		return current_year+'-'+wz(current_month)+'-01';
	
	},
	
	render_current_month : function(year, month)
	{
		$('#evcal_select_month_title').css("opacity", 0.3);
		$('#evcal_select_month_title span').html(get_month_rus_name_by_month(month-1)+' '+year);
		$('#evcal_select_month_title').animate({
			 "opacity": 1
		}, 300);
	},
	
	get_month_events : function(date)
	{
		var date = Evcal.get_selected_month();
		
		var types_option = {};
		var types_default = {};
		
		n = 1;
		$('#types_list_wrap a input:checkbox:checked[option=1]').each(function(){
			types_option[n] = $(this).val();
			n++;
		})
		
		n = 1;
		$('#types_list_wrap a input:checkbox:checked[option=0]').each(function(){
			types_default[n] = $(this).val();
			n++;
		})
		
		$.post('/ajax/ajaxEvcal.php', 
		{   
			mode : 'get_month_events',
			date : date,
			evcal_user_id : evcal_user_id,
			types_option : $.toJSON(types_option),
			types_default : $.toJSON(types_default)
		},
		function(data){ 
			
			// Очищаем ячейки от событий
			$('.evcal_dates .event_icon_wrap').html('');
		
			$.each(data['events'], function(i, j){
			 
				// Помечаем дни с событиями на календаре
				Evcal.render_selected_icon(j);
			})
			 
			
		}, 'json');
	},
	// Метки в календаре на днях, где есть событие
	render_selected_icon : function(data)
	{  
		var start_date = data['date_start'];
		var finish_date = data['date_finish'];
		//alert(data['event_item'])
		
		//var selected_icon = '<div class="cell_have_event"></div>';
		
		var selected_icon = data['event_item'];
		
		// стоп цикл
		var st;
		
		//alert(start_date.substr(0, start_date.indexOf(' ')))
		var date_from = to_mktime(start_date.substr(0, start_date.indexOf(' ')), 1);
		var date_to = to_mktime(finish_date.substr(0, finish_date.indexOf(' ')), 1);
		
				
		// Проходим по циклу каждого диапазона дат и помечаем дни с событиями
		while(!st)
		{
			if(date_from<=date_to)
			{	
				var date = new Date(date_from * 1000);
				
				var event_date = date.getFullYear()+'-'+wz(date.getMonth()+1)+'-'+wz(date.getDate());
				 
				var count_events_id_day = $('.evcal_dates .cell[date="'+event_date+'"] .event_icon_wrap .ev_item').size();
				 
				if(count_events_id_day<3)
				{
					$('.evcal_dates .cell[date="'+event_date+'"] .event_icon_wrap').append(selected_icon);
				}
				else
				{
					$('.evcal_dates .cell[date="'+event_date+'"] .more').html('<a href="javascript:;" class="more_btn" onclick="Evcal.get_events_list(this)" date="'+event_date+'">Еще события &darr;</a>');
				}
				 
			}
			 
			
			if(date_from>=date_to)
			{
				st = 1;
			}
			else date_from += 3600 * 24;
		}
	},
	get_selected_month : function()
	{
		return $('#evcal_showed_date').val();	
	},
	evcal_get_month : function(act)
	{
		var this_year // год просматриваемого месяца;
		var this_month // просматриваемый месяц;
		var this_date // общий вид даты просматриваемого месяца;
		
		// Первый запуск 
		if(!act && show_event_date_start)
		{ 
			var tmpsplt = show_event_date_start.split('-');
			 
			this_date = tmpsplt[0]+'-'+tmpsplt[1]+'-01';
			$('#evcal_showed_date').val(this_date);
			 
		} 
		// Первый запуск 
		else if(!act)
		{ 
			this_date = new Date().getFullYear()+'-'+wz(new Date().getMonth()+1)+'-01';
			$('#evcal_showed_date').val(this_date);
			 
		}
		// Перелеснуть календарь
		else if(act=='next' || act=='prev')
		{
			this_date = Evcal.get_next_date(act);
			$('#evcal_showed_date').val(this_date);			 
		}
		
		this_year = new Date(parseDate(this_date)).getFullYear();
		this_month = new Date(parseDate(this_date)).getMonth()+1;
		 
		// Получаем массив с данными кол-ва дней в месяцах года
		var monthdays = get_month_days_count('array', this_year);
		
		// Вид даты первого числа месяца (2014-04-01)
		tmpDateMonthStart = this_date;
		// Вид даты послденего числа месяца (2014-04-31)
		tmpDateMonthFinish = this_year+'-'+this_month+'-'+monthdays[this_month-1];
		// День недели первого числа месяца
		startMonthDayWeek = new Date(parseDate(tmpDateMonthStart)).getDay();
		// День недели последнего числа месяца
		finishMonthDayWeek = new Date(parseDate(tmpDateMonthFinish)).getDay();
		
		// ЗАписываем название месяца и год
		Evcal.render_current_month(this_year, this_month);
		
		// Возвращает массив дней
		// in - начальная дата; кол-во дней, которое выводим после даты
		var return_days_array = function(start_date, count_days, pre_minus_days)
		{
			// Если есть дни, которые отобразятся в календаре как дни прошлого месяца, добавляем их в массив
			if(count_days>0)
			{
				// Отнимаем от даты n-дней. Для того, чтобы строить дни выводимого месяца начиная с 1 числа
				if(pre_minus_days)
				{ 
					start_date = date_minus_days(start_date, pre_minus_days);
				}
				
				var days = [];
				
				var days_start = start_date;
				
				for(i=0;i<count_days;i++)
				{
					tmp_date = date_plus_days(days_start, 1);
					
					days.push(tmp_date);
					 
					days_start = tmp_date;
				}
				return days;
			}
		}
			
			
		var result_days = []; 
		 
		var before_month_days_count = 0; // кол-во дней прошлого месяца, которые отобразятся в календаре
		var after_month_days_count = 0; // кол-во дней будущего месяца, которые отобразятся в календаре
		
		//### Вычисляем, сколько дней отобразится прошлого месяца
		if(startMonthDayWeek==0)
		{
			before_month_days_count = 6;
		}
		else if(startMonthDayWeek>1)
		{
			before_month_days_count = startMonthDayWeek-1;
		}
		
		// Если есть дни, которые отобразятся в календаре как дни прошлого месяца, добавляем их в массив
		if(before_month_days_count>0)
		{
			var days_start = date_minus_days(tmpDateMonthStart, before_month_days_count + 1); // Установка даты - с какого дня идем по прошлым датам, отображая часть дней прошлого месяца
			
			result_days = $.merge(result_days, return_days_array(days_start, before_month_days_count));
		}
		
		
		// Добавляем в массив дни выбранного месяца
		result_days = $.merge(result_days, return_days_array(this_date, monthdays[this_month-1], 1));
		 
		
		//### Вычисляем, сколько дней отобразится будущего месяца 
		if(finishMonthDayWeek==6)
		{
			after_month_days_count = 1;
		}
		else if(finishMonthDayWeek>0)
		{
			after_month_days_count = 7 - finishMonthDayWeek;
		}
		 
		// Если есть дни, которые отобразятся в календаре как дни будущего месяца, добавляем их в массив
		if(after_month_days_count>0)
		{
			var days_start = tmpDateMonthFinish; // Установка даты - с какого дня отображаем часть дней будущего месяца
			 
			result_days = $.merge(result_days, return_days_array(days_start, after_month_days_count));
		}
		 
		var row = '';
		var td = '';
		var num = 0;
		
		var acd = new Date();
		var actual_date_str = acd.getFullYear()+'-'+wz(acd.getMonth()+1)+'-'+wz(acd.getDate());
		  
		 // Формируем таблицу дат
		$.each(result_days, function(i, j){
			
			var tmp = j.split('-');
			var dyear = parseInt(tmp[0]);
			var dmonth = parseInt(tmp[1]);
			var dday = parseInt(tmp[2]);
			
			var day_cell = '';
			var actual_date_class = '';
			 
			// Текущия дата 
			if(actual_date_str==j)
			{ 
				actual_date_class = 'cell_actual_date_class';
			}
			 
			
			/*if(this_month==dmonth)
			{
				day_cell = '<a href="javascript:;" class="cell act '+actual_date_class+'" date="'+j+'">'+dday+'<div class="event_icon_wrap"></div></a>';
			}
			else
			{
				day_cell = '<span class="cell nact">'+dday+'</span>';
			}*/
			
			
			
			if(this_month==dmonth)
			{
				day_cell = '<div  class="cell_bl act" date="'+j+'"><div class="day '+actual_date_class+'">'+dday+'</div><div class="event_icon_wrap"></div><div class="more"></div></div>';
			}
			else
			{
				day_cell = '<span class="cell_bl nact"><div class="day">'+dday+'</div></span>';
			}
			
			td += '<td date="'+j+'" class="cell">'+day_cell+'<div class="pop_wrap"></div></td>';
			
			num++;
				
			if(num>=7)
			{
				row += '<tr>'+td+'</tr>';
				td = '';
				num = 0;
			}
		})
		 
		$('#evcal_dates').html(row);
		
		$('.evcal_dates .act').bind('click', Evcal.get_events_list);
		$('.evcal_dates .ev_item').bind('click', Evcal.get_events_list);
		 
		// Получаем дни, в которых есть события
		Evcal.get_month_events();

	},
	
	show_event_add_form : function(date)
	{
		$('#events_list_'+date+' #add_form_block_'+date).show();
		$('#events_list_'+date+' .list').hide();
	},
	hide_event_add_form : function(date)
	{
		$('#events_list_'+date+' #add_form_block_'+date).hide();
		$('#events_list_'+date+' .list').show();
	},
	get_events_list : function(elem, event_id, date)
	{
		if(date)
		{
			
		}
		else if(event_id)
		{
			var date = $(elem).parent().parent().attr('date');
		}
		else
		{
			var date = $(elem.target).attr('date');
		}
		 
		
		 
	 
		Evcal.hide_events_list();
		
		//$('.evcal_dates .cell[date="'+event_date+'"] .event_icon_wrap').append(selected_icon);
		
		$('.evcal_dates .cell[date="'+date+'"] .cell_bl').addClass('cell_active');
		 
		 
		 
		$.post('/ajax/ajaxEvcal.php', 
		{   
			mode : 'get_events_list',
			date : date,
			evcal_user_id : evcal_user_id,
			event_id : event_id
		},
		function(data){ 
			
			if(data)
			{
				$('.evcal_dates .cell[date="'+date+'"] .pop_wrap').html('<div class="pop_bl">'+data+'</div>');
			 
			}
		});
	},
	
	hide_events_list : function(date)
	{
		$('#evcal_events_list').html('');
		$('.evcal_dates .cell .pop_wrap').html('');
		$('.evcal_dates .cell .cell_bl').removeClass('cell_active');
		 
	},
	
	add_event : function(from_cell, form_id, pars)
	{
		var event_name, event_desc, event_start, event_finish, event_start_hour, event_start_minute, event_finish_minute, event_finish_hour;
		
		if(add_evcal_btn==1)
		{
			return '';
		}
		
		$('#add_form_block_'+form_id+' #error_box_'+form_id).hide();
		$('#add_form_block_'+form_id+' .evcal_add_tb .input_text').removeClass('light_error_input');
		
		event_name = $('#event_name_'+form_id).val();
		event_desc = $('#event_desc_'+form_id).val();
		event_start = $('#event_start_'+form_id).val();
		event_finish = $('#event_finish_'+form_id).val();
		
		event_start_hour = $('#event_start_hour_'+form_id).val();
		event_start_minute = $('#event_start_minute_'+form_id).val();
		
		event_finish_hour = $('#event_finish_hour_'+form_id).val();
		event_finish_minute = $('#event_finish_minute_'+form_id).val();
		
		var event_reminder = $('#event_reminder_'+form_id).val();
		
		category_id = $('#event_category_'+form_id).val();
		
		loading_btn('add_event_btn');
		
		add_evcal_btn = 1;
		
		$.post('/ajax/ajaxEvcal.php', 
		{   
			mode : 'add_event',
			event_name : event_name,
			event_desc : event_desc,
			event_start : event_start,
			event_finish : event_finish,
			event_start_hour : event_start_hour,
			event_start_minute : event_start_minute,
			event_finish_hour : event_finish_hour,
			event_finish_minute : event_finish_minute,
			evcal_user_id : evcal_user_id,
			pars : pars,
			category_id : category_id,
			event_reminder : event_reminder
		},
		function(data){ 
			
			add_evcal_btn = 0;
			
			loading_btn('add_event_btn', 1);
			
			 
			
			if(data['error'])
			{
				Evcal.event_form_error(data, form_id);
			}
			else if(data['success']==1)
			{
				if(pars)
				{
					document.location.reload();
				}
				
				
				$('#event_name_'+form_id).val('');
				$('#event_desc_'+form_id).val('');
				
				if(!from_cell)
				{
					$('#event_start_'+form_id).val('');
					$('#event_finish_'+form_id).val('');
					
					$('#event_start_hour_'+form_id).val(32400);
					$('#event_start_minute_'+form_id).val(0);
			
					$('#event_finish_hour_'+form_id).val(32400);
					$('#event_finish_minute_'+form_id).val(0);
				}
				
				$('#success_'+form_id).html('<div class="success">Событие успешно добавлено.</div>');
				setTimeout(function(){$('#success_'+form_id).html('')},2000); 
				// Обновляем точки на календаре
				Evcal.get_month_events();
			}
			 
			 
			
			
		}, 'json');
	},
	
	event_form_error : function(data, form_id)
	{
		var error_text = '';
		
		if(data['error'])
			{
				$.each(data['error'], function(i,j){
					  
					if(i=='event_name')
					{
						$('#event_name_'+form_id).addClass('light_error_input');
						error_text += '<div>Название события не указано.</div>';
					}
					if(i == 'date' && j==0)
					{
						error_text += '<div>Некорректно указана дата</div>';
					}
					if(i == 'date' && j==1)
					{
						$('#event_start_'+form_id).addClass('light_error_input');
						error_text += '<div>Не указана дата старта.</div>';
					}
					if(i == 'date' && j==2)
					{
						$('#event_start_'+form_id).addClass('light_error_input');
						$('#event_finish_'+form_id).addClass('light_error_input');
						
						error_text += '<div>Дата старта не может быть меньше даты завершения</div>';
					}
					 
				})
				
				if(error_text)
				{ 
					$('#error_box_'+form_id).html(error_text);
					$('#error_box_'+form_id).show();
				}
			}
	},
	get_notice : function()
	{
		$.post('/ajax/ajaxEvcal.php', 
		{   
			mode : 'get_notice'
		},
		function(data){ 
			
			if(data)
			{
				$('#left_evcal_notice').html(data);
			}
			
		});
	},
	
	show_evcal_popup : function()
	{
		if(!toggle_top_popups('evcal'))
		{
			return false;
		}
		
		$.post('/ajax/ajaxEvcal.php', 
		{   
			mode : 'show_evcal_popup'
		},
		function(data){ 
			
			if(data)
			{
				open_top_popup(data, 'evcal')
				bind_hide_tp_popup_form();
			}
			
		});
	},
	hide_history_item : function(event_id)
	{
		$.post('/ajax/ajaxEvcal.php', 
		{   
			mode : 'hide_history_item',
			event_id : event_id
		},
		function(data){ 
			
			if(data)
			{
				$('#evcal_p_'+event_id).remove();
			}
			
		});
	}
	
}