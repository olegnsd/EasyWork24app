<script>
current_page = 0;
current_user_id = '{CURRENT_USER_ID}'
no_dialog_msg_tpl = '{NO_DIALOG_MSG_TPL}';
pages_count = '{PAGES_COUNT}';
</script>


<div class="msg_main_list_top_line"></div>


<div id="search_in_msg_wrap"><div id="search_in_messages_btn" class="msg_main_list_item search_in_messages_btn" href="">Найти <span id="msg_search_text"></span> в сообщениях</div></div>

<div id="dialog_list" class="dialog_list">
{DIALOG_LIST}
</div>

{MORE_DIALOG_BTN}

<script>
$(document).ready(function(){
	dialog_init();
})
</script>
