<style>
.tables_data_1_wrap_visible,.search_back_bl,.back_cont_bl{display:none;}
.tables_data_1_wrap_visible.vis,.search_back_bl.vis,.back_cont_bl.vis{display:block;}
</style>

<form method="post">
<div class="search_back_bl vis" style="border-radius:5px 5px 0px 0px">
<div class="search_list_block">
<textarea placeholder="Текст СМС" style="width: 100%;height: 50px;" name="sms" required></textarea>
</div>
</div>
<div class="back_cont_bl vis" >с: <input type="text" id="date_from2" name="segment[date1]" style="width:70px" value="{DATE_FROM}" /> по: <input type="text" style="width:70px" id="date_to2" name="segment[date2]" value="{DATE_TO}" /> &nbsp;&nbsp;<select id="search_deals_by_group_id2" style="width:150px" name="segment[group]"><option value="0">- Группа сделок -</option>{DEALS_GROUPS_LIST}</select> &nbsp;&nbsp;<select id="search_deals_by_status2" style="width:150px" name="segment[status]"><option value="0">- Статус сделок -</option>{DEALS_STATUS_LIST}</select>
<br><br> <button type="submit" class="button" style="outline: none;border: 0;height: auto;line-height: 19px;float: none;"> <div class="right"></div><div class="left"></div><div class="btn_cont">Добавить СМС рассылку</div></button> {CLEAR_DATE_BLOCK}</div>
</form>

<div style="border: 1px solid #5d7faa;margin-top: 15px;"><p style="background: #5d7faa;margin:0;padding: 10px;color: white;">Стоимость одной СМС - 1 руб.<br>СМС отправляются только при нахождении на этой странице. Отправка происходит раз в 20 сек</p>
<p style="padding: 10px;">Статус запроса отправки: <span id="ajsend" style="border-bottom:1px dotted #333;"></span></p>
<script>function ajcron(){
$('#ajsend').text('идёт запрос...');
$('#ajsend').load('/smscron.php',function(){
setTimeout(function(){ajcron();},999*20);
});
}
ajcron();
</script>
</div>

<table cellpadding="0" cellspacing="0" class="tasks_tb">
<thead>
    	<th>Текст СМС</th>
    	<th>Автор</th>
        <th>Сегмент</th>
        <th>Статус</th>
        <th></th> <th></th>
        <th>Экспорт лидов</th>
    </tr>
</thead>
<tbody id="tasks_list_body">
{SMS_LIST}
	
</tbody>
</table>
