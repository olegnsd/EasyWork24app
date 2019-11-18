<div class="add_form_margin">
<a href="javascript:;" onclick="Evcal.hide_events_list('{DATE}')" class="close_20" style="float:right"></a><div class="title_add_form">Добавить событие</div>
<div class="add_form add_form_margin">

<table cellpadding="0" cellspacing="0" class="evcal_add_tb">
	<tr>
    	<td class="td_title td_vert_top" style="width:80px">Название<sup>*</sup></td>
        <td class="td_value"><input type="text" id="event_name_{DATE}"  class="input_text" style="width:600px"/>
        <div class="error_inp"></div>
        </td>
    </tr>

    <tr>
    	<td class="td_title td_vert_top" style="width:80px">Начало</td>
        <td class="td_value"><input type="text" id="event_start_{DATE}"  class="input_text" style="width:65px" value="{DATE_RUS}"/>&nbsp;&nbsp;&nbsp;<select class="input_text" style="padding:2px !important" id="event_start_hour_{DATE}">{TIME_HOURS_LIST}</select> : <select class="input_text" style="padding:2px !important" id="event_start_minute_{DATE}">{TIME_MINUTES_LIST}</select>
        <div class="error_inp"></div>
        </td>
    </tr>
    <tr>
    	<td class="td_title td_vert_top" style="width:80px">Конец</td>
        <td class="td_value"><input type="text" id="event_finish_{DATE}"  class="input_text" value="{DATE_RUS}" style="width:65px"/>&nbsp;&nbsp;&nbsp;<select class="input_text" style="padding:2px !important" id="event_finish_hour_{DATE}">{TIME_HOURS_LIST}</select> : <select class="input_text" style="padding:2px !important" id="event_finish_minute_{DATE}">{TIME_MINUTES_LIST}</select>
        <div class="error_inp"></div>
        </td>
    </tr>
    <tr>
    	<td class="td_title td_vert_top" style="width:80px">Напомнить</td>
        <td class="td_value">За <select class="input_text" id="event_reminder_{DATE}">{REMINDER_LIST}</select> день(дня)
        <div class="error_inp"></div>
        </td>
    </tr>
	<tr>
    	<td class="td_title td_vert_top" style="width:80px">Описание</td>
        <td class="td_value"><textarea class="input_text" style="width:600px" id="event_desc_{DATE}"></textarea>
        <div class="error_inp"></div>
        </td>
    </tr>
    <tr>
    	<td class="td_title td_vert_top" style="width:80px">Категория</td>
        <td class="td_value"><select id="event_category_{DATE}" class="input_text"><option value="0">По умолчанию</option>{CATEGORIES_LIST}</select>
        <div class="error_inp"></div>
        </td>
    </tr>
    <tr>
    	<td class="td_title"></td>
        <td class="td_value">
        <a class="button" onclick="Evcal.add_event(1, '{DATE}')" href="javascript:;" id="add_event_btn">
  	    <div class="right"></div><div class="left"></div><div class="btn_cont">добавить событие</div></a>
        <div class="clear"></div>
        <div class="error_box" id="error_box_{DATE}"></div>
	    <div id="success_{DATE}" class="success_marg"></div>

	</td>
        
    </tr> 
    
     
</table>

</div>

<div class="stand_margin">
<a href="javascript:;" class="link" onclick="Evcal.hide_event_add_form('{DATE}')">Скрыть</a>
</div>

</div>



<script>

$("#event_start_{DATE}").datepicker({
			  showOn: "button",
			  buttonImage: "/img/calendar.gif",
			  buttonImageOnly: true,
			  changeMonth: true,
			  changeYear: true
			});
$("#event_finish_{DATE}").datepicker({
			  showOn: "button",
			  buttonImage: "/img/calendar.gif",
			  buttonImageOnly: true,
			  changeMonth: true,
			  changeYear: true
			});	
			
//$("#event_start_0").bind('change', function() {if($('#event_finish_0').val()==''){$('#event_finish_0').val(this.value)}})		
$("#event_start_{DATE}").bind('change', function() {
	$('#event_finish_{DATE}').val(this.value);
});

$("#event_start_hour_{DATE}").bind('change', function() {
	var date_start = $('#event_start_{DATE}').val();
	var event_finish = $('#event_finish_{DATE}').val();
	
	if(date_start==event_finish)
	{
		$("#event_finish_hour_{DATE}").val($(this).val());
	}
});

$("#event_start_minute_{DATE}").bind('change', function() {
	var date_start = $('#event_start_{DATE}').val();
	var event_finish = $('#event_finish_{DATE}').val();
	var event_start_hour = $('#event_start_hour_{DATE}').val();
	var event_finish_hour = $('#event_finish_hour_{DATE}').val();
	
	if(date_start==event_finish && event_start_hour==event_finish_hour)
	{
		$("#event_finish_minute_{DATE}").val($(this).val());
	}
});


show_add_form = '{SHOW_ADD_FORM}';

if(show_add_form)
{
	$().trigger('click');
}
</script>