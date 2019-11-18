<div class="content_block_padn">

{TASK_NOTICE_BLOCK}

<div class="task_title">{TASK_THEME}</div>
<div class="task_text">{TASK_TEXT}</div>
{FILES_LIST}
</div>
<div class="content_block_padn" style="margin-top:15px">
<div class="task_sub_bl">{TASK_MAX_DATE} Срочность: <b>{TASK_PRIORITY}</b> | Сложность: <b>{TASK_DIFFICULTY}</b></div>
</div>

<div class="task_datetime">{TASK_DATE_ADD} {EDIT_BLOCK_INFO}</div>

<div class="task_status_bar" id="task_status_bar">
{TASK_STATUS_BAR}
</div>

<div class="content_block_padn">
{TASK_BTNS}
</div>

<div class="clear"></div>
<div class="task_tabs">

<a href="javascript:;" class="item active" id="task_tab_1" onclick="Tasks.show_task_tab(1);">Отчет</a>
<a href="javascript:;" class="item" id="task_tab_2" onclick="Tasks.show_task_tab(2);">Пользователи</a>
</div>
<div class="tab_content_wrap">

<div class="tab" id="tab_1" style="display:block">
{TASK_REPORT_BLOCK}
</div>
<div class="tab" id="tab_2">
{TASK_ROLES}
</div>

</div>


<script>
task_id = '{TASK_ID}';
</script>
 



 