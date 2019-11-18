<div class="project_task_comments" id="task_comments_bl_{TASK_ID}">
<div class="cat_block" style=""><div class="" style="float:right"><a href="javascript:;" class="link" onclick="show_project_tasks_comments('{TASK_ID}', 1)">Закрыть</a></div>Отчет:</div>
<div id="report_add_form ">
<div class="add_form  add_form_ntrb" id="add_report_form">
<textarea id="task_report_text_{TASK_ID}" class="input_text" style="width:99%"></textarea>
 
<div class="add_form_btn_margin">
<a class="button" onclick="add_project_task_report('{TASK_ID}')" href="javascript:;" id="add_task_report_btn_{TASK_ID}"><div class="right"></div><div class="left"></div><div class="btn_cont">написать</div></a>	
<div  class="clear"></div>
</div>

</div>
</div>

<div id="task_reports_list_{TASK_ID}">{REPORTS_LIST}</div>
</div>

<script type="text/javascript">
tinymce.init({
    selector: "#task_report_text_{TASK_ID}",
	language : 'ru',
	plugins: ['table'
         
   ],menubar:false,
   toolbar1: "table"
 });
</script>