<div id="message_{MESSAGE_ID}" class="msg_list_item {MSG_NOT_READ}" {BIND_READ_MSG} read_status="{READ_STATUS}" select="0"  message_id='{MESSAGE_ID}' deleted="0" msg_is_my="0">

<div class="msgs_date">{MSG_DATE}</div>

<table cellpadding="0" cellspacing="0" class="msgs_item_tb">
	<tr>
    	<td class="user_left"><img src="/img/client_avatar.jpg" /></td>
        <td class="user_right"><b>{CLIENT_TYPE} {CLIENT_NAME}</b> {CLIENT_ONLINE}
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

<!--<div id="message_{MESSAGE_ID}" class="msg_list_item {MSG_NOT_READ}"  msg_is_my="0" {BIND_READ_MSG} message_id='{MESSAGE_ID}' read_status="{READ_STATUS}" deleted="0" >
<div id="message_container_{MESSAGE_ID}">
<table cellpadding="0" cellspacing="0">
<tr>
<td class="user_avatar_block" style="vertical-align:top"><img src="/img/client_avatar.jpg" /></td>
<td class="message_us_con_block">
    <div class="msg_name_title"><span class="msg_client_name">{CLIENT_TYPE} {CLIENT_NAME}</span> {CLIENT_ONLINE}</div>
    <div class="msg_date">{MSG_DATE}</div>
    <div class="msg_text">{MSG_TEXT}</div>
</td>
</tr>
</table>
</div>
<div class="message_item_result" id="message_result_{MESSAGE_ID}"></div>
</div>-->