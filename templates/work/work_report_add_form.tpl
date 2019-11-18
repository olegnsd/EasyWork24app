<div class="add_form">
<textarea style="width:700px; height:100px" class="input_text" id="work_report_text"></textarea>

<div id="file_form_{WORK_ID}"></div>
        
<div class="add_form_btn_margin">
<a class="button" onclick="add_report_for_work('{WORK_ID}')" href="javascript:;" id="add_work_report_btn">
<div class="right"></div><div class="left"></div><div class="btn_cont">добавить отчет</div></a>
<div class="clear"></div>
</div>

</div>

<script>
Disk.get_content_file_upload_form('{WORK_ID}', 1, 'file_form_{WORK_ID}');
</script>
