<div class="users_list_item reject workers_item"  id="task_{USER_ID}">

<div class="user_list_action_block">
<div class="user_list_online_stat">Предложение отклонено | <a href="javascript:;" class="link" onclick="hide_rejected_notice('{CURRENT_USER_ID}', '{USER_ID}')">Скрыть</a></div>
</div>

<table cellpadding="0" cellspacing="0">
<tr>
    <td class="user_left"><a href="/id{USER_ID}" class="user_link"><img src="{AVATAR_SRC}"/></a><div class="loading" id="loading_{COMMENT_ID}"></div></td>
    <td class="user_right"><b><a href="/id{USER_ID}" class="user_link">{SURNAME} {NAME} {MIDDLENAME}</a></b> <span class="user_position">{USER_POSITION}</span>
    <div class="user_cont">
    <div class="user_cont_text">Заданий всего: {TASKS_COUNT}</div>
    </div>

    </td>
</tr>
</table>
<div class="clear" > </div>
</div>