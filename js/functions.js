function msg_audio_init()
{
  soundManager.setup({
  url: '/objects/swf/',
  onready: function() {
	  
	 soundManager.createSound({
	 id: 'nmsound',
	 url: '/audio/wav/new_msg.wav?r=3',
	 volume: 80,
	 useConsole: false
	 });
  }
});
}
function new_msgs_title_animate()
{
	if(!animate_title_stop)
	{ 
		$(document).attr('title', '***** Новое сообщение *****')
		//$('title').html('***** Новое сообщение *****');
		
		setTimeout(function() { $(document).attr('title', title)},500)
	
		setTimeout(new_msgs_title_animate, 1000);
	}
}

function nmsg_sound()
{
	soundManager.play('nmsound');
}

// Подсвечивает тег TITLE при новом сообщении
function set_title_default()
{
	//clearInterval(intervalID)
	//document).attr('title',title)
}

// Расставляет бекграунды заданий в стиле "Зебра"
function draw_background_list_item(class_name, style)
{   
	var counter;
	counter = 1;
	style = style ? style : 'zebra';  
	$('.'+class_name).removeClass(style);
	
	$('.'+class_name).each(function(){
		
		if(counter%2!=0)
		{
			$(this).addClass(style)
		}
		counter++;
	})
}



// Получение координат выделения в инпуте
function sum_mask_proc(e)  
{   
 	var input_value, point_pos, str_before_point, str_after_point, input_range_start, input_range_end, range_obj, result_value;
	  
	// Инпут
	input_elem = $(e.target).get(0);
	
	// Значение поля
	input_value = $(input_elem).val();
	
	// Позиция точки 
	point_pos = input_value.indexOf('.');
	
	// Часть строки перед точкой
	str_before_point = input_value.substr(0, point_pos);
	
	// Часть строки после точки
	str_after_point = input_value.substr(point_pos+1);
	
	// Массив позиций курсора в инпуте
	range_obj = get_input_range(input_elem);
	
	// Начало позиции
	input_range_start = range_obj['start'];
	
	// Конец позиции
	input_range_end = range_obj['end'];
	//alert(n['start'])
	 
	 
	// Допустимые символы
	if (e.which==190 || e.which==78 ||  e.which==191 || e.which==110)
	{ 
		 // Меняем позицию курсора в инпуте
		  select_input_range(input_elem, point_pos+1,point_pos+1)
	}  
	 
	// Допустимые символы
	if (!(e.which==8 ||  e.which==46 || e.which==37 || e.which==39 || (e.which>47 && e.which<58) || (e.which>=96 && e.which<=105) || (e.which==65 && e.ctrlKey)))
	{ 
		return false;
	}  
	 
	
	// Выделение всего инпута
	if(input_range_start==0 && input_range_end==input_value.length)
	{
		  result_value = '.00';
		  
		  $(input_elem).val(result_value);
		  
		  // Меняем позицию курсора в инпуте
		  select_input_range(input_elem, 0,0)
	}
	// Если выделение частичной строки и под выделение попадает точка
	else if(input_range_start != input_range_end && point_pos>=input_range_start && point_pos<input_range_end)
	{

		// Меняем позицию курсора в инпуте до точки
		if(document.selection)
		{ 
			// Для ИЕ
			var range_ie = point_pos - input_range_start ;
			 
			// Меняем позицию курсора в инпуте до точки 
			select_input_range(input_elem, input_range_start, range_ie)
			 
		}
		else
		{
			// Меняем позицию курсора в инпуте до точки
			select_input_range(input_elem, input_range_start,point_pos)
		}
	 
	}
	
	// Удаление строки после точки, до точки
	if(e.which==8 && point_pos+1 == input_range_start)
	{
		return false;
	}
	
	// Удаление строки перед точкой, до точки
	if(e.which==46 && point_pos == input_range_start)
	{
		return false;
	}
	
	// Проверка строки после точки
  	if(input_range_start > point_pos)
	{  
	    // Перемещение селекта в инпуте
		if(((e.which>47 && e.which<58) || (e.which>=96 && e.which<=105)) && input_range_start==point_pos+1)
		{
			result_value = str_before_point+'.'+str_after_point.substr(0,1)
			
			$(input_elem).val(result_value);
			
			// Меняем позицию курсора в инпуте
		  	select_input_range(input_elem, point_pos+1,point_pos+1)
		}
		else if(((e.which>47 && e.which<58) || (e.which>=96 && e.which<=105)) && input_range_start==point_pos+2)
		{
			result_value = str_before_point+'.'+str_after_point.substr(0,1)
			
			$(input_elem).val(result_value);
			
			// Меняем позицию курсора в инпуте
		  	select_input_range(input_elem, point_pos+2,point_pos+2)
		}
		else if(str_after_point.length>=2)
		{ 
			if(input_range_start==input_range_end && e.which!=8 && e.which!=46 && e.which!=37 && e.which!=39)
			{
				return false;
			}
		}
	}

} 

