<div class="user_cont_block hover_v_edit_tools" id="comment_{COMMENT_ID}">
<div class="cont_hide_after_act_{COMMENT_ID}">
{EDIT_TOOLS}
</div>

<table cellpadding="0" cellspacing="0">
	<tr>
    	<td class="user_left"><a href="/id{USER_ID}" class="user_link"><img src="{AVATAR_SRC}"/></a><div class="loading" id="loading_{COMMENT_ID}"></div></td>
        <td class="user_right"><b><a href="/id{USER_ID}" class="user_link">{SURNAME} {NAME} {MIDDLENAME}</a></b> <span class="user_position">{USER_POSITION}</span>
        
        <div class="user_cont">
        <div class="cont_hide_after_act_{COMMENT_ID}" id="comment_content_{COMMENT_ID}">
            <div class="user_cont_text">{COMMENT_TEXT}</div>
            <div class="user_cont_sub">{DATE}</div>
        </div>
        <div id="comment_edit_{COMMENT_ID}"></div>
        </div>
        <div class="action_notice" id="comment_notice_{COMMENT_ID}"></div>
        </td>
    </tr>
</table>
</div>
 