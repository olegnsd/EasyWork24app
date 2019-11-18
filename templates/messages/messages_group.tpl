<script>
current_page = 0;
group_id = '{GROUP_ID}';
pages_count = '{PAGES_COUNT}';
</script>
 


<div id="planning_session">
<div class="msg_group_msgs_head_block">
{SESSION_PLANNING_CLOSED}
{CLOSE_PLANNING_SESSION}
<div class="msg_group_msgs_head">Планерку проводит: {PLANNING_SESSION_HEAD_USER}</div>
<div class="clear"></div>
</div>

<div id="messages_container" class="messages_container">

<div class="more_msgs_link_block" id="more_msgs_link_block">{MORE_MSGS_LINK}</div>

<div id="msgs_list">{MSGS_LIST}</div>

<div style="height:10px"></div>
</div>

<div class="add_form msgs_group_add_form add_form_ntrb_all" >
<div style="float:left">
    <textarea class="msg_add_block_textarea input_empty input_text" id="msg_add_text" onfocus="check_add_msg_textarea(1)" onblur="check_add_msg_textarea(2)" style="width:354px">Введите сообщение..</textarea>
    <div class="message_file_form" id="message_file_form" style="width:362px"></div>
    <div class="stand_margin msgs_group_add_form_btn">
    <a class="button" onclick="add_new_msg_to_msgs_group('{GROUP_ID}')" href="javascript:;" id="add_msg_btn"><div class="right"></div><div class="left"></div><div class="btn_cont">отправить</div></a> 
    <div class="msg_key_bl"><div class="msg_key"><b>Ctrl + Enter</b> - отправка сообщения</div></div>
    <div class="msgs_attach_wrap"><a href="javascript:;" onclick="show_msgs_add_file_form();">Файл</a></div>
    </div>
    
</div>

  
<div class="messages_group_us">
<div class="messages_group_us_wrap">
{MESSAGES_GROUP_USERS}
</div>
{ADD_USER_FORM}
</div>

<div class="clear"></div>
</div>
</div>

<script>
Disk.get_content_file_upload_form('{GROUP_ID}', 4, 'message_file_form');
$(document).ready(function(){
scroll_message_container();
refresh_new_messages_group(group_id, 1);
})
group_msg_s_init();
 
 $('#select_user').easycomplete(
		{
			str_word_select : 'Выбрать сотрудника',
			url:'/ajax/ajaxGetUsers.php?by=name&result_name=2',
			width:350,
			trigger : 0
		});
</script>



