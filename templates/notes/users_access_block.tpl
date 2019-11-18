<div class="access_bl">
<div class="access_block"  style="display:block">

<div class="popup" style="display:block">
                <table cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td class="pp_top_left"></td>
                    <td class="pp_top_border"></td>
                    <td class="pp_top_right"></td>
                </tr>
                <tr>
                    <td class="pp_left_border"></td>
                    <td class="pp_cont">
                    <div class="access_proc" id="access_proc_{NOTE_ID}"></div>

<div class="clear"></div>
					<div class="access_users_list_a_bl1" id="access_users_list_{NOTE_ID}">
                    {USERS_LIST}
                    </div>
                    
                    <a class="link link_cfr_act" id="" onclick="add_user_to_access_note('{NOTE_ID}');" href="javascript:;">ƒобавить пользовател€</a>
                    
                    <div class="clear"></div>
                    <br />
                    <a id="save_access_btn_{NOTE_ID}" href="javascript:;" onclick="save_note_user_access('{NOTE_ID}')" class="button">
    				<div class="right"></div><div class="left"></div><div class="btn_cont">сохранить</div></a>
                    <a class="cancel_add_btn" onclick="$('#owner_block_{NOTE_ID}').html('')" href="javascript:;">закрыть</a>
                    <div class="clear"></div>
                    <div class="stand_marg" id="access_result_{NOTE_ID}"></div>
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
