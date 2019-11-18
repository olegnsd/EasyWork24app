<tr class="task {USER_TASK}" id="task_{TASK_ID}" task_id="{TASK_ID}" completed="{COMPLETED}" after_task_id="{AFTER_TASK_ID}" date_finished="{DATE_FINISHED}" num="{NUM}" is_participation=1>

<td class="task_num" num=""></td>

<td class="task_user_c" >{SELECTED_USER}{TASK_DESC}

<div style="margin:5px 0px 8px 0px"><a href="javascript:;" onclick="show_project_tasks_comments('{TASK_ID}')" class="link_proc">Отчет <span class="new_count" id="new_task_report_count_{TASK_ID}">{TASK_REPORTS_COUNT}</span></a></div>
<div id="comments_{TASK_ID}" class="project_task_comments_bl"></div>

<div class="project_task_completed_bl" id="project_task_completed_bl_{TASK_ID}">{COMPLETED_BTN}</div>
</td>

<td class="prt_after_sel_bl"><select id="after_task_{TASK_ID}" class="input_text after_task_s" disabled="disabled"></select></td>


<td class="prt_dates_bl">
<div class="prt_date_t">По плану</div>
<span task_id="{TASK_ID}" id="task_date_start_{TASK_ID}" class="prtdt date_start" not_picker="1">{DATE_START}</span>
<span task_id="{TASK_ID}" id="task_date_finish_{TASK_ID}" class="prtdt date_finish" value="{DATE_FINISH}" not_picker="1">{DATE_FINISH}</span>
<div class="clear"></div>
<div class="prt_date_t">По факту</div> 
<div><div class="prtdt" id="fact_date_start_{TASK_ID}"></div> <div class="prtdt" id="fact_date_finish_{TASK_ID}"></div>
<div class="clear"></div>
</div>
</td>
<td>&nbsp;</td>
</tr>