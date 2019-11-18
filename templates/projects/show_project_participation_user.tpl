<div class="project_closed_title" id="project_str_closed_status">{PROJECT_CLOSED_STR}</div>
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
    <th title="Предшествующая задача">Пред.</th>
    <th class="">Дата старта &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Дата завершения</th>
    </tr>
   <tbody id="projects_tasks">
   {PROJECT_TASKS_LIST}
   </tbody>
   </table>
 
</td>
 
</tr>
</table>
 
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

<div >

<div class="clear"></div>
</div>

<div class="error_box" id="error_box"></div>
<div id="success" class="success_marg"></div>

</div>
</div>

{REPORT_BLOCK}

<script>
project_id = '{PROJECT_ID}';
$('.task .date_inp').live('change', show_gr_edited_notice);
projects_tasks_init();
after_tasks_select_init();
$('.after_task_s').live('change', pr_task_after_task_change);
</script>