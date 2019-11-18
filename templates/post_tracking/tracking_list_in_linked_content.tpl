<div class="cat_block cat_block_margin">Привязанные трекинги</div>
<div class="posttr_update_all_btn_bl" id="posttr_update_all_btn_bl" style="padding-top:5px">
<a class="button" onclick="PostTracking.update_tarcking_status(0, 1)" href="javascript:;" id="add_tracking_btn">
<div class="right"></div><div class="left"></div><div class="btn_cont">обновить статусы выделенных трекингов</div></a>
</div>
<table cellpadding="0" cellspacing="0" class="tables_data_1 posttr_tb" style="width:100%;margin-top:0px">
<thead>
	<tr class="tr_th">
    	<th style="width:10px"><input type="checkbox" id="track_checked_all" onchange="PostTracking.checked_all_list(this)"/></td>
    	<th>Номер</td>
        <th>Описание</td>
        <th>Добавлен</td>
        <th>Автор</td>
        <th>Текущий статус</td>
        <th style="width:10px"></td>
    </tr>
</thead>
<tbody id="posttr_list">
	{TRACKING_LIST}
</tbody>
</table>

<script>
$('.posttr_tb .track_checked_chbx').live('change', PostTracking.check_for_all_checked_list);
</script>