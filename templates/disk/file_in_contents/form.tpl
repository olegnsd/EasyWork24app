<script>
folder_id = 0;
token = '{TOKEN}';
timestamp = '{TIMESTAMP}';
max_upload_size_limit = '{UPLOAD_SIZE_LIMIT}';
upload_version_file = 0;
act = "content";
</script> 

<div class="upl_cont_add_form" id="content_file_upload_form_{ID}"> 
<div id="content_file_upload_{ID}_queue"></div>
<input type="hidden" id="content_file_upload_cont_type_{ID}" value="{CONTENT_TYPE}" />


<div class="upl_wrap_btn">
<div><input type="file" name="content_file_upload_{ID}" id="content_file_upload_{ID}" /></div><div class="max_upl_size_inf">Максимальный размер файла не более {UPLOAD_SIZE_LIMIT}Мб</div>
</div>
</div>

<div class="clear"></div>
 

<script>
$(document).ready(function(){
	Disk.init_upload_html5_version('content_file_upload_{ID}');
})
</script>