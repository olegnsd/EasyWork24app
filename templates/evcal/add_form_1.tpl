<div class="title_add_form">Добавить событие</div>
<div class="add_form add_form_margin">

<table cellpadding="0" cellspacing="0" class="evcal_add_tb">
	<tr>
    	<td class="td_title td_vert_top" style="width:80px">Название<sup>*</sup></td>
        <td class="td_value"><input type="text" id="event_name_0"  class="input_text" style="width:600px"/>
        <div class="error_inp"></div>
        </td>
    </tr>

    <tr>
    	<td class="td_title td_vert_top" style="width:80px">Начало</td>
        <td class="td_value"><input type="text" id="event_start_0"  class="input_text" style="width:65px"/>&nbsp;&nbsp;&nbsp;<select class="input_text" style="padding:2px !important" id="event_start_hour_0">{TIME_HOURS_LIST}</select> : <select class="input_text" style="padding:2px !important" id="event_start_minute_0">{TIME_MINUTES_LIST}</select>
        <div class="error_inp"></div>
        </td>
    </tr>
    <tr>
    	<td class="td_title td_vert_top" style="width:80px">Конец</td>
        <td class="td_value"><input type="text" id="event_finish_0"  class="input_text" style="width:65px"/>&nbsp;&nbsp;&nbsp;<select class="input_text" style="padding:2px !important" id="event_finish_hour_0">{TIME_HOURS_LIST}</select> : <select class="input_text" style="padding:2px !important" id="event_finish_minute_0">{TIME_MINUTES_LIST}</select>
        <div class="error_inp"></div>
        </td>
    </tr>
	<tr>
    	<td class="td_title td_vert_top" style="width:80px">Описание</td>
        <td class="td_value"><textarea class="input_text" style="width:600px" id="event_desc_0"></textarea>
        <div class="error_inp"></div>
        </td>
    </tr>
    <tr>
    	<td class="td_title td_vert_top" style="width:80px">Напомнить</td>
        <td class="td_value">За <select class="input_text" id="event_reminder_0">{REMINDER_LIST}</select> день(дня)
        <div class="error_inp"></div>
        </td>
    </tr>
    
    <tr>
    	<td class="td_title"></td>
        <td class="td_value">
        <a class="button" onclick="Evcal.add_event(0,0, '{PARS}')" href="javascript:;" id="add_event_btn">
  	    <div class="right"></div><div class="left"></div><div class="btn_cont">добавить событие</div></a>
        <a onclick="close_popup('', 1)" href="javascript:;" class="cancel_add_btn">отмена</a>
        
        <div class="clear"></div>
        <div class="error_box" id="error_box_0"></div>
	    <div id="success_0" class="success_marg"></div>

	</td>
        
    </tr> 
    
     
</table>

</div>




<script>

evcal_user_id = '{EVCAL_USER_ID}';

$("#event_start_0").datepicker({
			  showOn: "button",
			  buttonImage: "/img/calendar.gif",
			  buttonImageOnly: true,
			  changeMonth: true,
			  changeYear: true
			});
$("#event_finish_0").datepicker({
			  showOn: "button",
			  buttonImage: "/img/calendar.gif",
			  buttonImageOnly: true,
			  changeMonth: true,
			  changeYear: true
			});	
			
 

$("#event_start_0").bind('change', function() {
	$('#event_finish_0').val(this.value);
});

$("#event_start_hour_0").bind('change', function() {
	var date_start = $('#event_start_0').val();
	var event_finish = $('#event_finish_0').val();
	
	if(date_start==event_finish)
	{
		$("#event_finish_hour_0").val($(this).val());
	}
});

$("#event_start_minute_0").bind('change', function() {
	var date_start = $('#event_start_0').val();
	var event_finish = $('#event_finish_0').val();
	var event_start_hour = $('#event_start_hour_0').val();
	var event_finish_hour = $('#event_finish_hour_0').val();
	
	if(date_start==event_finish && event_start_hour==event_finish_hour)
	{
		$("#event_finish_minute_0").val($(this).val());
	}
});

</script>