<div class="cat_block" style="margin-top:5px">Отчет:</div>
<div class="" id="task_reports_list_{TASK_ID}">{TASK_REPORT_LIST}</div>

<div class="add_form add_form_margin" id="task_add_report_form_{TASK_ID}" style="display:{DISPLAY_ADD_TASK_REPORT_FORM}">

<div id="task_reports_proc_{TASK_ID}"></div>
<textarea id="task_report_{TASK_ID}" class="input_text task_add_report_text"></textarea>
<br /> 
<div id="file_task_report_form_{TASK_ID}"></div>

<div class="add_form_btn_margin">
<a class="button" onclick="add_task_report('{TASK_ID}')" href="javascript:;" id="add_report_btn_{TASK_ID}"><div class="right"></div><div class="left"></div><div class="btn_cont">{ADD_REPORT_TASK_BTN_VALUE}</div></a>

<div class="msg_key_bl" style="{BY_SMS_DESPLAY}"><div class="msg_key"><input type="checkbox" id="task_report_by_sms_{TASK_ID}" /> <label for="task_report_by_sms_{TASK_ID}">продублировать по <b>SMS</b></label></div></div>

<div  class="clear"></div>
</div>

</div>

<script>
Disk.get_content_file_upload_form('task_report_{TASK_ID}', 7, 'file_task_report_form_{TASK_ID}');
</script>

