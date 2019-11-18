<div class="file_users_access_block">
<a href="javascript:;" onclick="show_edit_desc_block('{FOLDER_ID}', '{FILE_ID}')" class="access_btn link">Редактировать описание</a>
<div class="access_block edit_file_desk_block" id="edit_file_desc_block_{FOLDER_ID}_{FILE_ID}">
 
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
                     
                    	<textarea class="input_text" style="width:160px" id="desc_text_{FOLDER_ID}_{FILE_ID}"></textarea>
                        
                        <div style="margin-top:5px">
                        <a class="button" onclick="save_file_desc('{FOLDER_ID}', '{FILE_ID}')" href="javascript:;" 
                        id="save_file_desc_btn_{FOLDER_ID}_{FILE_ID}">
    <div class="right"></div><div class="left"></div><div class="btn_cont">сохранить</div></a>
    <div class="button_sep"></div>
    <a class="button" onclick="$('#edit_file_desc_block_{FOLDER_ID}_{FILE_ID}').hide()" href="javascript:;" id="add_good_btn">
    <div class="right"></div><div class="left"></div><div class="btn_cont">отмена</div></a>
    					<div class="clear"></div>
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
