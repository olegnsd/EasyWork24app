<script src="/js/upload/jquery.uploadifive.min.js"></script>
<script src="/js/upload/jquery.uploadify.min.js"></script> 
<script>
folder_id = '{FOLDER_ID}';
token = '{TOKEN}';
timestamp = '{TIMESTAMP}';
max_upload_size_limit = '{UPLOAD_SIZE_LIMIT}';
upload_version_file = '{UPLOAD_VERSION_FILE}';
act = "{ACT}";
</script> 

<style>
#queue .uploadifive-queue-item, #queue .uploadify-queue-item
{
	border:none;
	border-radius: 5px 5px 5px 5px !important;
}
</style>

<div style="display:none" id="add_form_block" class="add_form_margin">
<div class="title_add_form">Добавить версию файла</div>
<div class="  add_form_margin">

<div class="disk_head_bl">


<div class="upload_file_form" id="upload_file_form" style="display:block">
 

<div id="file_upload_queue"></div>



<div id="upload_file_proc" class="upl_proc_info"></div>

<div class="after_upload_on_select" id="after_upl_on_select"  >

 

<a id="to_upload_file_btn" href="javascript:;" class="button"><div class="right"></div><div class="left"></div><div class="btn_cont"><span>Загрузить выбранный файл</span></div></a>
<div style="float:left; padding:4px 0px 0px 10px;">
или <a href="javascript:;" onclick="Disk.select_other_file();" >выбрать другой</a>
</div>

</div>

<a href="javascript:;" class="upl_ch_cancel_btn" onclick="$('#add_form_block').hide(); $('#show_add_form_a').show()">Отменить</a>

<div id="upl_wrap_btn">	
 
<div style="float:left"><input type="file" name="file_upload" id="file_upload" /></div><div class="max_upl_size_inf">Максимальный размер файла не более {UPLOAD_SIZE_LIMIT}Мб</div>
</div>

<div class="clear"></div>
</div>

</div>

</div>


</div>


<div class="clear"></div>


<div class="add_new_list_item" id="show_add_form_a" > 
<a href="javascript:;" class="link" onclick="$('#add_form_block').fadeIn(200); $('#show_add_form_a').hide()" style="float:right; margin-bottom:3px"><span style="color:#0C0; font-weight:bold">+</span> Добавить версию</a>
<div class="clear"></div>
</div>

<script>
$(document).ready(function(){
	Disk.init_upload_html5_version('file_upload');
})
</script>