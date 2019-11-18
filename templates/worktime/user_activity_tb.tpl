<a id="show_activity_a" href="javascript:;" onclick="$(this).hide(); $('#activity_list').slideDown(300)" class="link">показать активность за {CHECKED_DATE}</a>
<div class="d_none" id="activity_list">
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
    	<div class="title">Активность за {DATE}</div>
        <table cellpadding="0" cellspacing="0" class="activity_tb">
            <thead>
                <tr>
                    <th>время</th>
                    <th>статус</th>
                    <th>компьютер</th>
                </tr>
            </thead>
            <tbody>
            {ACTIVITY_LIST}
            </tbody>
        </table>
        <div style="margin-bottom:10px"></div>
        <a href="javascript:;" onclick="$('#show_activity_a').show(); $('#activity_list').slideUp(200)" class="link">скрыть</a>
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