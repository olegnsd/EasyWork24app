<div class="file_users_access_block">
<a href="javascript:;" onclick="show_access_file_block('{FOLDER_ID}', '{FILE_ID}')" class="access_btn link">Разрешения</a>
<div class="access_block" id="owner_block_{FOLDER_ID}_{FILE_ID}">
 
<div class="popup">
                <table cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td class="pp_top_left"></td>
                    <td class="pp_top_border"></td>
                    <td class="pp_top_right"></td>
                </tr>
                <tr>
                    <td class="pp_left_border"></td>
                    <td class="pp_cont">
                    <div class="access_proc" id="access_proc_{FOLDER_ID}_{FILE_ID}"></div>
<a class="link access_bl_close" style="float:right" onclick="$('#owner_block_{FOLDER_ID}_{FILE_ID}').hide()" href="javascript:;">Закрыть</a>
<div class="clear"></div>
					<div class="access_users_list_a_bl">
                    {USERS_LIST}
                    </div>
                    </td>
                    <td class="pp_right_border"></td>
                </tr>
                <tr>
                    <td class="pp_bottom_left"></td>
                    <td class="pp_bottom_border"></td>
                    <td class="pp_bottom_right"></td>
                </tr>
                </table>
                <div class="clear" style="clear:both"></div>
                </div>

</div>
</div>
