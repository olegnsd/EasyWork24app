{ADD_FORM}

<script>
is_archive = '{IS_ARCHIVE}';
list_type = '{LIST_TYPE}';
key_words = '{KEY_WORDS}';
pages_count = '{PAGES_COUNT}';
status = '{STATUS}';
</script>

<div class="add_form  stand_margin" style=" margin-bottom:15px">
<select id="list_type"><option value="1">Мои трекинги</option><option value="2">Все трекинги</option></select>&nbsp;&nbsp;<span style="color:#D4BFFF">|</span>&nbsp;&nbsp;<input type="checkbox" id="show_acrhive" {ARCHIVE_CHECKED} /> <label for="show_acrhive">Архив ({ARCHIVE_COUNT})</label> &nbsp;&nbsp;<span style="color:#D4BFFF">|</span>&nbsp;&nbsp;

<input type="input_text"    class="input_text" id="key_words" placeholder="Ключевое слово.." value="{KEY_WORDS}">
&nbsp;&nbsp;<span style="color:#D4BFFF">|</span>&nbsp;&nbsp;
<select id="status" style="width:150px"><option value="0">-Статус-</option><option value="1">Обработка</option><option value="8_2">Прибыло в место вручения</option><option value="2">Вручение адресату</option><option value="12">Неудачная попытка вручения</option></select>

&nbsp;&nbsp;<a href="javascript:;" onclick="PostTracking.show_list();" class="link">Показать</a>
</div>
<div class="posttr_update_all_btn_bl" id="posttr_update_all_btn_bl">
<a class="button" onclick="PostTracking.update_tarcking_status(0, 1)" href="javascript:;" id="add_tracking_btn">
<div class="right"></div><div class="left"></div><div class="btn_cont">обновить статусы выделенных трекингов</div></a>
</div> 

        
<table cellpadding="0" cellspacing="0" class="tables_data_1 posttr_tb" style="width:100%">
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
{MORE_BTN}

<script>
$('.posttr_tb .track_checked_chbx').live('change', PostTracking.check_for_all_checked_list);
$('#list_type').val(list_type);
$('#status').val(status);
</script>


 

 