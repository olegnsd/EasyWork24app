<div class="popup" style="width:370px; margin:auto">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
    <td class="pp_top_left"></td>
    <td class="pp_top_border"></td>
    <td class="pp_top_right"></td>
</tr>
<tr>
    <td class="pp_left_border"></td>
    <td class="pp_cont">
        <div class="user_tree_item" style="width:300px" >
            <table cellpadding="0" cellspacing="0" >
            <td class="user_tree_avatar_bl"><a href="/id{USER_ID}" class="link"><img src="{USER_AVATAR_SRC}" /></a></td>
            <td class="user_tree_name_block"><a href="/id{USER_ID}" class="link">{SURNAME} {NAME} {MIDDLENAME}</a>
            <div class="user_tree_reg">Дата регистрации: {REG_DATE}</div>
            <div class="user_tree_positions">
            <a  href="javascript:;" onclick="show_user_positions_block('{USER_ID}')" class="link_proc">{USER_POSITION}</a>
            </div>
            
            <div class="user_tree_positions_block" id="user_positions_{USER_ID}">
            
            <table cellpadding="0" cellspacing="0" class="user_tree_pos_tb">
            {USER_POSITIONS_HISTORY}
            </table>
            
            </div>
            </td>
            </table>
         
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