// Меняет позицию курсора в инпуте
function select_input_range(input_elem, start, end)
{
	 if ('selectionStart' in input_elem) {
          input_elem.selectionStart = start;
          input_elem.selectionEnd = end;
          input_elem.focus ();
     }
     else { 
		  var inputRange = input_elem.createTextRange ();
          inputRange.moveStart ("character", start);
          inputRange.collapse ();
          inputRange.moveEnd ("character", end);
          inputRange.select ();
     }
}


// Првоеряет на наличие нулей и добавляет недостающие
function check_for_format(e)
{
	 
	var elem, value;
	
	elem = $(e.target);
	
	value = $(elem).val();
	
	tmp_value = value.split('.');
	
	if(tmp_value[1].length == 0)
	{
		value = value + '00'
	}
	else if(tmp_value[1].length < 2)
	{
		value = value + '0'
	}
	
	if(value.length>4)
	{
		
	// Убрать ведущие нули
	value = value.replace(/^0+(.*)/, '$1')
	}
		
	// В поле заносим сумму без нулей
	//$(e.target).val(value)
		
	$(elem).val(value);
}


// Возвращает позиции курсора в инпуте
function get_input_range(inputBox)
{
	if(document.selection) {
			var bookmark = document.selection.createRange().getBookmark()  
			var selection = inputBox.createTextRange()  
	   
			selection.moveToBookmark(bookmark)  
	  
			var before = inputBox.createTextRange()  
			before.collapse(true)  
			before.setEndPoint("EndToStart", selection)  
	  
			var beforeLength = before.text.length  
			var selLength = selection.text.length  
			
			start = beforeLength;
			end = beforeLength + selLength ;
			 
		}
		else {
			start = inputBox.selectionStart
			end = inputBox.selectionEnd
			  	
		}
		 
		return {  
		
                start: start,  
                end: end  
        }  
}



// Метод преобразования мс в формат чч:мм:сс
var timeFormat = (function (){
    function num(val){
        val = Math.floor(val);
        return val < 10 ? '0' + val : val;
    }

    return function (ms/**number*/, mode){
        var sec = ms / 1000
          , hours = sec / 3600  % 24
          , minutes = sec / 60 % 60
          , seconds = sec % 60
        ;
		
		if(mode==1)
		{
			return num(hours) + ":" + num(minutes);
		}
		else
		{
        	return num(hours) + ":" + num(minutes) + ":" + num(seconds);
		}
    };
	
	
})
();

defaults_data = {};

function loading_btn(btn_id, default_text)
{
	var elem = $('#'+btn_id).children('.btn_cont');
	var width = $(elem).css('width');
	
	if(!default_text)
	{
		defaults_data[btn_id] = $(elem).html();
		$(elem).css('width', width);
		$(elem).html('');
		$(elem).html('<div class="btn_loading"></div>');
	}
	else
	{
		$(elem).html(defaults_data[btn_id]);
	}
 
}

function simple_loading_btn(btn_id, default_text)
{
	var elem = $('#'+btn_id);
	var width = $(elem).css('width');
	var height = $(elem).css('height');
	
	if(!default_text)
	{
		defaults_data[btn_id] = $(elem).html();
		$(elem).css('width', width);
		$(elem).css('height', height);
		$(elem).html('');
		$(elem).html('<div class="btn_loading"></div>');
	}
	else
	{
		$(elem).html(defaults_data[btn_id]);
	}
 
}

function loading(elem_id, none)
{
	if(!none)
	{
		$('#'+elem_id).html('<img src="/img/loading5.gif">');
	}
	else
	{
		$('#'+elem_id).html('');
	}
}

// Подсветка полей с ошибками
function light_error_input(input)
{
	$('#'+input).addClass('light_error_input');
}

function _error()
{
	alert('Произошла ошибка. Попробуйте перезагрузить страницу');
}

