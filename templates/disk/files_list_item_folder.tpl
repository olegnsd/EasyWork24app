<tr class="f_item {NOTICE_CLASS}" id="item_folder_{FOLDER_ID}" item="folder_{FOLDER_ID}" {BIND_ACT}>
 
<td><a href="javascript:;" onclick="Disk.show_file_tools('folder_{FOLDER_ID}')" class="f_sel_btn"></a>
<div class="">
<div class="file_tools_bl file_open_popup" id="file_tools_folder_{FOLDER_ID}">
{TOOLS_LIST}
</div>
</div>
<div id="container_folder_{FOLDER_ID}" class="file_container_form file_open_popup">
<div class="title">Права доступа</div>
<div class="file_container_form_wrap_in">

</div>
</div>
</td>
<td class="name_td">{ACCESS_ICON}<div class="name_wrap"><div class="icon_cl ic_fl"></div><div class="name"><a href="/disk?fid={FOLDER_ID}&act={ACT}{NAV_TO_FOLDER_ID}" id="name_a_folder_{FOLDER_ID}">{NAME}</a></div></div><div id="act_folder_{FOLDER_ID}"></div></td>
<td class="info_author">{AUTHOR}</td>
<td class="info_c"></td>
<td class="info_c">{DATE_EDIT}</td>
</tr>