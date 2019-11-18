<script>
current_page = 0;
client_id = '{CLIENT_ID}'
from_user_id = '{FROM_USER_ID}';
from_client_id = '{FROM_CLIENT_ID}';
</script>
 



<div id="messages_container" class="messages_container">

<div class="more_msgs_link_block" id="more_msgs_link_block">{MORE_MSGS_LINK}</div>

<div id="msgs_list">{MSGS_LIST}</div>

<div style="height:10px"></div>
</div>

<div class="add_form msgs_add_form">
<textarea class="msg_add_block_textarea input_empty input_text" id="msg_add_text" onfocus="check_add_msg_textarea(1)" onblur="check_add_msg_textarea(2)">¬ведите сообщение..</textarea>

<div class="msgs_add_form_btn">
<a class="button" onclick="add_new_msg('{CLIENT_ID}', 0)" href="javascript:;" id="add_msg_btn"><div class="right"></div><div class="left"></div><div class="btn_cont">отправить</div></a>
<div class="msg_key_bl"><div class="msg_key"><b>Ctrl + Enter</b> - отправка сообщени€</div></div>	
<br  class="clear"/>
</div>

</div>

<script>
$(document).ready(function(){
	scroll_message_container();
	refresh_new_client_messages(client_id);
})

client_msg_s_init();
</script>