// Преобразует сумму к виду 1.000.000,00
function sumProcess(string, sep_rubl, sep_kop)
{ 
	var tmp_str, j, value;
	var value_str = string;
	
	tmp_str = value_str.split('.');
	 
	  
	value = ''; 
	// Преобразуем рубли к виду 1.000.000
	if(tmp_str[0]!='')
	{
		j = 0;
		 
		for(i=tmp_str[0].length-1; i>=0; i--)
		{ 
			if(j==2)
			{
				value = sep_rubl+tmp_str[0].substr(i,1)+value;
				 
				j=0; 
			}
			else
			{ 
			 	value = tmp_str[0].substr(i,1)+value;
				j++;
			}
		}
		if(value.indexOf('.')===0)
		{
			 value = value.substr(1); 
		}
	}
	  
	if(tmp_str[1]!='')
	{
		value = value+sep_kop+tmp_str[1];
	}
	return value;
}

function num_format(string)
{
	num = string +''
	
	if(num.indexOf('.')<=0)
	{  
		num = num+'.00'
	}
	
	tmp = num.split('.')
	if(tmp[1].length<2)
	{
		num = num+'0'
	}
	
	return num;
}

function to_norm_date(date)
{
	var tmp;
	
	if(!date.match(/^[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{4}$/))
	{ 
		return '';
	}
	
	tmp = date.split('.');
	
	if(!parseInt(tmp[0]) || !parseInt(tmp[1]) || !parseInt(tmp[2]))
	{
		return '';
	}
	
	tmp[0] = tmp[0].length < 2 ?  '0'+tmp[0] : tmp[0];
	tmp[1] = tmp[1].length < 2 ?  '0'+tmp[1] : tmp[1];
	
	return tmp[2]+'-'+tmp[1]+'-'+tmp[0];
}

function valid_date_rus(date)
{
	if(!date.match( /[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{4}/g ))
	{
		return false;
	}
	 
	tmp = date.split('.');
	
	if(!parseInt(tmp[0]) || !parseInt(tmp[1]) || !parseInt(tmp[2]))
	{
		return false;
	}
	else return true;
}
// Переводит в формат времени
function parseDate(input, format) {
  if(!input)
  {
	  return '';
  }
  format = format || 'yyyy-mm-dd hh:ii:ss'; // default format
  var parts = input.match(/(\d+)/g),
      i = 0, fmt = {};
  // extract date-part indexes from the format
  format.replace(/(yyyy|dd|mm|hh|ii|ss)/g, function(part) { fmt[part] = i++; });
  parts[fmt['hh']] =  parts[fmt['hh']] ?  parts[fmt['hh']] : 0;
  parts[fmt['ii']] =  parts[fmt['ii']] ?  parts[fmt['ii']] : 0;
  parts[fmt['ss']] =  parts[fmt['ss']] ?  parts[fmt['ss']] : 0;
   
  return new Date(parts[fmt['yyyy']], parts[fmt['mm']]-1, parts[fmt['dd']], parts[fmt['hh']], parts[fmt['ii']], parts[fmt['ss']]);
}

function time_to_rus_date(date)
{
	// Разбираем заданную дату
	new_date = new Date(date);
	 
	month = new_date.getMonth()+1;
	day = new_date.getDate();
	year = new_date.getFullYear();
	
	month = month < 10 ?  '0'+month : month;
	day = day < 10 ?  '0'+day : day;
	
	return day+'.'+month+'.'+year;
}

function clear_block_by_settime(elem_id)
{
	setTimeout(function(){$('#'+elem_id).html('')},2000);
}

function isset_date(datestr)
{
	if($.trim(datestr)=='')
	{
		return false
	}
	if(/0000/.test(datestr))
	{
		return false;
	}
	else return true
}

// Прибавляет к дате необходимое кол-во дней
function date_plus_days(date, days)
{
	 
	if(date!='')
	{
		var dateobj = new Date(parseDate(date));
	}
	else
	{
		var dateobj = new Date();
	}
	 
	var new_date_seconds1 = Math.round(dateobj.getTime()/1000) + 3600 * 24 * days;
	
	var tmp_date1 = new Date(new_date_seconds1*1000);
	
	var new_date1 = tmp_date1.getFullYear()+'-'+wz(tmp_date1.getMonth() + 1)+'-'+wz(tmp_date1.getDate());
	
	return new_date1;
}

// Отнимает от даты необходимое кол-во дней
function date_minus_days(date, days)
{
	 
	if(date!='')
	{
		var dateobj = new Date(parseDate(date));
	}
	else
	{
		var dateobj = new Date();
	}
	 
	var new_date_seconds1 = Math.round(dateobj.getTime()/1000) - 3600 * 24 * days;
	
	var tmp_date1 = new Date(new_date_seconds1*1000);
	
	var new_date1 = tmp_date1.getFullYear()+'-'+wz(tmp_date1.getMonth() + 1)+'-'+wz(tmp_date1.getDate());
	
	return new_date1;
}

function wz(date_char)
{
	var date_char = date_char+'';
	 
	if(date_char.length<2)
	{
		date_char = '0'+date_char
	}
	
	return date_char;
}

function difference_in_days_between_dates(date1, date2, not_return_minus)
{
	var difference;
	
	 
	difference = to_mktime(date1, 1) - to_mktime(date2, 1);
	
	if(not_return_minus==1 && difference < 0)
	{
		return 0;
	}
	
	//alert(formate_seconds(difference, 'days'))
	
	return formate_seconds(difference, 'days');
}

function to_mktime(date, in_seconds)
{  //if(g)alert(date+' '+parseDate(date, '', g))
	var dateobj = new Date(parseDate(date));
	 
	var mktime = in_seconds==1 ? Math.round(dateobj.getTime() / 1000) : dateobj.getTime();
	
	return mktime;
}

function formate_seconds(seconds, return_w)
{
	days = Math.floor(seconds/86400);
		
	hours = Math.floor((seconds/3600)-days*24); 
		
	minuts = Math.floor((seconds-hours*3600-days*86400)/60); 
		
	seconds = seconds-(minuts*60+hours*3600+days*86400);
	
	if(return_w=='days')
	{
		return days;
	}
	 
}

function date ( format, timestamp ) {	// Format a local time/date
	// 
	// +   original by: Carlos R. L. Rodrigues
	// +	  parts by: Peter-Paul Koch (http://www.quirksmode.org/js/beat.html)
	// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +   improved by: MeEtc (http://yass.meetcweb.com)
	// +   improved by: Brad Touesnard
	
	var a, jsdate;
	
	if(!timestamp)
	{
		a, jsdate = new Date();
	}
	else
	{
		a, jsdate = new Date(timestamp ? timestamp : null);
	}
	
	var pad = function(n, c){
		if( (n = n + "").length < c ) {
			return new Array(++c - n.length).join("0") + n;
		} else {
			return n;
		}
	};
	var txt_weekdays = ["Sunday","Monday","Tuesday","Wednesday",
		"Thursday","Friday","Saturday"];
	var txt_ordin = {1:"st",2:"nd",3:"rd",21:"st",22:"nd",23:"rd",31:"st"};
	var txt_months =  ["", "January", "February", "March", "April",
		"May", "June", "July", "August", "September", "October", "November",
		"December"];

	var f = {
		// Day
			d: function(){
				return pad(f.j(), 2);
			},
			D: function(){
				t = f.l(); return t.substr(0,3);
			},
			j: function(){
				return jsdate.getDate();
			},
			l: function(){
				return txt_weekdays[f.w()];
			},
			N: function(){
				return f.w() + 1;
			},
			S: function(){
				return txt_ordin[f.j()] ? txt_ordin[f.j()] : 'th';
			},
			w: function(){
				return jsdate.getDay();
			},
			z: function(){
				return (jsdate - new Date(jsdate.getFullYear() + "/1/1")) / 864e5 >> 0;
			},

		// Week
			W: function(){
				var a = f.z(), b = 364 + f.L() - a;
				var nd2, nd = (new Date(jsdate.getFullYear() + "/1/1").getDay() || 7) - 1;

				if(b <= 2 && ((jsdate.getDay() || 7) - 1) <= 2 - b){
					return 1;
				} else{

					if(a <= 2 && nd >= 4 && a >= (6 - nd)){
						nd2 = new Date(jsdate.getFullYear() - 1 + "/12/31");
						return date("W", Math.round(nd2.getTime()/1000));
					} else{
						return (1 + (nd <= 3 ? ((a + nd) / 7) : (a - (7 - nd)) / 7) >> 0);
					}
				}
			},

		// Month
			F: function(){
				return txt_months[f.n()];
			},
			m: function(){
				return pad(f.n(), 2);
			},
			M: function(){
				t = f.F(); return t.substr(0,3);
			},
			n: function(){
				return jsdate.getMonth() + 1;
			},
			t: function(){
				var n;
				if( (n = jsdate.getMonth() + 1) == 2 ){
					return 28 + f.L();
				} else{
					if( n & 1 && n < 8 || !(n & 1) && n > 7 ){
						return 31;
					} else{
						return 30;
					}
				}
			},

		// Year
			L: function(){
				var y = f.Y();
				return (!(y & 3) && (y % 1e2 || !(y % 4e2))) ? 1 : 0;
			},
			//o not supported yet
			Y: function(){
				return jsdate.getFullYear();
			},
			y: function(){
				return (jsdate.getFullYear() + "").slice(2);
			},

		// Time
			a: function(){
				return jsdate.getHours() > 11 ? "pm" : "am";
			},
			A: function(){
				return f.a().toUpperCase();
			},
			B: function(){
				// peter paul koch:
				var off = (jsdate.getTimezoneOffset() + 60)*60;
				var theSeconds = (jsdate.getHours() * 3600) +
								 (jsdate.getMinutes() * 60) +
								  jsdate.getSeconds() + off;
				var beat = Math.floor(theSeconds/86.4);
				if (beat > 1000) beat -= 1000;
				if (beat < 0) beat += 1000;
				if ((String(beat)).length == 1) beat = "00"+beat;
				if ((String(beat)).length == 2) beat = "0"+beat;
				return beat;
			},
			g: function(){
				return jsdate.getHours() % 12 || 12;
			},
			G: function(){
				return jsdate.getHours();
			},
			h: function(){
				return pad(f.g(), 2);
			},
			H: function(){
				return pad(jsdate.getHours(), 2);
			},
			i: function(){
				return pad(jsdate.getMinutes(), 2);
			},
			s: function(){
				return pad(jsdate.getSeconds(), 2);
			},
			//u not supported yet

		// Timezone
			//e not supported yet
			//I not supported yet
			O: function(){
			   var t = pad(Math.abs(jsdate.getTimezoneOffset()/60*100), 4);
			   if (jsdate.getTimezoneOffset() > 0) t = "-" + t; else t = "+" + t;
			   return t;
			},
			P: function(){
				var O = f.O();
				return (O.substr(0, 3) + ":" + O.substr(3, 2));
			},
			//T not supported yet
			//Z not supported yet

		// Full Date/Time
			c: function(){
				return f.Y() + "-" + f.m() + "-" + f.d() + "T" + f.h() + ":" + f.i() + ":" + f.s() + f.P();
			},
			//r not supported yet
			U: function(){
				return Math.round(jsdate.getTime()/1000);
			}
	};

	return format.replace(/[\\]?([a-zA-Z])/g, function(t, s){
		if( t!=s ){
			// escaped
			ret = s;
		} else if( f[s] ){
			// a date function exists
			ret = f[s]();
		} else{
			// nothing special
			ret = s;
		}

		return ret;
	});
}

function init_select_date_block(current_date)
{
	var days_list, months_list, years_list = '';
	
	if(isset_date(current_date))
	{
		var current_date_day = parseInt(current_date.substr(8,2));
		var current_date_month = parseInt(current_date.substr(5,2));
		var current_date_year = parseInt(current_date.substr(0,4));
	}

	
	var monthnames = new Array(
	0,
	"Января",
	"Февраля",
	"Марта",
	"Апреля",
	"Мая",
	"Июня",
	"Июля",
	"Августа",
	"Сентября",
	"Октября",
	"Ноября",
	"Декабря");
	
	var actual_date = new Date();
	var actual_datetime = actual_date.getTime();
	
	var selected = '';
	
	months_list = '<option value="0">--</option>';
	years_list = '<option value="0">--</option>';
	
	// месяцы
	for(i=1; i<=12; i++)
	{
		selected = '';
		
		if(isset_date(current_date) && i==current_date_month)
		{
			selected = 'selected="selected"';
		}
		months_list += '<option value="'+i+'" '+selected+'>'+monthnames[i]+'</option>';
	}
	// года
	for(i=14; i<115; i++)
	{
		var tmp_year = actual_datetime - 3600 * 24 * 365 * i * 1000;
		
		var tmpdate = new Date(tmp_year);
		
		var year = tmpdate.getFullYear();
		
		selected = '';
	 
		if(isset_date(current_date) && year==current_date_year)
		{ 
			selected = 'selected="selected"';
		}
		
		years_list += '<option value="'+year+'" '+selected+'>'+year+'</option>';;
	} 
	 
	$('#bdate_month').html(months_list);
	$('#bdate_year').html(years_list);
	
	// Добавляем дни
	init_select_date_block_init_days(current_date);
}

chenge_to_day = 0;

function init_select_date_block_init_days(current_date)
{
	var year = $('#bdate_year').val();
	var month = $('#bdate_month').val();
	
	if(isset_date(current_date))
	{
		var current_date_day = parseInt(current_date.substr(8,2));
	}
	 
	// Кол-во дней в месяцах
	var monthdays = new Array(12);
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
	if(((year % 4 == 0) && !(year % 100 == 0)) ||(year % 400 == 0))
	{ 
		monthdays[1]++;
	}
	//alert(monthdays[month-1])
	var days_list = '<option value="0">--</option>';
	var selected;
	 
	if(month==0)
	{
		month = 1;
	}
	// дни
	for(i=1; i<monthdays[month-1] + 1; i++)
	{
		selected = '';
		 
		if((i==current_date_day && !chenge_to_day) || (chenge_to_day > 0 && chenge_to_day==i))
		{
			selected = 'selected="selected"';
		}
		days_list += '<option value="'+i+'" '+selected+'>'+i+'</option>';
	}
	
	$('#bdate_day').html(days_list);

}

// Слагает слова
function numToword(num, words)
{
	var num = num % 100;
	
	if (num > 19) 
	{
		num = num % 10;
	}
	switch (num) {
		case 1: {
			return(words[0]);
		}
		case 2: case 3: case 4: {
			return(words[1]);
		}
		default: {
			return(words[2]);
		}
	}
}



function open_popup()
{
	$('body').append('<div id="outer_shadow"></div>')  ;
	
	$("#outer_shadow").css('height', $(document).height());
	$(window).bind('mousewheel',function(event) {event.preventDefault();});
	$('#outer_shadow').fadeToggle();
	//var top_coord = (($(window).height() - $('.login').height()) / 2);
	//$('.login').css({top:top_coord+"px"});
	//$('.login').fadeToggle();
	 

}
	
function close_login()
{
	$('#outer_shadow').remove();
	$(window).unbind('mousewheel');
	 
	 
}

/*window.onload = function () {
			function sh(elem) {
				if (elem.style.display != 'block') {
					elem.style.display = 'block';
				} else {
					elem.style.display = 'none';
				}
			}
		
			var div = document.getElementById('div');
			
			document.body.onclick = function (e) {
				e = e || event;
				var target = e.target || e.srcElement;
				if (target.className == 'but') {
					sh(div);
				} else {
					while (target && target != div) {
						target = target.parentNode;
					}
					if (target != div) {
						div.style.display = 'none';
					}
				}
			}
		
		}*/
		
function create_popup_block(elem_id, width, data, cancel_text, hide_bottom)
{ 
	if(!width)
	{ 
		width = 500;
	}
	
	var cancel_text_str = 'Отмена';
	
	  
	if(cancel_text)
	{
		 cancel_text_str = cancel_text;
	}
	
	var bottom = '<br><br><a href="javascript:;" onclick="close_popup(\''+elem_id+'\')" class="cancel_add_btn">'+cancel_text_str+'</a>';
	
	if(hide_bottom)
	{
		 bottom = '';
	}
	
	 
	
	if($('#'+elem_id).attr('id')!=elem_id)
	{
		$('body').prepend('<div id="'+elem_id+'" class="_form_popup" style="width:'+width+'px"><div class="p_cont"></div>'+bottom+'</div>');
	}
	
	
	var open_width = $('#'+elem_id).width();
	 
		 
	var w_height = $(document).height();
	var w_width = $(window).width();
	
	var fxt = $('.fxc').offset();
	
	//alert(fxt.left)
	var top_coord = Number(fxt.top + 100);
	var left_coord =  Number((w_width / 2 + fxt.left) - (open_width / 2));
	
	
 
	if($('#outer_shadow_1').attr('id')!='outer_shadow_1')
	{
		$('body').prepend('<div id="outer_shadow_1"></div>');
		
		$('#'+elem_id).css('left', left_coord+'px');
		$('#'+elem_id).css('top', top_coord ? top_coord+'px' : 'auto');
	}
	 
	 
	$('#'+elem_id).show();
	
	$('#'+elem_id+' .p_cont').html(data);
	
}

function close_popup(elem_id, closeall)
{  
	if(closeall)
	{
		$('._form_popup').hide();
		$('#outer_shadow_1').remove();
	}
	else
	{
		$('#'+elem_id).hide();
		$('#outer_shadow_1').remove();
		 
	}
}