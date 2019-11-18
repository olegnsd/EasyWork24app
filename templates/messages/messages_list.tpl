<script>
to_user_id = '{TO_USER_ID}';
</script>
 


<div class="msg_add_block">
<div id="delete_selected_msgs_block" class="msgs_del_btn_block">
<a class="button" onclick="delete_messages()" href="javascript:;" id="delete_msgs_btn"><div class="right"></div><div class="left"></div><div class="btn_cont">удалить отмеченные сообщения</div></a>
</div>
<div>
<a href="javascript:;" onclick="dialog_search_toggle();" class="mdsl_sh" id="mdsl_sh">Поиск по истории сообщений</a>
<table cellpadding="0" cellspacing="0" class="search_msg_tb" id="mdsl_sh_tb">
	<tr>
    	<td><div class="wrap_msg_st" style="float:left"><input type="text" id="msg_search_text" class="search_msg_input" /><a href="javascript:;" onclick="dialog_search_toggle(1,1)" class="mdsl_hi"></a></div>
        
        <div class="search_dialogs_date">
    	 
    	
        <a href="javascript:;" class="msg_search_date_bl" onclick="msg_date_block_op('open')">Дата <span id="date_selected"></span></a> 
        
        <div class="search_date_block" id="search_date_block">
        
    	<input type="text" id="from_date" class=" input_text" style="width:80px" placeholder="С.."  onchange="if(!$('#to_date').val()) {$('#to_date').val($('#from_date').val())}"/>
        <span id="to_date_wrap">&nbsp;&nbsp;<input type="text" id="to_date"  class="input_text" style="width:80px;" placeholder="По.."/></span>
        
        <div  style="margin-top:20px">
        	<a href="javascript:;" class="link" onclick="msg_date_block_op('select')">Выбрать</a><span id="date_delete_btn_wrap" style="display:none">&nbsp;&nbsp <a href="javascript:;" class="link" onclick="msg_date_block_op('delete')">Сбросить дату</a> </span><span id="date_cancel_btn_wrap">&nbsp;&nbsp;<a href="javascript:;" class="link" onclick="msg_date_block_op('cancel')">Отмена</a></span>
        </div>
        
        </div>
    </div>
    
        </td>
        <td style="padding-left:10px"><a class="button" onclick="search_messages()" href="javascript:;" id="search_msgs_btn"><div class="right"></div><div class="left"></div><div class="btn_cont">поиск</div></a></td>
    </tr>
</table>
</div>
<br class="clear" />
</div>

<div id="messages_container" class="messages_container">
<div id="messages_body">{DIALOG_CONTENT}</div>
<div id="messages_search_body"><div id="prev_messages_searched_btn" style="text-align:center"></div><div id="messages_searched_list"></div></div>
<div style="height:0px;" id="mcbstr"></div>
</div>

<div class="add_form msgs_add_form" style="border-radius:0px">
<div id="to_end_of_dialog"></div>
<div id="message_add_form">
<div class="msg_theme_wrap" id="msg_theme_wrap">
<input type="text" class="msg_add_block_textarea input_text" style="height:auto;" placeholder="Тема.." id="msg_add_theme"/></div>

<div style="position:relative">
<a href="javascript:;" class="msg_theme_btn link_proc" onclick="$('#msg_theme_wrap').toggle();if(!$('#msg_theme_wrap').is(':visible')) {$('#msg_add_theme').val('')}">Тема сообщения</a>
<textarea class="msg_add_block_textarea input_text" id="msg_add_text" style="padding-right:90px !important; width:422px !important"   onkeydown="msg_read(0, 1);" placeholder="Введите сообщение.."></textarea>
</div>

<div class="message_add_users_form" id="message_group_user">
<div id="messages_users_group"></div>
<a href="javascript:;" onclick="msg_add_user_to_group_msgs();" id="" class="link link_cfr_act">Добавить еще пользователя</a>
</div>
<div class="message_file_form" id="message_file_form"></div>


<div class="msgs_add_form_btn">
<a class="button" onclick="add_new_msg('{TO_USER_ID}', 0)" href="javascript:;" id="add_msg_btn"><div class="right"></div><div class="left"></div><div class="btn_cont">отправить</div></a>
<div class="button_sep"></div>
<a class="button" onclick="add_new_msg('{TO_USER_ID}', 1)" href="javascript:;" id="add_msg_sms_btn"><div class="right"></div><div class="left"></div><div class="btn_cont">отправить и продублировать по смс</div></a>
<div class="msgs_attach_wrap"><a href="javascript:;" onclick="show_msgs_add_file_form();">Файл</a></div>
<div class="msgs_group_users_wrap"><a href="javascript:;" onclick="show_msgs_add_group_user_form();">Группа</a></div>
	
<br  class="clear"/>
<div class="msg_key"><b>Ctrl + Enter</b> - отправка сообщения</div>
</div>
</div>

</div>
<div style="position:fixed; height:1px solid; bottom:0px"></div>
<script>

dialog_window_size_init();

$("#from_date").datepicker({
      showOn: "button",
      buttonImage: "/img/calendar.gif",
      buttonImageOnly: true,
	  changeMonth: true,
      changeYear: true
    });

$("#to_date").datepicker({
      showOn: "button",
      buttonImage: "/img/calendar.gif",
      buttonImageOnly: true,
	  changeMonth: true,
      changeYear: true
    });	
	
msg_add_user_to_group_msgs();
$('.attached_files_tb .file_name').bind('click', function(event){
	event.stopPropagation();
}) 

$(document).ready(function(){
scroll_message_container();
	refresh_new_messages(to_user_id);
})
Disk.get_content_file_upload_form('{TO_USER_ID}', 3, 'message_file_form');
msg_s_init();
$('#messages_container').scroll(messages_list_scroll);

to_message_id = '{TO_MESSAGE_ID}'

if(to_message_id)
{
	sh_dialog_messages(to_message_id)
}

</script>



