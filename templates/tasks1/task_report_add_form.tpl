 <div class="add_form add_form_margin" id="task_add_report_form_{TASK_ID}" style="border-radius:0px;">

<div id="task_reports_proc_{TASK_ID}"></div>
<textarea id="report_text" class="input_text task_add_report_text" style="width:99% !important"></textarea>
 
<div id="file_task_report_form_{TASK_ID}"></div>

<div class="add_form_btn_margin">
<a class="button" onclick="Tasks.add_task_report('{TASK_ID}')" href="javascript:;" id="add_report_btn"><div class="right"></div><div class="left"></div><div class="btn_cont">добавить</div></a>

<div  class="clear"></div>
</div>


</div>

<script>
Disk.get_content_file_upload_form('task_report_{TASK_ID}', 7, 'file_task_report_form_{TASK_ID}');
</script>