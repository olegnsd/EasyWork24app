<div style="display:none1" id="add_form_block" class="add_form_margin">
<div class="title_add_form">Изменить задачу</div>
<div class="add_form add_form_margin">

<table cellpadding="0" cellspacing="0" class="add_task_tb">
<tr>
    <td class="td_title">Тема:</td>
    <td class="td_value"><input type="text" class="input_text"  id="task_theme" value="{TASK_THEME}" maxlength="255" style="width:494px"/></td>
</tr>
<tr>
    <td class="td_title">Задание:<sup>*</sup></td>
    <td class="td_value"><textarea id="task_text" style="height:100px; width:494px" class="input_text">{TASK_TEXT}</textarea>
    </td>
</tr>
<tr>
	<td></td>
    <td class="td_value">{FILES_LIST}<div id="file_task_form_{TASK_ID}"></div></td>
</tr>
<tr>
    <td class="td_title">Постановщик задачи:<sup>*</sup></td>
    <td class="td_value"><select id="task_from_user">{TASK_FROM_SELECT}</select>
    </td>
</tr>
<tr>
    <td class="td_title td_vert_top"><span style="background-color:#CF6">Исполнитель:<sup>*</sup></span></td>
    <td class="td_value">
    <select id="task_performer_main_user">{TASK_PERFORMER_MAIN}</select> 
    </td>
</tr>
<tr>
    <td class="td_title td_vert_top"><span style="background-color:#CFC">Соисполнители:</span></td>
    <td class="td_value">
    <div id="task_users_performers">{TASK_USERS_PERFORMERS}</div>
    <a href="javascript:;" onclick="Tasks.add_task_user_row('performer');" id="add_task_performer_btn" class="link link_cfr_act">Добавить еще исполнителя</a>
    </td>
</tr>

<tr>
    <td class="td_title td_vert_top"><span style="background-color:#CC6">Копия:</span></td>
    <td class="td_value">
    <div id="task_users_copies">{TASK_USERS_COPIES}</div>
    <a href="javascript:;" onclick="Tasks.add_task_user_row('copy');" id="add_task_performer_btn" class="link link_cfr_act">Добавить еще в копию</a>
    </td>
</tr>

<tr>
	<td class="td_title">Крайний срок выполнения:</td>
    <td class="td_value"><input type="text" id="task_max_date" class="input_text" value="{MAX_DATE}" style="width:80px" placeholder="дд.мм.гггг"/> <input type="text" class="input_text" style="width:20px" maxlength="2" placeholder="чч" value="{MAX_DATE_HOURS}" id="task_max_date_hours"/> : <input type="text" class="input_text" style="width:20px" maxlength="2" placeholder="мм" value="{MAX_DATE_MINUTS}" id="task_max_date_minuts"/>
    </td>
</tr>
 

</table>

<div class="d_none" id="add_task_ext_pars">

<table cellpadding="0" cellspacing="0" class="add_task_tb">

<tr>
    <td class="td_title">Срочность:</td>
    <td class="td_value"><select id="task_priority" class="input_text">{PRIORITY_OPTION_LIST}</select></td>
</tr>
<tr>
    <td class="td_title">Сложность:</td>
    <td class="td_value"><select id="task_difficulty" class="input_text">{DIFFICULTY_OPTION_LIST}</select></td>
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
    <a href="javascript:;"  onclick="Tasks.show_add_task_extend_form()" class="link">показать все параметры</a></td>
</tr>
<tr class="task_add_btn_block">
    <td class="td_title"></td>
    <td class="td_value">
    <a class="button" onclick="Tasks.save_task('{TASK_ID}')" href="javascript:;" id="add_task_btn_0">
    <div class="right"></div><div class="left"></div><div class="btn_cont">сохранить задачу</div></a> <a onclick="close_popup('', 1)" href="javascript:;" class="cancel_add_btn">отмена</a>
	<div  class="clear"></div>
    </td>
</tr>
<tr id="add_task_result" class="task_add_btn_block">
    <td class="td_title"></td>
    <td class="td_value">
    <div id="task_form_result_0"></div>
     </td>
</tr>
</table>


</div>


</div>


 
<script>

if(!$('#task_users_performers select').size())
{
	Tasks.add_task_user_row('performer');
}
if(!$('#task_users_copies select').size())
{
	Tasks.add_task_user_row('copy');
}


Tasks.init_task_user_select('task_from_user', 'Выбрать постановщика задачи');
Tasks.init_task_user_select('task_performer_main_user', 'Выбрать исполнителя');

 
$("#task_max_date").datepicker({
      showOn: "button",
      buttonImage: "/img/calendar.gif",
      buttonImageOnly: true,
	  changeMonth: true,
      changeYear: true
    });

Disk.get_content_file_upload_form('task_{TASK_ID}', 6, 'file_task_form_{TASK_ID}');

</script>
 