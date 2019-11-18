
// Смена года
function change_year(mode)
{ 
	actual_date = calendareStartDate.split('-');

	actual_year = Number(actual_date[0])

	actual_month = actual_date[1]

	actual_day = actual_date[2]

	if(mode=='prev')
	{
		new_year = actual_year - 1;

	}
	if(mode=='next')
	{
		new_year = actual_year + 1;
	}

	actual_day = '01';

	calendareStartDate = new_year+'-'+actual_month+'-'+actual_day

	render_event_calendar(calendareStartDate);

}

// Смена месяца
function change_month(mode)
{
	actual_date = calendareStartDate.split('-');

	actual_year = Number(actual_date[0])

	actual_month = Number(actual_date[1])

	actual_day = actual_date[2]

	if(mode=='prev')
	{
		new_month = actual_month - 1;

		if(new_month==0)
		{
			new_month=12;

			actual_year--;
		}
	}
	if(mode=='next')
	{
		new_month = actual_month + 1;

		if(new_month==13)
		{
			new_month=1;

			actual_year++;
		}
	}
	actual_day = '01';

	new_month = ''+new_month;

	new_month = new_month.length > 1 ? new_month : '0'+new_month

	calendareStartDate = actual_year+'-'+new_month+'-'+actual_day

	render_event_calendar(calendareStartDate);

}

// in - Рассматриваемая дата
// При первом запуске дата является сегодняшней
global_checked_date = 0;
global_checked_month = 0
global_checked_year = 0
function render_event_calendar(dateStr)
{
	// Разбираем заданную дату
	todayDate=new Date(parseDate(dateStr));
	thisday=todayDate.getDay()+1;
	thismonth=todayDate.getMonth();
	thisdate=todayDate.getDate();
	thisyear=todayDate.getFullYear();


	/// Актуальное дата
	nowDate=new Date();
	now_day=nowDate.getDay();
	now_month=nowDate.getMonth();
	now_date=nowDate.getDate();
	now_year=nowDate.getFullYear();
	
	
	monthdays = get_month_days_count('array', thisyear);
	
	var cal;
	

	/*startspaces=thisdate;

	while (startspaces > 7)
	{
		startspaces-=7;
	}*/


	// День недели первого числа месяца
	//startDayWeek = thisday - startspaces;

	// Высчитываем День недели первого числа месяца
	tmpdateStr = dateStr.split('-');
	tmpdateStr = tmpdateStr[0]+'-'+tmpdateStr[1]+'-01';
	tmpDate = new Date(parseDate(tmpdateStr));
	tmpWeekDay = tmpDate.getDay();
	startDayWeek = tmpWeekDay;


	// ЗАписываем название месяца и год
	jQuery('#change_calendar_month').html(get_month_rus_name_by_month(thismonth));
	jQuery('#change_calendar_year').html(thisyear);


	count=1; // Первое число месяца
	date_col = '';
	date_col_weekdays = '';
	 

 	// Глобальная выбранная дата дня
 	if(global_checked_date==0)
	{
		global_checked_date = thisdate
		global_checked_month = thismonth
		global_checked_year = thisyear
	}
	 
	while (count <= monthdays[thismonth])
	{
		if (count <= monthdays[thismonth])
		{
			// Подсветка сегодняшней даты
			if (count==now_date && now_year==thisyear && now_month == thismonth)
			{
				day_now_light = "<div class='calendar_day_now_light'><div class='panel'></div>Сегодня</div>";
				
			}
			else
			{
				day_now_light = "";
				
			}
			if(count==1)
			{
			// alert(thismonth)
			// alert(now_month)
			}
			// Подсветка выбранной даты  
			if(global_checked_date==count && global_checked_month == thismonth && global_checked_year==thisyear)
			{
				day_actual_back = 'calendar_actual_day_back';
			}
			else
			{
				day_actual_back = '';
			}
			// Подсветка выходных дней
			if(startDayWeek==6 || startDayWeek==0)
			{
				class_weekend = '';
				date_dayweek_class = 'calendar_dayweek_weekend';
			}
			else
			{
				class_weekend = '';
				date_dayweek_class = 'calendar_dayweek_budn'
			}

			// День недели
			date_day_week = get_dayweek_by_day(startDayWeek);

			// Форматируем день с ведущими нулями
			tmp_day = ''+count;
			tmp_day = tmp_day.length > 1 ? tmp_day : '0'+tmp_day;

			// Форматируем месяц с ведущими нулями
			tmp_month = thismonth+1;
			tmp_month = ''+tmp_month;
			tmp_month = tmp_month.length > 1 ? tmp_month : '0'+tmp_month;

			// Подготовка даты для нахождения ее в массиве мероприятий
			check_event_date = thisyear+'-'+tmp_month+'-'+tmp_day

			//alert(datesArr);
			//return;

			// Проверяем, есть ли событие на эту дату
			// datesArr - глобальный массив дат событий. Локация - bottom_js
			
			  
			if(datesArr && datesArr[check_event_date])
			{
				is_event_day_class = 'calendar_day_is_event';
				event_date = check_event_date;
				day_over_class = 'calendar_day_over';
				 
			}
			else
			{
				is_event_day_class = '';
				event_date = '';
				day_over_class = '';
			}

			// Разделитель между ячейками дней
			if(count < monthdays[thismonth])
			{
				td_sep = '<td width="1"></td>';
			}
			else
			{
				td_sep = '';
			}

			// Числа
			date_col +="<td rel='"+event_date+"' class='calendar_col_day "+class_weekend+" "+is_event_day_class+" "+day_over_class+" "+day_actual_back+" ' >"+count+"<div style='position:relative'>"+day_now_light+"</div></td>"+td_sep;
			// Дни недели
			date_col_weekdays += "<td class='"+date_dayweek_class+"'>"+date_day_week +"</td>"+td_sep;


			// Переход на следующий день недели
			if(startDayWeek<6)
			{
				startDayWeek++;
			}
			else
			{
				startDayWeek = 0;
			}
		}

		// Переход на след. день
		count++;

	}
	date_col_weekdays = "<tr>"+date_col_weekdays+"</tr>";
	date_col = "<tr>"+date_col+"</tr>";
 
	 
	  jQuery('#calendar-container').animate({
			 "opacity": 0
			}, 50);
		
		
			// Плавное появление фоток
			setTimeout(function(){
				 jQuery('#calendar-container').html(date_col_weekdays+date_col);
				 jQuery('#calendar-container').animate({
				 "opacity": 1
				}, 100);
			
			
			
	// Рисуем календарь
	//jQuery('#calendar-container').html(date_col_weekdays+date_col);
 
	// Вешаем обработчик событий для дней имеющие события
	jQuery('.calendar_day_is_event').each(function() {
 
		jQuery(this).bind('click',function()
		{
			 
			if(page_=='worker_task')
			{   
				document.location = '/tasks?id='+to_user_id+'&date='+jQuery(this).attr('rel');
			}
			if(page_=='my_tasks')
			{
				document.location = '/tasks?date='+jQuery(this).attr('rel');
			}
			
			
		})

	})
	
	},50)

}

