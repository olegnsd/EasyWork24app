<tr id="event_{EVENT_ID}">
<td colspan="5">
<div class="add_form add_form_margin event_edit_form">

<table cellpadding="0" cellspacing="0" class="evcal_add_tb">
	<tr>
    	<td class="td_title td_vert_top" style="width:80px">Название<sup>*</sup></td>
        <td class="td_value"><input type="text" id="event_name_{EVENT_ID}"  class="input_text" style="width:580px" value="{EVENT_NAME}"/>
        <div class="error_inp"></div>
        </td>
    </tr>

    <tr>
    	<td class="td_title td_vert_top" style="width:80px">Начало</td>
        <td class="td_value"><input type="text" id="event_start_{EVENT_ID}"  class="input_text" style="width:65px" value="{DATE_START_RUS}"/>&nbsp;&nbsp;&nbsp;<select class="input_text" style="padding:2px !important" id="event_start_hour_{EVENT_ID}">{TIME_HOURS_LIST_START}</select> : <select class="input_text" style="padding:2px !important" id="event_start_minute_{EVENT_ID}">{TIME_MINUTES_LIST_START}</select>
        <div class="error_inp"></div>
        </td>
    </tr>
    <tr>
    	<td class="td_title td_vert_top" style="width:80px">Конец</td>
        <td class="td_value"><input type="text" id="event_finish_{EVENT_ID}"  class="input_text" style="width:65px" value="{DATE_FINISH_RUS}"/>&nbsp;&nbsp;&nbsp;<select class="input_text" style="padding:2px !important" id="event_finish_hour_{EVENT_ID}">{TIME_HOURS_LIST_FINISH}</select> : <select class="input_text" style="padding:2px !important" id="event_finish_minute_{EVENT_ID}">{TIME_MINUTES_LIST_FINISH}</select>
        <div class="error_inp"></div>
        </td>
    </tr>
    <tr>
    	<td class="td_title td_vert_top" style="width:80px">Напомнить</td>
        <td class="td_value">За <select class="input_text" id="event_reminder_{EVENT_ID}">{REMINDER_LIST}</select> день(дня)
        <div class="error_inp"></div>
        </td>
    </tr>
	<tr>
    	<td class="td_title td_vert_top" style="width:80px">Описание</td>
        <td class="td_value"><textarea class="input_text" style="width:580px" id="event_desc_{EVENT_ID}">{EVENT_DESC}</textarea>
        <div class="error_inp"></div>
        </td>
    </tr>
    {CATEGORY_ROW}
    
    <tr>
    	<td class="td_title"></td>
        <td class="td_value">
        <a class="button" onclick="Evcal.save_event('{EVENT_ID}')" href="javascript:;" id="save_event_btn_{EVENT_ID}">
  	    <div class="right"></div><div class="left"></div><div class="btn_cont">сохранить</div></a>
        <div class="button_sep"></div>
        <a class="button" onclick="Evcal.get_event_item('{EVENT_ID}', 0, 'replace')" href="javascript:;" id="add_event_btn">
  	    <div class="right"></div><div class="left"></div><div class="btn_cont">отмена</div></a>
        
        <div class="clear"></div>
        <div class="error_box" id="error_box_{EVENT_ID}"></div>
	    <div id="success_{EVENT_ID}" class="success_marg"></div>

	</td>
        
    </tr> 
    
     
</table>

<script>
$("#event_start_"+{EVENT_ID}).datepicker({
			  showOn: "button",
			  buttonImage: "/img/calendar.gif",
			  buttonImageOnly: true,
			  changeMonth: true,
			  changeYear: true
			});
$("#event_finish_"+{EVENT_ID}).datepicker({
			  showOn: "button",
			  buttonImage: "/img/calendar.gif",
			  buttonImageOnly: true,
			  changeMonth: true,
			  changeYear: true
			});	
			
$("#event_start").bind('change', function() {if($('#event_finish').val()==''){$('#event_finish').val(this.value)}})		
</script>

</div>
</td>
</tr>
