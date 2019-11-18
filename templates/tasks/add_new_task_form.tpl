<div class="title_add_form cat_block_margin" id="nt">Добавить новое задание <span id="get_copy_task_proc"></span></div>
<div class="add_form">
<table cellpadding="0" cellspacing="0" class="add_task_tb">
<tr>
	<td class="td_title">Крайний срок выполнения:</td>
    <td class="td_value"><input type="text" id="task_max_date" class="select_date_inp d_none input_text" disabled="disabled" onchange="if(this.value!=''){$(this).show(); $('#clear_max_task_date').show()}" />
    <a  href="javascript:;" onclick="$('#task_max_date').focus()" class="link" >выбрать</a>
    <span id="clear_max_task_date" class="d_none add_task_l_s">| 
    <a  href="javascript:;"  onclick="$('#task_max_date').val('');$('#task_max_date').hide(); $('#clear_max_task_date').hide()" class="link">очистить</a>
    </span></td>
</tr>
<tr>
    <td class="td_title">Тема:</td>
    <td class="td_value"><input type="text" class="input_text"  maxlength="80" id="task_theme"/></td>
</tr>
<tr>
    <td class="td_title">Задание:</td>
    <td class="td_value"><textarea id="new_task_text" style="height:100px" class="input_text"></textarea>
    <div class="sub_text">
    <input type="checkbox" id="task_sms_notice_to_boss" /> <label for="task_sms_notice_to_boss">Уведомлять по <b>SMS</b> о новых отчетах и этапах выполнения задания</label></div>
    
    </td>
</tr>
<tr>
	<td></td>
    <td class="td_value"><div id="file_form_{TO_USER_ID}"></div></td>
</tr>

</table>
<div class="d_none" id="add_task_ext_pars">

<table cellpadding="0" cellspacing="0" class="add_task_tb">
<tr>
    <td class="td_title">Дата старта:</td>
    <td class="td_value"><input type="text" id="task_date" class="select_date_inp d_none input_text" disabled="disabled" onchange="if(this.value!=''){$(this).show(); $('#clear_task_date').show()}" /> <a  href="javascript:;" onclick="$('#task_date').focus()" class="link" >выбрать</a>
    <span id="clear_task_date" class="d_none add_task_l_s">| 
    <a  href="javascript:;"  onclick="$('#task_date').val('');$('#task_date').hide(); $('#clear_task_date').hide()" class="link">очистить</a></span>
    </td>
</tr>
<tr>
    <td class="td_title">Желаемое время выполнения:</td>
    <td class="td_value"><select id="task_desired_days" class="input_text"><option value="0">Не принципиально</option>{TASK_DESIRED_LIST}</select></td>
</tr>
<tr>
    <td class="td_title">Срочность:</td>
    <td class="td_value"><select id="task_priority" class="input_text">{PRIORITY_OPTION_LIST}</select></td>
</tr>
<tr>
    <td class="td_title">Сложность:</td>
    <td class="td_value"><select id="task_difficulty" class="input_text">{DIFFICULTY_OPTION_LIST}</select></td>
</tr>
<tr>
    <td class="td_title">Прикрепить сделку:</td>
    <td class="td_value"><select id="task_link_deal" class="input_text"></select></td>
</tr>
<tr>
    <td class="td_title"></td>
    <td class="td_value"><a href="javascript:;" id="add_form_block_hide" onclick="hide_add_task_extend_form()" class="link">скрыть</a></td>
</tr>
</table>

</div>

<table cellpadding="0" cellspacing="0" class="add_task_tb">
<tr id="add_form_block_show">
    <td class="td_title"></td>
    <td class="td_value">
    <a href="javascript:;"  onclick="show_add_task_extend_form()" class="link">показать все параметры</a></td>
</tr>
<tr class="task_add_btn_block">
    <td class="td_title"></td>
    <td class="td_value">
    <a class="button" onclick="add_task({TO_USER_ID})" href="javascript:;" id="add_task_btn">
    <div class="right"></div><div class="left"></div><div class="btn_cont">добавить задание</div></a>
	<div  class="clear"></div>
    </td>
</tr>
<tr id="add_task_result" class="d_none task_add_btn_block">
    <td class="td_title"></td>
    <td class="td_value">
    <div id="task_success"></div>
    <div id="task_error" class="error_box"></div></td>
</tr>
</table>

</div>



<script>
task_copy = "{TASK_COPY}";

if(task_copy > 0)
{
	copy_task(task_copy);	
}

$(function() {
	$.datepicker.setDefaults(
	        $.extend($.datepicker.regional["ru"])
	  );
	$( "#task_date" ).datepicker()
	$( "#task_max_date" ).datepicker()
});

$('#task_link_deal').easycomplete(
{
	str_word_select : 'Поиск сделки',
	width:520,
	url:'/ajax/ajaxDeals.php?mode=get_deals'
});

Disk.get_content_file_upload_form('{TO_USER_ID}', 6, 'file_form_{TO_USER_ID}');

</script>