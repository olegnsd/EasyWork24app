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
    <td class="td_value"><textarea style="height:100px" class="input_text" id="task_text_{TASK_ID}">{TASK_TEXT}</textarea>
    <div class="sub_text">
    <input type="checkbox" id="task_sms_notice_to_boss_{TASK_ID}" {TASK_BOSS_SMS_NOTICE_CHECKED} /> <label for="task_sms_notice_to_boss_{TASK_ID}">Уведомлять по <b>SMS</b> о новых отчетах и этапах выполнения задания</label></div>
    </td>
</tr>
<tr>
    <td class="td_title">Срочность:</td>
    <td class="td_value"><select id="task_priority_{TASK_ID}" class="input_text">{PRIORITY_OPTION_LIST}</select></td>
</tr>
<tr>
    <td class="td_title">Сложность:</td>
    <td class="td_value"><select id="task_difficulty_{TASK_ID}" class="input_text">{DIFFICULTY_OPTION_LIST}</select></td>
</tr>
<tr>
    <td class="td_title">Прикрепить сделку:</td>
    <td class="td_value"><select id="task_link_deal_{TASK_ID}" class="input_text">{DEAL_LINKED_SELECTED}</select></td>
</tr>
<tr>
	<td style="padding-bottom:0px"></td>
    <td class="td_value" style="padding-bottom:0px">{FILES_LIST}</td>
</tr>
<tr>
	<td></td>
    <td class="td_value"><div id="file_task_form_{TASK_ID}"></div></td>
</tr>
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
Disk.get_content_file_upload_form('task_{TASK_ID}', 6, 'file_task_form_{TASK_ID}');

$('#task_link_deal_{TASK_ID}').easycomplete(
{
	str_word_select : 'Поиск сделки',
	width:520,
	url:'/ajax/ajaxDeals.php?mode=get_deals'
});
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