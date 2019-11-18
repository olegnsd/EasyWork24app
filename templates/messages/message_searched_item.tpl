<div id="message_{MESSAGE_ID}" class="msg_list_item search_msg_item"  select="0" onclick="go_to_searched_message('{DIALOG_USER_ID}', '{MESSAGE_ID}')" message_id='{MESSAGE_ID}'>
<div class="msgs_date">{MSG_DATE}
 
</div> 
<table cellpadding="0" cellspacing="0" class="msgs_item_tb">
	<tr>
    	<td class="user_left"><img src="{USER_AVATAR_SRC}"/></td>
        <td class="user_right">{MSG_FROM_STR}<b><a href="/id{USER_ID}" class="user_link">{SURNAME} {NAME} {MIDDLENAME}</a></b> {USER_ONLINE}
        <div class="user_cont">
        <div id="msgs_content_{MESSAGE_ID}">
            <div class="user_cont_text">{MSG_THEME}{MSG_TEXT}</div>
        </div>
        </div>
        
        </td>
    </tr>
</table>
</div>

 