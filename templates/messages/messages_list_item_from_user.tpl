<div id="message_{MESSAGE_ID}" class="msg_list_item {MSG_NOT_READ}" {BIND_READ_MSG} read_status="{READ_STATUS}" select="0" onclick="select_msg_item('{MESSAGE_ID}')" message_id='{MESSAGE_ID}' deleted="0" msg_is_my="0">

<div class="msgs_date">{MSG_DATE}</div>

<table cellpadding="0" cellspacing="0" class="msgs_item_tb">
	<tr>
    	<td class="user_left"><img src="{USER_AVATAR_SRC}"/></td>
        <td class="user_right"><b><a href="/id{FROM_USER_ID}" class="user_link">{SURNAME} {NAME} {MIDDLENAME}</a></b> {USER_ONLINE}
        <div class="user_cont">
        <div id="msgs_content_{MESSAGE_ID}">
            <div class="user_cont_text">{MSG_TEXT}</div>
        </div>
        </div>
        <div class="action_notice" id="message_result_{MESSAGE_ID}"></div>
        </td>
    </tr>
</table>
</div>