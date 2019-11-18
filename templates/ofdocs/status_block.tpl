<div class="status_bl">
<a href="javascript:;" onclick="ofdoc_show_statuses_list('{OFDOC_ID}');" class="access_btn link">Свойства <span id="status_new_count_{OFDOC_ID}" class="new_count"><b>{NEW_COUNT}</b></span></a>
<div class="access_block status_file_desk_block" id="status_block_{OFDOC_ID}">
 
<div class="popup" style="width:820px">
                <table cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td class="pp_top_left"></td>
                    <td class="pp_top_border"></td>
                    <td class="pp_top_right"></td>
                </tr>
                <tr>
                    <td class="pp_left_border"></td>
                    <td class="pp_cont">   
<a class="link access_bl_close" style="float:right" onclick="$('#status_block_{OFDOC_ID}').hide()" href="javascript:;">Закрыть</a>
<div class="clear"></div>
                    	
                        <textarea class="input_text" id="status_text_{OFDOC_ID}" style="width:585px"></textarea>
                        <div class="task_status_bar_block">
                        <div class="files_status_btn_block">{STATUS_BTNS}</div>
                        <div class="clear"></div>
                        </div>
                                                
                        <div class="cat_block" style="margin-top:10px; margin-bottom:10px">История статусов:</div>
                        
                        <div id="ofdoc_statuses_list_{OFDOC_ID}" style="width:585px">
                        <img src="/img/ajax-loader.gif" />
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