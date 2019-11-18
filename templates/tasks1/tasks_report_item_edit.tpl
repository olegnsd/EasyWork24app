<div class="add_form add_form_margin" id="task_report_{REPORT_ID}">
<textarea id="report_text_{REPORT_ID}" class="input_text task_add_report_text" style="width:99% !important">{TASK_TEXT_EDIT}</textarea>
 
<div class="edit_files">
{FILES_LIST}
</div>

<div id="file_task_report_form_{REPORT_ID}_{TASK_ID}"></div>

<div class="add_form_btn_margin">
<a class="button" onclick="Tasks.save_task_report('{REPORT_ID}', '{TASK_ID}')" href="javascript:;" id="edit_report_btn_{REPORT_ID}"><div class="right"></div><div class="left"></div><div class="btn_cont">сохранить</div></a>
</div> <a class="cancel_add_btn" href="javascript:;" onclick="Tasks.get_report_item('{REPORT_ID}', 0, 1)">отмена</a>

<div class="clear"></div>

</div>

<script>
Disk.get_content_file_upload_form('task_report_{REPORT_ID}_{TASK_ID}', 7, 'file_task_report_form_{REPORT_ID}_{TASK_ID}');
</script>