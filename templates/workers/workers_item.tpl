<div class="users_list_item workers_item"  id="task_{USER_ID}">

<div class="user_list_action_block">
<div class="user_list_online_stat">{USER_ONLINE}{USER_IS_WORKING}{USER_LAST_ACTIVITY_BLOCK}</div>
<a href="/tasks?l=5&s=1&uid={USER_ID}" class="link">Задачи {NEW_TASK_REPORTS}</a><br />
<a href="/work?id={USER_ID}" class="link">Круг обязанностей {NEW_WORK_REPORTS}</a><br />
<a href="/msgs?id={USER_ID}" class="link">Написать сообщение</a><br />
<a href="/wktime/{USER_ID}" class="link">Присутствие на работе</a><br />

</div>

<table cellpadding="0" cellspacing="0">
<tr>
    <td class="user_left"><a href="/id{USER_ID}" class="user_link"><img src="{AVATAR_SRC}"/></a></td>
    <td class="user_right"><b><a href="/id{USER_ID}" class="user_link">{SURNAME} {NAME} {MIDDLENAME}</a></b> <span class="user_position">{USER_POSITION}</span>
    <div class="user_cont">
    <div class="user_cont_text">
    {USER_REMOVED_FROM_WORK} 
    {DEPUTY_WORKER}
    {USER_STATUS}Заданий всего: {TASKS_COUNT}
    
    </div>
    </div>

    </td>
</tr>
</table>
<div class="clear" > </div>
</div>
