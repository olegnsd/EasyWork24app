<div class="st_margin_top">
<a id="show_worktime_list_a" href="javascript:;" onclick="$(this).hide(); $('#worktime_list').slideDown(200)" class="link">показать время работы за последние 30 дней</a>
</div>

<div style="display:none;" id="worktime_list">
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
    	<div class="title">Время работы за последние 30 дней</div>
        <table cellpadding="0" cellspacing="0" class="activity_tb">
        <thead>
            <tr class="tr_th">
                <th>Дата</th>
                <th>Время работы с авторизованных компьютеров</th>
                <th>Общее время работы</th>
            </tr>
        </thead>
        <tbody>
        {WORK_TIME_LIST}
        </tbody>
        </table>
        <div style="margin-bottom:10px"></div>
        <a href="javascript:;" onclick="$('#show_worktime_list_a').show(); $('#worktime_list').hide()" class="link">скрыть</a>
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