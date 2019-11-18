<div class="notice_item" id="notice_{INVITE_USER_ID}">
{USER_POSITION} <b>{SURNAME} {NAME} {MIDDLENAME}</b> желает добавить Вас в список своих сотрудников.<br />
{COMMENT}
<div style="margin-top:10px">
<a href="javascript:;" onclick="confirm_to_worker_list('{INVITE_USER_ID}', '{INVITED_USER_ID}')" class="link">Разрешить</a> | 
<a href="javascript:;" onclick="not_confirm_to_worker_list('{INVITE_USER_ID}', '{INVITED_USER_ID}')" class="link">Отказать</a> | 
<a href="/tree/{INVITE_USER_ID}" class="link">Проверить подлинность</a>
</div>
</div>