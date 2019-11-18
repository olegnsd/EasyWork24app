<div style="display:none1" id="add_form_block" class="add_form_margin">
<div class="title_add_form">Добавить задачу</div>
<div class="add_form add_form_margin">

<table cellpadding="0" cellspacing="0" class="add_task_tb">
<tr>
    <td class="td_title">Тема:</td>
    <td class="td_value"><input type="text" class="input_text"  id="task_theme" maxlength="255" style="width:494px"/></td>
</tr>
<tr>
    <td class="td_title">Задание:<sup>*</sup></td>
    <td class="td_value"><textarea id="task_text" style="height:100px; width:494px" class="input_text"></textarea>
    </td>
</tr>
<tr>
	<td></td>
    <td class="td_value"><div id="file_form_new_task"></div></td>
</tr>
<tr>
    <td class="td_title">Постановщик задачи:<sup>*</sup></td>
    <td class="td_value"><select id="task_from_user">{TASK_FROM_SELECT}</select>
    </td>
</tr>
<tr>
    <td class="td_title td_vert_top"><span style="background-color:#CF6">Исполнитель:<sup>*</sup></span></td>
    <td class="td_value">
    <select id="task_performer_main_user"></select> 
    </td>
</tr>
<tr>
    <td class="td_title td_vert_top"><span style="background-color:#CFC">Соисполнители:</span></td>
    <td class="td_value">
    <div id="task_users_performers"></div>
    <a href="javascript:;" onclick="Tasks.add_task_user_row('performer');" id="add_task_performer_btn" class="link link_cfr_act">Добавить еще исполнителя</a>
    </td>
</tr>

<tr>
    <td class="td_title td_vert_top"><span style="background-color:#CC6">Копия:</span></td>
    <td class="td_value">
    <div id="task_users_copies"></div>
    <a href="javascript:;" onclick="Tasks.add_task_user_row('copy');" id="add_task_performer_btn" class="link link_cfr_act">Добавить еще в копию</a>
    </td>
</tr>

<tr>
	<td class="td_title">Крайний срок выполнения:</td>
    <td class="td_value"><input type="text" id="task_max_date" class=" input_text" style="width:80px" placeholder="дд.мм.гггг"/> <input type="text" class="input_text" style="width:20px" maxlength="2" placeholder="чч" value="" id="task_max_date_hours"/> : <input type="text" class="input_text" style="width:20px" maxlength="2" placeholder="мм" value="" id="task_max_date_minuts"/>
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
    <a class="button" onclick="Tasks.add_task()" href="javascript:;" id="add_task_btn_0">
    <div class="right"></div><div class="left"></div><div class="btn_cont">добавить задачу</div></a> <a onclick="close_popup('', 1)" href="javascript:;" class="cancel_add_btn">отмена</a>
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
Tasks.add_task_user_row('performer');
Tasks.add_task_user_row('copy');


$('#task_from_user').easycomplete(
{
	str_word_select : 'Выбрать постановщика задачи',
	width:396,
	url:'/ajax/ajaxGetUsers.php?current_user=1&by=name&who=all'
});	

$('#task_performer_main_user').easycomplete(
{
	str_word_select : 'Выбрать исполнителя',
	width:396,
	url:'/ajax/ajaxGetUsers.php?current_user=1&by=name&who=all'
});
 
$("#task_max_date").datepicker({
      showOn: "button",
      buttonImage: "/img/calendar.gif",
      buttonImageOnly: true,
	  changeMonth: true,
      changeYear: true
    });

Disk.get_content_file_upload_form('new_task', 6, 'file_form_new_task');

</script>
 