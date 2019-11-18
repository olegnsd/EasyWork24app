<div  id="add_session_planning" class="add_form_margin">
<div class="title_add_form">Уведомить сотрудников</div>
<div class="add_form add_form_margin">

<table cellpadding="0" cellspacing="0" class="tables_data_1" id="add_msg_group">
<tr>
	<td class="td_title td_vert_top">Сотрудники</td>
    <td class="td_value"><div class="users_to_msg_group">{USER_WORKERS_LIST}</div>
    <div id="error_users" class="td_error sub_input_error"></div></td>
    
</tr>

<tr> 
	<td class="td_title">Комментарий</td>
    <td class="td_value"><textarea id="msgs_group_desc" style="width:610px" class="input_text" maxlength="100" ></textarea>
    </td>
    </tr>

<tr>
	<td class="td_title"></td>
    <td class="td_value">
    <a class="button" onclick="add_msgs_group({USER_ID})" href="javascript:;" id="add_msgs_group">
    <div class="right"></div><div class="left"></div><div class="btn_cont">создать планерку</div></a>
    <div class="clear"></div>
    <div class="error_box" id="add_colleague_error"></div>
    <div id="add_colleague_result"></div>
    </td>
</tr>

</table>

</div>
<div class="stand_margin">
<a href="javascript:;" id="" class="link" onclick="$('#add_session_planning').hide();$('#add_session_planning_btn').show()">Скрыть</a>
</div>
</div>

<div class="add_new_list_item" id="add_session_planning_btn" >
<a href="javascript:;" class="link" onclick="$('#add_session_planning').show();$('#add_session_planning_btn').hide() ">+ Организовать планерку</a>
</div>


