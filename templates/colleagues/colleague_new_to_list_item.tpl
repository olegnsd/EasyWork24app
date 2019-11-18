<div class="users_list_item workers_item not_confirm"  id="colleague_{INVITE_USER_ID}_{INVITED_USER_ID}">
<div class="content_hiiden_block_{INVITE_USER_ID}_{INVITED_USER_ID}">

<div class="user_list_action_block">
<a href="javascript:;" class='link' onclick="colleague_confirm('{INVITE_USER_ID}', '{INVITED_USER_ID}')">Подтвердить</a> | <a href="javascript:;" class='link' onclick="colleague_cancel_confirm('{INVITE_USER_ID}', '{INVITED_USER_ID}')">Отменить</a>

</div>

<table cellpadding="0" cellspacing="0">
<tr>
    <td class="user_left"><a href="/id{USER_ID}" class="user_link"><img src="{AVATAR_SRC}"/></a><div class="loading" id="loading_{COMMENT_ID}"></div></td>
    <td class="user_right"><b><a href="/id{USER_ID}" class="user_link">{SURNAME} {NAME} {MIDDLENAME}</a></b> <span class="user_position">{USER_POSITION}</span>
    <div class="user_cont">
    <div class="user_cont_text"></div>
    </div>

    </td>
</tr>
</table>
</div>
<div id="colleague_result_{INVITE_USER_ID}_{INVITED_USER_ID}"></div>
<div class="clear" > </div>
</div>