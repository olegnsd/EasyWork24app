<div class="task_top_panel" id="user_tabs">
<a href="javascript:;" onclick="select_tab_on_main(this)" class="item active" tab="1">Основные</a>
<a href="javascript:;" onclick="select_tab_on_main(this)" class="item" tab="2">Задачи</a>
<a href="javascript:;" onclick="select_tab_on_main(this)" class="item" tab="4">Напоминания</a>
<a href="javascript:;" onclick="select_tab_on_main(this)" class="item" tab="3">Эффективность</a>
</div>

<div id="tab_conts" class="tab_conts">

<!-- Tab-->
<div tab="1" class="tab_cont_item" style="display:block">
<div style="margin-top:25px">
{PLANNING_CALENDAR}
</div>
{WORK_REPORTS_BLOCK}
{REPRIMANDS_BLOCK}
{FINANCES_BLOCK}
{COMMENTS_BLOCK}
</div>
<!-- end Tab-->

<!-- Tab-->
<div tab="2" class="tab_cont_item">
{ACTIVE_TASKS}
</div>
<!-- end Tab-->

<!-- Tab-->
<div tab="3" class="tab_cont_item">
<div class="charts_block">
{TASKS_FROM_USER_CHART}
{USER_EFFICIENCY_BLOCK}
{USER_EFFICIENCY_TASKS_COMPLETED_BLOCK}
{USER_EFFICIENCY_TASKS_BLOCK}
</div>
</div>
<!-- end Tab-->

<!-- Tab-->
<div tab="4" class="tab_cont_item">
<div style="margin-top:20px">{EVCAL_NOTICE}</div></div>
<!-- end Tab-->

</div>
