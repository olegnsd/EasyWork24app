<div class="project_closed_title">{PROJECT_CLOSED_STR}</div>
<div class="project_title">{PROJECT_NAME}</div>
{PROJECT_DESC}
<div id="add_form_block" class="stand_margin">
<div class="add_form add_form_margin"> 
<table cellpadding="0" cellspacing="0" style="margin-bottom:10px" id="project_tasks_tb">
<tr>
<td style="vertical-align:top; min-width:300px">
    <table class="project_tasks_tb"  cellpadding="0" cellspacing="0" id="project_tasks_tb">
    <tr>
    <th class="nopdl">№</th>
    <th class="">Сотрудник</th>
    <th class="">Дата старта</th>
    <th class="">Дата завершения</th>
    <th></th>
    </tr>
   <tbody id="projects_tasks">
   {PROJECT_TASKS_LIST}
   </tbody>
   </table>
   <a href="javascript:;" onclick="add_project_task()" class="link" id="add_more_project_task_btn">[+] добавить еще задачу</a> 
</td>
<td style="vertical-align:top"></td>
</tr>
</table>


<div style="margin-bottom:10px" id="add_project_task_btn">
<a href="javascript:;" onclick="add_project_task()" class="link">[+] добавить задачу</a>
</div>
<div id="ggg"></div> 
<div class="d_none project_period_date" id="project_period_date">Дата старта: <span id="project_date_start"></span> Дата завершения: <span id="project_date_finish"></span></div>     
<div class="project_scheme d_none" id="project_scheme">
<table cellpadding="0" cellspacing="0" class="project_scheme_tb">
<thead class="project_scheme_month_th">
<tr id="project_scheme_month_th_tr"></tr>
</thead>
<thead class="project_scheme_days_th">
<tr id="project_scheme_days_th_tr"></tr>
</thead>
<tbody class="project_scheme_days_rows" id="project_scheme_days_rows"></tbody>
</table>
</div>
<div>
<div style="float:right"><div class="project_closed_title project_str_closed_status">{PROJECT_CLOSED_STR}</div>{PROJECT_CLOSE_BTN}</div>

<a class="button" onclick="save_project()" href="javascript:;" id="add_project_btn">
<div class="right"></div><div class="left"></div><div class="btn_cont">сохранить изменения</div></a>

 

<div class="clear"></div>
</div>

<div class="error_box" id="error_box"></div>
<div id="success" class="success_marg"></div>
<div id="success_close" class="success_marg"></div>

</div>

</div>

{REPORT_BLOCK}

<script>
project_id = '{PROJECT_ID}';
$('.task .date_inp').live('change', scheme_pre_init);
show_task_form_init();
</script>