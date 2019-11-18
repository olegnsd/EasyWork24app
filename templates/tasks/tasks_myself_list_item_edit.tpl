<div class="add_form" id="task_{TASK_ID}" style="margin-top:20px" show=0>

<table cellpadding="0" cellspacing="0" class="add_task_tb">
<tr>
	<td class="td_title">Крайний срок выполнения:</td>
    <td class="td_value">
    
    <input type="text" id="task_max_date_{TASK_ID}" class="select_date_inp d_none input_text" disabled="disabled" onchange="if(this.value!=''){$(this).show(); $('#clear_max_task_date_{TASK_ID}').show()}" value="{TASK_MAX_DATE}"/>
    <a  href="javascript:;" onclick="$('#task_max_date_{TASK_ID}').focus()" class="link" >выбрать</a>
    <span id="clear_max_task_date_{TASK_ID}" class="d_none add_task_l_s">| 
    <a  href="javascript:;"  onclick="$('#task_max_date_{TASK_ID}').val(''); $('#task_max_date_{TASK_ID}').hide();
    $('#clear_max_task_date_{TASK_ID}').hide()" class="link">очистить</a>
    </span>
    </td>
</tr>
<tr>
    <td class="td_title">Тема:</td>
    <td class="td_value"><input type="text" class="input_text"  maxlength="80" id="task_theme_{TASK_ID}" value="{TASK_THEME}"/></td>
</tr>
<tr>
    <td class="td_title">Задание:</td>
    <td class="td_value"><textarea style="height:100px" class="input_text" id="task_text_{TASK_ID}">{TASK_TEXT}</textarea></td>
</tr>
</table>


<table cellpadding="0" cellspacing="0" class="add_task_tb">
<tr class="task_add_btn_block">
    <td class="td_title"></td>
    <td class="td_value">
    <a class="button" onclick="save_edit_task('{TASK_ID}')" href="javascript:;" id="save_task_btn_{TASK_ID}">
    <div class="right"></div><div class="left"></div><div class="btn_cont">сохранить</div></a>
    <div class="button_sep"></div>
    <a class="button" onclick="task_cancel_edit('{TASK_ID}')" href="javascript:;" id="cancel_save_task_btn_{TASK_ID}">
    <div class="right"></div><div class="left"></div><div class="btn_cont">отменить</div></a>
    
	<div  class="clear"></div>
    </td>
</tr>
<tr id="add_task_result" class="d_none task_add_btn_block">
    <td class="td_title"></td>
    <td class="td_value">
    <div id="task_success"></div>
    <div id="task_error_{TASK_ID}" class="error_box"></div></td>
</tr>
</table>

</div>

<script>
$(function() {
	$.datepicker.setDefaults(
	        $.extend($.datepicker.regional["ru"])
	  );
	$("#task_max_date_{TASK_ID}").datepicker();
	
	if($('#task_max_date_{TASK_ID}').val())
	{
		$('#task_max_date_{TASK_ID}').show();
		$('#clear_max_task_date_{TASK_ID}').show();
	}
});
</script>