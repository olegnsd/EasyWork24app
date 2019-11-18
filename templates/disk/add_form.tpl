<script>
folder_id = '{FOLDER_ID}';
token = '{TOKEN}';
timestamp = '{TIMESTAMP}';
max_upload_size_limit = '{UPLOAD_SIZE_LIMIT}';
upload_version_file = 0;
act = "{ACT}";
</script> 

<div class="disk_head_bl">
<div class="wrap_btns wrap_btns_open">
<a href="javascript:;" onclick="Disk.open_file_upload_form();" class="upload" id="open_upl_form_btn">Загрузить файл</a>
<a href="javascript:;" onclick="Disk.open_create_folder_form();" class="create_f" id="open_create_folder_btn">Создать папку</a>
</div>

<div class="clear"></div>


<div class="upload_file_form" id="upload_folder_create_form">
 
	<table cellspacing="0" cellpadding="0" style="margin-left:8px">
    <tr>
    	<td>Название</td>
        <td style="padding:0px 0px 0px 10px"><input type="text" class="input_text" id="folder_name" style="width:300px" /></td>
    </tr>
    <tr>
    	<td></td>
        <td style="padding:10px 0px 0px 10px">
        <a class="button" onclick="Disk.create_folder()" href="javascript:;" id="create_folder_btn">
    	<div class="right"></div><div class="left"></div><div class="btn_cont">создать папку</div></a><div class="button_sep"></div>
        <a class="button" onclick="$('#open_create_folder_btn').trigger('onclick')" href="javascript:;" id="create_folder_btn">
    	<div class="right"></div><div class="left"></div><div class="btn_cont">отмена</div></a>
        </td>
    </tr>
    </table>
    
    <div class="clear"></div>
    <div id="upload_create_folder_proc" class="upl_proc_info"></div>
</div>


<div class="upload_file_form" id="upload_file_form">
 
<div id="upload_notice"></div>
<div id="file_upload_queue"></div>

<a href="javascript:;" onclick="$(this).hide(); $('#upload_file_info').show();" class="link" style="display:none; margin:10px 0px 10px 14px" id="upload_file_info_btn">Изменить свойства файла<div style="height:10px"></div></a>

<div class="upload_file_info" id="upload_file_info">
 
<table cellpadding="0" cellspacing="0">
<tr>
	<td>Название</td>
    <td style="padding:6px"><input type="text" id="upload_file_name"  class="input_text" style="width:300px" onblur="Disk.check_file_for_exists()"/></td>
</tr>
	<tr>
	<td style="vertical-align:top">Описание</td>
    <td style="padding:6px"><textarea id="upload_file_desc" class="input_text" style="width:300px"></textarea></td>
</tr>
</table>
</div>

<div id="upload_file_proc" class="upl_proc_info"></div>

<div class="after_upload_on_select" id="after_upl_on_select"  >


 
<a id="to_upload_file_btn" href="javascript:;" class="button"><div class="right"></div><div class="left"></div><div class="btn_cont"><span>Загрузить выбранный файл</span></div></a>
<div style="float:left; padding:4px 0px 0px 10px;">
или <a href="javascript:;" onclick="Disk.select_other_file();" >выбрать другой</a>
</div>
</div>

<a href="javascript:;" class="upl_ch_cancel_btn" onclick="$('#open_upl_form_btn').trigger('onclick')">Отменить</a>

<div id="upl_wrap_btn">	
 
<div style="float:left"><input type="file" name="file_upload" id="file_upload" /></div><div class="max_upl_size_inf">Максимальный размер файла не более {UPLOAD_SIZE_LIMIT}Мб</div>
</div>

<div class="clear"></div>
</div>

</div>

<script>
$(document).ready(function(){
	Disk.init_upload_html5_version('file_upload');
})
</script>