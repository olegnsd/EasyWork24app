<div class="cat_block" style="margin-top:35px">Комментарии:</div>
<div id="report_add_form " style="display:{REPORT_ADD_FORM_DISPLAY}">
<div class="add_form  add_form_ntrb" id="add_report_form">
<textarea id="report_text" class="input_text" style="width:700px"></textarea>
<br /> 

<div class="add_form_btn_margin">
<a class="button" onclick="add_project_report('{PROJECT_ID}')" href="javascript:;" id="add_report_btn_{PROJECT_ID}"><div class="right"></div><div class="left"></div><div class="btn_cont">{ADD_REPORT_BTN_VALUE}</div></a>	
<div  class="clear"></div>
</div>

</div>
</div>

<div id="reports_list">{REPORT_LIST}</div>

{MORE_BTN}


<script type="text/javascript">
tinymce.init({
    selector: "#report_text",
	language : 'ru',
	plugins: ['table'
         
   ],
   menubar:false,
   toolbar1: "table"
 });
</script>

<script>
pages_count = '{PAGES_COUNT}';
</script>