function get_month_days_count(return_what, thisyear, month)
{
	// Кол-во дней в месяцах
	monthdays = new Array(12);
	monthdays[0]=31;
	monthdays[1]=28;
	monthdays[2]=31;
	monthdays[3]=30;
	monthdays[4]=31;
	monthdays[5]=30;
	monthdays[6]=31;
	monthdays[7]=31;
	monthdays[8]=30;
	monthdays[9]=31;
	monthdays[10]=30;
	monthdays[11]=31;
	
	// Поправка на високосный год
	if (((thisyear % 4 == 0) && !(thisyear % 100 == 0)) ||(thisyear % 400 == 0))
	{
		monthdays[1]++;
	}
	
	if(return_what=='array')
	{
		return monthdays;
	}
	else if(return_what=='count_month_days')
	{
		return monthdays[month];
	}
}

function get_dayweek_by_day(day)
{
	dayWeek = {};
	dayWeek[0] = 'Вс';
	dayWeek[1] = 'Пн';
	dayWeek[2] = 'Вт';
	dayWeek[3] = 'Ср';
	dayWeek[4] = 'Чт';
	dayWeek[5] = 'Пт';
	dayWeek[6] = 'Сб';
	
	return dayWeek[day]
}

function get_month_rus_name_by_month(month, upper)
{
	monthnames = new Array(
	"Январь",
	"Февраль",
	"Март",
	"Апрель",
	"Май",
	"Июнь",
	"Июль",
	"Август",
	"Сентябрь",
	"Октябрь",
	"Ноябрь",
	"Декабрь");
	
	monthnames_upper = new Array(
	"ЯНВАРЬ",
	"ФЕВРАЛЬ",
	"МАРТ",
	"АПРЕЛЬ",
	"МАЙ",
	"ИЮНЬ",
	"ИЮЛЬ",
	"АВГУСТ",
	"СЕНТЯБРЬ",
	"ОКТЯБРЬ",
	"НОЯБРЬ",
	"ДЕКАБРЬ");
	
	if(upper)
	{
		return monthnames_upper[month];
	}
	else
	return monthnames[month];
}